class Formatting {
	static money (m) {
		return new Intl.NumberFormat('fr-CA', { style: 'currency', currency: 'CAD' }).format(m);
	}

	static moneyDigits (m) {
		if (m.indexOf('.') < 0) {
			m = m + '.00';
		}

		if (m.indexOf('.') === 0) {
			m = '0' + m;
		}

		return m;
	}
}

export default Formatting;
