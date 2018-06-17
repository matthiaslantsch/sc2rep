<!DOCTYPE html>
<html>
	<head>
		<title><?=$title?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <meta name="description" content="" />
        <meta name="author" content="HIS5">
        <meta name="robots" content="index, follow"/>
        <meta name="revisit-after" content="2 month"/>
	    <!--[if lt IE 9]>
	      	<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      	<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<link type="text/css" rel="stylesheet" href="<?=linkCss("jquery.fs.dropper.min")?>"/>
		<link type="text/css" rel="stylesheet" href="<?=linkCss("loader")?>"/>
		<link type="text/css" rel="stylesheet" href="<?=linkCss("default")?>"/>
		<link type="text/css" rel="stylesheet" href="<?=linkCss("icons")?>"/>
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="//code.highcharts.com/highcharts.js"></script>
		<script src="<?=linkJs("jquery.fs.dropper.min")?>"></script>
		<script src="<?=linkJs("common")?>"></script>
		<script src="<?=linkJs("upload")?>"></script>
		<script>
			function returnFWAlias() {
				return "<?=linkTo()?>";
			}
		</script>
	</head>	
	<body>
