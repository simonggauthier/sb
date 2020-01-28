<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>
		<panels v-if="ready" :objects="objects"></panels>
	</div>
</template>

<script>
import Objects from 'api/model/objects';

import Login from 'components/login';
import Panels from 'components/panels';

export default {
	data () {
		var t = this;

		return {
			isLoggedIn: false,
			ready: false,

			objects: new Objects()
		}
	},

	mounted () {

	},

	methods: {
		onLoggedIn () {
			var t = this;

			this.isLoggedIn = true;

			this.objects.load().then(() => {
				console.log('Application objects loaded');

				t.ready = true;
			});
		}
	},

	components: {
		Login,
		Panels
	}
}
</script>

<style>

* {
	margin: 0;
	padding: 0;
}

html, body {
	overflow-x: hidden;
}

body {
	position: relative;
	font-family: 'Roboto', sans-serif;
	font-size: 20px;
	width: 100%;
	min-height: 100vh;
}

input, button, textarea, select {
	border: 0;
	display: block;
	font-family: 'Courier Prime', monospace;
	box-sizing: border-box;
	font-size: 1em;
	padding: 6px;
	margin-bottom: 10px;
}

button {
	font-family: 'Roboto', sans-serif;
}

input[type='color'] {
	box-sizing: content-box;
}

input, textarea, select {
	border-left: solid 8px #B9D4DB;
}

button {
	margin: 10px 0;
}

@media only screen and (max-width: 600px) {
	input, button, textarea, select {
		font-size: 0.8em;
	}
}

img {
	display: block;
}

table {
	width: 100%;
	border-collapse: collapse;
}

tbody {
	display:block;
}

thead, tbody tr {
	display:table;
	width:100%;
	table-layout:fixed;
}

thead {
	width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
}

th {
	text-align: left;
	padding: 10px 5px;
}

tbody {
	font-family: 'Courier Prime', monospace;
}

td {
	padding: 5px;
}

@media only screen and (max-width: 600px) {
	table {
		font-size: 0.6em;
	}

	thead {
	    width: 100% /* scrollbar is average 1em/16px width, remove it from thead width */
	}

	.color-square {
		width: 0.5em;
		height: 0.5em;
	}
}

.color-edit:after {
	content: '';
	display: block;
	clear: both;
}

label {
	padding: 3px 0;
	border-radius: 25px;
	font-size: 0.7em;
	font-weight: bold;
	margin: 10px 0;
	display: block;
	width: 125px;
	text-align: center;
}

input.color {
	width: 200px!important;
}

.form .info {
	padding: 10px 0;
	margin-bottom: 10px;
	border-left: 8px solid #222;
	padding-left: 10px;
	font-size: 0.8em;
	font-weight: bold;
}

</style>
