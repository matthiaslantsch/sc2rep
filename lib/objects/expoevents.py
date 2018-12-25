from sc2reader.events.base import Event

class ExpansionEvent(Event):
	"""
	Event class for all expansion events.
	"""
	def __init__(self, unit, frame, player):
		#: The player object of the person that generated the event.
		self.player = player

		#: The unit that is the expansion structure
		self.unit = unit

		#: The frame of the game this event was applied
		self.frame = frame

		#: The second of the game (game time not real time) this event was applied
		self.second = frame >> 4

		#: Short cut string for event class name
		self.name = self.__class__.__name__

	def _str_prefix(self):
		player_name = self.player.name if getattr(self, 'pid', 16) != 16 else "Global"
		return "{0}\t{1:<15} ".format(Length(seconds=int(self.frame / 16)), player_name)

	def __str__(self):
		return self._str_prefix() + self.name