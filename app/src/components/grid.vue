<template>
	<div class="grid">
		<table class="grid-table">
			<tbody>
				<tr v-for="y in height">
					<td v-for="x in width">
						<div class="grid-cell" v-on:click="onCellClick(x, y)">
							<div v-if="cellMode(x, y) === 'view'">
								{{ renderCellText(x, y) }}
							</div>

							<div v-if="cellMode(x, y) === 'edit'">
								<input type="text" :value="renderCellText(x, y)" />
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
class Cell {
	constructor (x, y, value) {
		this.x = x;
		this.y = y;
		this.value = value;
	}

	is (x, y) {
		return this.x === x && this.y === y;
	}
}

class Model {
	constructor () {
		this.cells = [];
	}

	setCell (x, y, value) {
		var cell = this.getCell(x, y);

		if (cell) {
			cell.value = value;
		} else {
			this.cells.push(new Cell(x, y, value));
		}
	}

	getCell (x, y) {
		return this.cells.find((cell) => { return cell.is(x, y) });
	}
};

export default {
	data () {
		return {
			width: 12,
			height: 10,
			model: new Model(),
			editedCell: null
		}
	},

	methods: {
		renderCellText: function (x, y) {
			var cell = this.model.getCell(x, y);

			if (cell) {
				return cell.value;
			} else {
				return '';
			}
		},

		cellMode: function (x, y) {
			if (this.editedCell != null && this.editedCell === (x + ':' + y)) {
				return 'edit';
			} else {
				return 'view';
			}
		},

		onCellClick: function (x, y) {
			this.editedCell = x + ':' + y;
		}
	}
}
</script>

<style>
.grid-table {
	border-collapse: collapse;
}

.grid-table td {
	border: 1px solid #ccc;
	width: 60px;
	height: 30px;
}

.grid-cell {
	width: 100%;
	height: 100%;
}

</style>
