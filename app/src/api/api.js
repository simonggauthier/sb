import $ from 'jquery'
import MobileDetect from 'mobile-detect';

const makeApiUrl = (url) => {
	return '/sbapi/' + url;
};

const getCurrentDevice = () => {
	var md = new MobileDetect(window.navigator.userAgent);

	if (md.mobile()) {
		return 'mobile';
	}

	return 'desktop';
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
}

export default Api;
