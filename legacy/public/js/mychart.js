function initCharts() {
	initChart($('#armyValChart'), 'Army value', function() {
		//no tooltip as we have the details bar
		updateStatsBar(this.x);
		return false;
	});

	$('#armyValChart').highcharts().renderer.image(returnFWAlias()+'public/gfx/maps/'+$("#mapField").val()+'.jpg', 50, 50, 250, 250).attr({
		zIndex: 3
	}).add();

	initChart($('#incomeChartMin'), 'Mineral Income', function() {
		var s = 'Mineral income at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#incomeChartGas'), 'Vespene Gas Income', function() {
		var s = 'Vespene gas income at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#workerCountChart'), 'Active Worker count', function() {
		var s = 'Active Worker count at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#resLostChart'), 'Total Ressources lost', function() {
		var s = 'Total lost ressources at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#resKilledArmyChart'), 'Destroyed Army value', function() {
		var s = 'Killed army value at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#spendTechChart'), 'Investment in Upgrades', function() {
		var s = 'Total Investment into upgrades at '+Math.ceil((this.x/60))+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	initChart($('#apmChart'), 'Actions per minute', function() {
		var s = 'APM at '+(this.x+1)+' minutes:<br>';
		$.each(this.points, function(i, point) {
			s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
		});
		
		return s;
	});

	$('#baseCountChart').highcharts({
		chart: {
			defaultSeriesType: 'line'
		},
		title: {
			text: "Active bases"
		},
		xAxis: {
			min: 0,
			tickInterval: 1,
			labels: {
				enabled: false
			}
		},
		plotOptions: {
			series: {
				marker: {
					enabled: false
				},
				step: 'left'
			}
		},
		tooltip: {
			formatter: function() {
				var s = 'Active bases count at '+Math.ceil((this.x/60))+' minutes:<br>';
				$.each(this.points, function(i, point) {
					s += '<div style="color:'+point.series.color+'";font-weight:bold;">'+point.series.name+' :</div> '+point.y+'<br>';
				});
				
				return s;
			},
			crosshairs: {
				color: 'blue',
				dashStyle: 'solid'
			},
			shared: true
		},
		yAxis: {
			min: 0,
			title: {
				text: null
			}
		},
		credits: {
			enabled: false
		}
	});
	addSeriesForPlayer($("#baseCountChart").highcharts());

	$("#structuresGraph").highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Structures'
		},
		xAxis: {
			categories: [
				'Structures built',
				'Structures razed'
			],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Structures'
			}
		},
		tooltip: {
			formatter: function() {
				return '<span>'+this.point.category+' for '+this.point.series.name+':</span><p>'+this.point.y+'</p>';
			},
			useHTML: true
		},
		credits: {
			enabled: false
		}
	});
	addSeriesForPlayer($("#structuresGraph").highcharts());

	$("#unitsGraph").highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Units'
		},
		xAxis: {
			categories: [
				'Units Trained',
				'Units killed'
			],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Units'
			}
		},
		tooltip: {
			formatter: function() {
				return '<span>'+this.point.category+' for '+this.point.series.name+':</span><p>'+this.point.y+'</p>';
			},
			useHTML: true
		},
		credits: {
			enabled: false
		}
	});
	addSeriesForPlayer($("#unitsGraph").highcharts());
}

//some charts are always loaded
$(function() {
	$("#baseTimingChart").highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Base expand timings'
		},
		xAxis: {
			categories: [
				'Second mining base',
				'Third mining base'
			],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Time (seconds)'
			}
		},
		tooltip: {
			formatter: function() {
				return '<span>'+this.point.category+' for '+this.point.series.name+':</span><p>'+createTimeString(this.point.y)+'</p>';
			},
			useHTML: true
		},
		credits: {
			enabled: false
		}
	});

	$("#saturationTimingChart").highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Saturation speed'
		},
		xAxis: {
			categories: [
				'One base saturation (640)',
				'Two base saturation (1280)',
				'Three base saturation (1920)'
			],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Time (seconds)'
			}
		},
		tooltip: {
			formatter: function() {
				if(this.point.category == "One base saturation (640)") {
					return '<span>Time from the game start to one base saturation (640) <br>for '+this.point.series.name+' was '+createTimeString(this.point.y)+'</span>';
				} else if(this.point.category == "Two base saturation (1280)") {
					return '<span>Time from second mining base complete to <br>two base saturation (1280) for '+this.point.series.name+' was '+createTimeString(this.point.y)+'</span>';
				} else {
					return '<span>Time from third mining base complete to <br>three base saturation (1920) for '+this.point.series.name+' was '+createTimeString(this.point.y)+'</span>';
				}
			},
			useHTML: true
		},
		credits: {
			enabled: false
		}
	});

	$("#workersBuilt").highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Workers created'
		},
		xAxis: {
			categories: [
				'Workers created'
			],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Workers'
			}
		},
		tooltip: {
			formatter: function() {
				return '<span>'+this.point.series.name+' created '+this.point.y+' workers</span>';
			},
			useHTML: true
		},
		credits: {
			enabled: false
		}
	});
	
	var baseTimingChart = $("#baseTimingChart").highcharts();
	var saturationTimingChart = $("#saturationTimingChart").highcharts();
	var workerCountChart = $("#workersBuilt").highcharts();

	$.each($("#oldChartArea").data("players"), function(sid, pl) {
		baseTimingChart.addSeries({
			name: $("#name_"+sid).text(),
			id: pl.sid,
			data: pl.baseTimings
		});

		saturationTimingChart.addSeries({
			name: $("#name_"+sid).text(),
			id: pl.sid,
			data: pl.saturationTimings
		});

		if(typeof(pl.workerCount) != "undefined") {
			workerCountChart.addSeries({
				name: $("#name_"+sid).text(),
				id: pl.sid,
				data: [pl.workerCount]
			});
		}
	});

	baseTimingChart.redraw();
	saturationTimingChart.redraw();
	workerCountChart.redraw();
});