from averagevalue import AverageValue
from chart import Chart
from sc2reader.constants import GAME_SPEED_FACTOR

class BaseStatsManager:
	"""
	Keeps track of the player's base statistics including charts during the parsing process
	"""

	def __init__(self):
		#average income
		self.averageIncome = AverageValue()
		#average unspent ressource AUR
		self.averageUnspent = AverageValue()        
		#ressource banks, army values (detailed) and supply count
		self.stats = {}
		self.workersBuilt = 0
		self.unitsTrained = 0
		self.structuresBuilt = 0
		self.unitsKilled = 0
		self.structuresRazed = 0
		self.baseCount = 1
		self.baseTimings = []
		self.saturationTimings = []

		self.charts = {}
		#army value chart (Total value of the current army)
		self.charts["armyValChart"] = Chart("armyValChart")
		#mineral income chart (Mineral collection rate)
		self.charts["incomeChartMin"] = Chart("incomeChartMin")
		#gas income chart (Vespene gas collection rate)
		self.charts["incomeChartGas"] = Chart("incomeChartGas")
		#workerCountChart: active worker count
		self.charts["workerCountChart"] = Chart("workerCountChart")
		#spendTechChart: spent ressources on technology
		self.charts["spendTechChart"] = Chart("spendTechChart")
		#resLostChart: total ressources lost
		self.charts["resLostChart"] = Chart("resLostChart")
		#resKilledArmyChart: ressources Killed in army value
		self.charts["resKilledArmyChart"] = Chart("resKilledArmyChart")
		#basecount chart
		self.charts["baseCountChart"] = Chart("baseCountChart")
		self.charts["baseCountChart"].add(0, 1)

	def update(self, event, replay):
		#average income
		self.averageIncome.add(event.minerals_collection_rate + event.vespene_collection_rate)
		#average unspent ressource AUR
		self.averageUnspent.add(event.minerals_current + event.vespene_current)
		#ressource banks, army values (detailed) and supply count
		self.stats[int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])] = {
			"mins": event.minerals_current,
			"gas": event.vespene_current,
			"armyMin": event.minerals_used_current_army,
			"armyGas": event.vespene_used_current_army,
			"foodMade": event.food_made,
			"foodUsed": event.food_used
		}

		#check if we need to save a saturation timing
		if len(self.saturationTimings) == 0 and event.minerals_collection_rate >= 640:
			#one base saturation
			self.saturationTimings.append(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])
		elif len(self.saturationTimings) == 1 and event.minerals_collection_rate >= 1280:
			#2 base saturation
			try:
				self.saturationTimings.append(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed] - self.baseTimings[0])
			except IndexError:
				#2 base income on 1 base is impressive :0
				self.saturationTimings.append(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])
		elif len(self.saturationTimings) == 2 and event.minerals_collection_rate >= 1920:
			#3 base saturation
			try:
				self.saturationTimings.append(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed] - self.baseTimings[1])
			except IndexError:
				#3 base income on not 3 base is impressive :0
				self.saturationTimings.append(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed])

		#army value chart (Total value of the current army)
		self.charts["armyValChart"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), event.minerals_used_current_army + event.vespene_used_current_army)
		#mineral income chart (Mineral collection rate)
		self.charts["incomeChartMin"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), event.minerals_collection_rate)
		#gas income chart (Vespene gas collection rate)
		self.charts["incomeChartGas"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), event.vespene_collection_rate)
		#workerCountChart: active worker count
		self.charts["workerCountChart"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), event.workers_active_count)
		#spendTechChart: spent ressources on technology
		self.charts["spendTechChart"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), (event.minerals_used_current_technology
			+event.minerals_used_in_progress_technology
			+event.minerals_lost_technology
			+event.vespene_used_current_technology
			+event.vespene_used_in_progress_technology
			+event.vespene_lost_technology))
		#resLostChart: total ressources lost
		self.charts["resLostChart"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), (event.minerals_lost_army
			+event.vespene_lost_army
			+event.minerals_lost_economy
			+event.vespene_lost_economy
			+event.minerals_lost_army
			+event.vespene_lost_technology))
		#resKilledArmyChart: ressources Killed in army value
		self.charts["resKilledArmyChart"].add(int(event.second//GAME_SPEED_FACTOR[replay.expansion][replay.speed]), event.minerals_killed_army + event.vespene_killed_army)

	def newBase(self, time):
		if len(self.baseTimings) < 2:
			self.baseTimings.append(time)
		self.baseCount += 1;
		self.charts["baseCountChart"].add(time, self.baseCount)

	def killBase(self, time):
		self.baseCount -= 1;
		self.charts["baseCountChart"].add(time, self.baseCount)

	def statsAt(self, time):
		while time > 0:
			if time in self.stats:
				return self.stats[time]
			time -= 1

		return None