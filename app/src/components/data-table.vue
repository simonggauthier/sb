<template>
	<div class="data-table">
		<div>
			<table>
				<thead>
					<tr>
						<th
							v-for="(col, key) in tableModel.columns"
							@click="changeSort(key)"
							:class="{ money: col.type === 'money' }"
							:style="{ width: col.width }"
							:key="key"
						>
							<span>{{ col.title }}</span>
							<i
								v-if="tableModel.sort.key === key && tableModel.sort.direction === 'descending'"
								class="fas fa-arrow-circle-up"
							></i>
							<i
								v-if="tableModel.sort.key === key && tableModel.sort.direction === 'ascending'"
								class="fas fa-arrow-circle-down"
							></i>
						</th>
					</tr>
				</thead>

				<tbody class="scroll">
					<tr
						v-for="(row, i) in sortedData"
						@click="onRowClick(row)"
						:class="{ selected: selectedRow === row }"
						:key="i"
					>
						<td
							v-for="(col, key) in tableModel.columns"
							:class="{ money: col.type === 'money' }"
							:style="{ width: col.width }"
							:key="key"
						>
							<color-square
								v-if="col.type === 'color' || col.type === 'category'"
								:edit="false"
								:color="findSquareColor(col, row[key], row, key)"
							></color-square>
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
import Dates from 'util/dates';
import Formatting from 'util/formatting';

export default {
	data () {
		return {
			selectedRow: null
		}
	},

	props: ['tableModel', 'tableData', 'selectable'],

	methods: {
		format (row, col, key) {
			var v = row[key];

			if (col.type === 'string' || col.type === 'number') {
				return v;
			} else if (col.type === 'date') {
				return v;
			} else if (col.type === 'money') {
				return Formatting.money(v);
			} else if (col.type === 'category') {
				return col.getCategory(v).name;
			} else {
				return v;
			}
		},

		changeSort (key) {
			if (this.tableModel.sort.key === key) {
				if (this.tableModel.sort.direction === 'ascending') {
					this.tableModel.sort.direction = 'descending';
				} else {
					this.tableModel.sort.direction = 'ascending';
				}
			}

			this.tableModel.sort.key = key;
		},

		findSquareColor (col, value, row, key) {
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

		unselect () {
			this.selectedRow = null;
		},

		onRowClick (row) {
			this.select(row);
		}
	},

	computed: {
		sortedData () {
			var key = this.tableModel.sort.key;
			var col = this.tableModel.columns[key];
			var direction = this.tableModel.sort.direction;

			let data = this.tableModel.filter ? this.tableData.filter((row) => {
				return this.tableModel.filter(row);
			}) : this.tableData;

			return data.sort((a, b) => {
				var x = direction === 'ascending' ? a : b;
				var y = direction === 'ascending' ? b : a;

				x = x[key];
				y = y[key];

				if (col.type === 'string' || col.type === 'color') {
					return x.localeCompare(y);
				} else if (col.type === 'number') {
					return x - y;
				} else if (col.type === 'date') {
					return Dates.compare(x, y);
				} else if (col.type === 'money') {
					return parseFloat(x) - parseFloat(y);
				} else if (col.type === 'category') {
					return col.getCategory(x).name.localeCompare(col.getCategory(y).name);
				} else {
					return 0;
				}
			});
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
