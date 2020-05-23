/**
 * Official date format: 2020-05-23
 * 						 YYYY-MM-DD
 */

import moment from 'moment';

class Dates {
	static inst (d) {
		return moment(d, Dates.FORMAT);
	}

	static compare (d1, d2) {
		return this.inst(d1) - this.inst(d2);
	}

	static getMonth (d) {
		let ret = '' + (this.inst(d).month() + 1);

		if (ret.length === 1) {
			ret = '0' + ret;
		}

		return ret;
	}

	static getYear (d) {
		return this.inst(d).year();
	}

	static getYearAndMonth (d) {
		return this.getYear(d) + '-' + this.getMonth(d);
	}

	static isValid (d) {
		return this.inst(d).isValid();
	}

	static now () {
		return moment().format(Dates.FORMAT);
	}

	static getMonthName (month) {
		let yearC = '';
		let monthC = month;

		if (month.length === 'YYYY-MM'.length) {
			yearC = month.substring(0, 4);
			monthC = month.substring(5);
		}

		let months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
		let index = parseInt(monthC, 10) - 1;

		return months[index] + (yearC.length > 0 ? ' ' : '') + yearC;
	}
};

Dates.FORMAT = 'YYYY-MM-DD';

export default Dates;
