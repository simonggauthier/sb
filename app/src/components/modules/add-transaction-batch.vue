<template>
	<div class="module add-transaction">
		<h2>Ajouter Une Liste De Transaction</h2>

		<label>Direction</label>

		<switcher :list="transactionDirectionSwitcherList" v-model="form.direction"></switcher>
	</div>
</template>

<script>
import Switcher from 'components/switcher';

export default {
	data () {
		return {
			form: {
				direction: 'output'
			},

			transactionDirectionSwitcherList: {
				'input': {
					name: 'Entrée'
				},

				'output': {
					name: 'Sortie'
				}
			}
		}
	},

	props: ['api', 'objects'],

	methods: {

	},

	components: {
		Switcher
	}
}
</script>

<style>
</style>
