<template>
	<div class="modal">
		<div class="mask"></div>

		<div class="dialog">
			<div class="title">{{ mission.title }}</div>

			<div class="body">
				<div class="field" v-for="(field, key) in mission.model" :key="key">
					<div class="input-string" v-if="field.type === 'string'">
						<input
							type="text"
							v-model="mission.target[key]"
							@keydown.enter="onClose('ok')"
							:placeholder="'> ' + field.label"
							:aria-label="field.label"
						/>
					</div>

					<div class="input-color" v-if="field.type === 'color'">
						<input
							type="color"
							v-model="mission.target[key]"
							:placeholder="'> ' + field.label"
							:aria-label="field.label"
						/>
					</div>

					<div class="input-string" v-if="field.type === 'date'">
						<input
							type="text"
							v-model="mission.target[key]"
							@keydown.enter="onClose('ok')"
							:placeholder="'> ' + field.label"
							:aria-label="field.label"
						/>
					</div>

					<div class="input-string" v-if="field.type === 'money'">
						<input
							type="number"
							min="0.01"
							step="0.01"
							:placeholder="'> ' + field.label"
							v-model="mission.target[key]"
							@change="onMoneyChange(mission.target, key)"
							@keydown.enter="onClose('ok')"
							:aria-label="field.label"
						/>
					</div>

					<div class="input-switcher" v-if="field.type === 'switcher'">
						<label>{{ field.label }}</label>
						<switcher :list="field.list" v-model="mission.target[key]"></switcher>
					</div>

					<div class="input-list" v-if="field.type === 'list'">
						<select
							v-bind:class="{ placeholder: mission.target[key] === '_' }"
							v-model="mission.target[key]"
							:aria-label="field.label"
						>
							<option value="_">> {{ field.label }}</option>

							<option
								v-for="entry in field.getList()"
								:value="entry.id"
								:key="entry.id"
							>{{ field.render(entry) }}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="controls">
				<button @click="onClose('ok')">{{ mission.okLabel }}</button>
				<button v-if="mission.canDelete" @click="onClose('delete')">Supprimer</button>
				<button v-if="mission.canClose" @click="onClose('close')">Fermer</button>
			</div>
		</div>
	</div>
</template>

<script>
import Formatting from 'util/formatting';

import Switcher from 'components/switcher';

export default {
	data () {
		return {

		};
	},

	props: ['mission'],

	mounted () {

	},

	methods: {
		onClose (action) {
			this.$emit('close');

			if (this.mission.onClose) {
				this.mission.onClose(action);
			}
		},

		onMoneyChange (field, key) {
			field[key] = Formatting.moneyDigits(field[key]);
		}
	},

	components: {
		Switcher
	}
}
</script>

<style>
.modal {
	position: fixed;
	z-index: 100;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
}

.modal .mask {
	width: 100%;
	height: 100%;
	background-color: #000;
	opacity: 0.7;
}

.modal .dialog {
	position: absolute;
	z-index: 200;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	padding: 20px;
}

.modal .title {
	margin-bottom: 10px;
	font-weight: bold;
	font-size: 0.9em;
	border-bottom-style: solid;
	border-bottom-width: 4px;
}

.modal .controls button {
	font-size: 0.7em;
	float: right;
	margin-left: 10px;
}
</style>
