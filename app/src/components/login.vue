<template>
	<div class="login center-page">
		<div class="logo">
			<h1>SB</h1>
			<img class="center-h" src="img/logo_transparent.png" alt="" />
		</div>

		<div class="form">
			<div class="error center-h" v-if="error.length > 0">
				{{error}}
			</div>

			<div class="fields">
				<form autocomplete="off">
				<input type="text" class="center-h" placeholder="Username" v-model="username" autocomplete="off" />
				<input type="password" class="center-h" placeholder="Password" v-model="password" autocomplete="new-password" />
				</form>
			</div>

			<button type="button" class="center-h" v-on:click="doLogin">Login</button>
		</div>
	</div>
</template>

<script>
import Objects from '../api/objects.js';

export default {
	data () {
		return {
			username: '',
			password: '',
			error: ''
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
				t.error = 'Wrong login';
			});
		}
	},

	components: {

	}
}
</script>

<style>

.login {
	width: 30%;
	padding-bottom: 30px;
}

.logo {
	height: 200px;
	width: 100%;
	margin-top: 20px;
}

.logo img {
	width: 75%;
}

h1 {
	text-align: center;
	color: #fff;
}

input {
	border: 0;
	padding: 6px;
	width: 80%;
	margin-bottom: 40px;
	font-size: 20px;
	background-color: #ddf;
}

button {
	border: 0;
	padding: 6px;
	width: 80%;
	font-size: 20px;
	font-weight: bold;
	background-color: #113;
	color: #fff;
}

.error {
	text-align: center;
	padding: 10px;
	background-color: #fff;
	color: #d66;
	margin-bottom: 30px;
	width: 90%;
	font-weight: bold;
}

@media only screen and (max-width: 600px) {
	.login {
		width: 90%;	
	}
}

</style>
