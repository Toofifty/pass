<template>
	<div class="container-fluid" id="app">
	    <div class="col-sm-4">
	    	<vault-list
	    		:vaults="vaults"
	    		@vaultrefresh="loadVaults"
	    		@editvault="editVault"
	    		@viewlogin="viewLogin"
	    	></vault-list>
	    </div>
	    <div class="col-sm-8">
	    	<!-- <dashboard></dashboard> -->
			<view-edit
				ref="view"
				:vaults="vaults"
				:target="target"
				@vaultRefresh="loadVaults"
			></view-edit>
	        <edit-login
	        	v-if="editingLogin"
	        	:login="login"
	        	:edit="editLogin"
	        	:vaults="vaults"
	        	@vaultrefresh="loadVaults"
	    		@viewlogin="viewLogin"
	        	@stopviewlogin="stopViewLogin"
	        ></edit-login>
	        <edit-vault
	        	v-if="editingVault"
	        	:vault="vault"
	        	:vaults="vaults"
	        	@vaultrefresh="loadVaults"
	    		@stopeditvault="stopEditVault"
	        ></edit-vault>
	        <!-- <note-list></note-list> -->
	    </div>
	</div>
</template>

<script>
import VaultList from './VaultList'
import Dashboard from './Dashboard'
import EditLogin from './EditLogin'
import EditVault from './EditVault'
import NoteList from './NoteList'

import ViewEdit from './ViewEdit'

export default {

	components: {
		Dashboard,
		VaultList,
		EditLogin,
		EditVault,
		NoteList,
		ViewEdit
	},

	data () {
		return {
			vaults: [],
			vault: null,
			login: null,
			editLogin: false,
			editType: 'login',
			target: null
		}
	},

	mounted () {
		this.loadVaults()
	},

	computed: {

		editingVault () {
			return this.editType === 'vault'
		},

		editingLogin () {
			return this.editType === 'login'
		}

	},

	methods: {

		loadVaults () {
			axios.get('api/store/vaults').then((res) => {
				this.vaults = res.data
			})
		},

		editVault (vault) {
			this.$refs.view.selectType('')
			// this.vault = true
			this.vault = vault
			this.login = null
			this.editType = 'vault'
		},

		stopEditVault () {
			this.vault = null
			this.editType = 'login'
		},

		viewLogin (login, edit) {
			axios.get('api/store/websitelogins/' + login.id)
				.then((res) => {
					this.target = res.data
					this.$refs.view.selectType('websitelogin')
					this.$refs.view.forceView()
				})
				.catch((err) => console.error(err))
		},

		stopViewLogin () {
			this.login = null
			this.editType = 'login'
			this.editLogin = false
		}

	}
}
</script>