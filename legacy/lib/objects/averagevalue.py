class AverageValue:
	"""
	AverageValue is used to save temporary variables used to calucalate one of these average values:
		-income
		-unspent ressources
	"""
	def __init__(self):
		self.count = 0
		self.values = 0

	def __call__(self):
		if(self.count == 0):
			return 0
		return self.values // self.count

	def add(self, val):
		self.count = self.count + 1
		self.values += val