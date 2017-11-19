<template>
	<form ref="form" action="#" @submit.prevent="submit" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">
				{{ isNew() ? 'Add new' : 'Edit' }} vault
			</div>
			<div class="panel-body">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
					<br>
				</div>
				<div class="col-lg-10">
					<label for="title">Title</label>
					<input type="text" class="form-control" placeholder="Title" name="title" required>
					<br>
					<div class="col-lg-12">
						<div class="form-group">
							<div class="input-group multi">
								<label for="parents">Parent Vaults</label>
								<multiselect :options="vaults" v-model="parents" label="title" :show-labels="false" :multiple="true">
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
					<textarea name="notes" class="form-control" rows="3" placeholder="Notes"></textarea>
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

		vaultId: {
			type: Number
		},

		vaults: {
			type: Array
		}

	},

	mounted () {
		this.$watch('parents', (parents) => {
			console.log(parents)
			parents.forEach((parent) => {
				if (!this.writePermissions[parent.title]) {
					this.writePermissions[parent.title] = this.permissionsList[this.defaultWritePermission]
				}
				if (!this.readPermissions[parent.title]) {
					this.readPermissions[parent.title] = this.permissionsList[this.defaultReadPermission]
				}
			})
			this.$forceUpdate()
		});
	},

	methods: {

		isNew () {
			return this.loginId === undefined || this.loginId < 0
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
			}
		}

	}

}
</script>

<style lang="scss">
@import '~@/_variables.scss';

.action-btns {
	text-align: right;
}

.input-group.multi {
	width: 100%;
}

.parent-name {
	color: $brand-primary;
}
</style>