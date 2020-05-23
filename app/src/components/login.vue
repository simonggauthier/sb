<template>
	<div class="login" v-if="triedToken">
		<div class="logo">
			<h1>SB</h1>
			<img src="img/logo.png" alt />
		</div>

		<div class="form">
			<div class="error" v-if="error.length > 0">{{error}}</div>

			<div class="fields">
				<input type="text" placeholder="Nom d'utilisateur" v-model="username" autocomplete="off" />
				<input
					type="password"
					placeholder="Mot de passe"
					v-model="password"
					autocomplete="new-password"
					@:keyup.enter="login"
				/>
			</div>

			<button type="button" class="center-h" @click="login">Connexion</button>
		</div>
	</div>
</template>

<script>
export default {
	data () {
		return {
			triedToken: false,
			username: '',
			password: '',
			error: ''
		}
	},

	props: ['api'],

	mounted () {
		this.loginByToken();
	},

	methods: {
		async login () {
			try {
				let data = await this.api.login(this.username, this.password);

				localStorage.setItem('loginToken', data.token.id);

				this.$emit('loggedIn');
			} catch {
				this.error = 'Nom d\'utilisateur / mot de passe invalide';
			}
		},

		async loginByToken () {
			try {
				var tokenId = localStorage.getItem('loginToken');

				await this.api.loginByToken(tokenId);

				this.$emit('loggedIn');
			} catch {
				this.triedToken = true;
			}
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
	margin: 0 auto 0 auto;
	padding-left: 20px;
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
	margin: 0 auto 30px auto;
	width: 90%;
	font-weight: bold;
}

@media only screen and (max-width: 600px) {
	.login {
		width: 90%;
	}
}
</style>
