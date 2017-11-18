<template>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">My Notes</div>
				<div class="panel-body">
					<h4>New Note</h4>
					<form action="#" @submit.prevent="createNote()">
						<div class="form-group">
							<input v-model="note.title" type="text" name="title" class="form-control" autofocus>
							<input v-model="note.content" type="text" name="content" class="form-control">
							<span class="form-group-btn pull-right">
								<button type="submit" class="btn btn-primary">New Note</button>
							</span>
						</div>
					</form>
					<h4>All Notes</h4>
					<ul class="list-group">
						<li class="list-group-item" v-if="list.length === 0">No notes found</li>
						<li class="list-group-item panel" v-for="(note, index) in list">
							<div class="panel-heading">{{ note.title }}</div>
							<div class="panel-body" style="word-wrap:break-word;">{{ note.decrypted_content }}</div>
							<button @click="deleteNote(note.id)" class="btn btn-danger btn-xs pull-right">Delete</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data () {
		return {
			list: [],
			note: {
				id: '',
				title: '',
				content: ''
			}
		}
	},

	created () {
		this.fetchNoteList()
	},

	methods: {
		fetchNoteList () {
			axios.get('api/notes').then((res) => {
				this.list = res.data
				console.log(this.list)
			})
		},

		createNote () {
			axios.post('api/notes', this.note).then((res) => {
				this.note.title = '';
				this.note.content = '';
				this.fetchNoteList()
			}).catch((err) => console.error(err))
		},

		deleteNote (id) {
			axios.delete('api/notes/' + id).then((res) => {
				this.fetchNoteList()
			}).catch((err) => console.error(err))
		},

		decryptNote (id) {
			if (localStorage.private_key && localStorage.private_key !== '') {
				// this.list[id].content = 
			}
			this.list[id].content = 'yes'
		}
	}
}
</script>