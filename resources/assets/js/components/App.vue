<template>
	<div class="container-fluid" id="app">
	    <div class="col-sm-4">
	    	<vault-list
	    		:vaults="vaults"
	    		@vaultrefresh="loadVaults"
	    		@editvault="editVault"
	    	></vault-list>
	    </div>
	    <div class="col-sm-8">
	    	<!-- <dashboard></dashboard> -->
	        <edit-login
	        	v-if="!editingVault"
	        	:vaults="vaults"
	        	@vaultrefresh="loadVaults"
	        ></edit-login>
	        <edit-vault
	        	v-if="editingVault"
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

export default {

	components: {
		Dashboard,
		VaultList,
		EditLogin,
		EditVault,
		NoteList
	},

	data () {
		return {
			vaults: [],
			editingVault: true
		}
	},

	mounted () {
		this.loadVaults()
	},

	methods: {

		loadVaults () {
			axios.get('api/store/vaults').then((res) => {
				this.vaults = res.data
			})
		},

		editVault () {
			// this.editingVault = true
			this.editingVault = !this.editingVault
		},

		stopEditVault () {
			this.editingVault = false
		}

	}
}
</script>