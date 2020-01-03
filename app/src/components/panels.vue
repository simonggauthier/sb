<template>
	<div class="panels">
		<div class="title">
			<h1>SB</h1>
			<img class="center-h" src="img/logo_transparent.png" alt="" />
		</div>

		<div class="modules">
			<div class="module">
				<add-transaction :appModel="appModel"></add-transaction>
			</div>

			<div class="module">
				<transaction-list :appModel="appModel"></transaction-list>
			</div>

			<div class="module">
				<category-editor :appModel="appModel"></category-editor>
			</div>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

import AddTransaction from './modules/add-transaction.vue';
import TransactionList from './modules/transaction-list.vue';
import CategoryEditor from './modules/category-editor.vue';

export default {
	data () {
		return {

		}
	},

	props: ['appModel'],

	mounted () {

	},

	methods: {

	},

	components: {
		AddTransaction,
		TransactionList,
		CategoryEditor
	}
}
</script>

<style>

.panels {

}

.panels > .title {
	width: 100%;
	padding: 10px 0 10px 0;
	text-align: center;
}

.panels > .title img {
	width: 20%;
}

.panels .modules {
	padding: 20px 10px 10px 10px;
}

.panels .module {
	margin-bottom: 20px;
}

.panels .module .title {
	font-weight: bold;
	font-size: 1.3em;
	margin-bottom: 5px;
}

@media only screen and (max-width: 600px) {
	.panels > .title img {
		width: 40%;
	}
}

</style>
