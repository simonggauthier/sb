<template>
	<div class="switcher">
		<div class="switch" v-on:click="next(-1)">
			<i class="fas fa-arrow-circle-left"></i>
		</div>

		<div class="option" v-for="(el, key) in list" v-if="key === value">
			{{ el.title }}
		</div>

		<div class="switch" v-on:click="next(1)">
			<i class="fas fa-arrow-circle-right"></i>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';

export default {
	data () {
		return {

		}
	},

	props: ['list', 'value'],

	mounted: function () {

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

.switcher > div {
	float: left;
	margin-left: 5px;
}

.switcher:after {
	content: '';
	display: block;
	clear: both;
}

.switcher .switch {
	cursor: pointer;
}

.switcher {
	font-size: 1em;
	font-weight: bold;
}

</style>
