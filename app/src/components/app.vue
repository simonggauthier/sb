<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>

		<panels v-if="isLoggedIn"></panels>
	</div>
</template>

<script>
import Vue from 'vue';

import Login from './login.vue';
import Panels from './panels.vue';

export default {
	data () {
		return {
			isLoggedIn: false
		}
	},

	mounted: function () {

	},

	methods: {
		onLoggedIn () {
			this.isLoggedIn = true;
		}
	},

	components: {
		Login,
		Panels
	}
}
</script>

<style>

</style>
