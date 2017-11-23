<template>
    <div class="form-group">
        <div v-if="edit" class="ms-input">
            <div :class="{ 'col-lg-6': isNew, 'col-lg-12': !isNew }">
                <label for="vault">{{ title }}</label>
                <div class="input-group multi">
                    <multiselect
                        :options="vaultList"
                        :value="value"
                        @input="update"
                        label="title"
                        :show-labels="false"
                        :multiple="true"
                    >
                    </multiselect>
                </div>
            </div>
            <div v-if="isNew" class="col-lg-6">
                <label for="vault-title">New Vault Title</label>
                <input type="text" class="form-control" placeholder="Vault Title" name="vault-title" v-model="newVaultTitle" required>
            </div>
        </div>
        <label v-if="!edit" for="vault">{{ title }}</label>
        <ul v-if="!edit" class="view-vault-list">
            <li v-bind:key="index" v-for="(vault, index) in value" class="view-vault-item">
                <span class="glyphicon glyphicon-book"></span>
                {{ vault.title }}
            </li>
        </ul>
        <div v-if="!value && !edit" class="view-field form-control"><i>None</i></div>
        <div class="clear-both"></div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import _ from 'lodash'

export default {

    components: {
        Multiselect
    },

    data () {
        return {
            newVaultTitle: ''
        }
    },

    props: {

        value: {
            type: Array
        },

        vaults: {
            type: Array
        },

        title: {
            type: String
        },

        createNew: {
            type: Boolean
        },

        edit: {
            type: Boolean
        }

    },

    mounted () {

        this.$watch('newVaultTitle', (title) => {
            _(this.value).find(v => v.id === -1).title = title
            this.update(this.value)
        })

    },

    computed: {

        /**
         * Get a list of valid vault objects to display.
         * 
         * if createNew is true, add dummy option
         * if filterSelf is true, remove self and all
         * children (TODO)
         * 
         * @return Object[]
         */
        vaultList () {
            if (this.createNew) {
                return [{
                    title: 'Create new',
                    id: -1,
                    icon: ''
                }].concat(this.vaults)
            } else {
                return this.vaults
            }
        },

        /**
         * Check if the selected vaults contains the dummy
         * 'Create new' option
         * 
         * @return Boolean
         */
        isNew () {
            return _(this.value).find(v => v.id === -1)
        }

    },

    methods: {

        /**
         * Emit the value to the parent (enables v-model binding)
         * @param string value
         */
        update (value) {
            this.$emit('input', value)
        }

    }

}
</script>

<style lang="scss" scoped>
.no-padding {
    padding: 0;
}

.clear-both {
    clear: both;
}

.ms-input {
    margin-left: -15px;
    margin-right: -15px;
}

.view-vault-list {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 0;
    .view-vault-item {
        display: inline-block;
        padding: 4px 12px;
        margin: 4px 4px;
        background: #f0f0f0;
        border-radius: 4px;
        .glyphicon-book {
            margin-right: 10px;
        }
    }
}

.multiselect {
    min-height: 38px;
}

.view-field {
    border-color: transparent;
    min-height: 38px;
    i {
        opacity: 0.5;
    }
}
</style>