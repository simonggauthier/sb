<template>
	<div class="color-square" v-bind:class="{ 'edit': edit }" v-bind:style="{ 'background-color': '#' + calcColor() }"></div>
</template>

<script>
export default {
	data () {
		return {
			defaultColor: 'aaf'
		};
	},

	props: ['edit', 'color'],

	mounted () {
		
	},

	methods: {
		calcColor () {
			if (!this.color || this.color.length === 0) {
				return this.defaultColor; 
			}

			return this.color;
		}
	},

	components: {

	}
}
</script>

<style>

.color-square {
	width: 1em;
	height: 1em;
	float: left;
	margin-right: 5px;
}

.color-square.edit {
	width: 1.6em;
	height: 1.6em;
}

</style>
