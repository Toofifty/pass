<!--
    Component for a plain text editable field.
-->
<template>
    <div class="form-group">
        <label :for="name">{{ title }}</label>
        <div v-if="edit" :class="{ 'input-group': encrypted }">
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
                v-if="!tooltip"
                :name="name"
                class="form-control"
                :placeholder="title"
                :value="value"
                @input="update($event.target.value)"
                :rows="height"
            ></component>
            <component :is="component"
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
            ></component>
        </div>
        <div v-if="!edit && value" class="view-field form-control">{{ value }}</div>
        <div v-if="!edit && !value" class="view-field form-control"><i>None</i></div>
    </div>
</template>

<script>
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
        this.$watch('edit', () => {
            if (this.tooltip || this.encrypted) {
                $('[data-toggle="tooltip"]').tooltip()
            }
        })

        if (this.tooltip || this.encrypted) {
            $('[data-toggle="tooltip"]').tooltip()
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
.view-field {
    border-color: transparent;
    i {
        opacity: 0.5;
    }
}
</style>