import uuid from 'uuid/v4';
import Dates from 'util/dates';

class Book {
	constructor () {
		this.id = null;
		this.categories = [];
		this.transactions = [];
		this.objectives = [];
	}

	addTransaction (title, categoryId, amount, date, direction) {
		this.transactions.push({
			title,
			categoryId,
			amount,
			date,
			direction
		});

		return this.transactions[this.transactions.length - 1];
	}

	removeTransaction (id) {
		var transaction = this.getTransaction(id);

		if (transaction === null) {
			return;
		}

		this.transactions.splice(this.transactions.indexOf(transaction), 1);
	}

	getTransaction (id) {
		return this.transactions.find((t) => {
			return t.id === id;
		});
	}

	addCategory (name, color) {
		this.categories.push({
			name,
			color
		});

		return this.categories[this.categories.length - 1];
	}

	getCategory (id) {
		return this.categories.find((c) => {
			return c.id === id;
		});
	}

	static fromJson (json) {
		var ret = Object.assign(new Book, json);

		return ret;
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
