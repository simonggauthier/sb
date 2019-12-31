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

var CURRENT_VERSION = 1;

class Book {
	constructor () {
		this.version = CURRENT_VERSION;
		this.transactions = [];
	}

	addTransaction (title, category, amount, date) {
		this.transactions.push({
			title,
			category,
			amount,
			date
		});
	}

	removeTransaction (id) {
		this.transactions = this.transactions.filter((v) => {
			return v.id != id;
		});
	}

	fromJson (json) {
		return Object.assign(new Book, json);
	}
}

export default Book;
