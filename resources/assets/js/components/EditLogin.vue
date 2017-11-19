<template>
	<form ref="form" action="#" @submit.prevent="submit" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">
				{{ isNew() ? 'Add new' : 'Edit' }} entry
			</div>
			<div class="panel-body">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
					<br>
				</div>
				<div class="col-lg-10">
					<label for="title">Title</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Title" name="title" required>
						<div class="input-group-btn type-select">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="glyphicon" :class="'glyphicon-' + loginIcons[typeId]"></span>
								<span class="login-type">{{ nicefy(loginType) }}</span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li v-for="(type, index) in nicefyTypes()">
									<a href="#" @click="selectType(index)">
										<span class="glyphicon" :class="'glyphicon-' + loginIcons[index]"></span>
										{{ type }}
									</a>
								</li>
							</ul>
						</div>
					</div>
					<br>
					<div class="form-group">
						<div :class="{ 'col-lg-6': newVaultSelected, 'col-lg-12': !newVaultSelected }">
							<div class="input-group multi">
								<label for="vault">Vault</label>
								<multiselect :options="vaultList" v-model="vault" label="title" :show-labels="false" :multiple="true">
									<template slot="option" slot-scope="props">
										{{ props.option.title }}
									</template>
								</multiselect>
							</div>
						</div>
						<div v-if="newVaultSelected" class="col-lg-6">
							<label for="vault-title">New Vault Title</label>
							<input type="text" class="form-control" placeholder="Vault Title" name="vault-title" required>
						</div>
					</div>
					<component :is="loginType"></component>
				</div>
			</div>
			<div class="panel-footer">
				<div role="group" class="action-btns">
					<button v-if="!isNew()" class="btn btn-danger" @click="deleteLogin()"><span class="glyphicon glyphicon-trash"></span></button>
					<button type="submit" class="btn btn-primary">{{ isNew() ? 'Add' : 'Update' }}</button>
				</div>
			</div>
			<toast ref="toast"></toast>
		</div>
	</form>
</template>

<script>
import _ from 'lodash'
import Multiselect from 'vue-multiselect'

import IconUpload from './IconUpload'
import WebsiteLogin from './Edit/WebsiteLogin'
import Note from './Edit/Note'
import Toast from './Util/Toast'

export default {
	components: {
		IconUpload,
		WebsiteLogin,
		Note,
		Toast,
		Multiselect
	},

	data () {
		return {
			loginTypes: [
				'WebsiteLogin',
				'Note'
			],
			loginIcons: [
				'globe',
				'book'
			],
			typeId: 0,
			vault: null
		}
	},

	props: {

		loginId: {
			type: Number
		},

		vaults: {
			type: Array
		}

	},

	computed: {

		loginType () {
			return this.loginTypes[this.typeId]
		},

		vaultList () {
			return [{
				title: 'Create new',
				id: -1,
				icon: ''
			}].concat(this.vaults)
		},

		newVaultSelected () {
			return _(this.vault).find(v => v.title === 'Create new')
		}

	},

	methods: {

		isNew () {
			return this.loginId === undefined || this.loginId < 0
		},

		nicefy (str) {
			return str
				.replace(/([a-z])([A-Z])/g, '$1 $2')
  				.replace(/([A-Z])([A-Z][a-z])/g, '$1 $2')
		},

		nicefyTypes () {
			return this.loginTypes.map((str) => this.nicefy(str))
		},

		selectType (index) {
			this.typeId = index
		},

		submit (event) {
			let data = {}
			_(event.target.elements).filter(v => v.name !== '').forEach(v => {
				data[v.name] = v.value
			})
			data['vault'] = this.vault
			if (this.isNew()) {
				axios.post('api/store/' + this.loginType.toLowerCase() + 's', data).then(() => {
					this.$refs['toast'].toast(
						'success',
						'<strong>Success!</strong> ' + this.nicefy(this.loginType) + ' created.',
						5,
						true
					)
					this.$refs['form'].reset()
					this.$emit('vaultrefresh')
				}).catch((err) => {
					this.$refs['toast'].toast(
						'danger',
						'<strong>Error:</strong> ' + err.toString().replace(/Error:/, ''),
						5,
						true
					)
				})
			}
		}

	}
}
</script>

<style lang="scss">
@import '~@/_variables.scss';

.dropdown-menu > li > a {
	padding: 3px 0px 3px 12px;
	width: auto;
}

.type-select {
	.glyphicon,
	.login-type {
		margin-right: 10px;
	}
}

.action-btns {
	text-align: right;
}

.input-group.multi {
	width: 100%;
}
</style>