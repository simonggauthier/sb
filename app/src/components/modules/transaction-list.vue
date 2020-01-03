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
			<data-table :tableModel="table" :tableData="appModel.book.transactions" :selectable="true" @selected="onTransactionSelected" @unselected="onTransactionUnselected"></data-table>
		</div>

		<button type="button" v-if="selectedTransaction != null" @click="onDeleteTransaction">Supprimer</button>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';
import { formatDate } from '../../date.js';

import Switcher from '../switcher/switcher.vue';
import DataTable from '../table/data-table.vue';

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
						
						findCategory: (key) => {
							return t.appModel.book.findCategory(key);
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
			month: formatDate(t.appModel.book.getLastTransaction().date, 'yyyy-MM'),

			selectedTransaction: null
		}
	},

	props: ['appModel'],

	mounted () {

	},

	updated () {
		var monthSwitcherList = this.monthSwitcherList;

		if (!monthSwitcherList[this.month]) {
			this.month = Object.keys(monthSwitcherList)[Object.keys(monthSwitcherList).length - 1];
		}
	},

	methods: {
		order () {
			return this.appModel.book.transactions.concat().sort((a, b) => {
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
			this.appModel.book.removeTransaction(this.selectedTransaction.key);
			this.selectedTransaction = null;

			this.appModel.save();
		}
	},

	computed: {
		monthSwitcherList () {
			return this.appModel.book.months();
		},

		monthEconomies () {
			var e = this.appModel.book.report(this.month);

			if (e < 0) {
				return 'En attente';
			}

			return Formatting.money(this.appModel.book.report(this.month));
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
