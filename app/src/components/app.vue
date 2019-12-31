<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>

		<panels v-if="isLoggedIn" :model="model"></panels>
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
			isLoggedIn: false,

			model: {
				book: null
			}
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
			var t = this;

			Objects.get('book').then((b) => {
				t.model.book = b;
			}).catch((e) => {
				if (e.error && e.error.id === 1) {
					t.createBook();
				}
			});
		},

		saveModel () {
			Objects.set('book', this.model.book);
		},

		createBook () {
			this.model.book = new Book();

			this.model.book.addTransaction('Bouffe', 'Épicerie', '20.00', new Date().getTime());

			this.saveModel();
		},
	},

	components: {
		Login,
		Panels
	}
}
</script>

<style>

</style>
