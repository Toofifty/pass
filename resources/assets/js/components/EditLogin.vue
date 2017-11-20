<template>
	<form ref="form" action="#" @submit.prevent="submit" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">
				{{ panelTitle }} entry{{ !isNew() ? ': ' + title : '' }}
			</div>
			<div class="panel-body">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
					<br>
				</div>
				<div class="col-lg-10">
					<label v-if="isNew() || edit" for="title">Title</label>
					<div v-if="isNew() || edit" class="input-group">
						<input type="text" class="form-control" placeholder="Title" name="title" :value="title" required>
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
					<div v-if="isNew() || edit" class="form-group">
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
					<div v-if="!isNew() && !edit" class="">
						
					</div>
					<component :is="loginType" :edit="edit || isNew()" :login="login"></component>
				</div>
			</div>
			<div class="panel-footer">
				<div class="panel-btn-group left-btns">
					<button v-if="!isNew() && !edit" class="btn btn-secondary" @click.prevent="$emit('stopviewlogin')">Cancel</span></button>
					<button v-if="edit" class="btn btn-secondary" @click.prevent="$emit('viewlogin', login, false)">Cancel</span></button>
				</div>
				<div role="group" class="panel-btn-group right-btns">
					<button v-if="edit" class="btn btn-danger" @click="deleteLogin()">Delete<span class="glyphicon glyphicon-trash"></span></button>
					<button v-if="isNew() || edit" type="submit" class="btn btn-primary">{{ isNew() ? 'Add' : 'Update' }}<span v-if="!isNew()" class="glyphicon glyphicon-arrow-up"></span></button>
					<button v-if="!isNew() && !edit" class="btn btn-primary" @click.prevent="$emit('viewlogin', login, true)">Edit<span v-if="!isNew()" class="glyphicon glyphicon-pencil"></span></button>
				</div>
				<div class="clear-both"></div>
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
			vault: null,
			title: ''
		}
	},

	props: {

		vaults: {
			type: Array
		},

		login: {
			type: Object
		},

		edit: {
			type: Boolean
		}

	},

	mounted () {
		this.$watch('login', (login) => {
			if (login) {
				this.title = login.title
				// this.vault = login.
			} else {
				this.title = ''
				this.vault = null
			}
		})
		if (this.login) {
			this.title = this.login.title
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
		},

		panelTitle () {
			return this.isNew() ? 'Add new' : this.edit ? 'Edit' : 'View'
		}

	},

	methods: {

		isNew () {
			return this.login === null || this.login === undefined
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
		},

		deleteLogin () {
			axios.delete('api/store/' + this.loginType.toLowerCase() + 's/' + this.login.id).then(() => {
				this.$refs['toast'].toast(
					'success',
					'<strong>Success!</strong> ' + this.nicefy(this.loginType) + ' deleted.',
					5,
					true
				)
				this.$refs['form'].reset()
				this.$emit('vaultrefresh')
				this.$emit('stopviewlogin')
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
</script>

<style lang="scss">
@import '~@/_variables.scss';

.clear-both {
	clear: both;
}

.panel-btn-group {
	display: inline-block;
	&.right-btns {
		float: right;
	}
}

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

.input-group.multi {
	width: 100%;
}

.glyphicon-trash,
.glyphicon-pencil,
.glyphicon-arrow-up {
	margin-left: 10px;
}
</style>