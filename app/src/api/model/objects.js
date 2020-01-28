import Api from 'api/api';

import { Book } from 'api/model/book'

class Objects {
	constructor () {
		this.book = null;
	}

	load () {
		var t = this;

		return new Promise((resolve, reject) => {
			Api.getBook('Budget').then((data) => {
				t.book = Book.fromJson(data);

				resolve();
			}).catch((e) => {
				reject(e);
			});
		});
	}

	saveCategory (category) {

	}
}

export default Objects;
