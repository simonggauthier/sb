
function uuidv4() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	});
}

class Transaction {
	constructor () {
		this.id = uuidv4();
		this.title = '';
		this.category = null;
		this.amount = '0';
		this.date = new Date();
	}
}

export default {
	Transaction
}
