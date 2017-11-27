<!--
    Component for a editable password field.
-->
<template>
    <div class="form-group">
        <label :for="name">{{ title }}</label>
        <div v-if="edit" class="input-group">
            <span
                v-if="encrypted"
                class="input-group-addon"
                data-toggle="tooltip"
                data-placement="bottom"
                title="This field will be stored securely"
            >
                <span class="glyphicon glyphicon-lock"></span>
            </span>
            <input
                :id="fieldType === 'password' ? '' : name"
                v-if="!tooltip"
                :type="fieldType"
                :name="name"
                class="form-control"
                :placeholder="title"
                :value="value"
                @input="update($event.target.value)"
                autocomplete="new-password"
            >
            <input
                :id="fieldType === 'password' ? '' : name"
                v-if="tooltip"
                :type="fieldType"
                :name="name"
                class="form-control"
                :placeholder="title"
                :value="value"
                @input="update($event.target.value)"
                data-toggle="tooltip"
                data-placement="bottom"
                :title="tooltip"
                autocomplete="new-password"
            >
            <div style="display:none;opacity:0;height:0;visibility:hidden" :id="name" v-if="fieldType === 'password'">{{ value }}</div>
            <div class="input-group-btn">
                <button @click.prevent="visible = !visible" class="btn btn-default">
                    <span :class="'glyphicon-eye-' + (visible ? 'close' : 'open')" class="glyphicon"></span>
                </button>
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
                <button
                    @click.prevent
                    class="btn btn-default"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Generate new password"
                >
                    <span class="glyphicon glyphicon-refresh"></span>
                </button>
            </div>
        </div>
        <div v-if="!edit && value" class="input-group">
            <div class="view-field form-control">{{ viewedText }}</div>
            <div class="input-group-btn standalone">
                <button @click.prevent="visible = !visible" class="btn btn-default">
                    <span :class="'glyphicon-eye-' + (visible ? 'close' : 'open')" class="glyphicon"></span>
                </button>
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

    data () {
        return {
            visible: false
        }
    },

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

        encrypted: {
            type: Boolean
        },

        copyable: {
            type: Boolean
        }

    },

    mounted () {

        this.$watch('edit', this.initialize)
        this.initialize()

    },

    computed: {

        /**
         * Get the input type based on this.visible
         * 
         * @return String
         */
        fieldType () {
            return this.visible ? 'text' : 'password'
        },

        /**
         * Get the value to show in view mode. Replaces
         * the text with dots if visible is false.
         * 
         * @return String
         */
        viewedText () {
            return this.visible ? this.value : (this.value || '').replace(/./g, '•')
        }

    },

    methods: {

        /**
         * Initialise any library functionality in
         * the component
         */
        initialize () {
            $('[data-toggle="tooltip"]').tooltip()
            if (this.copyable) {
                let clip = new Clipboard('#copy-' + this.name)
            }
        },

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