<template>
	<div class="module transaction-list">
		<div class="title">
			Transactions
		</div>

		<div class="transactions">
			<table>
				<thead>
					<tr>
						<th>Date</th>
						<th>Title</th>
						<th>Category</th>
						<th>Amount</th>
					</tr>
				</thead>

				<tbody>
					<tr v-for="transaction in model.book.transactions">
						<td>{{Formatting.date(transaction.date)}}</td>
						<td>{{transaction.title}}</td>
						<td>{{transaction.category}}</td>
						<td>{{Formatting.money(transaction.amount)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import Formatting from '../../formatting.js';

export default {
	data () {
		return {
			Formatting: Formatting
		}
	},

	props: ['model'],

	mounted: function () {
		console.log(this.model.book);
	},

	methods: {

	},

	components: {

	}
}
</script>

<style>

</style>
