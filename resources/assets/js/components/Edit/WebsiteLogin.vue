<template>
	<div class="form-group">
		<div class="col-lg-6">
			<label for="url">URL</label>
			<input v-if="edit" name="url" class="form-control" rows="3" placeholder="URL" @keyup="updateDomain()" v-model="url">
			<div v-if="!edit" class="field">{{ login ? login.url : '' }}</div>
			<br>
			<label for="username">Username</label>
			<div v-if="!edit" class="field">{{ login ? login.username : '' }}</div>
			<input v-if="edit" name="username" class="form-control" rows="3" placeholder="Username">
		</div>
		<div class="col-lg-6">
			<label for="domain">Domain</label>
			<div v-if="!edit" class="field">{{ login ? login.domain : '' }}</div>
			<input v-if="edit" data-toggle="tooltip" data-placement="bottom" title="Easily categorize and autofill passwords with matching domain names" name="domain" class="form-control full-width" rows="3" placeholder="Domain" v-model="domain" @change="hasChangedDomain = true">
			<br>
			<label for="password">Password</label>
			<div v-if="!edit" class="field">{{ login ? password : '' }}</div>
			<div v-if="edit" class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="This field will be stored securely"><span class="glyphicon glyphicon-lock"></span></span>
				<input name="password" class="form-control" rows="3" placeholder="Username">
			</div>
		</div>
		<div class="col-lg-12">
			<br>
			<label for="notes">Notes</label>
			<div v-if="edit" class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="This field will be stored securely"><span class="glyphicon glyphicon-lock"></span></span>
				<textarea name="notes" class="form-control" rows="3" placeholder="Notes"></textarea>
			</div>
			<div v-if="!edit" class="field">{{ login ? notes : '' }}</div>
		</div>
	</div>
</template>

<script>
export default {
	data () {
		return {
			url: '',
			domain: '',
			hasChangedDomain: false
		}
	},

	props: {

		edit: {
			type: Boolean
		},

		login: {
			type: Object
		}

	},

	computed: {

		password () {
			if (this.login.decrypted_password || this.login.decrypted_password === '') {
				return this.login.decrypted_password
			}
			return 'Decrypting...'
		},

		notes () {
			if (this.login.decrypted_notes || this.login.decrypted_notes === '') {
				return this.login.decrypted_notes
			}
			return 'Decrypting...'
		}

	},

	mounted () {
		$('[data-toggle="tooltip"]').tooltip()
	},

	methods: {

		updateDomain () {
			if (!this.hasChangedDomain) {
				let domain = this.url
					.replace(/http(?:s).{3}/g, '')
					.replace(/\/.*/, '')

				this.domain = domain
			}
		}

	}
}
</script>