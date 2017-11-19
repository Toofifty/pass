<template>
    <div class="panel panel-default">
        <div class="panel-heading">
        	<span class="glyphicon glyphicon-home" @click="closeVault()"></span>
        	Vaults
        	<button class="btn btn-primary pull-right btn-xs">New Vault</button>
        </div>
        <div class="panel-body vaults">
            <!-- <div class="input-group"> -->
                <!-- <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                </span> -->
            <!-- </div> -->
            <div class="vault-list-container" :class="{ 'vault-open': vaultOpen }">
                <input type="text" class="form-control" placeholder="Quick search" v-model="filter">
	            <br>
            	<ul>
            		<li v-for="(vault, index) in filteredVaults" @click="openVault(vault.id)">
            			{{ vault.title }}
            			<span class="glyphicon glyphicon-cog pull-right"></span>
            		</li>
            		<li v-if="filteredVaults.length === 0">
            			No vaults found.
            		</li>
            	</ul>
	            <span class="back-btn glyphicon glyphicon-menu-left" @click="closeVault()"></span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
	data () {
		return {
			filter: '',
			vaultOpen: false
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

		openVault () {
			this.vaultOpen = true
		},

		closeVault () {
			this.vaultOpen = false
		}

	}
}
</script>

<style lang="scss" scoped>
@import "~@/_variables.scss";

.glyphicon-home {
	margin-right: 10px;
	cursor: pointer;
}

.vaults {
	overflow: hidden;
	.vault-list-container {
		position: relative;
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
			}
		}
		&.vault-open {
			transform: translateX(calc(-100% - 15px));
		}
	}

	.back-btn {
		position: absolute;
		padding: 10px;
		right: -40px;
		cursor: pointer;
	}
}
</style>