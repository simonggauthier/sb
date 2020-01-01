<template>
	<div class="data-table">
		<div>
			<table>
				<thead>
					<tr>
						<th v-for="(col, key) in tableModel.columns" v-on:click="changeSort(key)" v-bind:class="{ money: col.type === 'money' }" style="width: ">
							<span>{{ col.title }}</span>
							<i v-if="tableModel.sort.key === key && tableModel.sort.direction === 'descending'" class="fas fa-arrow-circle-up"></i>
							<i v-if="tableModel.sort.key === key && tableModel.sort.direction === 'ascending'" class="fas fa-arrow-circle-down"></i>
						</th>
					</tr>
				</thead>

				<tbody class="scroll">
					<tr v-for="(row, i) in sort()" v-if="tableModel.filter && tableModel.filter(row)">
						<td v-for="(col, key) in tableModel.columns" v-bind:class="{ money: col.type === 'money' }">
							{{ format(row, col, key) }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';

export default {
	data () {
		return {
			
		}
	},

	props: ['tableModel', 'tableData'],

	mounted: function () {

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

				if (col.type === 'string') {
					return x.localeCompare(y);
				} else if (col.type === 'number') {
					return x - y;
				} else if (col.type === 'date') {
					return x - y;
				} else if (col.type === 'money') {
					return parseFloat(x) - parseFloat(y);
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
		}
	},

	components: {

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
