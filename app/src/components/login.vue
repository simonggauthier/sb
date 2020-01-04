<template>
	<div class="login" v-if="triedToken">
		<div class="logo">
			<h1>SB</h1>
			<img src="img/logo_transparent.png" alt="" />
		</div>

		<div class="form">
			<div class="error" v-if="error.length > 0">
				{{error}}
			</div>

			<div class="fields">
				<input type="text" placeholder="Nom d'utilisateur" v-model="username" autocomplete="off" />
				<input type="password" placeholder="Mot de passe" v-model="password" autocomplete="new-password" v-on:keyup.enter="doLogin" />
			</div>

			<button type="button" class="center-h" v-on:click="doLogin">Connexion</button>
		</div>
	</div>
</template>

<script>
import Api from 'api/api';

export default {
	data () {
		return {
			triedToken: false,
			username: '',
			password: '',
			error: ''
		}
	},

	mounted () {
		var t = this;

		this.$nextTick(() => {
			console.log('Login by token');

			Api.loginByToken().then(() => {
				console.log('Logged in');

				t.$emit('loggedIn');
			}).catch((e) => {
				t.triedToken = true;
			});
		});
	},

	methods: {
		doLogin () {
			var t = this;

			Api.login(this.username, this.password).then(() => {
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
	margin: 80px auto 0 auto;
	padding-top: 20px;
}

.login .logo {
	height: 200px;
	width: 100%;
}

.login .logo img {
	width: 75%;
	margin: 0 auto 0 auto;
}

.login h1 {
	text-align: center;
	font-size: 3em;
}

.login .form {
	border: 0;
}

.login input {
	width: 80%;
	margin: 0 auto 40px auto;
}

.login button {
	width: 80%;
	margin: 0 auto 0 auto;
}

.login .error {
	text-align: center;
	padding: 10px;
	background-color: #fff;
	color: #d66;
	margin: 0 auto 30px auto;;
	width: 90%;
	font-weight: bold;
}

@media only screen and (max-width: 600px) {
	.login {
		width: 90%;	
	}
}

</style>
