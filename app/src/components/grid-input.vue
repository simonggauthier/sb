<template>
	<div class="grid-input">
		<input ref="input" type="text" :value="value" v-on:input="onInput" v-on:blur="onBlur" v-on:keyup.enter="onEnter" />
	</div>
</template>

<script>
export default {
	props: ['value'],

	methods: {
		onInput (e) {
			this.$emit('input', e.target.value);
		},

		onBlur (e) {
			this.$emit('blur', e.target.value);
		},

		onEnter (e) {
			this.$refs.input.blur();
		}
	},

	mounted () {
		this.$refs.input.focus();
		this.$refs.input.setSelectionRange(0, this.value.length)
	}
}
</script>

<style>

.grid-input {
	width: 100%;
}

.grid-input input {
	width: 100%;
	border: 0;
	outline: none;
	text-align: center;
	background: transparent;
}

</style>
