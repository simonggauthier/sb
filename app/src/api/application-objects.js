import Api from 'api/api';
import { Book } from 'api/model/book';

class ApplicationObjects {
	constructor () {
		this.loaded = false;

		this.book = null;
	}

	load () {
		var t = this;
		var requests = [];

		requests.push(new Promise((resolve, reject) => {
			Api.getObject('book').then((book) => {
				t.book = Book.fromJson(book).book;

				resolve();
			}).catch((e) => {
				reject(e);
			});
		}));

		return new Promise((resolve, reject) => {
			Promise.all(requests).then(() => {
				t.loaded = true;

				resolve();
			}).catch((e) => {
				reject(e);
			});
		});
	}

	save () {
		var t = this;
		var requests = [];

		requests.push(new Promise((resolve, reject) => {
			Api.setObject('book', t.book).then(() => {
				resolve();
			}).catch((e) => {
				reject(e);
			});
		}));

		return Promise.all(requests);
	}
};


export default ApplicationObjects;
