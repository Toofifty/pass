<template>
	<div class="upload-container">
		<label for="icon">Icon</label>
		<div class="dropbox" @click="$refs['icon'].click()">
			<!-- TODO: default image per login type -->
			<p v-if="image === null">Upload an icon</p>
			<img ref="preview" v-if="image !== null" :src="image" height="100" width="100">
		</div>
		<input ref="icon" type="file" name="icon" accept="image/*" class="input-file" @change="updatePreview()">
	</div>
</template>

<script>
export default {
	data () {
		return {
			image: null
		}
	},

	methods: {

		updatePreview () {
			let reader = new FileReader()
			reader.onloadend = () => {
				this.image = reader.result
			}

			let file = this.$refs['icon'].files[0]
			if (file) {
				reader.readAsDataURL(file)
			} else {
				this.image = null
			}
		}

	}
}
</script>

<style lang="scss" scoped>
.dropbox {
	border: 1px solid #d3e0e9;
	border-radius: 5px;
	outline-offset: -5px;
	background: #f5f8fa;
	height: 100px;
	width: 100px;
	max-height: 100px;
	max-width: 100px;
	cursor: pointer;
	position: relative;
	overflow: hidden;
	transition: 0.25s;
	text-align: center;

	&:before {
		content: '+';
		color: white;
		font-size: 4em;
		font-weight: 700;
		background: #f5f8fa;
		text-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
		width: 100%;
		height: 100%;
		position: absolute;
		left: 0;
		top: 0;
		opacity: 0;
		transition: 0.25s;
		pointer-events: none;
	}

	&:hover {
		color: rgba(0, 0, 0, 0.3);
		&:before {
			opacity: 1;
		}
	}

	p {
		text-align: center;
		margin: 25px 10px;
	}
}

.input-file {
	opacity: 0;
	width: 100px;
	height: 100px;
	position: absolute;
	cursor: pointer;
}
</style>