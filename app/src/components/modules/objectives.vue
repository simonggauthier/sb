<template>
	<div class="module objectives">
		<h2>Objectifs</h2>

		<div class="objectives">
			<data-table ref="table" :tableModel="table" :tableData="objects.book.objectives" :selectable="true" @selected="onObjectiveSelected"></data-table>
		</div>
	</div>
</template>

<script>
import Api from 'api/api';
import Formatting from 'util/formatting';

import DataTable from 'components/data-table';

export default {
	data () {
		var t = this;

		return {
			table: {
				sort: {
					key: 'categoryId',
					direction: 'ascending'
				},

				columns: {
					'categoryId': {
						title: 'Catégorie',
						type: 'category',
						width: '60%',

						getCategory: (id) => {
							return t.objects.book.getCategory(id);
						}
					},

					'amount': {
						title: 'Montant',
						type: 'money',
						width: '40%'
					}
				}
			},

			dialogModel: {
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

	},

	methods: {
		updateObjective (objective) {
			Api.saveObjective(objective);
		},

		createModalMission (objective) {
			return {
				title: 'Modifier un objectif',
				model: this.dialogModel,
				target: objective,
				okLabel: 'Modifier',
				canClose: true,
				canDelete: false,

				onClose: (action) => {
					this.$refs.table.unselect();

					if (action === 'ok') {
						this.updateObjective(objective);
					}
				}
			}
		},

		onObjectiveSelected (objective) {
			this.$emit('requestModal', this.createModalMission(objective));
		}
	},

	computed: {
		
	},

	components: {
		DataTable
	}
}
</script>

<style>

.objectives .scroll {
	max-height: 300px;
}

</style>
