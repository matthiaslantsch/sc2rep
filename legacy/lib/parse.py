from __future__ import absolute_import, print_function, unicode_literals, division
import argparse, math, json, os, time, hashlib
import sc2reader
from plugins import ExpansionTracker, BaseStatsTracker, CompositionTracker, MsgTracker, AbilityTracker, APMTracker, SPMTracker
from sc2reader.engine.plugins import SelectionTracker
from sc2reader.constants import GAME_SPEED_FACTOR

parser = argparse.ArgumentParser(description="Prints all replay details to a json string and saves the rest in the cache.")
parser.add_argument("cacheFolder")
parser.add_argument('path', metavar='path', type=str, nargs=1, help="Path to the replay to serialize.")
args = parser.parse_args()

# Register all the replay plugins; order matters!
sc2reader.engine.register_plugin(SelectionTracker())
sc2reader.engine.register_plugin(APMTracker())
sc2reader.engine.register_plugin(SPMTracker())
sc2reader.engine.register_plugin(ExpansionTracker())
sc2reader.engine.register_plugin(BaseStatsTracker())
sc2reader.engine.register_plugin(CompositionTracker())
sc2reader.engine.register_plugin(AbilityTracker())
sc2reader.engine.register_plugin(MsgTracker())

replay = sc2reader.load_replay(args.path[0])

if(len(replay.plugin_failures) > 0):
	print("At least one plugin failed:")
	print(replay.plugin_result)
	exit(1)

repHash = str(replay.region.upper().replace("US", "NA"))
for ent in replay.entities:
	repHash += ent.name.encode("utf-8")+str(ent.toon_id)

repHash = str((replay.unix_timestamp - replay.real_length.seconds))+":"+hashlib.md5(b""+repHash).hexdigest()

replayData = {
	"tags": {
		"gametype": replay.real_type,
		"map": replay.map_name,
		"matchup": "v".join(sorted([team.lineup for team in replay.teams])),
	},
	"ladder": replay.is_ladder,
	"timestamp": time.mktime(replay.start_time.timetuple()),
	"length": replay.real_length.seconds,
	"loops": replay.frames,
	#maybe the start time will be off by ~ 1 minute
	"repHash": repHash,
	"players": {},
	"observers": {},
	"teams": {}
}

for obs in replay.observers:
	if obs.clan_tag is None:
		#on some replays, maybe old ones, this is None instead of ""
		obs.clan_tag = ""
	replayData['observers'][obs.toon_id] = {
		"name": obs.name,
		"clantag": obs.clan_tag,
		"fullname": '['+obs.clan_tag+']'+obs.name if len(obs.clan_tag) else obs.name,
		"region": obs.region,
		"toon": obs.toon_handle,
		"subregion": obs.subregion,
		"bnetId": obs.toon_id,
		"url": obs.url
	}

replay.charts["apmChart"] = {}

for pl in replay.players:
	if not hasattr(pl, "clan_tag") or pl.clan_tag is None:
		#on some replays, maybe old ones, this is None instead of ""
		pl.clan_tag = ""
	if not hasattr(pl, "url"):
		#computer players have no url
		pl.url = ""
	if not hasattr(pl, "avg_apm"):
		#computer players have no avg_apm
		pl.avg_apm = 0
		pl.avg_spm = 0

	if pl.toon_id == 0:
		#it's a computer player
		replayData["tags"]["other"] = "vs AI"

	replayData['players'][pl.sid] = {
		"name": pl.name,
		"clantag": pl.clan_tag,
		"region": pl.region, 
		"toon": pl.toon_handle, 
		"subregion": pl.subregion, 
		"bnetId": pl.toon_id, 
		"url": pl.url,
		"sid": pl.sid,
		"color": {
			"r": pl.color.r,
			"g": pl.color.g,
			"b": pl.color.b
		},
		"pickedRace": pl.pick_race if not pl.pick_race == "Unknown" else "Random", #Required to display fancy random icons
		"race": pl.play_race,
		"win": True if pl.result == 'Win' else False,
		"AI": not pl.is_human,
		"region": pl.region,
		"teamId": pl.team_id,
		"averageIncome": pl.averageIncome,
		"averageUnspent": pl.averageUnspent,
		"spendingSkill": pl.spendingSkill,
		"averageApm": pl.avg_apm,
		"averageSpm": pl.avg_spm,
		"basetimings": pl.basetimings,
		"saturationTimings": pl.saturationTimings,
		"workersBuilt": pl.workersBuilt
	}

	replay.charts["advStats_"+str(pl.sid)] = {
		"unitStatistics": pl.unitStatistics,
		"abilities": pl.abilities
	}

	if hasattr(pl, "apm"):
		replay.charts["apmChart"][pl.sid] = []
		for minute in pl.apm:
			replay.charts["apmChart"][pl.sid].append({"x": (minute), "y": pl.apm[minute]})


if replay.real_type == "1v1":
	modeTag = "1v1"
elif replay.real_type == "archon":
	modeTag = "archon"
else:
	modeTag = "team-"+replay.real_type

for tm in replay.teams:
	replayData['teams'][tm.number] = {
		"players": [pli.sid for pli in tm.players],
		"fullRanked": "?mode="+modeTag+"&player="+"&player=".join(sorted([pli.url for pli in tm.players]))
	}

details = {
	"players": {
		pl.sid: {
			"sid": pl.sid,
			"color": {"r": pl.color.r, "g": pl.color.g, "b": pl.color.b},
			"stats": pl.basestats.stats,
			"composition": pl.composition
		}
	for pl in replay.players}
}

if not os.path.isdir(args.cacheFolder):
	os.makedirs(args.cacheFolder)

f = open(args.cacheFolder+os.sep+'details.json','w')
f.write(json.dumps(details))
f.close()

f = open(args.cacheFolder+os.sep+'msg.json','w')
f.write(json.dumps(replay.msg))
f.close()

for ch in replay.charts:
	f = open(args.cacheFolder+os.sep+ch+'.json','w')
	f.write(json.dumps(replay.charts[ch]))
	f.close()

print(json.dumps(replayData))