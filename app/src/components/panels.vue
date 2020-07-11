<template>
	<div class="panels">
		<modal v-if="modal.mission" :mission="modal.mission" @close="onModalClose"></modal>

		<div class="title">
			<h1>SB</h1>
			<img src="img/logo.png" alt />
		</div>

		<div class="modules">
			<div class="module">
				<add-transaction-batch :api="api" :objects="objects" @requestModal="onRequestModal"></add-transaction-batch>
			</div>

			<div class="module">
				<transaction-list :api="api" :objects="objects" @requestModal="onRequestModal"></transaction-list>
			</div>

			<div class="module">
				<add-transaction :api="api" :objects="objects"></add-transaction>
			</div>

			<div class="module">
				<reports :objects="objects"></reports>
			</div>

			<div class="module">
				<category-editor :api="api" :objects="objects" @requestModal="onRequestModal"></category-editor>
			</div>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';
import Modal from 'components/modal';
import AddTransaction from 'components/modules/add-transaction';
import AddTransactionBatch from 'components/modules/add-transaction-batch';
import Reports from 'components/modules/reports';
import TransactionList from 'components/modules/transaction-list';
import CategoryEditor from 'components/modules/category-editor';

export default {
	data () {
		return {
			modal: {
				mission: null
			}
		}
	},

	props: ['api', 'objects'],

	methods: {
		onModalClose () {
			this.modal.mission = null;

			this.$forceUpdate();
		},

		onRequestModal (mission) {
			this.modal.mission = mission;
		}
	},

	components: {
		Modal,
		AddTransaction,
		AddTransactionBatch,
		Reports,
		TransactionList,
		CategoryEditor
	}
}
</script>

<style>
.panels > .title {
	width: 100%;
	padding: 10px 0 10px 0;
	text-align: center;
}

.panels > .title img {
	margin: 0 auto 0 auto;
	padding-left: 20px;
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
