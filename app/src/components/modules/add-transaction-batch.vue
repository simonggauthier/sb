<template>
	<div class="module add-transaction">
		<h2>Ajouter Une Liste De Transactions</h2>

		<label>Direction</label>

		<switcher :list="transactionDirectionSwitcherList" v-model="form.direction"></switcher>

		<textarea v-model="form.raw"></textarea>

		<button class="icon" type="button" @click="onAdd">Ajouter</button>
	</div>
</template>

<script>
import Dates from 'util/dates';
import Switcher from 'components/switcher';

class Tokenizer {
	constructor(raw) {
		this.lines = raw.split('\n');
		this.cursor = 0;
		this.entries = [];
		this.nextEntry = null;

		this.tokenize();
	}

	tokenize () {
		while (this.cursor < this.lines.length) {
			let line = this.consume();

			if (this.isEntryLine(line)) {
				// New Entry
				if (this.nextEntry != null) {
					this.entries.push(this.nextEntry);
				}

				this.nextEntry = {};
				this.nextEntry.creationDate = Dates.parseBank(line[0]);
				this.nextEntry.isCc = line.length > 1 && Dates.isValidBank(line[1])

				// Special case: Frais
				if (line[1].trim().toLowerCase().indexOf('frais') >= 0) {
					this.nextEntry.context = line[1].trim().toLowerCase();
				}

				// Special case: CC
				if (this.nextEntry.isCc) {
					this.nextEntry.context = line[2].trim();
				}
			} else if (this.isAmountLine(line)) {
				// Amount & direction
				this.nextEntry.direction = line[0].trim().length > 0 ? 'output' : 'input';

				let amount = this.nextEntry.direction === 'output' ? line[0] : line[1];

				this.nextEntry.amount = this.toAmount(amount);
			} else if (!this.nextEntry.context) {
				// Context
				this.nextEntry.context = line.join(' ').toLowerCase();
			} else if (this.isAddressLine(line) && !this.nextEntry.isCc) {
				// Address
				this.nextEntry.address = line.join(' ').toLowerCase();
			} else if (line.join(' ').indexOf('confirmation') >= 0) {
				this.nextEntry.confirmationNumber = line.join(' ');
			} else {
				console.log('Cannot parse line ' + line);
			}
		}

		if (this.nextEntry != null) {
			this.entries.push(this.nextEntry);
		}
	}

	consume () {
		return this.lines[this.cursor++].split('\t');
	}

	isEntryLine (line) {
		return Dates.isValidBank(line[0]);
	}

	isAmountLine (line) {
		if (line.length < 3 && !this.nextEntry.isCc) {
			return false;
		}

		let amount = line[0].trim();

		if (amount.length === 0) {
			amount = line[1].trim();
		}

		if (amount.length === 0) {
			return false;
		}

		return this.isAmount(amount);
	}

	isAddressLine (line) {
		return Number.isInteger(parseInt(line[0].trim().charAt(0), 10));
	}

	isAmount (str) {
		return Number.isFinite(this.toAmount(str));
	}

	toAmount (str) {
		return parseFloat(str.replace(/\(/g, '').replace(/\)/g, '').replace(/\$/g, '').replace(/,/g, '.').replace(/ /g, ''));
	}
}

export default {
	data () {
		return {
			form: {
				direction: 'output',
				raw: ''
			},

			transactionDirectionSwitcherList: {
				'input': {
					name: 'Entrée'
				},

				'output': {
					name: 'Sortie'
				}
			}
		}
	},

	props: ['api', 'objects'],

	methods: {
		onAdd () {
			let transactions = [];
			let tokenizer = new Tokenizer(this.form.raw);

			console.log(tokenizer.entries);
		}
	},

	components: {
		Switcher
	}
}
</script>

<style scoped>
textarea {
	width: 100%;
	height: 200px;
}
</style>
