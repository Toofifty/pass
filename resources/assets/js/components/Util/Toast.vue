<template>
	<div ref="toast" :class="'alert-' + type + (active ? ' active' : '')" class="alert alert-dismissable fade in" role="alert">
		<button type="button" class="close" @click="close()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<span v-html="message"></span>
	</div>
</template>

<script>
export default {
	data () {
		return {
			type: 'success',
			message: '',
			dismissable: true,
			active: false
		}
	},

	methods: {

		toast (type, message, time, dismissable) {
			this.type = type
			this.message = message
			if (dismissable !== undefined) {
				this.dismissable = dismissable
			}
			this.active = true
			if (time !== undefined) {
				clearTimeout(this.close)
				setTimeout(this.close, time * 1000)
			}
		},

		close () {
			this.active = false
		}

	}
}
</script>

<style lang="scss" scoped>
.alert {
	opacity: 0;
	transform: translate(-50%, -100%);
	transition: 1s;
	position: absolute;
	left: 50%;
	top: 10px;
	z-index: 1100;
	&.active {
		transform: translateX(-50%);
		opacity: 1;
		transition: 0.25s;
	}
}
</style>