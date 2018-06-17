from collections import defaultdict
from sc2reader.constants import GAME_SPEED_FACTOR
from collections import OrderedDict

ABILITY_LOOKUP = {
	"Corruption": "Corruption",
	"FungalGrowth": "Fungal Growth",
	"GuardianShield": "Guardian Shield",
	"Feedback": "Feedback",
	"MassRecall": "Mass Recall",
	"PlacePointDefenseDrone": "Place Point Defense Drone",
	"HallucinationArchon": "Hallucinate Archon",
	"HallucinationColossus": "Hallucinate Colossus",
	"HallucinationHighTemplar": "Hallucinate HighTemplar",
	"HallucinationImmortal": "Hallucinate Immortal",
	"HallucinationPhoenix": "Hallucinate Phoenix",
	"HallucinationProbe": "Hallucinate Probe",
	"HallucinationStalker": "Hallucinate Stalker",
	"HallucinationVoidRay": "Hallucinate VoidRay",
	"HallucinationWarpPrism": "Hallucinate Prism",
	"HallucinationZealot": "Hallucinate Zealot",
	"SeekerMissile": "Seeker Missile",
	"CalldownMULE": "Calldown MULE",
	"GravitonBeam": "Graviton Beam",
	"SpawnChangeling": "Spawn Changeling",
	"PhaseShift": "Phase Shift",
	"InfestedTerrans": "Spawn Infested Terran",
	"NeuralParasite": "Neural Parasite",
	"SpawnLarva": "Spawn Larva",
	"StimpackMarauder": "Stimpack",
	"SupplyDrop": "Supply Drop",
	"250mmStrikeCannons": "250mm Strike Cannons",
	"TimeWarp": "Timewarp",
	"WormholeTransit": "Wormhole Transit",
	"Stimpack": "Stimpack",
	"Snipe": "Snipe",
	"SiegeMode": "Siege up",
	"Unsiege": "Unsiege",
	"BansheeCloak": "Banshee Cloak",
	"ScannerSweep": "Scanner Sweep",
	"Yamato": "Yamato",
	"PsiStorm": "Psi Storm",
	"Blink": "Blink",
	"ForceField": "Force Field",
	"TacNukeStrike": "Tactical Nuke",
	"SalvageBunker": "Salvage Bunker",
	"EMP": "EMP",
	"Vortex": "Vortex",
	"BurrowCreepTumorDown": "Spread Creep",
	"Transfusion": "Transfusion",
	"GenerateCreep": "Generate Creep",
	"CreepTumorBuild": "Spread Creep",
	"BuildAutoTurret": "Build AutoTurret",
	"ArchonWarp": "Merge Archon",
	"Charge": "Charge",
	"Contaminate": "Contaminate",
	"MothershipCorePurifyNexus": "Photon Overcharge"
}

class AbilityTracker(object):
	"""
	Builds ``player.abilities`` which contains a statistic about which abilities where used how often
	"""
	name = 'AbilityTracker'

	def handleInitGame(self, event, replay):
		for pl in replay.players:
			pl.abilities = {}

	def handleCommandEvent(self, event, replay):
		if event.ability is not None and event.ability.name in ABILITY_LOOKUP:
			if not ABILITY_LOOKUP[event.ability.name] in event.player.abilities:
				event.player.abilities[ABILITY_LOOKUP[event.ability.name]] = 0
			event.player.abilities[ABILITY_LOOKUP[event.ability.name]] += 1
			
	def handleEndGame(self, event, replay):
		for pl in replay.players:
			statsOrdered = OrderedDict()
			for key, value in sorted(pl.abilities.iteritems(), key=lambda (k,v): (v,k), reverse=True):
				statsOrdered[key] = value
			pl.abilities = statsOrdered