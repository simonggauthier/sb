<template>
	<div class="panels">
		<div class="title">
			<h1>SB</h1>
			<img src="img/logo_transparent.png" alt="" />
		</div>

		<div class="modules">
			<div class="module">
				<add-transaction :objects="objects"></add-transaction>
			</div>

			<div class="module">
				<transaction-list :objects="objects"></transaction-list>
			</div>

			<div class="module">
				<category-editor :objects="objects"></category-editor>
			</div>
		</div>
	</div>
</template>

<script>
import AddTransaction from 'components/modules/add-transaction';
import TransactionList from 'components/modules/transaction-list';
import CategoryEditor from 'components/modules/category-editor';

export default {
	data () {
		return {

		}
	},

	props: ['objects'],

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
	margin: 0 auto 0 auto;
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
