$(function(){
	$("#dropper").dropper({
		action: returnFWAlias()+"upload.json",
		label: '<h3><span class="glyphicon glyphicon-upload"></span> Drag and drop or click to select the replay file</h3>',
		maxQueue: 1,
		postKey: "replayfile"
	}).on("fileStart.dropper", onFileStart)
	  .on("fileComplete.dropper", onFileComplete)
	  .on("fileError.dropper", onFileError);

	$("#uploadBtn").on("click", uploadModal);
});

function uploadModal() {
	$("#uploadModal").modal("show");
	$(".alert").fadeOut();
}

function onFileStart(e, file) {
	setLoading(true);
	$("#uploadModal").modal("hide");
	$("#uploadBtn").prop("disabled", true);
	$("#uploadBtn").html('<span class="glyphicon glyphicon-cd"></span> Processing...');
}

function onFileComplete(e, file, response) {
	if(typeof(response.idMatch) == "undefined") {
 		showAlert('<span class="glyphicon glyphicon-warning-sign"></span><strong> Error:</strong> '+response.error, "danger");
    } else {
 		showAlert("Successfully uploaded replay file", "success");
	    $(window).unbind('beforeunload');
	    window.location.assign(returnFWAlias()+"match/"+response.idMatch);
    }
}

function onFileError(e, file, error) {
	showAlert('<span class="glyphicon glyphicon-warning-sign"></span><strong> Error</strong> while uploading: '+error, "danger");
}

function showAlert(msg, level) {
	$(".alertArea").html('<div class="alert alert-'+level+'">'+msg+'</div>');
	$("#uploadBtn").removeProp("disabled");
	$("#uploadBtn").html('<span class="glyphicon glyphicon-upload"></span> Upload');
	setLoading(false);
}