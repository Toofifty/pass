<template>
	<div class="input-group">
		<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="This field will be stored securely"><span class="glyphicon glyphicon-lock"></span></span>
		<textarea name="content" class="form-control" rows="3" placeholder="Note"></textarea>
	</div>
</template>

<script>
export default {
	mounted () {
		$('[data-toggle="tooltip"]').tooltip()
	}
}
</script>

<style scoped>
textarea {
	resize: vertical;
	min-height: 80px;
}
</style>