<template>
	<div class="switcher">
		<div class="switch" v-on:click="next(-1)">
			<i class="fas fa-arrow-circle-left"></i>
		</div>

		<div class="option">{{ list[value].name }}</div>

		<div class="switch" v-on:click="next(1)">
			<i class="fas fa-arrow-circle-right"></i>
		</div>
	</div>
</template>

<script>
import Formatting from 'util/formatting';

export default {
	props: ['list', 'value'],

	mounted () {

	},

	methods: {
		next (inc) {
			var keys = Object.keys(this.list);
			var index = keys.indexOf(this.value);

			index += inc;

			if (index < 0) {
				index = keys.length - 1;
			}

			if (index >= keys.length) {
				index = 0;
			}

			this.$emit('input', keys[index]);
		}
	},

	components: {

	}
}
</script>

<style>
.switcher {
	margin-bottom: 10px;
}

.switcher > div {
	float: left;
	margin-left: 5px;
}

.switcher:after {
	content: "";
	display: block;
	clear: both;
}

.switcher .switch {
	cursor: pointer;
}

.switcher {
	font-size: 0.8em;
	font-weight: bold;
}
</style>
