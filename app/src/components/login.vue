<template>
	<div class="login center-page" v-if="triedToken">
		<div class="logo">
			<h1>SB</h1>
			<img class="center-h" src="img/logo_transparent.png" alt="" />
		</div>

		<div class="form">
			<div class="error center-h" v-if="error.length > 0">
				{{error}}
			</div>

			<div class="fields">
				<input type="text" class="center-h" placeholder="Nom d'utilisateur" v-model="username" autocomplete="off" />
				<input type="password" class="center-h" placeholder="Mot de passe" v-model="password" autocomplete="new-password" v-on:keyup.enter="doLogin" />
			</div>

			<button type="button" class="center-h" v-on:click="doLogin">Connexion</button>
		</div>
	</div>
</template>

<script>
import Objects from '../api/objects.js';

export default {
	data () {
		return {
			triedToken: false,
			username: '',
			password: '',
			error: ''
		}
	},

	mounted: function () {
		var t = this;

		this.$nextTick(() => {
			console.log('Login by token');

			Objects.loginByToken().then(() => {
				console.log('Logged in');

				t.$emit('loggedIn');
			}).catch((e) => {
				console.log('Catch');

				t.triedToken = true;
			});
		});
	},

	methods: {
		doLogin () {
			var t = this;

			Objects.login(this.username, this.password).then(() => {
				t.$emit('loggedIn');
			}).catch((e) => {
				t.error = 'Erreur de connexion';
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

.login .logo {
	height: 200px;
	width: 100%;
	margin-top: 20px;
}

.login .logo img {
	width: 75%;
}

.login h1 {
	text-align: center;
	font-size: 3em;
}

.login input {
	width: 80%;
	margin-bottom: 40px;
}

.login button {
	width: 80%;
}

.login .error {
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
