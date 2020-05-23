<template>
	<div class="module reports">
		<h2>Rapports</h2>

		<div class="options">
			<div class="option">
				<label>Type</label>
				<switcher :list="reports" v-model="reportType"></switcher>
			</div>

			<div class="report" :class="key" v-for="(report, key) in reports" :key="key">
				<data-table
					v-if="report && reportType === key && report.buildTableModel"
					:tableModel="report.buildTableModel()"
					:tableData="report.buildTableData()"
					:selectable="false"
				></data-table>
			</div>
		</div>
	</div>
</template>

<script>
import Formatting from 'util/formatting';

import DataTable from 'components/data-table';
import Switcher from 'components/switcher';

export default {
	data () {
		return {
			reportType: 'savings-total',

			reports: {
				'savings-total': {
					name: 'Économies totales',

					buildTableModel: () => {
						return {
							sort: {
								key: 'key',
								direction: 'ascending'
							},

							columns: {
								'key': {
									title: 'Économies',
									type: 'string',
									width: '60%'
								},
								'amount': {
									title: 'Montant',
									type: 'money',
									width: '40%'
								}
							}
						};
					},

					buildTableData: () => {
						return [{
							key: 'Moyenne d\'économies par mois',
							amount: this.objects.book.report.averageSavingsPerMonth
						}, {
							key: 'Économies totales',
							amount: this.objects.book.report.totalSavings
						}]
					}
				},

				'category-average-month': {
					name: 'Moyenne mensuelle par catégorie',

					buildTableModel: () => {
						return {
							sort: {
								key: 'categoryId',
								direction: 'ascending'
							},

							columns: {
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
							}
						};
					},

					buildTableData: () => {
						var ret = [];

						this.objects.book.categories.forEach((category) => {
							ret.push({
								categoryId: category.id,
								amount: this.calculateTotalCategoryAverage(category.id)
							});
						});

						return ret;
					}
				}
			}
		}
	},

	props: ['objects'],

	methods: {
		calculateTotalCategoryAverage (categoryId) {
			var a = Object.keys(this.objects.book.report.monthlyTotalPerCategory)
				.filter((month, i, a) => i < a.length - 1)
				.map((month) => this.objects.book.report.monthlyTotalPerCategory[month][categoryId]);

			if (a.length === 0) {
				return 0;
			}

			return a.reduce((a, b) => (a ? a : 0) + (b ? b : 0)) / a.length;
		}
	},

	components: {
		DataTable,
		Switcher
	}
}
</script>

<style>
.reports .options {
	margin-top: 20px;
}

.reports .scroll {
	max-height: 300px;
}
</style>
