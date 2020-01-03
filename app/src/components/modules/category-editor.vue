<template>
	<div class="module category-editor">
		<h2>Catégories</h2>
		
		<div class="actions">
			<span class="add" @click="add"><i class="fas fa-plus-square"></i></span>
		</div>

		<div class="categories">
			<data-table ref="table" :tableModel="table" :tableData="appModel.book.categories" :selectable="true" @selected="onSelect" @unselected="onUnselect"></data-table>
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
import Vue from 'vue';

import DataTable from '../table/data-table.vue';
import ColorSquare from '../color-square/color-square.vue';

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

	props: ['appModel'],

	mounted () {

	},

	methods: {
		onSelect (row) {
			if (this.edited.name.length > 0) {
				this.appModel.save();
			}

			this.edited = row;
			this.editing = true;
		},

		onUnselect (row) {
			this.edited = {
				name: '',
				color: ''
			};

			this.appModel.save();

			this.editing = false;
		},

		add () {
			this.appModel.book.addCategory('Nouvelle catégorie', '000000');

			if (this.editing) {
				this.appModel.save();
			}

			this.$refs.table.select(this.appModel.book.categories[this.appModel.book.categories.length - 1]);
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
