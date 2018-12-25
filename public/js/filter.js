$(function() {
	$("#mapInput").autocomplete({
		source: returnFWAlias()+"api/maps",
		minLength: 3,
		select: function(event, ui) {
			$("#mapInput").val(ui.item.value);
			$("#mapInput").focusout();
		}
	});

	$(".filterTf").on("focusout", function() {
		dispatch();
	});

	$("#seasonSelect").on("focusout", function() {
		dispatch();
	});

	$(".playerField").autocomplete({
		source: returnFWAlias()+"api/players",
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
		window.location.assign(returnFWAlias()+"matches/"+$(this).data("idmatch"));
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

function loadMatches() {
	$.ajax({
		url: returnFWAlias()+"api/matches",
		data: {tags: collectFilter(), pager: $("#pager").val()},
		success: function(res) {
			var html = '';
			$.each(res.keys, function(i, key) {
				html += '<td><strong>'+key+'</strong></td>';
			});
			$("#tableHeader").html(html);
			if(res.matches.length > 0) {
				var html = '';
				$.each(res.matches, function(i, match) {
					html += '<tr class="matchRow pointer" data-idMatch="'+match.idMatch+'"><td>'+match.idMatch+'</td>'
							+'<td>'+match.map+'</td>'
							+'<td>'+match.type+'</td>'
							+'<td>'+match.matchup+'</td>'
							+'<td>'+match.teamOne+'</td>'
							+'<td>'+match.teamTwo+'</td>'
							+'<td>'+match.leagues+'</td>'
							+'<td>'+match.length+'</td>'
							+'<td>'+match.playedAgo+'</td>';
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
