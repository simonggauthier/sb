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

class Api {
	static login (username, password) {
		return new Promise((resolve, reject) => {
			$.post(makeApiUrl('login'), {
				username: username, 
				password: password, 
				device: getCurrentDevice()
			}, (data) => {
				localStorage.setItem('loginToken', data['id']);

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
			}, (data) => {
				resolve();
			}).fail((e) => {
				localStorage.removeItem('loginToken');

				Api.tryReject(e, reject);
			});
		});
	};

	static getObject (id) {
		return new Promise((resolve, reject) => {
			$.get(makeApiUrl(id), (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static setObject (id, value) {
		return new Promise((resolve, reject) => {
			$.post(makeApiUrl(id), {
				'value': JSON.stringify(value)
			}, (data) => {
				resolve(data);
			}).fail((e) => {
				Api.tryReject(e, reject);
			});
		});
	}

	static tryReject (e, reject) {
		var r;

		try {
			r = JSON.parse(e.responseText);
		} catch (e) {
			r = e;
		}

		reject(json);
	}
};

export default Api;
