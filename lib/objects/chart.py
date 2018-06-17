class Chart:
	"""
	A chart is the baseclass for all our chart objects
	It is used to save it's chart data in an object

	:param string name: The name of the given chart
	"""
	def __init__(self, name):
		self.name = name
		self.chartData = []

	def add(self, x, y):
		self.chartData.append({'x': x, 'y': y})

	def count(self):
		return len(self.chartData)