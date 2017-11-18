<template>
	<div class="panel panel-default">
		<div class="panel-heading">
			{{ isNew() ? 'Add new' : 'Edit' }} entry</div>
		<div class="panel-body">
			<form action="#" @submit.prevent="createLogin()" class="form-horizontal">
				<div class="col-lg-2">
					<icon-upload></icon-upload>
				</div>
				<div class="col-lg-10">
					<label for="title">Title</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Title" name="title">
						<div class="input-group-btn">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="glyphicon" :class="'glyphicon-' + loginIcons[typeId]"></span>
								{{ nicefy(loginTypes[typeId]) }}
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
					<component :is="loginTypes[typeId]"></component>
					<br>
					<div class="pull-right" role="group">
						<button v-if="!isNew()" class="btn btn-danger" @click="deleteLogin()"><span class="glyphicon glyphicon-trash"></span></button>
						<button type="submit" class="btn btn-primary">{{ isNew() ? 'Add' : 'Update' }}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import IconUpload from './IconUpload'
import WebsiteLogin from './Edit/WebsiteLogin'
import Note from './Edit/Note'

export default {
	components: {
		IconUpload,
		WebsiteLogin,
		Note
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
			typeId: 0
		}
	},

	props: ['loginId'],

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