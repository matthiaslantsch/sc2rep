from sc2reader.constants import GAME_SPEED_FACTOR
from collections import OrderedDict
from assetsloader import assets

class CompositionTracker:
	"""
	The CompositionTracker keeps track of the units the player is building
	"""
	name = 'CompositionTracker'

	def handleInitGame(self, event, replay):
		#create a units manager object for the simulation
		for pl in replay.players:
			pl.workersBuilt = 0
			pl.structuresBuilt = 0
			pl.structuresRazed = 0
			pl.unitsTrained = 0
			pl.unitsKilled = 0

			pl.upgrades = []

	def handleTrackerEvent(self, event, replay):
		if event.name == 'UnitBornEvent':
			if event.unit.minerals > 0 and event.unit.owner is not None:
				if event.unit_type_name in ['SCV', 'Probe', 'Drone']:
					event.unit.owner.workersBuilt = event.unit.owner.workersBuilt + 1
				elif "Larva" not in event.unit.name:
					event.unit.owner.unitsTrained = event.unit.owner.unitsTrained + 1
				event.unit.journey = {}
				event.unit.journey[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] = {"x": event.x, "y": event.y}
				
		elif event.name == 'UnitDiedEvent':
			if event.killer is not None:
				if event.unit.is_army or event.unit.is_worker:
					event.killer.unitsKilled = event.killer.unitsKilled + 1
				else:
					event.killer.structuresRazed = event.killer.structuresRazed + 1
			if event.unit.minerals > 0 and event.unit.owner is not None:
				event.unit.journey[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] = {"x": event.x, "y": event.y}

		elif event.name == 'UnitDoneEvent':
			if event.unit.owner is not None:
				if event.unit.is_army or event.unit.is_worker:
					event.unit.owner.unitsTrained = event.unit.owner.unitsTrained + 1
				else:
					event.unit.owner.structuresBuilt = event.unit.owner.structuresBuilt + 1
		elif (event.name == 'UnitInitEvent'):
			#warped units are "placed" instead of spawned
			event.unit.journey = {}
			event.unit.journey[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] = {"x": event.x, "y": event.y}
		elif (event.name == 'UnitPositionsEvent'):
			for unit in event.units:
				if unit.minerals > 0 and unit.owner is not None:
					unit.journey[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] = {"x": unit.location[0], "y": unit.location[1]}
	def handleExpansionEvent(self, event, replay):
		event.player.basestats.newBase(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])

		for pli in replay.players:
			if event.player.sid != pli.sid:
				pli.basestats.charts["baseCountChart"].add(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed], pli.basestats.baseCount)

	def handleUpgradeCompleteEvent(self, event, replay):
		if event.frame > 100 and event.player is not None: #skip out on all the skin and rewards that pop in at the start of the game
			event.player.upgrades.append({"name": event.upgrade_type_name, "frame": event.frame})

	def handleEndGame(self, event, replay):
		#save the structures and the units to their owners details
		for pl in replay.players:
			if not pl.is_observer:
				pl.composition = {}
				pl.unitStatistics = {}
				for unit in pl.units:
					if unit.name is not None and unit.minerals > 0 and unit.finished_at is not None:
						previous = None
						lastTypeFrame = next(reversed(unit.type_history))
						#temporary list for the units
						saveMe = {}

						for frame in unit.type_history:
							formerType = unit.type_history[frame]
							if formerType.name in assets:
								if previous is not None and "canMorph" not in assets[unit.type_history[previous].name]:
									#no morphs avaible, skip
									break
								ret = {
									"id": str(unit.id)+str(frame),
									"name": formerType.name,
									"started": (frame >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed],
									"kills": 0,
									"type": assets[formerType.name]["type"],
									"journey": unit.journey
								}


								if previous is None:
									#save the finish for the first unit
									ret["spawned"] = (unit.finished_at >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
									ret["started"] = (unit.started_at >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
									if ret["spawned"] == ret["started"] and ret["spawned"] != 0:
										ret["started"] = ret["spawned"] - assets[ret["name"]]["time"]//GAME_SPEED_FACTOR[replay.expansion][replay.speed]

								else:
									#Subtract the morphing time from the started
									ret["started"] = ret["started"] - assets[ret["name"]]["time"]//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
									ret["spawned"] = (frame >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed]

									#mark the previous unit "dead" because it was morphed
									saveMe[previous]["died"] = ret["spawned"]
									saveMe[previous]["morphed"] = ret["id"]

								# if lastTypeFrame = frame => it's the last morph, meaning the death should count for that unit
								if lastTypeFrame == frame and unit.died_at is not None:
									ret["died"] = (unit.died_at >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed]

								stats = pl.basestats.statsAt(ret["started"])
								if stats is not None:
									ret["foodMade"] = stats["foodMade"]
									ret["foodUsed"] = stats["foodUsed"]
								else:
									#start supply
									ret["foodMade"] = 15
									ret["foodUsed"] = 12

								#save into temporary list
								saveMe[frame] = ret
								#keep a reference to the previous to enable setting the "morphed" time
								previous = frame
						for kill in unit.killed_units:
							for frame in unit.type_history:
								if frame in saveMe:
									possibleKiller = saveMe[frame]
									timeKill = (kill.died_at >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
									if possibleKiller["spawned"] < timeKill and ("died" not in possibleKiller or possibleKiller["died"] > timeKill):
										#it is the killer
										possibleKiller["kills"] += 1
										break

						for frame in saveMe:
							asset = saveMe[frame]
							if ("Adept" in unit.name or unit.is_army) and "spawned" in asset:
								#unit statistics
								if asset["name"] not in pl.unitStatistics:
									pl.unitStatistics[asset["name"]] = {
										"produced": 1,
										"lost": int(1 if "died" in asset else 0),
										"killed": asset["kills"],
										"maxKills": asset["kills"],
										"shortestLifeTime": unitLifeTime(asset, replay),
										"longestLifeTime": unitLifeTime(asset, replay),
										"avgLifetime": unitLifeTime(asset, replay)
									}
								else:

									pl.unitStatistics[asset["name"]]["produced"] += 1
									pl.unitStatistics[asset["name"]]["lost"] += int(1 if "died" in asset else 0)
									pl.unitStatistics[asset["name"]]["killed"] += asset["kills"]
									pl.unitStatistics[asset["name"]]["avgLifetime"] += unitLifeTime(asset, replay)

									if asset["kills"] > pl.unitStatistics[asset["name"]]["maxKills"]:
										pl.unitStatistics[asset["name"]]["maxKills"] = asset["kills"]

									if unitLifeTime(asset, replay) < pl.unitStatistics[asset["name"]]["shortestLifeTime"]:
										pl.unitStatistics[asset["name"]]["shortestLifeTime"] = unitLifeTime(asset, replay)

									if unitLifeTime(asset, replay) > pl.unitStatistics[asset["name"]]["longestLifeTime"]:
										pl.unitStatistics[asset["name"]]["longestLifeTime"] = unitLifeTime(asset, replay)

							#save it into the player composition
							pl.composition[asset["id"]] = asset

				for up in pl.upgrades:
					if up["name"] in assets:
						pl.composition[up["frame"]] = {
							"id": up["frame"],
							"name": assets[up["name"]]["name"],
							"spawned": (up["frame"] >> 4)//GAME_SPEED_FACTOR[replay.expansion][replay.speed],
							"type": "upgrade"
						}

						#subtract the build time from the json file
						pl.composition[up["frame"]]["started"] = (
							pl.composition[up["frame"]]["spawned"] - assets[up["name"]]["time"]//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
						)

						stats = pl.basestats.statsAt(pl.composition[up["frame"]]["started"])
						if stats is not None:
							pl.composition[up["frame"]]["foodMade"] = stats["foodMade"]
							pl.composition[up["frame"]]["foodUsed"] = stats["foodUsed"]

						if "level" in assets[up["name"]]:
							pl.composition[up["frame"]]["level"] = assets[up["name"]]["level"]

				#save structures build/razed graph
				if not "structuresGraph" in replay.charts:
					replay.charts["structuresGraph"] = {}
				replay.charts["structuresGraph"][pl.sid] = [
					pl.structuresBuilt,
					pl.structuresRazed
				]
				#save units trained/killed graph
				if not "unitsGraph" in replay.charts:
					replay.charts["unitsGraph"] = {}
				replay.charts["unitsGraph"][pl.sid] = [
					pl.unitsTrained,
					pl.unitsKilled
				]

				for unit_name in pl.unitStatistics:
					pl.unitStatistics[unit_name]["avgLifetime"] //= pl.unitStatistics[unit_name]["produced"]
					if pl.unitStatistics[unit_name]["lost"] == 0:
						pl.unitStatistics[unit_name]["percentLost"] = 0
					else:
						pl.unitStatistics[unit_name]["percentLost"] = round(float(pl.unitStatistics[unit_name]["lost"])/float(pl.unitStatistics[unit_name]["produced"]) * 100)

				statsOrdered = OrderedDict()
				for key, value in sorted(pl.unitStatistics.iteritems(), key=lambda (k,v): (v["produced"],k), reverse=True):
					statsOrdered[key] = value
				pl.unitStatistics = statsOrdered

def unitLifeTime(unit, replay):
	return (unit["died"] if "died" in unit else replay.real_length.seconds) - unit["spawned"]