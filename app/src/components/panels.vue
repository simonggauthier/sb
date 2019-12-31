<template>
	<div class="panels">
		<div class="title">
			<h1>SB</h1>
			<img class="center-h" src="img/logo_transparent.png" alt="" />
		</div>

		<div class="modules">
			<transaction-list :model="model"></transaction-list>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import TransactionList from './modules/transaction-list.vue';

export default {
	data () {
		return {

		}
	},

	props: ['model'],

	mounted: function () {

	},

	methods: {

	},

	components: {
		TransactionList
	}
}
</script>

<style>

.panels {
	height: 2000px;
}

.panels > .title {
	width: 100%;
	padding: 10px 0 10px 0;
	text-align: center;
}

.panels > .title img {
	width: 20%;
}

.panels .modules {
	padding: 20px 0 10px 10px;
}

.panels .module .title {
	font-weight: bold;
	font-size: 1.3em;
	margin-bottom: 5px;
}

@media only screen and (max-width: 600px) {
	.panels > .title img {
		width: 40%;
	}
}

</style>
