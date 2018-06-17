$(function() {
	$("#mapInput").autocomplete({
		source: returnFWAlias()+"api/maps.json",
		minLength: 3,
		select: function(event, ui) {
			$("#mapInput").val(ui.item.value);
		}
	});

	$(".filterTf").on("focusout", function() {
		dispatch();
	});

	$("#seasonSelect").on("focusout", function() {
		dispatch();
	});

	$(".playerField").autocomplete({
		source: returnFWAlias()+"api/players.json",
		minLength: 3,
		select: function(event, ui) {
			$(this).data("tag-value", ui.item.id);
		}
	});

	$(".filter").on("click", function() {
		$(this).toggleClass("filter-off filter-on");
		$("#pager").val("1");
		dispatch();
	});

	$("#tableContent").on("click", ".matchRow", function() {
		window.location.assign(returnFWAlias()+"match/"+$(this).data("idmatch"));
	});

	$("#prevBtn").on("click", function() {
		if($(this).parent("li").hasClass("disabled")) {
			return;
		}

		$("#pager").val((parseInt($("#pager").val()) - 1));
		dispatch();
	});

	$("#nextBtn").on("click", function() {
		if($(this).parent("li").hasClass("disabled")) {
			return;
		}

		$("#pager").val((parseInt($("#pager").val()) + 1));
		dispatch();
	});

	dispatch();
});

function collectFilter() {
	var tags = {};
	if($("#mapInput").val() !== "") {
		tags.map = $("#mapInput").val();
	}

	$.each($(".filter-on"), function() {
		if(typeof(tags[$(this).data("tag-key")]) == "undefined") {
			tags[$(this).data("tag-key")] = [];
		}

		tags[$(this).data("tag-key")].push($(this).data("tag-value"));
	});

	$.each($(".playerField"), function() {
		if($(this).val() != "") {
			tags[$(this).prop("id")] = $(this).data("tag-value");
		}
	});

	if($("#seasonSelect").val() != "") {
		tags["season"] = $("#seasonSelect").val();
	}

	setLoading();
	$("#prevBtn").parent("li").addClass("disabled");
	$("#nextBtn").parent("li").addClass("disabled");

	return tags;
}

