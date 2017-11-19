<template>
	<div ref="container" class="subfolder">
		<div class="subfolder-header">
	        <span class="back-btn glyphicon glyphicon-menu-left" @click="$emit('closevault')"></span>
			<span class="glyphicon glyphicon-book"></span>
			<div class="subfolder-title">{{ vault.title }}</div>
			<span class="glyphicon glyphicon-cog pull-right"></span>
		</div>
		<ul>
			<li v-for="child in vault.children">
				<span class="glyphicon glyphicon-book"></span>
				{{ child.title }}
    			<span class="glyphicon glyphicon-cog pull-right"></span>
			</li>
			<li v-for="login in vault.website_logins">
				<span class="glyphicon glyphicon-globe"></span>
				{{ login.title }}
    			<span class="glyphicon glyphicon-cog pull-right"></span>
			</li>
		</ul>
	</div>
</template>

<script>
export default {

	props: {

		vault: {
			type: Object
		},

		children: {
			type: Array
		}

	},

	computed: {

		vaultList () {
			if (!this.vault) {
				return this.vaults
			}
			return this.vault.children
		}

	},

	methods: {

		getHeight () {
			return this.$refs.container.clientHeight
		}

	}

}
</script>

<style lang="scss" scoped>
@import "~@/_variables.scss";

.glyphicon-book,
.glyphicon-globe {
	margin-right: 10px;
}

.glyphicon-cog {
	margin: 3px;
	font-size: 1.2em;
	color: $laravel-border-color;
	transition: 0.25s;
	&:hover {
		color: darken($laravel-border-color, 10%);
		cursor: pointer;
	}
}

.subfolder {
	position: absolute;
	top: 0;
	right: calc(-100% - 15px);
	width: 100%;

	.subfolder-header {
		padding-bottom: 15px;
		border-bottom: 1px solid $laravel-border-color;

		.subfolder-title {
			font-size: 1.5em;
			display: inline-block;
		}

		.glyphicon-cog {
			margin-right: 6px;
			padding: 5px;
			font-size: 1.5em;		
		}
	}
}

.back-btn {
	padding: 10px;
	cursor: pointer;
}

ul {
	padding: 0;
	margin: 0;
	li {
		list-style-type: none;
		border-bottom: 1px solid $laravel-border-color;
		padding: 10px;
		cursor: pointer;

		&:last-child {
			border-bottom: none;
		}
	}
}
</style>