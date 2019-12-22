<template>
	<div class="grid">
		<table class="grid-table">
			<tbody>
				<tr v-for="y in height" :class="'row-' + y">
					<td v-for="x in width" :class="'col-' + x + ' ' + (cellMode(x, y) === 'edit' ? 'edit' : '')">
						<div class="grid-cell" v-on:click="onCellClick(x, y)">
							<div class="cell-render" v-if="cellMode(x, y) === 'view'">
								{{ renderCellText(x, y) }}
							</div>

							<div class="input-render" v-if="cellMode(x, y) === 'edit'">
								<grid-input v-on:blur="onCellBlur(x, y)" v-model="editedCellValue" ref="editedCellInput" />
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import GridInput from './grid-input.vue';

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

	setCells (cells) {
		for (var i = 0; i < cells.length; i++) {
			this.cells.push(new Cell(cells[i].x, cells[i].y, cells[i].value));
		}
	}
};

export default {
	data () {
		return {
			width: 12,
			height: 10,
			model: new Model(),
			editedCell: null,
			editedCellValue: ''
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
			this.editedCellValue = this.renderCellText(x, y);
			this.editedCell = x + ':' + y;
		},

		onCellBlur: function (x, y) {
			if (this.editedCellValue.length != 0) {
				this.model.setCell(x, y, this.editedCellValue);
				this.$emit('modelChange', this.model);
			}

			this.editedCell = null;
			this.editedCellValue = '';
		}
	},

	components: {
		GridInput
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
	max-width: 60px;
	height: 30px;
	overflow: hidden;
	padding: 2px;
}

.grid-table td.edit {
	background-color: #efe;
}

.grid-cell {
	width: 100%;
	height: 100%;
	position: relative;
}

.grid-cell > div {
	width: 100%;

	position: absolute;
	top: 50%;
	-ms-transform: translateY(-50%);
	transform: translateY(-50%);
}

.cell-render {
	text-align: right;
}

</style>
