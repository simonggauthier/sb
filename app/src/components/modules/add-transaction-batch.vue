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
					this.nextEntry.context = line[2].trim().toLowerCase();
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
		return Number.isFinite(parseFloat(this.toAmount(str)));
	}

	toAmount (str) {
		let ret = str.replace(/\(/g, '').replace(/\)/g, '').replace(/\$/g, '').replace(/,/g, '.').replace(/ /g, '');

		if (ret.length < 4) {
			return ret + '0';
		}

		return ret;
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
			},

			dialogModel: {
				context: {
					label: 'Contexte',
					type: 'string'
				},

				categoryId: {
					label: 'Catégorie',
					type: 'list',
					getList: () => { return this.objects.book.categories; },
					render: (entry) => { return entry.name; }
				},

				title: {
					label: 'Titre',
					type: 'string'
				},

				creationDate: {
					label: 'Date',
					type: 'date'
				},

				amount: {
					label: 'Montant',
					type: 'money'
				}
			},

			entries: [],
			index: 0,

			messageLog: []
		}
	},

	props: ['api', 'objects'],

	methods: {
		onAdd () {
			let transactions = [];
			let tokenizer = new Tokenizer(this.form.raw);

			this.entries = tokenizer.entries;
			this.index = 0;

			if (this.entries.length > 0) {
				this.$emit('requestModal', this.createModalMission(this.getNextEntry()));
			}
		},

		async addTransaction (entry) {
			await this.api.setContextCategory({
				context: entry.context,
				categoryId: entry.categoryId,
				title: entry.title
			});

			var transaction = this.objects.book.addTransaction(
				entry.title,
				entry.categoryId,
				entry.amount,
				entry.creationDate,
				entry.direction
			);

			transaction.id = (await this.api.saveTransaction(transaction)).id;
		},

		getNextEntry () {
			if (this.entries.length <= this.index) {
				return null;
			}

			let ret = this.entries[this.index];

			this.index++;

			let context = this.objects.book.contextCategories.find((c) => ret.context.indexOf(c.context) === 0);

			if (context) {
				ret.categoryId = context.categoryId;
				ret.title = context.title;
			}

			return ret;
		},

		createModalMission (entry) {
			let doNext = () => {
				let next = this.getNextEntry();

				if (next) {
					this.$emit('requestModal', this.createModalMission(next));
				}
			};

			return {
				title: 'Ajouter une transaction',
				model: this.dialogModel,
				target: entry,

				buttons: [{
					action: 'add',
					label: 'Ajouter',
					close: true,

					click: () => {
						this.addTransaction(entry);

						doNext();
					},

					validate: () => {
						if (!entry.categoryId) {
							throw 'Catégorie invalide';
						}

						if (!entry.title) {
							throw 'Titre invalide';
						}
					}
				}, {
					action: 'ignore',
					label: 'Ignorer',
					close: true,

					click: () => {
						doNext();
					}
				}, {
					action: 'cancel',
					label: 'Annuler',
					close: true,

					click: () => {
						this.entries = [];
						this.index = 0;
					}
				}]
			}
		},
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
