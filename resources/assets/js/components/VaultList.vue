<template>
    <div class="panel panel-default">
        <div class="panel-heading">
        	<span class="glyphicon glyphicon-home" @click="closeVault()"></span>
        	Vaults
        	<button class="btn btn-primary pull-right btn-xs" @click="$emit('editvault')">New Vault</button>
        </div>
        <div class="panel-body vaults">
            <div class="top-level-container" :class="{ 'vault-open': vault !== null }" v-bind:style="topStyles">
            	<div class="list-level">
	                <input type="text" class="form-control" placeholder="Filter Vaults" v-model="filter">
		            <br>
	            	<ul>
	            		<li v-for="(vault, index) in filteredVaults" @click="openVault(vault)">
	            			<span class="glyphicon glyphicon-book"></span>
	            			{{ vault.title }}
	            			<span class="glyphicon glyphicon-cog pull-right" @click.stop="editVault(vault)"></span>
	            		</li>
	            		<li v-if="filteredVaults.length === 0">
	            			No vaults found.
	            		</li>
	            	</ul>
		        </div>
	            <vault-subfolder ref="subfolder" v-if="vault !== null" :parent="vault" :topStyles="topStyles" @closevault="closeVault" @editvault="editVault" @viewlogin="viewLogin"></vault-subfolder>
            </div>
        </div>
    </div>
</template>

<script>
Vue.component('vault-subfolder', require('./VaultSubfolder.vue'))

export default {

	data () {
		return {
			filter: '',
			vault: null,
			children: [],
			topStyles: {}
		}
	},

	props: {
		vaults: {
			type: Array
		}
	},

	computed: {

		filteredVaults () {
			return _(this.vaults)
				.filter(v => v.title.toLowerCase().indexOf(this.filter.toLowerCase()) > -1)
				.value()
		}

	},

	methods: {

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
			this.$set(this.topStyles, 'min-height', '0px')
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

.glyphicon-home,
.glyphicon-book {
	margin-right: 10px;
	cursor: pointer;
}

.vaults {
	overflow: hidden;
	position: relative;
	.top-level-container {
		transition: 0.25s;

		ul {
			padding: 0;
			margin: 0;
			li {
				list-style-type: none;
				border-bottom: 1px solid $laravel-border-color;
				padding: 10px;
				cursor: pointer;

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

				&:last-child {
					border-bottom: none;
				}

				&:hover {
					background: #f5f5f5;
				}
			}
		}
		&.vault-open {
			transform: translateX(calc(-100% - 15px));
		}
	}
}
</style>