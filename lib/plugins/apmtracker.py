from collections import defaultdict
from sc2reader.constants import GAME_SPEED_FACTOR

class APMTracker(object):
	"""
	Builds ``player.aps`` and ``player.apm`` dictionaries where an action is
	any Selection, ControlGroup, or Command event.

	Also provides ``player.avg_apm`` which is defined as the sum of all the
	above actions divided by the number of seconds played by the player (not
	necessarily the whole game) multiplied by 60.

	APM is 0 for games under 1 minute in length.
	"""
	name = 'APMTracker'

	def handleInitGame(self, event, replay):
		for human in replay.humans:
			human.apm = defaultdict(int)
			human.aps = defaultdict(int)
			human.seconds_played = replay.length.seconds//GAME_SPEED_FACTOR[replay.expansion][replay.speed]

	def handleControlGroupEvent(self, event, replay):
		event.player.aps[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] += 1
		event.player.apm[int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]/60)] += 1

	def handleSelectionEvent(self, event, replay):
		event.player.aps[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] += 1
		event.player.apm[int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]/60)] += 1

	def handleCommandEvent(self, event, replay):
		event.player.aps[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] += 1
		event.player.apm[int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]/60)] += 1

	def handlePlayerLeaveEvent(self, event, replay):
		event.player.seconds_played = event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]

	def handleEndGame(self, event, replay):
		for human in replay.humans:
			if human.seconds_played > replay.length.seconds//GAME_SPEED_FACTOR[replay.expansion][replay.speed]:
				#strange bug on some replays, the leave event has faulty frame
				human.seconds_played = replay.length.seconds//GAME_SPEED_FACTOR[replay.expansion][replay.speed]
			if len(human.apm.keys()) > 0:
				human.avg_apm = sum(human.aps.values())/float(human.seconds_played)*60
			else:
				human.avg_apm = 0