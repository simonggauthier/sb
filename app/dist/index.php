<!DOCTYPE html>
<html>
	<head>
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		<link rel="apple-touch-icon" sizes="180x180" href="img/leaf180.png">
		<meta name="theme-color" content="#478BA2" />

		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />

		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<style>
			@import url('https://fonts.googleapis.com/css?family=Roboto&display=swap');
			@import url('https://fonts.googleapis.com/css?family=Courier+Prime&display=swap');
			@import url('css/font-awesome-all.css');
		</style>

		<link rel="stylesheet" type="text/css" href="css/theme.css" />

		<script>
			var _id = () => {
				return Math.random().toString(36);
			}
		</script>
	</head>

	<body>
		<div id="app">
			<app></app>
		</div>
		
		<script src="bundle.js?<?php echo time(); ?>"></script>
	</body>
</html>
