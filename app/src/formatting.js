import { LocalDateTime, DateTimeFormatter, Instant } from '@js-joda/core';

var Formatting = {
	date: (d) => {
		var t = LocalDateTime.ofInstant(Instant.ofEpochMilli(d));

		return t.format(DateTimeFormatter.ofPattern('yyyy-MM-dd'));
	},

	money: (m) => {
		return new Intl.NumberFormat('fr-CA', { style: 'currency', currency: 'CAD' }).format(m);
	}
};

export default Formatting;
