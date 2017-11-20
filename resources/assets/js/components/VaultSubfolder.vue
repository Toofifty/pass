<template>
	<div ref="container" class="subfolder" :class="{ 'vault-open': vault !== null }" >
		<div class="subfolder-header">
	        <span class="back-btn glyphicon glyphicon-menu-left" @click="$emit('closevault')"></span>
			<span class="glyphicon glyphicon-book"></span>
			<div class="subfolder-title">{{ parent.title }}</div>
			<span class="glyphicon glyphicon-cog pull-right" @click.stop="editVault(parent)"></span>
		</div>
		<ul>
			<li v-for="child in parent.children" @click="openVault(child)">
				<span class="glyphicon glyphicon-book"></span>
				{{ child.title }}
    			<span class="glyphicon glyphicon-cog pull-right" @click.stop="editVault(child)"></span>
			</li>
			<li v-for="login in parent.website_logins" @click="viewLogin(login, false)">
				<span class="glyphicon glyphicon-globe"></span>
				{{ login.title }}
    			<span class="glyphicon glyphicon-pencil pull-right" @click.stop="viewLogin(login, true)"></span>
			</li>
		</ul>
        <vault-subfolder ref="subfolder" v-if="vault !== null" :parent="vault" @closevault="closeVault" @editvault="editVault" @viewlogin="viewLogin"></vault-subfolder>
	</div>
</template>

<script>
export default {
	data () {
		return {
			vault: null,
		}
	},

	props: {

		parent: {
			type: Object
		},

		children: {
			type: Array
		},

		topStyles: {
			type: Object
		}

	},

	computed: {

		vaultList () {
			if (!this.parent) {
				return this.vaults
			}
			return this.parent.children
		}

	},

	methods: {

		getHeight () {
			return this.$refs.container.clientHeight
		},

		openVault (vault) {
			axios.get('api/store/vaults/' + vault.id + '/all').then((res) => {
				this.vault = res.data
				this.$nextTick(() => {
					let el = this.$refs.subfolder
					this.$set(this.topStyles, 'min-height', el.getHeight() + 'px')
				})
			}).catch((err) => console.log(err))
		},

		closeVault () {
			this.vault = null
			this.$set(this.topStyles, 'min-height', this.getHeight() + 'px')
		},

		editVault (vault) {
			this.$emit('editvault', vault)
		},

		viewLogin (login, edit) {
			this.$emit('viewlogin', login, edit)
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

.glyphicon-cog,
.glyphicon-pencil {
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
	transition: 0.25s;

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
	&.vault-open {
		transform: translateX(calc(-100% - 15px));
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

		&:hover {
			background: #f5f5f5;
		}
	}
}
</style>