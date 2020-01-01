<template>
	<div class="module transaction-list">
		<div class="title">
			Transactions
		</div>

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

		<div class="transactions">
			<data-table :tableModel="table" :tableData="appModel.book.transactions"></data-table>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';

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
						width: '20%'
					},
					'title': {
						title: 'Titre',
						type: 'string',
						width: '30%'
					},
					'category': {
						title: 'Catégorie',
						type: 'string',
						width: '30%'
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

			monthSwitcherList: {
				'2019-11': {
					title: 'Novembre'
				},

				'2019-12': {
					title: 'Décembre'
				}
			},

			transactionDirectionSwitcherList: {
				'input': {
					title: 'Entrées'
				},

				'output': {
					title: 'Sorties'
				}
			},

			transactionDirection: 'output',
			month: '2019-12'
		}
	},

	props: ['appModel'],

	mounted: function () {

	},

	methods: {
		order: function () {
			return this.appModel.book.transactions.concat().sort((a, b) => {
				if (this.sort.mode === 'date') {
					if (this.sort.dir === 'acsending') {
						return a.date - b.date;
					} else {
						return b.date - a.date;
					}
				}
			});
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

	.transaction-list .option {
		margin-bottom: 10px;
	}

	.transaction-list .scroll {
		max-height: 300px;
	}

	.transaction-list .switcher {
		margin-top: 10px;
	}
</style>
