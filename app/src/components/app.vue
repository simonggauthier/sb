<template>
	<div class="app">
		<div class="cmd-bar">
			<button v-on:click="save">Save</button>
		</div>

		<div class="content">
			<grid ref="grid"></grid>
		</div>
	</div>
</template>

<script>
import Grid from './grid.vue';

export default {
	data () {
		return {}
	},

	methods: {
		save: function (e) {
			console.log(JSON.stringify(this.$refs.grid.model));
		}
	},

	components: {
		Grid
	}
}
</script>

<style>
.app {
	font-size: 16px;
}

.cmd-bar {
	margin-bottom: 20px;
}

button {
	padding: 5px 10px 5px 10px;
	font-family: 'Segoe UI';
	font-size: 1em;
}
</style>
