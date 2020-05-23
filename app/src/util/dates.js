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

	static isValidBank (d) {
		let comps = d.split('-');

		if (comps.length < 3) {
			return false;
		}

		let month = Dates.MONTHS_BANK.indexOf(comps[1]);

		return month >= 0 && Number.isInteger(parseInt(comps[0], 10), parseInt(comps[2], 10));
	}

	static parseBank (d) {
		let comps = d.split('-');

		if (comps.length < 3) {
			return null;
		}

		let month = (Dates.MONTHS_BANK.indexOf(comps[1]) + 1);

		if (month === 0) {
			throw 'Invalid bank date';
		}

		month = '' + month;

		if (month.length === 1) {
			month = '0' + month;
		}

		return comps[2] + '-' + month + '-' + comps[0];
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

		let index = parseInt(monthC, 10) - 1;

		return Dates.MONTHS[index] + (yearC.length > 0 ? ' ' : '') + yearC;
	}
};

Dates.FORMAT = 'YYYY-MM-DD';
Dates.MONTHS = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
Dates.MONTHS_BANK = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];

export default Dates;
