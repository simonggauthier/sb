<template>
	<div class="content">
		<login v-if="!isLoggedIn" v-on:loggedIn="onLoggedIn"></login>
	</div>
</template>

<script>
import Login from './login.vue';

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
		Login
	}
}
</script>

<style>

* {
	margin: 0;
	padding: 0;
}

body {
	background-color: #222;
}

</style>
