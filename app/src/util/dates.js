import { LocalDateTime, DateTimeFormatter, Instant } from '@js-joda/core';
import { Locale } from '@js-joda/locale_fr';

class Dates {
	static toLocalDateTime (ts)  {
		return LocalDateTime.ofInstant(Instant.ofEpochMilli(ts));
	}

	static format (ts, pattern) {
		return Dates.toLocalDateTime(ts).format(DateTimeFormatter.ofPattern(pattern).withLocale(Locale.FRANCE));
	}

	static getMonth (ts) {
		return Dates.format(ts, 'yyyy-MM');
	}

	static getMonthName (ts) {
		var ret =  Dates.format(ts, 'MMMM yyyy');

		return ret.substring(0, 1).toUpperCase() + ret.substring(1);
	}

	static parse (str) {
		var toInt = (str) => {
			var ret = parseInt(str, 10);

			if (isNaN(ret)) {
				throw 'Invalid int';
			}

			return ret;
		};

		if (!str) {
			throw 'Invalid string';
		}

		var comps = str.split('-');

		if (comps.length != 3) {
			throw 'Invalid string';
		}

		var now = new Date();

		return new Date(toInt(comps[0]), toInt(comps[1]) - 1, toInt(comps[2]), now.getHours(), now.getMinutes(), now.getSeconds()).getTime();
	}
};

export default Dates;
