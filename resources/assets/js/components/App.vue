<template>
	<div class="container" id="app">
	    <div class="col-sm-4">
	    	<vault-list :vaults="vaults" @vaultrefresh="loadVaults"></vault-list>
	    </div>
	    <div class="col-sm-8">
	    	<!-- <dashboard></dashboard> -->
	        <edit :vaults="vaults" @vaultrefresh="loadVaults"></edit>
	        <!-- <note-list></note-list> -->
	    </div>
	</div>
</template>

<script>
import VaultList from './VaultList'
import Dashboard from './Dashboard'
import Edit from './Edit'
import NoteList from './NoteList'

export default {

	components: {
		Dashboard,
		VaultList,
		Edit,
		NoteList
	},

	data () {
		return {
			vaults: []
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
		}

	}
}
</script>