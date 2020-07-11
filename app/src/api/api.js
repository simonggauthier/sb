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

	setContextCategory (contextCategory) {
		return this.req('post', '/set-context-category', contextCategory);
	}
}

export default Api;