function initCharts() {
	$("#apmChart").highcharts({
		chart: {
			type: 'area'
		},
		title: {
			text: "APM"
		},
		xAxis: {
			min: 0,
			tickInterval: 1,
			labels: {
				enabled: false
			}
		},
		tooltip: {
			formatter: function() {
				var ret = '<span>'+this.point.series.name+' has had average APM of '+this.point.y+' in ';

				if($("#sampler").val() == 1) {
					ret += ' game # '+(this.point.x+1);
				} else {
					ret += ' games #'+(this.point.x+1)+'-#'+(this.point.x+parseInt($("#sampler").val())+1)+'</span>';
				}
				return ret;
			}
		},
		legend: {
			enabled: false
		},
		series: [
			{
				name: $("#name").text(),
				id: 1,
				data: []
			}
		],
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

	$("#winRateChart").highcharts({
		chart: {
			type: 'area'
		},
		title: {
			text: "Winrate"
		},
		xAxis: {
			min: 0,
			tickInterval: 1,
			labels: {
				enabled: false
			}
		},
		tooltip: {
			formatter: function() {
				var ret = '<span>'+this.point.series.name+' did'+(this.point.y == 100 && $("#sampler").val() == 1 ? "" : "n't")+' win ';

				if($("#sampler").val() == 1) {
					ret += 'game # '+(this.point.x+1);
				} else {
					ret += this.point.y+'% of games #'+(this.point.x+1)+'-#'+(this.point.x+parseInt($("#sampler").val())+1)+'</span>';
				}
				return ret;
			}
		},
		legend: {
			enabled: false
		},
		series: [
			{
				name: $("#name").text(),
				id: 1,
				data: []
			}
		],
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

	$("#spendingChart").highcharts({
		chart: {
			type: 'area'
		},
		title: {
			text: "Spending Skill"
		},
		xAxis: {
			min: 0,
			tickInterval: 1,
			labels: {
				enabled: false
			}
		},
		tooltip: {
			formatter: function() {
				var ret = '<span>'+this.point.series.name+' had a spending skill of '+this.point.y+' in';

				if($("#sampler").val() == 1) {
					ret += ' game # '+(this.point.x+1);
				} else {
					ret += ' games #'+(this.point.x+1)+'-#'+(this.point.x+parseInt($("#sampler").val())+1)+'</span>';
				}
				return ret;
			}
		},
		legend: {
			enabled: false
		},
		series: [
			{
				name: $("#name").text(),
				id: 1,
				data: []
			}
		],
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
}

function loadProfileData(id) {
	$.ajax({
		url: returnFWAlias()+"profile/"+id+".json",
		data: {tags: collectFilter(), pager: $("#pager").val()},
		success: function(res) {
			if(typeof(res.matches) != "undefined" && res.matches.length > 0) {
				$("#sampler").val(res.sampler);
				$.each(res.statistics, function(key, val) {
					var chart = $("#"+key+"Chart").highcharts();
					chart.series[0].setData(val);
					chart.redraw();
					chart.hideLoading();

					//update average values
					$("#"+key+"Avg").text(Math.floor(res.statsData.all[key])+(key == "winRate" ? "%" : ""));
					$("#"+key+"Recent").text(Math.floor(res.statsData.recent[key])+(key == "winRate" ? "%" : ""));
				});

				var html = '';

				$.each(res.matches, function(i, match) {
					html += '<tr class="matchRow pointer" data-idMatch="'+match.idMatch+'"><td>'+match.idMatch+'</td>'
							+'<td>'+match.map+'</td>'
							+'<td>'+match.type+'</td>'
							+'<td>'+match.matchup+'</td>'
							+'<td>'+match.length+'</td>'
							+'<td>'+match.playedAgo+'</td>'
							+'<td>'+match.apm+'</td>'
							+'<td>'+match.spending+'</td>'
							+'<td>'+(match.result == 1 ? "Victory" : "Defeat")+'</td></tr>';
				});
				$("#prevBtn").parent("li").removeClass("disabled");
				$("#nextBtn").parent("li").removeClass("disabled");
				$("#tableContent").html(html);

				if($("#pager").val() <= 1) {
					$("#prevBtn").parent("li").addClass("disabled");
				} else {
					$("#prevBtn").parent("li").removeClass("disabled");	
				}

			} else {
				$.each($(".chart"), function() {
					var chart = $(this).highcharts();
					chart.showLoading('<div>Unable to load this chart at this moment. Please try again later</div>');
					chart.series[0].setData([]);
					chart.redraw();

				});
				$("#tableContent").html('<tr><td colspan="9">Unable to find matches with these filters.</td></tr>');
				$("#prevBtn").parent("li").addClass("disabled");
				$("#nextBtn").parent("li").addClass("disabled");
				$(".legendRow strong").text("--");
			}

			setLoading();
		},
		error: function() {
			$("#tableContent").html('<tr><td colspan="9">Unable to load matches at this moment. Please try again later</td></tr>');
		}
	});
}

function loadMatches() {
	$.ajax({
		url: returnFWAlias()+"api/matches.json",
		data: {tags: collectFilter(), pager: $("#pager").val()},
		success: function(res) {
			if(res.length > 0) {
				var html = '';
				$.each(res, function(i, match) {
					html += '<tr class="matchRow pointer" data-idMatch="'+match.idMatch+'"><td>'+match.idMatch+'</td>'
							+'<td>'+match.map+'</td>'
							+'<td>'+match.type+'</td>'
							+'<td>'+match.matchup+'</td>'
							+'<td>'+match.teamOne+'</td>'
							+'<td>'+match.teamTwo+'</td>'
							+'<td>'+match.leagues+'</td>'
							+'<td>'+match.length+'</td>'
							+'<td>'+match.playedAgo+'</td></tr>';
				});
				$("#prevBtn").parent("li").removeClass("disabled");
				$("#nextBtn").parent("li").removeClass("disabled");
				$("#tableContent").html(html);

				if($("#pager").val() <= 1) {
					$("#prevBtn").parent("li").addClass("disabled");
				} else {
					$("#prevBtn").parent("li").removeClass("disabled");	
				}
			} else {
				$("#tableContent").html('<tr><td colspan="9">Unable to find matches with these filters.</td></tr>');
				$("#prevBtn").parent("li").addClass("disabled");
				$("#nextBtn").parent("li").addClass("disabled");
			}

			setLoading();
		},
		error: function() {
			$("#tableContent").html('<tr><td colspan="9">Unable to load matches at this moment. Please try again later</td></tr>');
		}
	});
}