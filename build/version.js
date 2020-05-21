const sha256File = require('sha256-file');
const fs = require('fs');

const version = {
	bundle: 'app/dist/bundle.js',
	theme: 'app/dist/css/theme.css'
};

const index = 'app/dist/index.version.php';
const out = 'app/dist/index.php';

fs.readFile(index, 'utf8', (e, data) => {
	if (e) {
		console.log(e);

		return;
	}

	Object.keys(version).forEach((k) => {
		data = data.replace('$' + k + '.version', sha256File(version[k]));
	});

	fs.writeFile(out, data, 'utf8', (e) => {

	});
});
