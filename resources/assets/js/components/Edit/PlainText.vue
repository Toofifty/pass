<!--
    Component for a plain text editable field.
-->
<template>
    <div class="form-group">
        <label :for="name">{{ title }}</label>
        <div v-if="edit" :class="{ 'input-group': encrypted || copyable }">
            <span
                v-if="encrypted"
                class="input-group-addon"
                data-toggle="tooltip"
                data-placement="bottom"
                title="This field will be stored securely"
            >
                <span class="glyphicon glyphicon-lock"></span>
            </span>
            <component :is="component"
                :id="name"
                v-if="!tooltip"
                :name="name"
                class="form-control"
                :placeholder="title"
                :value="value"
                @input="update($event.target.value)"
                :rows="height"
            >{{ value }}</component>
            <component :is="component"
                :id="name"
                v-if="tooltip"
                :name="name"
                class="form-control"
                :placeholder="title"
                :value="value"
                @input="update($event.target.value)"
                data-toggle="tooltip"
                data-placement="bottom"
                :title="tooltip"
                :rows="height"
            >{{ value }}</component>
            <div v-if="copyable" class="input-group-btn">
                <button
                    :id="'copy-' + name"
                    @click.prevent
                    class="btn btn-default"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Copy to clipboard"
                    :data-clipboard-target="'#' + this.name"
                >
                    <span class="glyphicon glyphicon-copy"></span>
                </button>
            </div>
        </div>
        <div v-if="!edit && value && !copyable" class="view-field form-control">{{ value }}</div>
        <div v-if="!edit && value && copyable" class="input-group">
            <div :id="name" class="view-field form-control">{{ value }}</div>
            <div class="input-group-btn standalone">
                <button
                    :id="'copy-' + name"
                    @click.prevent
                    class="btn btn-default"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Copy to clipboard"
                    :data-clipboard-target="'#' + name"
                >
                    <span class="glyphicon glyphicon-copy"></span>
                </button>
            </div>
        </div>
        <div v-if="!edit && !value" class="view-field form-control"><i>None</i></div>
    </div>
</template>

<script>
import Clipboard from 'clipboard'

export default {

    props: {

        value: {
            type: String
        },

        title: {
            type: String
        },

        name: {
            type: String
        },

        edit: {
            type: Boolean
        },

        tooltip: {
            type: String
        },

        height: {
            type: Number
        },

        encrypted: {
            type: Boolean
        },

        copyable: {
            type: Boolean
        }

    },

    computed: {

        /**
         * Get the type of the input component. If a height is 
         * specified and above 0, a textarea will be used.
         * 
         * @return String
         */
        component () {
            return this.height && this.height > 1 ? 'textarea' : 'input'
        }

    },

    mounted () {

        // trigger tooltips if editing is enabled
        this.$watch('edit', this.initialize)
        this.initialize()

    },

    methods: {

        /**
         * Emit the value to the parent (enables v-model binding)
         * @param string value
         */
        update (value) {
            this.$emit('input', value)
        },

        initialize () {
            if (this.tooltip || this.encrypted) {
                $('[data-toggle="tooltip"]').tooltip()
            }

            if (this.copyable) {
                let clip = new Clipboard('#copy-' + this.name)
            }
        }

    }

}
</script>

<style lang="scss" scoped>
.view-field {
    border-color: transparent;
    i {
        opacity: 0.5;
    }
}

.input-group-btn.standalone {
    &:last-child > .btn:first-child {
        border-bottom-left-radius: 4px;
        border-top-left-radius: 4px;
    }
}
</style>