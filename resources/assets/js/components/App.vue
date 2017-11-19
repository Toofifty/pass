<template>
	<div class="container" id="app">
	    <div class="col-sm-3">
	    	<vault-list></vault-list>
	    </div>
	    <div class="col-sm-9">
	    	<!-- <dashboard></dashboard> -->
	        <edit></edit>
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
	}
}
</script>