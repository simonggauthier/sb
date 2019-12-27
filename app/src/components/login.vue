<template>
	<div class="login">
		<input type="text" placeholder="Username" v-model="username" />
		<input type="password" placeholder="Password" v-model="password" />

		<button type="button" v-on:click="doLogin">Login</button>
	</div>
</template>

<script>
import Objects from '../api/objects.js';

export default {
	data () {
		return {
			username: '',
			password: ''
		}
	},

	mounted: function () {

	},

	methods: {
		doLogin () {
			var t = this;

			Objects.login(this.username, this.password).then((data) => {
				console.log(data);

				t.$emit('loggedIn');
			}).catch((e) => {
				console.log('Wrong login');
			});
		}
	},

	components: {

	}
}
</script>

<style>

</style>
