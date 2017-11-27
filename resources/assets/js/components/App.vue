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
				@clearTarget="clearTarget"
			></view-edit>
	    </div>
	</div>
</template>

<script>
import VaultList from './VaultList'
import Dashboard from './Dashboard'

import ViewEdit from './ViewEdit'

export default {

	components: {
		Dashboard,
		VaultList,
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
			this.$refs.view.selectType('vault')
			this.target = null
			this.$refs.view.forceView()
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
		},

		clearTarget () {
			this.target = null
		}

	}
}
</script>