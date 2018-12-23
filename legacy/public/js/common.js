$(function() {
	// Change JQueryUI plugin names to fix name collision with Bootstrap.
	$.widget.bridge('uitooltip', $.ui.tooltip);
	$.widget.bridge('uibutton', $.ui.button);

	$("#searchField").autocomplete({
		source: returnFWAlias()+"api/players.json",
		minLength: 5,
		select: function(event, ui) {
			window.location.assign(returnFWAlias()+"player/"+ui.item.id);
		},
		open: function(){
			$(this).autocomplete('widget').css('z-index', 1050);
		},
	});

	$(document).uitooltip({
		items: "[help]",
		content: function () {
			return $(this).attr("help");
		}
	});

	$(".upgrade_icons").css("background-image: url("+returnFWAlias()+"public/gfx/upgrades.jpg)");
	$(".unit_icons").css("background-image: url("+returnFWAlias()+"public/gfx/units.jpg)");
	$(".buildings_icons").css("background-image: url("+returnFWAlias()+"public/gfx/structures.jpg)");

	$(document).on("click", ".copyButton", function() {
		$("#copyFrom").focus();
		$("#copyFrom").select();
	});
});

function setLoading(isLoading) {
	if(isLoading) {
		$(".alert").fadeOut();
		$("#loading").addClass("spinner");
	} else {
		$("#loading").removeClass("spinner");
	}
}