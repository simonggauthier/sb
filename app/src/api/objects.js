import $ from 'jquery'

var baseUrl = '/sbapi/';

var Objects = {
	login: (username, password) => {
		var ret = new Promise((resolve, reject) => {
			$.post(baseUrl + 'login', {username: 'simon', password: 'hello'}, (data) => {
				resolve(data);
			}).fail((e) => {
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
