import uuid from 'uuid/v4';
import { formatDate } from '../../date.js';

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

	TransactionDirection: {
		key: {
			type: 'string'
		},

		name: {
			type: 'string'
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
			type: 'TransactionDirection'
		}
	}
};


var CURRENT_VERSION = 6;

var Converter = {
	'1-2': (book) => {
		book.transactions.forEach((t) => {
			t.direction = 'output';
		});

		book.version = 2;
	},

	'2-3': (book) => {
		book.transactions.forEach((t) => {
			t.amount = t.amount.replace(',', '');
		});

		book.version = 3;
	},

	'3-4': (book) => {
		book.version = 4;
	},

	'4-5': (book) => {
		book.transactions.forEach((t) => {
			t.key = uuid()
		});

		book.version = 5;
	},

	'5-6': (book) => {
		book.transactions.forEach((t) => {
			var c = null;

			for (var i = 0; i < book.categories.length; i++) {
				if (t.category === 'Hydro') {
					t.category = 'Électricité';
				}

				if (book.categories[i].name === t.category) {
					c = book.categories[i];
				}
			}

			if (!c) {
				console.log('Category ' + t.category + ' not found');
			} else {
				t.category = c.key;
			}
		});	

		book.version = 6;	
	}
};

var TransactionSorters = {
	date: (direction) => {
		return (a, b) => {
			var x = direction === 'ascending' ? a : b;
			var y = direction === 'ascending' ? b : a;

			return x.date - y.date;
		};
	}
};

class Book {
	constructor () {
		this.version = CURRENT_VERSION;

		this.categories = [];
		this.transactions = [];
	}

	addCategory (name, color) {
		this.categories.push({
			key: uuid(),
			name,
			color
		});

		return this.categories[this.categories.length - 1];
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

	getLastTransaction () {
		if (this.transactions.length === 0) {
			return null;
		}

		return this.transactions.sort(TransactionSorters.date('descending'))[0];
	}

	findCategory (key) {
		return this.categories.find((c) => {
			return c.key === key;
		});
	}

	cleanup () {
		var is = [];

		this.categories.forEach((c, i) => {
			if (c.color === '000000') {
				is.push(i);
			}
		})

		is.reverse().forEach((i) => {
			this.categories.splice(i, 1);
		});
	}

	months () {
		var ret = {};

		this.transactions.sort(TransactionSorters.date('ascending')).forEach((t) => {
			var m = formatDate(t.date, 'yyyy-MM');

			if (!ret[m]) {
				ret[m] = {
					name: Book.findMonthName(t)
				}
			};
		});

		return ret;
	}

	static findMonthName (transaction) {
		var ret = formatDate(transaction.date, 'MMMM yyyy');

		return ret.substring(0, 1).toUpperCase() + ret.substring(1);
	}

	static fromJson (json) {
		var ret = Object.assign(new Book, json);
		var converted = false;

		if (ret.version != CURRENT_VERSION) {
			var f = Converter[ret.version + '-' + CURRENT_VERSION];

			if (f) {
				console.log('Converting book from version ' + ret.version + ' to ' + CURRENT_VERSION);

				f(ret);

				if (ret.version === CURRENT_VERSION) {
					converted = true;
				}
				
			}	
		}

		ret.cleanup();

		return {
			converted,
			book: ret
		};
	}
}

export default Book;
