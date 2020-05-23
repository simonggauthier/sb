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
		var report = new BookReport();

		// mostRecentTransaction
		if (this.transactions.length === 0) {
			report.mostRecentTransaction = null;
		} else {
			report.mostRecentTransaction = this.transactions.sort(this.sorters.transactionDate('descending'))[0];
		}

		// mostRecentOutputTransaction
		if (this.transactions.length === 0) {
			report.mostRecentOutputTransaction = null;
		} else {
			report.mostRecentOutputTransaction = this.transactions.filter((transaction) => {
				return transaction.direction === 'output';
			}).sort(this.sorters.transactionDate('descending'))[0];
		}

		// allMonths
		report.allMonths = this.transactions.sort(this.sorters.transactionDate('ascending')).map((transaction) => {
			return Dates.getYearAndMonth(transaction.creationDate);
		}).filter((monthName, i, self) => {
			return self.indexOf(monthName) === i;
		});

		report.transactionsForMonths = {};
		report.savingsForMonths = {};
		report.monthlyTotalPerCategory = {};

		report.allMonths.forEach((month) => {
			// transactionsForMonths
			report.transactionsForMonths[month] = this.transactions.filter((transaction) => {
				return Dates.getYearAndMonth(transaction.creationDate) === month;
			});

			// savingsForMonths
			var s = report.transactionsForMonths[month].map((transaction) => {
				var v = parseFloat(transaction.amount);

				if (transaction.direction === 'output') {
					v = 0 - v;
				}

				return v;
			});

			if (s.length === 0) {
				report.savingsForMonths[month] = 0;
			} else {
				report.savingsForMonths[month] = s.reduce((acc, curr) => acc + curr);
			}

			// monthlyTotalPerCategory
			report.monthlyTotalPerCategory[month] = {};

			this.categories.forEach((category) => {
				var t = report.transactionsForMonths[month]
					.filter((transaction) => transaction.categoryId === category.id)
					.map((transaction) => parseFloat(transaction.amount));

				if (t.length > 0) {
					t = t.reduce((acc, curr) => acc + curr);
				} else {
					t = 0;
				}

				report.monthlyTotalPerCategory[month][category.id] = t;
			});
		});

		// averageSavingsPerMonth
		// totalSavings
		Object.keys(report.savingsForMonths).forEach((k) => {
			report.totalSavings += report.savingsForMonths[k];
		})

		report.averageSavingsPerMonth = report.totalSavings / Object.keys(report.savingsForMonths).length;

		this.report = report;
	}

	addTransaction (title, categoryId, amount, date, direction) {
		var transaction = {
			title,
			categoryId,
			amount,
			date,
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
