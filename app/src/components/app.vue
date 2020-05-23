<template>
	<div class="content">
		<login v-if="!isLoggedIn" @loggedIn="onLoggedIn" :api="api"></login>

		<panels v-if="ready" :api="api" :objects="objects"></panels>

		<div class="loading" v-if="isLoggedIn && !ready">{{ loadingInfo.message }}</div>
	</div>
</template>

<script>
import Api from 'api/api';
import Objects from 'api/model/objects';
import { Book } from 'api/model/book'

import Login from 'components/login';
import Panels from 'components/panels';

export default {
	data () {
		return {
			isLoggedIn: false,
			ready: false,

			api: new Api(),

			objects: new Objects(),

			loadingInfo: {
				message: ''
			}
		}
	},

	methods: {
		async onLoggedIn () {
			this.isLoggedIn = true;

			await this.objects.load(this.api, this.loadingInfo);

			this.ready = true;
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

html,
body {
	overflow-x: hidden;
}

body {
	position: relative;
	font-family: "Roboto", sans-serif;
	font-size: 20px;
	width: 100%;
	min-height: 100vh;
}

input,
button,
textarea,
select {
	border: 0;
	display: block;
	font-family: "Courier Prime", monospace;
	box-sizing: border-box;
	font-size: 1em;
	padding: 6px;
	margin-bottom: 10px;
}

button {
	font-family: "Roboto", sans-serif;
}

input[type="color"] {
	box-sizing: content-box;
}

input,
textarea,
select {
	border-left: solid 8px #b9d4db;
}

button {
	margin: 10px 0;
}

@media only screen and (max-width: 600px) {
	input,
	button,
	textarea,
	select {
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
	display: block;
}

thead,
tbody tr {
	display: table;
	width: 100%;
	table-layout: fixed;
}

thead {
	width: calc(
		100% - 1em
	); /* scrollbar is average 1em/16px width, remove it from thead width */
}

th {
	text-align: left;
	padding: 10px 5px;
}

tbody {
	font-family: "Courier Prime", monospace;
}

td {
	padding: 5px;
}

@media only screen and (max-width: 600px) {
	table {
		font-size: 0.6em;
	}

	thead {
		width: 100%; /* scrollbar is average 1em/16px width, remove it from thead width */
	}

	.color-square {
		width: 0.5em;
		height: 0.5em;
	}
}

.color-edit:after {
	content: "";
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
	width: 200px !important;
}

.form .info {
	padding: 10px 0;
	margin-bottom: 10px;
	border-left: 8px solid #222;
	padding-left: 10px;
	font-size: 0.8em;
	font-weight: bold;
}

.loading {
	width: 100%;
	text-align: center;
	font-size: 3em;
	color: #fff;
}
</style>
