import Api from 'api/api';

import { Book } from 'api/model/book'

class Objects {
	constructor() {
		this.book = null;
	}

	async load (api, loadingInfo) {
		loadingInfo.message = 'Chargement du budget';

		let data = await api.getBook('budget');

		loadingInfo.message = 'Construction du budget';

		this.book = Book.fromData(data);
	}
}

export default Objects;
