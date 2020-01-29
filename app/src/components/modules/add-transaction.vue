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

			<input type="text" placeholder="> Date" v-model="form.date" @focus="onDateFocus" aria-label="Date" />

			<input type="text" placeholder="> Titre" v-model="form.title" aria-label="Titre">

			<select v-bind:class="{ placeholder: form.categoryId === '_' }" v-model="form.categoryId" :style="{ 'border-color': (form.categoryId === '_' ? '' : objects.book.getCategory(form.categoryId).color) }" aria-label="Catégorie">
				<option value="_">> Catégorie</option>

				<option v-for="category in objects.book.categories" :value="category.id">
					{{ category.name }}
				</option>
			</select>

			<input type="number" min="0.01" step="0.01" placeholder="> Montant" v-model="form.amount" @change="onAmountChange" aria-label="Montant" >

			<button class="icon" type="button" @click="onAdd">Ajouter</button>
		</div>
	</div>
</template>

<script>
import Api from 'api/api';
import Formatting from 'util/formatting';
import Dates from 'util/dates';

import Switcher from 'components/switcher';

export default {
	data () {
		return {
			form: {
				date: '',
				title: '',
				categoryId: '_',
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

	props: ['objects'],

	mounted () {

	},

	methods: {
		validate () {
			this.error = '';

			try {
				Dates.parse(this.form.date);
			} catch (e) {
				this.error = 'Date invalide';
			}

			if (this.form.title.length === 0) {
				this.error = 'Le titre est obligatoire'
			}

			if (this.form.categoryId === '_') {
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
				var transaction = this.objects.book.addTransaction(
					this.form.title, 
					this.form.categoryId, 
					this.form.amount, 
					Dates.parse(this.form.date), 
					this.form.direction
				);

				this.form = {
					date: '',
					title: '',
					categoryId: '_',
					amount: '',
					direction: 'output'				
				};

				Api.saveTransaction(transaction, this.objects.book).then((data) => {
					transaction.id = data.id;
				});
			}
		},

		onDateFocus (e) {
			var t = this;

			var date = () => {
				var transaction = t.objects.book.report.mostRecentTransaction;

				if (t.dateMode === 'today' || transaction == null) {
					return new Date().getTime();
				} else {
					return transaction.date;
				}
			}

			if (this.form.date.length === 0) {
				this.form.date = Dates.format(date(), 'yyyy-MM-dd');
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
