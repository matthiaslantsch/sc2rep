from sc2reader.constants import GAME_SPEED_FACTOR

class MsgTracker(object):
	"""
	Builds ``replay.msg`` dictionary where a chat log is contained
	"""
	name = 'MsgTracker'

	def handleInitGame(self, event, replay):
		replay.msg = {}

	def handleMessageEvent(self, event, replay):
		if(event.name == 'ProgressEvent'):
			return
		msg = {
			'type': event.name,
			'time': event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed],
			'user': event.player.name,
		}

		if(event.to_all):
			msg['rec'] = 'all'
		elif(event.to_allies):
			msg['rec'] = 'allied'
		elif(event.to_observers):
			msg['rec'] = 'obs'
		else:
			#somehow the booleans are not true and certain allied messages end up here
			msg['rec'] = 'allied'

		if event.name == 'ChatEvent':
			msg['msg'] = event.text
		elif event.name == 'PingEvent':
			msg['loc'] = {"x": event.x, "y": event.y}
		replay.msg[msg['time']] = msg