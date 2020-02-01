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
			<input type="text" disabled="disabled" :value="monthSavings" aria-label="Économies" />
		</div>

		<div class="transactions">
			<data-table ref="table" :tableModel="table" :tableData="objects.book.transactions" :selectable="true" @selected="onTransactionSelected"></data-table>
		</div>
	</div>
</template>

<script>
import Api from 'api/api';
import Formatting from 'util/formatting';
import Dates from 'util/dates';

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
					'categoryId': {
						title: 'Catégorie',
						type: 'category',
						width: '30%',
						
						getCategory: (id) => {
							return t.objects.book.getCategory(id);
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

			dialogModel: {
				direction: {
					label: 'Direction',
					type: 'switcher',
					list: {
						'input': {
							name: 'Entrée'
						},

						'output': {
							name: 'Sortie'
						}
					}
				},

				_fdate: {
					label: 'Date',
					type: 'date'
				},

				title: {
					label: 'Titre',
					type: 'string'
				},

				categoryId: {
					label: 'Catégorie',
					type: 'list',
					getList: () => { return t.objects.book.categories; },
					render: (entry) => { return entry.name; }
				},

				amount: {
					label: 'Montant',
					type: 'money'
				}
			}
		}
	},

	props: ['objects'],

	mounted () {
		var transaction = this.objects.book.report.mostRecentTransaction;

		if (transaction) {
			this.month = Dates.getMonth(transaction.date);
		}
	},

	methods: {
		updateTransaction (transaction) {
			transaction.date = Dates.parse(transaction._fdate);

			Api.saveTransaction(transaction, this.objects.book);

			this.objects.book.buildReport();
		},

		deleteTransaction (transaction) {
			this.objects.book.removeTransaction(transaction);

			Api.deleteTransaction(transaction);
		},

		createModalMission (transaction) {
			transaction._fdate = Formatting.date(transaction.date);

			return {
				title: 'Modifier une transaction',
				model: this.dialogModel,
				target: transaction,
				okLabel: 'Modifier',
				canClose: true,
				canDelete: true,

				onClose: (action) => {
					this.$refs.table.unselect();

					if (action === 'ok') {
						this.updateTransaction(transaction);
					} else if (action === 'delete') {
						this.deleteTransaction(transaction);
					}
				}
			}
		},

		onTransactionSelected (transaction) {
			this.$emit('requestModal', this.createModalMission(transaction));
		}
	},

	computed: {
		monthSwitcherList () {
			var months = this.objects.book.report.allMonths;
			var ret = {};

			months.forEach((month) => {
				ret[month] = {
					name: Dates.getMonthName(Dates.parse(month + '-01'))
				};
			});

			return ret;
		},

		monthSavings () {
			var e = this.objects.book.report.savingsForMonths[this.month];

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

</style>
