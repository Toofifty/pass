<template>
	<form ref="form" action="#" @submit.prevent="submit" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">
				{{ isNew() ? 'Add new' : 'Edit' }} vault{{ isNew() ? '' : ': ' + vault.title }}
			</div>
			<div class="panel-body">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
					<br>
				</div>
				<div class="col-lg-10">
					<label for="title">Title</label>
					<input type="text" class="form-control" placeholder="Title" name="title" :value="title" required>
					<br>
					<div class="col-lg-12">
						<div class="form-group">
							<div class="input-group multi">
								<label for="parents">Parent Vaults</label>
								<multiselect :options="validParents" v-model="parents" label="title" :show-labels="false" :multiple="true">
									<template slot="option" slot-scope="props">
										{{ props.option.title }}
									</template>
								</multiselect>
							</div>
						</div>
					</div>
					<div class="form-group" v-for="parent in parents">
						<div class="col-md-6">
							<label for="permission"><span class="parent-name">{{ parent.title }}</span> Write Access</label>
							<multiselect :options="permissionsList" v-model="writePermissions[parent.title]" :show-labels="false" default="No one">
							</multiselect>
						</div>
						<div class="col-md-6">
							<label for="permission"><span class="parent-name">{{ parent.title }}</span> Read Access</label>
							<multiselect :options="permissionsList" v-model="readPermissions[parent.title]" :show-labels="false">
							</multiselect>
						</div>
					</div>
					<label for="notes">Notes</label>
					<textarea name="notes" class="form-control" rows="3" placeholder="Notes" :value="notes"></textarea>
				</div>
			</div>
			<div class="panel-footer">
				<div class="panel-btn-group left-btns">
					<button class="btn btn-secondary" @click="$emit('stopeditvault')">Cancel</span></button>
				</div>
				<div role="group" class="panel-btn-group right-btns">
					<button v-if="!isNew()" class="btn btn-danger" @click="deleteVault()">Delete<span class="glyphicon glyphicon-trash"></span></button>
					<button type="submit" class="btn btn-primary">{{ isNew() ? 'Add' : 'Update' }}<span v-if="!isNew()" class="glyphicon glyphicon-arrow-up"></span></button>
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
import Toast from './Util/Toast'

export default {
	components: {
		Multiselect,
		IconUpload,
		Toast
	},

	data () {
		return {
			parents: [],
			// TODO: will be replaced by user
			// selected permissions
			title: '',
			notes: '',
			permissionsList: [
				'No one',
				'Everyone',
				'All users',
				'All developers',
				'All administrators',
				'All owners'
			],
			writePermissions: [],
			readPermissions: [],
			defaultWritePermission: 0,
			defaultReadPermission: 1
		}
	},

	props: {

		vault: {
			type: Object
		},

		vaults: {
			type: Array
		}

	},

	mounted () {
		this.$watch('parents', (parents) => {
			parents.forEach((parent) => {
				if (!this.writePermissions[parent.title]) {
					this.writePermissions[parent.title] = this.permissionsList[this.defaultWritePermission]
				}
				if (!this.readPermissions[parent.title]) {
					this.readPermissions[parent.title] = this.permissionsList[this.defaultReadPermission]
				}
			})
			this.$forceUpdate()
		})
		if (this.vault) {
			this.title = this.vault.title
			this.notes = this.vault.notes
		}
		this.$watch('vault', (vault) => {
			if (vault) {
				this.title = vault.title
				this.notes = vault.notes
			}
		})
	},

	computed: {

		validParents () {
			if (this.vault) {
				return _(this.vaults).filter(v => v.id !== this.vault.id).value()
			} else {
				return this.vaults
			}
		}

	},

	methods: {

		isNew () {
			return this.vault === null || this.vault === undefined
		},

		submit () {
			let data = {}
			_(event.target.elements).filter(v => v.name !== '').forEach(v => {
				data[v.name] = v.value
			})
			data['parents'] = this.parents
			data['parents'].forEach((parent) => {
				parent['write_permission'] = this.writePermissions[parent.title]
				parent['read_permission'] = this.readPermissions[parent.title]
			})
			if (this.isNew()) {
				axios.post('api/store/vaults', data).then(() => {
					this.$refs['toast'].toast(
						'success',
						'<strong>Success!</strong> Vault created.',
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
			} else {
				axios.put('api/store/vaults/' + this.vault.id, data).then(() => {
					this.$refs['toast'].toast(
						'success',
						'<strong>Success!</strong> Vault updated.',
						5,
						true
					)
					this.$refs['form'].reset()
					this.$emit('vaultrefresh')
					this.$emit('stopeditvault')
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

		deleteVault () {
			axios.delete('api/store/vaults/' + this.vault.id).then(() => {
				this.$refs['toast'].toast(
					'success',
					'<strong>Success!</strong> Vault deleted.',
					5,
					true
				)
				this.$refs['form'].reset()
				this.$emit('vaultrefresh')
				this.$emit('stopeditvault')
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

.panel-btn-group {
	display: inline-block;
	&.right-btns {
		float: right;
	}
}

.input-group.multi {
	width: 100%;
}

.parent-name {
	color: $brand-primary;
}

.glyphicon-trash,
.glyphicon-arrow-up {
	margin-left: 10px;
}
</style>