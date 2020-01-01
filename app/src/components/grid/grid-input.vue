<template>
	<div class="grid-input">
		<input ref="input" type="text" :value="value" v-on:input="onInput" v-on:blur="onBlur" v-on:keydown="onKeydown" />
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

		onKeydown (e) {
			switch(e.keyCode)
			{
				// Enter
				case 13:
					this.$refs.input.blur();
					break;
				// Left
				case 37:
					this.$emit('left');
					this.$refs.input.blur();
					break;
				// Right
				case 37:
					this.$emit('right');
					this.$refs.input.blur();
					break;
			}
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
