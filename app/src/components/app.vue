<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>
		<panels v-if="isLoggedIn && appModel.loaded" :appModel="appModel"></panels>
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
		var t = this;

		return {
			isLoggedIn: false,

			appModel: {
				loaded: false,
				book: null,

				save: function () {
					return t.saveAppModel();
				}
			}
		}
	},

	mounted () {

	},

	methods: {
		onLoggedIn () {
			this.isLoggedIn = true;

			this.loadAppModel();
		},

		loadAppModel () {
			var t = this;

			Objects.get('book').then((b) => {
				var result = Book.fromJson(b);

				t.appModel.book = result.book;

				if (result.converted) {
					t.saveAppModel();
				}
				
				t.appModel.loaded = true;

				window.appModel = t.appModel;
			}).catch((e) => {
				if (e.error && e.error.id === 1) {
					t.createBook();
				}
			});
		},

		saveAppModel () {
			return Objects.set('book', this.appModel.book);
		},

		createBook () {
			this.appModel.book = new Book();

			this.saveAppModel();
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
