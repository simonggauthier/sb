import { formatDate } from './date.js';

var Formatting = {
	date: (d) => {
		return formatDate(d, 'yyyy-MM-dd');
	},

	money: (m) => {
		return new Intl.NumberFormat('fr-CA', { style: 'currency', currency: 'CAD' }).format(m);
	},

	moneyDigits: (m) => {
		if (m.indexOf('.') < 0) {
			m = m + '.00';
		}

		return m;
	}
};

export default Formatting;
