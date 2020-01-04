<template>
	<div class="module transaction-list">
		<h2>Transactions</h2>

		<div class="options">
			<div class="option">
				<label>Mois</label>
				<switcher :list="monthSwitcherList" v-model="month"></switcher>
			</div>

			<div class="option">
				<label>Direction</label>
				<switcher :list="transactionDirectionSwitcherList" v-model="transactionDirection"></switcher>
			</div>
		</div>

		<div class="report">
			<label>Économies</label>
			<input type="text" disabled="disabled" :value="monthEconomies" />
		</div>

		<div class="transactions">
			<data-table :tableModel="table" :tableData="objects.book.transactions" :selectable="true" @selected="onTransactionSelected" @unselected="onTransactionUnselected"></data-table>
		</div>

		<button type="button" v-if="selectedTransaction != null" @click="onDeleteTransaction">Supprimer</button>
	</div>
</template>

<script>
import Formatting from 'util/formatting';
import Dates from 'util/dates';
import { BookReport } from 'api/model/book';

import Switcher from 'components/switcher';
import DataTable from 'components/data-table';

export default {
	data () {
		var t = this;

		return {
			table: {
				sort: {
					key: 'date',
					direction: 'descending'
				},

				columns: {
					'date': {
						title: 'Date',
						type: 'date',
						width: '25%'
					},
					'title': {
						title: 'Titre',
						type: 'string',
						width: '25%'
					},
					'category': {
						title: 'Catégorie',
						type: 'category',
						width: '30%',
						
						getCategory: (key) => {
							return t.objects.book.getCategory(key);
						}
					},
					'amount': {
						title: 'Montant',
						type: 'money',
						width: '20%'
					}
				},

				filter: (transaction) => {
					return (transaction.direction === t.transactionDirection) &&
						   (Formatting.date(transaction.date).indexOf(t.month) === 0);
				}
			},

			transactionDirectionSwitcherList: {
				'input': {
					name: 'Entrées'
				},

				'output': {
					name: 'Sorties'
				}
			},

			transactionDirection: 'output',
			month: '',

			selectedTransaction: null
		}
	},

	props: ['objects'],

	mounted () {
		var transaction = new BookReport(this.objects.book).getMostRecentTransaction();

		if (transaction) {
			this.month = Dates.getMonth(transaction.date);
		}
	},

	updated () {

	},

	methods: {
		order () {
			return this.objects.book.transactions.concat().sort((a, b) => {
				if (this.sort.mode === 'date') {
					if (this.sort.dir === 'acsending') {
						return a.date - b.date;
					} else {
						return b.date - a.date;
					}
				}
			});
		},

		onTransactionSelected (transaction) {
			this.selectedTransaction = transaction;
		},

		onTransactionUnselected () {
			this.selectedTransaction = null;
		},

		onDeleteTransaction () {
			this.objects.book.removeTransaction(this.selectedTransaction.key);
			this.selectedTransaction = null;

			this.objects.save();
		}
	},

	computed: {
		monthSwitcherList () {
			var months = new BookReport(this.objects.book).getAllMonths();
			var ret = {};

			months.forEach((month) => {
				ret[month] = {
					name: Dates.getMonthName(Dates.parse(month + '-01'))
				};
			});

			return ret;
		},

		monthEconomies () {
			var e = new BookReport(this.objects.book).getSavingsForMonth(this.month);

			if (e < 0) {
				return 'En attente';
			}

			return Formatting.money(e);
		}
	},

	components: {
		Switcher,
		DataTable
	}
}
</script>

<style>

	.transaction-list .options {
		margin-top: 20px;
	}

	.transaction-list .scroll {
		max-height: 300px;
	}

	.transaction-list button {
		width: 100%;
	}

</style>
