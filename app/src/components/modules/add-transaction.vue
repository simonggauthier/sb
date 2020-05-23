<template>
	<div class="module add-transaction">
		<h2>Ajouter Une Transaction</h2>

		<label>Nouvelle dates</label>

		<switcher :list="dateModeSwitcherList" v-model="dateMode"></switcher>

		<div class="form">
			<div class="info error" v-if="error.length > 0">
				<i class="fas fa-exclamation-circle"></i>
				{{ error }}
			</div>

			<div class="info message" v-if="message.length > 0" @click="onMessageClick">
				<i class="fas fa-check-circle"></i>
				{{ message }}
			</div>

			<label>Direction</label>
			<switcher :list="transactionDirectionSwitcherList" v-model="form.direction"></switcher>

			<input
				type="text"
				placeholder="> Date"
				v-model="form.creationDate"
				@focus="onDateFocus"
				aria-label="Date"
			/>

			<input type="text" placeholder="> Titre" v-model="form.title" aria-label="Titre" />

			<select
				v-bind:class="{ placeholder: form.categoryId === '_' }"
				v-model="form.categoryId"
				:style="{ 'border-color': (form.categoryId === '_' ? '' : objects.book.getCategory(form.categoryId).color) }"
				aria-label="Catégorie"
			>
				<option value="_">> Catégorie</option>

				<option
					v-for="category in objects.book.categories"
					:value="category.id"
					:key="category.id"
				>{{ category.name }}</option>
			</select>

			<input
				type="number"
				min="0.01"
				step="0.01"
				placeholder="> Montant"
				v-model="form.amount"
				@change="onAmountChange"
				aria-label="Montant"
			/>

			<button class="icon" type="button" @click="onAdd">Ajouter</button>
		</div>
	</div>
</template>

<script>
import Formatting from 'util/formatting';
import Dates from 'util/dates';

import Switcher from 'components/switcher';

export default {
	data () {
		return {
			form: {
				creationDate: '',
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

	props: ['api', 'objects'],

	methods: {
		validate () {
			this.error = '';

			if (!Dates.isValid(this.form.creationDate)) {
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

		async onAdd () {
			this.validate();

			if (this.error.length === 0) {
				var transaction = this.objects.book.addTransaction(
					this.form.title,
					this.form.categoryId,
					this.form.amount,
					this.form.creationDate,
					this.form.direction
				);

				transaction.id = (await this.api.saveTransaction(transaction)).id;

				this.clear();

				this.message = 'Nouvelle transaction ' + transaction.id + ' ajoutée avec succès';
			}
		},

		onDateFocus (e) {
			if (this.form.creationDate.length === 0) {
				let mostRecentTransaction = this.objects.book.report.mostRecentOutputTransaction;

				let date = this.dateMode === 'today' || !mostRecentTransaction ? Dates.now() : mostRecentTransaction.creationDate;

				this.form.creationDate = date;

				e.target.setSelectionRange(0, this.form.creationDate.length);
			}
		},

		onAmountChange () {
			this.form.amount = Formatting.moneyDigits(this.form.amount);
		},

		onMessageClick () {
			this.message = '';
		},

		clear () {
			this.form = {
				creationDate: '',
				title: '',
				categoryId: '_',
				amount: '',
				direction: 'output'
			};

			this.message = '';
		}
	},

	components: {
		Switcher
	}
}
</script>

<style>
.add-transaction input,
.add-transaction select,
.add-transaction button {
	width: 100%;
}

.add-transaction .form {
	margin-top: 20px;
}

.add-transaction .message {
	cursor: pointer;
}
</style>
