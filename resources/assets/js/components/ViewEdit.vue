<!--
	Vue component for both viewing and editing all login types
	and vaults.

	Component stencils are stored in Stencils.js.
-->
<template>
	<form ref="form" @submit.prevent="submit" autocomplete="false">
		<div class="panel panel-default">
			<div class="panel-heading" @click="editing = false">
				{{ panelTitle }} entry
			</div>
			<div v-if="stencil" class="panel-body">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
				</div>
				<div class="col-lg-10 form-group">
					<label for="title">Title</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Title" name="title" v-model="data['title']" required>
						<div class="input-group-btn type-select">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="glyphicon" :class="'glyphicon-' + stencil.icon"></span>
								<span class="login-type">{{ stencil.title }}</span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li v-bind:key="index" v-for="(type, index) in allTypes">
									<a href="#" @click="selectType(type.type)">
										<span class="glyphicon" :class="'glyphicon-' + type.icon"></span>
										{{ type.title }}
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div v-if="stencil.vault" :class="'col-lg-' + (stencil.vault.size || 12)">
					<VaultEntry
						v-model="data['vaults']"
						:vaults="vaults"
						:title="stencil.vault.title"
						:filterSelf="stencil.vault.filterSelf"
						:createNew="stencil.vault.createNew"
						:edit="isEditMode"
					></VaultEntry>
				</div>
				<div v-bind:key="fieldName" v-for="(field, fieldName) in stencil.fields" :class="'col-lg-' + (field.size || 12)">
					<component
						v-model="data[fieldName]"
						:is="field.component"
						:edit="isEditMode"
						:name="fieldName"
						:title="field.title"
						:tooltip="field.tooltip"
						:encrypted="field.encrypted"
						:height="field.height"
					></component>
				</div>
			</div>
			<div class="panel-footer">
				<div v-if="isViewing" role="group" class="panel-btn-group right-btns">
					<button class="btn btn-secondary" @click.prevent="debug">Share</button>
					<button class="btn btn-primary" @click.prevent="editing = !editing">Edit</button>
				</div>
				<div v-if="isEditing" role="group" class="panel-btn-group right-btns">
					<button class="btn btn-danger" @click.prevent="debug">Debug</button>
					<button class="btn btn-primary">Update</button>
				</div>
				<div v-if="isNew && isEditMode" role="group" class="panel-btn-group right-btns">
					<button class="btn btn-danger" @click.prevent="debug">Debug</button>
					<button class="btn btn-primary">Create</button>
				</div>
				<div class="clear-both"></div>
			</div>
			<toast ref="toast"></toast>
		</div>
	</form>
</template>

<script>
import Stencil from './Edit/Stencil'

import IconUpload from './IconUpload'
import VaultEntry from './Edit/VaultEntry'
import PlainText from './Edit/PlainText'
import Password from './Edit/Password'

import Toast from './Util/Toast'

export default {

	components: {
		IconUpload,
		VaultEntry,
		PlainText,
		Password,
		Toast
	},

	data () {
		return {

			/**
			 * @var Boolean
			 */
			editing: false,

			/**
			 * A duplicate copy of the target for modification
			 * and comparison. This is sent on submit.
			 * @var Object
			 */
			data: {},

			/**
			 * The type of entry that is being accessed. Determines
			 * which stencil structure is used in the form.
			 */
			targetType: 'websitelogin',

			/**
			 * The current stencil structure, loaded when the target
			 * type changes.
			 */
			stencil: {}

		}
	},

	props: {

		/**
		 * The set of loaded vaults.
		 */
		vaults: {
			type: Array
		},

		/**
		 * The login or vault being viewed or
		 * edited. If null, will show the form
		 * for creation.
		 */
		target: {
			type: Object
		}

	},

	mounted () {

		// update stencil to match the target object's
		// type
		this.$watch('targetType', (newType) => {
			this.stencil = Stencil.get(newType)
		})

		this.$watch('target', (newTarget) => {
			// deep clone the target into the form data
			// TODO: check for changes in current data,
			// confirm the user will lose their changes
			console.log(newTarget)
			this.data = JSON.parse(JSON.stringify(newTarget))
		})

		// update the stencil on initial load
		this.stencil = Stencil.get(this.targetType)

	},

	computed: {

		/**
		 * Determine if the user is creating a new item.
		 * @return Boolean
		 */
		isNew () {
			return !this.target
		},

		/**
		 * Determine if the user is editing a pre existing
		 * entry.
		 * @return Boolean
		 */
		isEditing () {
			return !this.isNew && this.editing
		},

		/**
		 * Determine whether to display the inputs fields.
		 * @return Boolean
		 */
		isEditMode () {
			return this.isNew || this.editing
		},

		/**
		 * Determine if the user is only viewing the entry.
		 * @return Boolean
		 */
		isViewing () {
			return !this.isNew && !this.editing
		},

		/**
		 * Get the panel title dependent on the editing state.
		 * @return String
		 */
		panelTitle () {
			return this.isNew ? 'Add new' : this.editing ? 'Edit' : 'View'
		},

		/**
		 * Get all stencils from the helper.
		 * @return Object[]
		 */
		allTypes () {
			return Stencil.allTypes()
		}

	},

	methods: {

		/**
		 * Change the stencil type of the form.
		 * @param string type
		 */
		selectType (type) {
			this.targetType = type
		},

		debug () {
			console.log(this.data)
		},

		/**
		 * Force the component into view mode.
		 */
		forceView () {
			this.editing = false
		},

		/**
		 * On success, show a toast with the specified success
		 * message and refresh the global vault list.
		 * @param String message
		 */
		onSuccess (message) {
			this.$refs['toast'].toast(
				'success',
				'<strong>Success!</strong> ' + message,
				5,
				true
			)
			this.$refs['form'].reset()
			this.$emit('vaultRefresh')
		},

		/**
		 * On fail, show a toast with the specified error
		 * message.
		 * @param String message
		 */
		onFail (message) {
			this.$refs['toast'].toast(
				'danger',
				'<strong>Error:</strong> ' + message,
				5,
				true
			)
		},

		/**
		 * On form submit.
		 * 
		 * Check if creating or editing, then send post/put
		 * accordingly.
		 */
		submit () {
			if (this.isNew && this.isEditMode) {
				// create
				axios.post('api/store/' + this.targetType + 's', this.data)
					.then(() => {
						this.onSuccess(this.stencil.title + ' created.')
						this.data = {}
					})
					.catch((err) => {
						this.onFail(err.toString().replace(/Error: /, ''))
					})
			} else if (this.isEditing) {
				// update
				axios.put('api/store/' + this.targetType + 's/' + this.data.id, this.data)
					.then(() => {
						this.onSuccess(this.stencil.title + ' updated.')
						this.editing = false
					})
					.catch((err) => {
						this.onFail(err.toString().replace(/Error: /, ''))
					})
			} else {
				this.onFail('Something went wrong. Please refresh the page.');
			}
		}

	}

}
</script>