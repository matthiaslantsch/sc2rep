$(function() {
	$("#detailsBtn").on("click", function(){
		if(!$("#detailsBtn").hasClass("loader")) {
			toggleDetails();
		}
	});

	$("#tabs").tabs({
		activate: function() {
			$.each($(".chart"), function() {
				var highchart = $(this).highcharts();
				highchart.reflow();
			});
		},
		beforeLoad: function(event, ui) {
			ui.jqXHR.fail(function() {
				ui.panel.html("Couldn't load this tab. We'll try to fix this as soon as possible.");
			});
		},
		active: 0,
		cache: true
	});

	$('.comp').popover({
		html: true,
		placement: "bottom",
		container: "body",
		animation: false,
		content: function () {
			var html = "<h4>Army Compositon:</h4>";
			var units = $(this).data().units;
			var unitsCurrent = Object.keys(units);
			$.each($(this).data().unitsnames, function(i, name) {
				if($.inArray(name, unitsCurrent) != -1) {
					html += '<div class="unit_icons unit_icons_smaller '+name.toLowerCase()+'_smaller"><span>'+units[name]+'</span></div>';
				} else {
					html += '<div class="unit_icons unit_icons_smaller '+name.toLowerCase()+'_smaller washed_out"><span></span></div>';
				}
			});

			html += "<h4>Upgrades:</h4>";
			var ups = $(this).data().upgrades;
			var upsNow = Object.keys(ups);
			$.each($(this).data().upgradesnames, function(i, name) {
				if($.inArray(name, upsNow) != -1) {
					if(ups[name] > 0) {
						html += '<div class="upgrade_icons '+name.toLowerCase()+'"><span>'+ups[name]+'</span></div>';
					} else {
						html += '<div class="upgrade_icons '+name.toLowerCase()+'"><span></span></div>';
					}
				} else {
					html += '<div class="upgrade_icons '+name.toLowerCase()+' washed_out"><span></span></div>';
				}
			});

			html += "<h4>Structures:</h4>";
			var structs = $(this).data().structures;
			var structsCurrent = Object.keys(structs);
			$.each($(this).data().structuresnames, function(i, name) {
				if($.inArray(name, structsCurrent) != -1) {
					html += '<div class="buildings_icons buildings_icons_smaller '+name.toLowerCase()+'_smaller"><span>'+structs[name]+'</span></div>';
				} else {
					html += '<div class="buildings_icons buildings_icons_smaller '+name.toLowerCase()+'_smaller washed_out"><span></span></div>';
				}
			});

			return html;
		}
	});
});

function toggleDetails() {
	$(".popover").popover("hide");
	if(typeof($("#playerArea").data("players")) == "undefined") {
		loadDetails();
	}

	if(typeof($("#playerArea").data("players")) == "undefined") {
		setTimeout(toggleDetails, 5000);
	} else {
		$("#detailsBtn").text("").removeClass("loader");
		setLoading(false);

		$("#detailsArea").collapse('toggle');

		if($("#detailsBtn").hasClass("glyphicon-menu-up")) {
			$("#detailsBtn").addClass("glyphicon-menu-down").removeClass("glyphicon-menu-up");
		} else {
			$("#detailsBtn").removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
			$.each($(".chart"), function() {
				var highchart = $(this).highcharts();
				highchart.reflow();
			});
			updateStatsBar(0);
		}
	}
}

function loadDetails() {
	setLoading(true);
	//has to be loaded
	$("#detailsBtn").removeClass("glyphicon-menu-down").text("Loading...").addClass("loader");

	$.ajax({
		url: returnFWAlias()+"matches/"+$("#idMatch").val()+"/details",
		dataType: "json",
		accepts: {
			json: "application/json, text/javascript"
		},
		success: function(res) {
			if(!res.error) {
				data = JSON.parse(res.data);
				$("#playerArea").data("players", data.players);
				initCharts();
				$.each($(".chart"), function() {
					loadChartData($(this));
				});

				$.each($(".oldchart"), function() {
					var highchart = $(this).highcharts();
					$.each(highchart.series, function(sid, series) {
						series.options.color = "rgb("+data.players[sid].color.r+","+data.players[sid].color.g+","+data.players[sid].color.b+")";
						series.update(series.options);
					});
					highchart.redraw();
				});
			}
		},
		error: function() {
			alert('Cannot connect to the server at this time :\'(');
		},
		cache: true,
		async: false
	});
}

function addSeriesForPlayer(chart) {
	$.each($("#playerArea").data("players"), function(sid, pl) {
		chart.addSeries({
			color: "rgb("+pl.color.r+","+pl.color.g+","+pl.color.b+")",
			name: $("#name_"+pl.sid).text(),
			id: pl.sid,
			data: []
		});
	});
	chart.showLoading('<div class="loader">Loading...</div>');
}

