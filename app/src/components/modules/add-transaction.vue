<template>
	<div class="module add-transaction">
		<h2>Ajouter Une Transaction</h2>

		<label>Nouvelle dates</label>

		<switcher :list="dateModeSwitcherList" v-model="dateMode"></switcher>

		<div class="form">
			<div class="info error" v-if="error.length > 0">
				<i class="fas fa-exclamation-circle"></i> {{ error }}
			</div>

			<div class="info message" v-if="message.length > 0" @click="onMessageClick">
				<i class="fas fa-check-circle"></i> {{ message }}
			</div>

			<label>Direction</label>
			<switcher :list="transactionDirectionSwitcherList" v-model="form.direction"></switcher>

			<input type="text" placeholder="> Date" v-model="form.date" @focus="onDateFocus" />

			<input type="text" placeholder="> Titre" v-model="form.title" >

			<select v-bind:class="{ placeholder: form.categoryKey === '_' }" v-model="form.categoryKey" :style="{ 'border-color': (form.categoryKey === '_' ? '' : '#' + appModel.book.findCategory(form.categoryKey).color) }">
				<option value="_">> Catégorie</option>

				<option v-for="category in appModel.book.categories" :value="category.key">
					{{ category.name }}
				</option>
			</select>

			<input type="number" min="0.01" step="0.01" placeholder="> Montant" v-model="form.amount" @change="onAmountChange" >

			<button class="icon" type="button" @click="onAdd">Ajouter</button>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';
import { formatDate, parseDate } from '../../date.js';

import Switcher from '../switcher/switcher.vue';

export default {
	data () {
		return {
			form: {
				date: '',
				title: '',
				categoryKey: '_',
				amount: '',
				direction: 'output'
			},

			dateMode: 'today',

			error: '',
			message: '',

			dateModeSwitcherList: {
				'today': {
					name: 'Aujourd\'hui'
				},

				'last': {
					name: 'Dernière transaction'
				}
			},

			transactionDirectionSwitcherList: {
				'input': {
					name: 'Entrée'
				},

				'output': {
					name: 'Sortie'
				}
			}
		}
	},

	props: ['appModel'],

	mounted () {

	},

	methods: {
		validate () {
			this.error = '';

			try {
				parseDate(this.form.date);
			} catch (e) {
				this.error = 'Date invalide';
			}

			if (this.form.title.length === 0) {
				this.error = 'Le titre est obligatoire'
			}

			if (this.form.categoryKey === '_') {
				this.error = 'La catégorie est obligatoire';
			}

			if (isNaN(parseFloat(this.form.amount))) {
				this.error = 'Montant invalide';
			}

			if (this.form.direction.length === 0) {
				this.error = 'Direction invalide';
			}
		},

		onAdd () {
			this.validate();

			if (this.error.length === 0) {
				var transaction = this.appModel.book.addTransaction(
					this.form.title, 
					this.form.categoryKey, 
					this.form.amount, 
					parseDate(this.form.date), 
					this.form.direction
				);

				this.form = {
					date: '',
					title: '',
					categoryKey: '_',
					amount: '',
					direction: 'output'				
				};

				var t = this;

				this.appModel.save().then(() => {
					t.message = 'Transaction ' + transaction.key + ' créée avec succès!';
				}).catch((e) => {
					t.error = 'Could not save';
				});
			}
		},

		onDateFocus (e) {
			var t = this;

			var date = () => {
				var lt = t.appModel.book.getLastTransaction();

				if (t.dateMode === 'today' || lt == null) {
					return new Date().getTime();
				} else {
					return lt.date;
				}
			}

			if (this.form.date.length === 0) {
				this.form.date = formatDate(date(), 'yyyy-MM-dd');
				e.target.setSelectionRange(0, this.form.date.length);
			}
		},

		onAmountChange () {
			this.form.amount = Formatting.moneyDigits(this.form.amount);
		},

		onMessageClick () {
			this.message = '';
		}
	},

	components: {
		Switcher
	}
}
</script>

<style>

.add-transaction input, .add-transaction select, .add-transaction button {
	width: 100%;
}

.add-transaction .form {
	margin-top: 20px;
}

.add-transaction .message {
	cursor: pointer;
}

</style>
