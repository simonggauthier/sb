<template>
	<div class="data-table">
		<div>
			<table>
				<thead>
					<tr>
						<th v-for="(col, key) in tableModel.columns" v-on:click="changeSort(key)" v-bind:class="{ money: col.type === 'money' }" v-bind:style="{ width: col.width }">
							<span>{{ col.title }}</span>
							<i v-if="tableModel.sort.key === key && tableModel.sort.direction === 'descending'" class="fas fa-arrow-circle-up"></i>
							<i v-if="tableModel.sort.key === key && tableModel.sort.direction === 'ascending'" class="fas fa-arrow-circle-down"></i>
						</th>
					</tr>
				</thead>

				<tbody class="scroll">
					<tr v-for="(row, i) in sort()" v-if="tableModel.filter && tableModel.filter(row)" v-on:click="onRowClick(row)" v-bind:class="{ selected: selectedRow === row }">
						<td v-for="(col, key) in tableModel.columns" v-bind:class="{ money: col.type === 'money' }" v-bind:style="{ width: col.width }">
							<color-square v-if="col.type === 'color' || col.type === 'category'" :edit="false" :color="findSquareColor(col, row[key])"></color-square>

							{{ format(row, col, key) }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import ColorSquare from 'components/color-square';

import Formatting from 'util/formatting';

export default {
	data () {
		return {
			selectedRow: null
		}
	},

	props: ['tableModel', 'tableData', 'selectable'],

	mounted () {

	},

	methods: {
		format (row, col, key) {
			var v = row[key];

			if (col.type === 'string' || col.type === 'number') {
				return v;
			} else if (col.type === 'date') {
				return Formatting.date(v);
			} else if (col.type === 'money') {
				return Formatting.money(v);
			} else if (col.type === 'category') {
				return col.getCategory(v).name;
			} else {
				return v;
			}
		},

		sort () {
			var key = this.tableModel.sort.key;
			var col = this.tableModel.columns[key];
			var direction = this.tableModel.sort.direction;

			return this.tableData.concat().sort((a, b) => {
				var x = direction === 'ascending' ? a : b;
				var y = direction === 'ascending' ? b : a;

				x = x[key];
				y = y[key];

				if (col.type === 'string' || col.type === 'color') {
					return x.localeCompare(y);
				} else if (col.type === 'number') {
					return x - y;
				} else if (col.type === 'date') {
					return x - y;
				} else if (col.type === 'money') {
					return parseFloat(x) - parseFloat(y);
				} else if (col.type === 'category') {
					return col.getCategory(x).name.localeCompare(col.getCategory(y).name);
				} else {
					return 0;
				}
			});
		},

		changeSort (key) {
			if (this.tableModel.sort.key === key)
			{
				if (this.tableModel.sort.direction === 'ascending') {
					this.tableModel.sort.direction = 'descending';
				} else {
					this.tableModel.sort.direction = 'ascending';
				}
			}

			this.tableModel.sort.key = key;
		},

		findSquareColor (col, value) {
			if (col.type === 'color') {
				return value;
			} else if (col.type === 'category') {
				return col.getCategory(value).color;
			}
		},

		select (row) {
			if (this.selectable) {
				if (this.selectedRow === row) {
					this.selectedRow = null;
					this.$emit('unselected');
				} else {
					this.selectedRow = row;

					this.$emit('selected', row);
				}
			}
		},

		onRowClick (row) {
			this.select(row);
		}
	},

	components: {
		ColorSquare
	}
}
</script>

<style>

.data-table .money {
	text-align: right;
}

.data-table thead {
	cursor: pointer;
}

.data-table .scroll {
	overflow-y: scroll;
}

</style>
