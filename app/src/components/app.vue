<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>

		<panels v-if="isLoggedIn"></panels>
	</div>
</template>

<script>
import Vue from 'vue';

import Book from '../api/model/book.js';

import Objects from '../api/objects.js';

import Login from './login.vue';
import Panels from './panels.vue';

export default {
	data () {
		return {
			isLoggedIn: false
		}
	},

	mounted: function () {
		this.loadBook();
	},

	methods: {
		onLoggedIn () {
			this.isLoggedIn = true;
		},

		loadBook () {

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
