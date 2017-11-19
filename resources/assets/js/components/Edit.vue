<template>
	<div class="panel panel-default">
		<div class="panel-heading">
			{{ isNew() ? 'Add new' : 'Edit' }} entry</div>
		<div class="panel-body">
			<form ref="form" action="#" @submit.prevent="submit" class="form-horizontal">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
				</div>
				<div class="col-lg-10">
					<label for="title">Title</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Title" name="title" required>
						<div class="input-group-btn">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="glyphicon" :class="'glyphicon-' + loginIcons[typeId]"></span>
								{{ nicefy(loginType) }}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
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
					<component :is="loginType"></component>
					<br>
					<div class="pull-right" role="group">
						<button v-if="!isNew()" class="btn btn-danger" @click="deleteLogin()"><span class="glyphicon glyphicon-trash"></span></button>
						<button type="submit" class="btn btn-primary">{{ isNew() ? 'Add' : 'Update' }}</button>
					</div>
				</div>
			</form>
		</div>
		<toast ref="toast"></toast>
	</div>
</template>

<script>
import _ from 'lodash'

import IconUpload from './IconUpload'
import WebsiteLogin from './Edit/WebsiteLogin'
import Note from './Edit/Note'
import Toast from './Util/Toast'

export default {
	components: {
		IconUpload,
		WebsiteLogin,
		Note,
		Toast
	},

	data () {
		return {
			loginTypes: [
				'WebsiteLogin',
				'Note'
			],
			loginIcons: [
				'floppy-disk',
				'book'
			],
			typeId: 0,
			alertActive: false
		}
	},

	props: ['loginId'],

	computed: {

		loginType () {
			return this.loginTypes[this.typeId]
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
			console.log(data)
			if (this.isNew()) {
				axios.post('api/store/' + this.loginType.toLowerCase() + 's', data).then(() => {
					this.$refs['toast'].toast(
						'success',
						'<strong>Success!</strong> ' + this.nicefy(this.loginType) + ' created.',
						5,
						true
					)
					this.$refs['form'].reset()
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

<style lang="scss" scoped>
.dropdown-menu > li > a {
	padding: 3px 0px 3px 12px;
	width: auto;
}
</style>