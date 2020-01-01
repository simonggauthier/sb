var TransactionDescription = {
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
	}
};

var CURRENT_VERSION = 3;

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
	}
};

class Book {
	constructor () {
		this.version = CURRENT_VERSION;
		this.transactions = [];
	}

	addTransaction (title, category, amount, date, direction) {
		this.transactions.push({
			title,
			category,
			amount,
			date,
			direction
		});
	}

	removeTransaction (id) {
		this.transactions = this.transactions.filter((v) => {
			return v.id != id;
		});
	}

	static fromJson (json) {
		var ret = Object.assign(new Book, json);
		var converted = false;

		if (ret.version != CURRENT_VERSION) {
			var f = Converter[ret.version + '-' + CURRENT_VERSION];

			if (f) {
				console.log('Converting book from version ' + ret.version + ' to ' + CURRENT_VERSION);

				f(ret);

				converted = true;
			}	
		}

		return {
			converted,
			book: ret
		};
	}
}

export default Book;
