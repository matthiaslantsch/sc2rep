from sc2reader.constants import GAME_SPEED_FACTOR
from objects import ExpansionEvent

class ExpansionTracker(object):
	"""
	yields "ExpansionEvent" and "ExpansionDestroyedEvent"
	['CommandCenter', 'Nexus', 'Hatchery']
	"""
	name = 'ExpansionTracker'

	def handleTrackerEvent(self, event, replay):
		if event.name == 'UnitDiedEvent':
			if not event.unit is None:
				if event.unit.name in ['CommandCenter', 'Nexus', 'Hatchery'] and event.unit.owner is not None:
					event.unit.owner.basestats.killBase(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])
					for pli in replay.players:
						if event.unit.owner.sid != pli.sid:
							pli.basestats.charts["baseCountChart"].add(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed], pli.basestats.baseCount)
		elif event.name == 'UnitDoneEvent':
			if not event.unit is None:
				if event.unit.name in ['CommandCenter', 'Nexus', 'Hatchery'] and event.unit.owner is not None:
					#expansion timing
					yield ExpansionEvent(event.unit, event.frame, event.unit.owner)