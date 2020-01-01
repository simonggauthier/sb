<template>
	<div class="module transaction-list">
		<div class="title">
			Import de transactions
		</div>

		<div class="transactions">
			Mois: 
			<input type="text" v-model="month" />
			Direction: 
			<input type="text" v-model="direction" />
			Texte:
			<textarea v-model="raw"></textarea>
			<button v-on:click="doImport">Importer</button>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

export default {
	data () {
		return {
			raw: '',
			month: '',
			direction: 'output'
		}
	},

	props: ['appModel'],

	mounted: function () {

	},

	methods: {
		doImport: function () {
			var lines = this.raw.split('\n');
			var d = this.month.split('-');

			for (var i = 0; i < lines.length; i++) {
				var line = lines[i].split('\t');
				var date = new Date(d[0], parseInt(d[1], 10) - 1, line[0]).getTime();

				this.appModel.book.addTransaction(line[1], line[2], line[3].replace('$', '').replace(',', ''), date, this.direction);
			}

			this.appModel.save();
		}
	},

	components: {

	}
}
</script>

<style>

</style>
