import Dates from 'util/dates';

class BookReport {
	constructor() {
		this.mostRecentTransaction = null;
		this.mostRecentOutputTransaction = null;
		this.allMonths = null;
		this.transactionsForMonths = {};
		this.savingsForMonths = {};
		this.averageSavingsPerMonth = 0;
		this.monthlyTotalPerCategory = {};
		this.totalSavings = 0;
	}
}

class Book {
	constructor() {
		this.id = null;
		this.categories = [];
		this.transactions = [];
		this.report = null;

		this.sorters = {
			transactionDate: (direction) => {
				return (a, b) => {
					var x = direction === 'ascending' ? a : b;
					var y = direction === 'ascending' ? b : a;

					return Dates.compare(x.creationDate, y.creationDate);
				};
			}
		};
	}

	buildReport () {
		console.log('Build report');

		let report = new BookReport();
		let cache = {};

		cache.transactions = {};
		cache.transactions.sortedByCreationDate = {};
		cache.transactions.sortedByCreationDate.descending = this.transactions.sort(this.sorters.transactionDate('descending'));
		cache.transactions.sortedByCreationDate.ascending = cache.transactions.sortedByCreationDate.descending.slice().reverse();

		// mostRecentTransaction
		report.mostRecentTransaction = null;

		if (this.transactions.length > 0) {
			report.mostRecentTransaction = cache.transactions.sortedByCreationDate['descending'][0];
		}

		// mostRecentOutputTransaction
		report.mostRecentOutputTransaction = null;

		if (this.transactions.length > 0) {
			let output = cache.transactions.sortedByCreationDate['descending'].filter((transaction) => {
				return transaction.direction === 'output';
			});

			if (output.length > 0) {
				report.mostRecentOutputTransaction = output[0];
			}
		}

		// allMonths
		cache.allMonths = {};

		report.allMonths = [];
		report.transactionsForMonths = {};
		report.savingsForMonths = {};
		report.monthlyTotalPerCategory = {};
		report.totalSavings = 0;

		cache.transactions.sortedByCreationDate.ascending.forEach((transaction) => {
			// allMonths
			let yam = Dates.getYearAndMonth(transaction.creationDate);

			if (!cache.allMonths[yam]) {
				cache.allMonths[yam] = true;

				report.allMonths.push(yam);
			}

			// transactionsForMonths
			if (!(yam in report.transactionsForMonths)) {
				report.transactionsForMonths[yam] = [];
			}

			report.transactionsForMonths[yam].push(transaction);

			// savingsForMonths
			// totalSavings
			if (!(yam in report.savingsForMonths)) {
				report.savingsForMonths[yam] = 0;
			}

			let mod = transaction.direction === 'output' ? -1 : 1;
			let amount = (mod * parseFloat(transaction.amount));

			report.savingsForMonths[yam] += amount;
			report.totalSavings += amount;

			// monthlyTotalPerCategory
			if (!(yam in report.monthlyTotalPerCategory)) {
				report.monthlyTotalPerCategory[yam] = {};
			}

			if (!(transaction.categoryId in report.monthlyTotalPerCategory[yam])) {
				report.monthlyTotalPerCategory[yam][transaction.categoryId] = 0;
			}

			report.monthlyTotalPerCategory[yam][transaction.categoryId] += parseFloat(transaction.amount);
		});

		report.averageSavingsPerMonth = report.totalSavings / Object.keys(report.savingsForMonths).length;

		this.report = report;
	}

	addTransaction (title, categoryId, amount, creationDate, direction) {
		var transaction = {
			title,
			categoryId,
			amount,
			creationDate,
			direction,
			bookId: this.id
		};

		this.transactions.push(transaction);

		this.buildReport();

		return transaction;
	}

	removeTransaction (transaction) {
		this.transactions.splice(this.transactions.indexOf(transaction), 1);

		this.buildReport();
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

		this.buildReport();

		return this.categories[this.categories.length - 1];
	}

	getCategory (id) {
		return this.categories.find((c) => {
			return c.id === id;
		});
	}

	static fromData (data) {
		var ret = Object.assign(new Book, data);

		ret.buildReport();

		return ret;
	}
}

export {
	Book
};
