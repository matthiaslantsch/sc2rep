from objects import BaseStatsManager
from sc2reader.constants import GAME_SPEED_FACTOR
import math

class BaseStatsTracker:
	"""
	The BaseStatsTracker keeps track of most of the base stats and constructs beauthiful charts from it:
		- armyValChart
		- incomeChartMin
		- incomeChartGas
		- workerCountChart
		- spendStructChart
		- spendArmyChart
		- spendTechChart
		- resLostArmyChart
		- resLostStructChart
		- resLostChart
		- resKilledArmyChart
		- resKilledStructChart
		- resKilledChart

		-average income
		-average unspent ressources
	"""
	name = 'BaseStatsTracker'

	def handleInitGame(self, event, replay):
		#create a base stats manager object for each player
		for pl in replay.players:
			pl.basestats = BaseStatsManager()

	def handleTrackerEvent(self, event, replay):
		if event.name == 'PlayerStatsEvent':
			if event.player is not None and not event.player.is_observer:
				event.player.basestats.update(event, replay)

	def handleEndGame(self, event, replay):
		replay.charts = {}
		for pl in replay.players:
			#save average income in the player object
			pl.averageIncome = pl.basestats.averageIncome()
			#save average unspent ressources in the player object
			pl.averageUnspent = pl.basestats.averageUnspent()
			#Calculating the spending skill; see http://www.teamliquid.net/forum/starcraft-2/266019-do-you-macro-like-a-pro
			pl.spendingSkill = round(35*(0.00137*pl.averageIncome-math.log1p(pl.averageUnspent))+240)
			#basetimings
			pl.basetimings = pl.basestats.baseTimings
			#saturation speeds
			pl.saturationTimings = pl.basestats.saturationTimings
			#save all the chart data into one big list in the replay object for saving
			for chartName in pl.basestats.charts:
				chart = pl.basestats.charts[chartName]
				if not chartName in replay.charts:
					replay.charts[chartName] = {}
				replay.charts[chartName][pl.sid] = chart.chartData
