<template>
	<div class="module transaction-list">
		<h2>Transactions</h2>

		<div class="options">
			<div class="option" v-if="month.length > 0">
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
			<data-table
				v-if="month && transactionDirection"
				ref="table"
				:tableModel="table"
				:tableData="objects.book.transactions"
				:selectable="true"
				@selected="onTransactionSelected"
			></data-table>
		</div>
	</div>
</template>

<script>
import Formatting from 'util/formatting';
import Dates from 'util/dates';
import Switcher from 'components/switcher';
import DataTable from 'components/data-table';

export default {
	data () {
		return {
			table: {
				sort: {
					key: 'creationDate',
					direction: 'descending'
				},

				columns: {
					'creationDate': {
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
							return this.objects.book.getCategory(id);
						}
					},
					'amount': {
						title: 'Montant',
						type: 'money',
						width: '20%'
					}
				},

				filter: (transaction) => {
					let ret = (transaction.direction === this.transactionDirection) &&
						(Dates.getYearAndMonth(transaction.creationDate) === this.month);

					return ret;
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

				creationDate: {
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
					getList: () => { return this.objects.book.categories; },
					render: (entry) => { return entry.name; }
				},

				amount: {
					label: 'Montant',
					type: 'money'
				}
			}
		}
	},

	props: ['api', 'objects'],

	mounted () {
		if (this.objects.book.report.mostRecentTransaction) {
			this.month = Dates.getYearAndMonth(this.objects.book.report.mostRecentTransaction.creationDate);
		}
	},

	methods: {
		updateTransaction (transaction) {
			this.api.saveTransaction(transaction);

			this.objects.book.buildReport();
		},

		deleteTransaction (transaction) {
			this.objects.book.removeTransaction(transaction);

			this.api.deleteTransaction(transaction, this.objects.book);
		},

		createModalMission (transaction) {
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
					name: Dates.getMonthName(month)
				};
			});

			return ret;
		},

		monthSavings () {
			var savings = this.objects.book.report.savingsForMonths[this.month];

			return Formatting.money(savings);
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
	max-height: 600px;
}
</style>
