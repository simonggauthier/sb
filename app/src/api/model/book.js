var Transaction = require('./transaction.js');

class Book {
	constructor () {
		this.transactions = [];
	}

	addTransaction (title, category, amount, date) {
		var ret = new Transaction(title, category, amount, date);

		this.transactions.push(ret);
	}

	removeTransaction (id) {
		this.transactions = this.transactions.filter((v) => {
			return v.id != id;
		});
	}
}

export default {
	Book
}
