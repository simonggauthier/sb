<!DOCTYPE html>
<html lang="fr">
	<head>
		<link rel="icon" type="image/png" href="img/leaf.png" />
		<link rel="manifest" href="manifest.json" />

		<meta name="description" content="Simon's budget" />

		<meta name="theme-color" content="#478BA2" />

		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />

		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<style>
			@import url('https://fonts.googleapis.com/css?family=Roboto&display=swap');
			@import url('https://fonts.googleapis.com/css?family=Courier+Prime&display=swap');
			@import url('css/font-awesome-all.css');
		</style>

		<link rel="stylesheet" type="text/css" href="css/theme.css?version=8" />

		<title>SB</title>

		<script>
			if ('serviceWorker' in navigator) {
				navigator.serviceWorker.register('service-worker.js');
			}
		</script>
	</head>

	<body>
		<div id="app">
			<app></app>
		</div>
		
		<script src="bundle.js?version=8"></script>
	</body>
</html>
