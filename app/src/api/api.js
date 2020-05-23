import axios from 'axios';

class Api {
	constructor() {
		this.baseUrl = '/sbapi';
	}

	wrap (request) {
		return new Promise((resolve, reject) => {
			request.then((response) => {
				resolve(response.data);
			}).catch((error) => {
				reject(error);
			});
		})
	}

	req (method, action, data) {
		let query = '';

		if (method === 'post') {
			let form = new FormData();

			Object.keys(data).forEach((key) => {
				form.set(key, data[key]);
			});

			data = form;
		} else if (method === 'get') {
			query = '?';

			Object.keys(data).forEach((key, i, keys) => {
				query += key + '=' + encodeURIComponent(data[key]);

				if (i < keys.length - 1) {
					query += '&';
				}
			});
		}

		return this.wrap(axios({
			method: method,
			url: this.baseUrl + action + query,
			data: data
		}));
	}

	login (username, password) {
		return this.req('post', '/login', {
			username,
			password,
			device: 'sb'
		});
	}

	loginByToken (tokenId) {
		return this.req('post', '/login-by-token', {
			tokenId
		});
	}

	getBook (name) {
		return this.req('get', '/book', {
			name
		});
	}

	saveTransaction (transaction) {
		var transaction = Object.assign({}, transaction);

		return this.req('post', '/transaction', transaction);
	}

	deleteTransaction (transaction) {
		return this.req('post', '/transaction', {
			id: transaction.id,
			delete: 'delete'
		});
	}

	saveCategory (category) {
		var category = Object.assign({}, category);

		return this.req('post', '/category', category);
	}

	deleteCategory (category) {
		return this.req('post', '/category', {
			id: category.id,
			delete: 'delete'
		});
	}
}

/*const makeApiUrl = (url) => {
	return '/sbapi/' + url;
};

var findMode = (entity) => {
	return entity.id === 0 || entity.id ? 'update' : 'create';
}

class Api {
	static login (username, password) {
		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('login'), {
				username: username,
				password: password,
				device: getCurrentDevice()
			}, (data) => {
				console.log(data);
				localStorage.setItem('loginToken', data['token']['id']);

				resolve();
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static loginByToken () {
		var tokenId = localStorage.getItem('loginToken');

		return new Promise((resolve, reject) => {
			if (!tokenId) {
				reject('no-token');

				return;
			}

			$.post(makeApiUrl('login-by-token'), {
				tokenId: tokenId
			}, () => {
				resolve();
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static getBook (name) {
		return new Promise((resolve, reject) => {
			$.get(makeApiUrl('book?name=' + name), (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static saveCategory (category, book) {
		var obj = Object.assign({}, category);

		if (book) {
			obj.bookId = book.id;
		}

		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('category'), obj, (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static deleteCategory (category) {
		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('category'), {
				id: category.id,
				delete: 'yes'
			}, (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static saveTransaction (transaction, book) {
		var obj = Object.assign({}, transaction);

		if (book) {
			obj.bookId = book.id;
		}

		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('transaction'), obj, (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static deleteTransaction (transaction) {
		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('transaction'), {
				id: transaction.id,
				delete: 'yes'
			}, (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static tryReject (error, reject) {
		reject(error);
	}
}*/

export default Api;
