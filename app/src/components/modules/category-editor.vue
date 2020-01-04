<template>
	<div class="module category-editor">
		<h2>Catégories</h2>
		
		<div class="actions">
			<span class="add" @click="add"><i class="fas fa-plus-square"></i></span>
		</div>

		<div class="categories">
			<data-table ref="table" :tableModel="table" :tableData="objects.book.categories" :selectable="true" @selected="onSelect" @unselected="onUnselect"></data-table>
		</div>

		<div class="form" v-if="editing">
			<h3>Modifier</h3>

			<input type="text" v-model="edited.name" placeholder="Nom" />

			<div class="color-edit">
				<input type="text" class="color" v-model="edited.color" placeholder="Couleur" />
			</div>
		</div>
	</div>
</template>

<script>
import DataTable from 'components/data-table';
import ColorSquare from 'components/color-square';

export default {
	data () {
		return {
			edited: {
				name: '',
				color: ''
			},

			editing: false,

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
		}
	},

	props: ['objects'],

	mounted () {

	},

	methods: {
		onSelect (row) {
			if (this.edited.name.length > 0) {
				this.objects.save();
			}

			this.edited = row;
			this.editing = true;
		},

		onUnselect (row) {
			this.edited = {
				name: '',
				color: ''
			};

			this.objects.save();

			this.editing = false;
		},

		add () {
			this.objects.book.addCategory('Nouvelle catégorie', '000000');

			if (this.editing) {
				this.objects.save();
			}

			this.$refs.table.select(this.objects.book.categories[this.objects.book.categories.length - 1]);
		}
	},

	components: {
		DataTable,
		ColorSquare
	}
}
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
		font-size: 1em;
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
