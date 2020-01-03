import { LocalDateTime, DateTimeFormatter, Instant } from '@js-joda/core';
import { Locale } from '@js-joda/locale_fr';

var toLocalDateTime = (ts) => {
	return LocalDateTime.ofInstant(Instant.ofEpochMilli(ts));
};

var formatDate = (ts, pattern) => {
	return toLocalDateTime(ts).format(DateTimeFormatter.ofPattern(pattern).withLocale(Locale.FRANCE));
};

var toInt = (str) => {
	var ret = parseInt(str, 10);

	if (isNaN(ret)) {
		throw 'Invalid int';
	}

	return ret;
};

var parseDate = (str) => {
	if (!str) {
		throw 'Invalid string';
	}

	var comps = str.split('-');

	if (comps.length != 3) {
		throw 'Invalid string';
	}

	var now = new Date();

	return new Date(toInt(comps[0]), toInt(comps[1]) - 1, toInt(comps[2]), now.getHours(), now.getMinutes(), now.getSeconds()).getTime();
};

export {
	formatDate,
	parseDate
};
