import uuid from 'uuid/v4';

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

			book.version = 6;
		});		
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
			console.log('splice ' + i);
			this.categories.splice(i, 1);
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
