import $ from 'jquery'
import MobileDetect from 'mobile-detect';

var baseUrl = '/sbapi/';

var getDevice = function () {
	var md = new MobileDetect(window.navigator.userAgent);

	if (md.mobile()) {
		return 'mobile';
	}

	return 'desktop';
};

var Objects = {
	login: (username, password) => {
		var ret = new Promise((resolve, reject) => {
			$.post(baseUrl + 'login', {username: username, password: password, device: getDevice()}, (data) => {
				data = JSON.parse(data);

				localStorage.setItem('loginToken', data['id']);

				resolve();
			}).fail((e) => {
				reject(JSON.parse(e.responseText));
			});
		});

		return ret;
	},

	loginByToken: () => {
		var tokenId = localStorage.getItem('loginToken');

		var ret = new Promise((resolve, reject) => {
			if (!tokenId) {
				reject('no-token');
			}

			$.post(baseUrl + 'login-by-token', {tokenId: tokenId}, (data) => {
				resolve();
			}).fail((e) => {
				localStorage.removeItem('loginToken');

				reject(JSON.parse(e.responseText));
			});
		});

		return ret;
	},

	get: (id) => {
		var ret = new Promise((resolve, reject) => {
			$.get(baseUrl + id, (data) => {
				resolve(data);
			}).fail((e) => {
				reject(JSON.parse(e.responseText));
			});
		});

		return ret;
	},

	set: (id, value) => {
		var ret = new Promise((resolve, reject) => {
			$.post(baseUrl + id, {value: value}, (data) => {
				resolve(data);
			}).fail((e) => {
				reject(JSON.parse(e.responseText));
			});
		});

		return ret;
	}
};

export default Objects;