function initChart(domObj, title, formatter) {
   domObj.highcharts({
		chart: {
			renderTo: domObj.prop("id"),
			defaultSeriesType: 'line'
		},
		title: {
			text: title
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
				}
			}
		},
		tooltip: {
			formatter: formatter,
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
   addSeriesForPlayer(domObj.highcharts());
}

function loadChartData(dom) {
	var chart = dom.highcharts();
	$.ajax({
		url: returnFWAlias()+"matches/"+$("#idMatch").val()+"/"+dom.prop("id"),
		dataType: "json",
		accepts: {
			json: "application/json, text/javascript"
		},
		success: function(res) {
			$.each(JSON.parse(res.data), function(plId, data) {
				chart.series[parseInt(plId)].setData(data, false);
			});

			chart.hideLoading();
			chart.redraw();
		},
		error: function() {
			chart.showLoading('<div>Unable to load this chart at this moment. Please try again later</div>');
		},
		cache: true,
		async: false
	});
}

function updateStatsBar(time) {
	if($("#timeField").data("time") == time) {
		//somehow it gets called multiple time with the same time
		return;
	}

	$("#timeField").data("time", time);
	$("#timeField").text(createTimeString(time));
	//reset minimap
	$(".redraw").remove();
	var renderer = $('#armyValChart').highcharts().renderer;
	var conversionX = parseInt($("#mapField").data("sizex")) / 250;
	var conversionY = parseInt($("#mapField").data("sizey")) / 250;

	$.each($("#playerArea").data("players"), function(sid, pl) {
		var clostestKey = closest(time, Object.keys($("#playerArea").data("players")[0].stats));
		$("#armyMins_"+pl.sid).text(pl.stats[clostestKey].armyMin);
		$("#armyGas_"+pl.sid).text(pl.stats[clostestKey].armyGas);
		$("#supply_"+pl.sid).text(Math.floor(pl.stats[clostestKey].foodUsed));
		$("#mins_"+pl.sid).text(pl.stats[clostestKey].mins);
		$("#gas_"+pl.sid).text(pl.stats[clostestKey].gas);

		var comp = {
			"units": {"current": {}, "names": []},
			"structures": {"current": {}, "names": []},
			"upgrades": {"current": {}, "names": []}
		}

		$.each(pl.composition, function(asset_id, asset) {
			if(asset.name == "WarpGate") {
				name = "Gateway"
			} else {
				name = asset.name
			}

			if(asset.type.indexOf("army") != -1 || asset.type.indexOf("worker") != -1) {
				//army/worker unit
				key = "units"
			} else if(asset.type.indexOf("struct") != -1) {
				//structure
				key = "structures"
			} else {
				//upgrade
				key = "upgrades"
			}

			if($.inArray(name, comp[key].names) == -1) {
				comp[key].names.push(name);
			}

			if(asset.spawned <= time && (typeof(asset.died) == "undefined" || asset.died > time)) {
				if(typeof(comp[key].current[name]) == "undefined") {
					comp[key].current[name] = 0;
				}
				if(key == "upgrades") {
					if(typeof(asset.level) != "undefined") {
						comp[key].current[name] = asset.level;
					}
				} else {
					//draw the unit/structure
					var clostestJourneyEntry = closest(time, Object.keys(asset.journey));
					var position = asset.journey[clostestJourneyEntry];
					var size = (key == "structures" ? 6 : 3);

					renderer.rect((position.x / conversionX) + 50, ((parseInt($("#mapField").data("sizey")) - position.y) / conversionY) + 30, size, size, 1).attr({
				        fill: "rgb("+pl.color.r+","+pl.color.g+","+pl.color.b+")",
						zIndex: 5,
						class: "redraw"
				     }).add();
					comp[key].current[name]++;
				}
			}

		});

		$.each(comp, function(name, lists) {
			lists.names.sort();
			$("#comp_"+pl.sid).data(name, sortByObjectKey(lists.current));
			$("#comp_"+pl.sid).data(name+"names", lists.names);
		});

		$(".popover:visible").popover("show");
	});
}

function initAdvancedTab(id) {
	$("#advtabs_"+id).tabs({active: 0});

	$(".buildorderBtn").on("click", function() {
		if(!$(this).hasClass("disabled")) {
			$("."+$(this).data("hide-type")).toggle();
		}
	});
}

function createTimeString(time) {
	time = Math.floor(time);
	if(time > 60) {
		mins = Math.floor(time / 60);
		secs = time - mins * 60;
		return mins+":"+twoDigitSeconds(secs);
	} else {
		return "0:"+twoDigitSeconds(time);
	}
}

function twoDigitSeconds(seconds) {
	if(seconds < 10) {
		return "0"+seconds;
	} else {
		return seconds;
	}
}

function closest (num, arr) {
	var mid;
	var lo = 0;
	var hi = arr.length - 1;
	while (hi - lo > 1) {
		mid = Math.floor ((lo + hi) / 2);
		if (arr[mid] < num) {
			lo = mid;
		} else {
			hi = mid;
		}
	}
	if (num - arr[lo] <= arr[hi] - num) {
		return arr[lo];
	}
	return arr[hi];
}

function sortByObjectKey(object) {
	keys = Object.keys(object),
	len = keys.length;

	keys.sort();

	var ret = {}
	for (i = 0; i < len; i++) {
		k = keys[i];
		ret[k] = object[k];
	}

	return ret;
}
