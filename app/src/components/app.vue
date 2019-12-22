<template>
	<div class="app">
		<div class="status-bar">
			{{status}}
		</div>

		<div class="content">
			<grid ref="grid" v-on:modelChange="onModelChange"></grid>
		</div>
	</div>
</template>

<script>
import Grid from './grid.vue';
import Objects from '../api/objects.js';

export default {
	data () {
		return {
			status: ''
		}
	},

	mounted: function () {
		this.status = 'Logging in...';

		Objects.login().then(() => {
			this.status = '';

			this.load();
		});
	},

	methods: {
		load () {
			this.status = 'Loading...';

			Objects.get('grid').then((data) => {
				this.$refs.grid.model.setCells(data.cells);

				
			}).finally(() => {
				this.status = '';
			});
		},

		save () {
			this.status = 'Saving...';

			Objects.set('grid', JSON.stringify(this.$refs.grid.model)).then((data) => {
				this.status = '';
			});
		},

		onModelChange (model) {
			this.save();
		}
	},

	components: {
		Grid
	}
}
</script>

<style>

* {
	margin: 0;
	padding: 0;
}

.app {
	font-family: 'Segoe UI';
	font-size: 16px;
}

.status-bar {
	height: 30px;
	padding: 5px;
}

button {
	padding: 5px 10px 5px 10px;
}

.grid, .grid input {
	font-family: 'consolas';
	font-size: 16px;
}

</style>
