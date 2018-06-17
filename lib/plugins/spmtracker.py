import math
from collections import defaultdict
from sc2reader.constants import GAME_SPEED_FACTOR

class SPMTracker(object):
	"""
	Builds ``player.sps`` and ``player.spm`` dictionaries where an action a camera movement with bigger delta than 16

	Also provides ``player.avg_spm`` which is defined as the sum of all the
	above actions divided by the number of seconds played by the player (not
	necessarily the whole game) multiplied by 60.

	SPM is 0 for games under 1 minute in length.
	"""
	name = 'SPMTracker'

	def handleInitGame(self, event, replay):
		for human in replay.humans:
			human.spm = defaultdict(int)
			human.sps = defaultdict(int)
			human.old_loc = None

	def handleCameraEvent(self, event, replay):
		if event.player.old_loc is None:
			event.player.old_loc = event.location
			delta = 100
		else:
			distanceX = event.player.old_loc[0] - event.location[0]
			distanceY = event.player.old_loc[1] - event.location[1]

			delta = math.sqrt(math.pow(distanceX, 2) + math.pow(distanceY, 2))

		if delta >= 15:
			event.player.sps[event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]] += 1
			event.player.spm[int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]/60)] += 1

	def handleEndGame(self, event, replay):
		for human in replay.humans:
			if len(human.spm.keys()) > 0:
				human.avg_spm = sum(human.sps.values())/float(human.seconds_played)*60
			else:
				human.avg_spm = 0