import uuid from 'uuid/v4';
import Dates from 'util/dates';

var Descriptions = {
	TransactionCategory: {
		key: {
			type: 'string'
		},

		name: {
			type: 'string'
		},

		color: {
			type: 'color'
		}
	},

	Transaction: {
		title: {
			type: 'string'
		},

		category: {
			type: 'TransactionCategory'
		},

		amount: {
			type: 'Currency'
		},

		date: {
			type: 'Date'
		},

		direction: {
			type: 'TransactionDirection { input, output }'
		}
	}
};

const CURRENT_VERSION = 6;

class Book {
	constructor () {
		this.version = CURRENT_VERSION;

		this.categories = [];
		this.transactions = [];
	}

	addTransaction (title, category, amount, date, direction) {
		this.transactions.push({
			key: uuid(),
			title,
			category,
			amount,
			date,
			direction
		});

		return this.transactions[this.transactions.length - 1];
	}

	removeTransaction (key) {
		var transaction = this.getTransaction(key);

		if (transaction === null) {
			return;
		}

		this.transactions.splice(this.transactions.indexOf(transaction), 1);
	}

	getTransaction (key) {
		return this.transactions.find((t) => {
			return t.key === key;
		});
	}

	addCategory (name, color) {
		this.categories.push({
			key: uuid(),
			name,
			color
		});

		return this.categories[this.categories.length - 1];
	}

	getCategory (key) {
		return this.categories.find((c) => {
			return c.key === key;
		});
	}

	static fromJson (json) {
		var ret = Object.assign(new Book, json);
		var converted = false;

		if (ret.version != CURRENT_VERSION) {
			console.log('Current book version != json.version');

			console.log(json.version);

			ret = null;
		}

		return {
			converted,
			book: ret
		};
	}
}

class BookReport {
	constructor (book) {
		this.book = book;

		this.sorters = {
			date: (direction) => {
				return (a, b) => {
					var x = direction === 'ascending' ? a : b;
					var y = direction === 'ascending' ? b : a;

					return x.date - y.date;
				};
			}
		};
	}

	getMostRecentTransaction () {
		if (this.book.transactions.length === 0) {
			return null;
		}

		return this.book.transactions.sort(this.sorters.date('descending'))[0];
	}

	getAllMonths () {
		return this.book.transactions.sort(this.sorters.date('ascending')).map((transaction) => {
			return Dates.getMonth(transaction.date);
		}).filter((monthName, i, self) => {
			return self.indexOf(monthName) === i;
		});
	}

	getTransactionsForMonth (monthName) {
		return this.book.transactions.filter((transaction) => {
			return Dates.getMonth(transaction.date) === monthName;
		});
	}

	getSavingsForMonth (monthName) {
		var ret = this.getTransactionsForMonth(monthName).map((transaction) => {
			var v = parseFloat(transaction.amount);
			
			if (transaction.direction === 'output') {
				v = 0 - v;
			}

			return v;
		});

		if (ret.length === 0) {
			return 0;
		}

		 return ret.reduce((acc, curr) => {
			return acc + curr;
		});
	}
}

export { 
	Book,
	BookReport
};
