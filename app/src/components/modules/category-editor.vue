<template>
	<div class="module category-editor">
		<h2>Catégories</h2>

		<div class="actions">
			<span class="add" @click="onAdd">
				<i class="fas fa-plus-square"></i>
			</span>
		</div>

		<div class="categories">
			<data-table
				ref="table"
				:tableModel="table"
				:tableData="objects.book.categories"
				:selectable="true"
				@selected="onSelect"
			></data-table>
		</div>
	</div>
</template>

<script>
import DataTable from 'components/data-table';
import ColorSquare from 'components/color-square';

export default {
	data () {
		return {
			table: {
				sort: {
					key: 'name',
					direction: 'ascending'
				},

				columns: {
					'name': {
						title: 'Nom',
						type: 'string',
						width: '60%'
					},
					'color': {
						title: 'Couleur',
						type: 'color',
						width: '40%'
					}
				},

				filter: (category) => {
					return category;
				}
			},

			dialogModel: {
				name: {
					label: 'Nom',
					type: 'string'
				},

				color: {
					label: 'Couleur',
					type: 'color'
				}
			}
		}
	},

	props: ['api', 'objects'],

	methods: {
		updateCategory (category) {
			this.api.saveCategory(category);
		},

		async addCategory (category) {
			category.bookId = this.objects.book.id;

			category.id = (await this.api.saveCategory(category)).id;
		},

		deleteCategory (category) {
			this.api.deleteCategory(category);
		},

		createModalMission (row) {
			var mode = (row.id || row.id === 0) ? 'update' : 'add';

			return {
				title: mode === 'update' ? 'Modifier une catégorie' : 'Ajouter une catégorie',
				model: this.dialogModel,
				target: row,
				okLabel: mode === 'update' ? 'Modifier' : 'Ajouter',
				canClose: true,
				canDelete: mode === 'update',

				onClose: (action) => {
					this.$refs.table.unselect();

					if (action === 'ok') {
						if (mode === 'add') {
							this.addCategory(row);
						} else {
							this.updateCategory(row);
						}
					} else if (action === 'close') {
						if (mode === 'add') {
							this.objects.book.categories.splice(this.objects.book.categories.length - 1, 1);
						}
					} else if (action === 'delete') {
						this.objects.book.categories.splice(this.objects.book.categories.indexOf(row), 1);

						this.deleteCategory(row);
					}
				}
			}
		},

		onAdd () {
			this.objects.book.addCategory('Nouvelle catégorie', '#ff00ff');

			this.$refs.table.select(this.objects.book.categories[this.objects.book.categories.length - 1]);
		},

		onSelect (row) {
			this.$emit('requestModal', this.createModalMission(row));
		}
	},

	components: {
		DataTable,
		ColorSquare
	}
};
</script>

<style>
.category-editor .form {
	margin-top: 10px;
	width: 100%;
}

.category-editor .form input {
	width: 100%;
}

.category-editor .form button {
	width: 100%;
}

.category-editor .add {
	font-size: 1.5em;
	cursor: pointer;
}

.category-editor .scroll {
	max-height: 300px;
}

@media only screen and (max-width: 600px) {
	.category-editor .form {
		width: 100%;
	}
}
</style>
