<template>
    <div :class="[colClass, colMdClass]">
        <div class="form-group">

            <div class="d-flex justify-content-between">
                <label :for="id" v-if="label " class="form-label">{{ label }}</label>
                <label v-if="refreshFunction" @click="refreshFunction" class="form-label">
                    <strong><i class="pi pi-refresh"></i></strong>
                </label>
            </div>
            <multiselect
                v-model="selectedOptions"
                :options="options"
                :id="id"
                :class="{ 'is-invalid': error }"
                :placeholder="'-- Select ' + label"
                track-by="key"
                label="value"
                :multiple="true"
                :close-on-select="false"
                @select="onSelect"

            >
            </multiselect>
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
    name: "FormControlSelect2Multiple",
    components: { Multiselect },
    props: {
        modelValue: {
            type: Array,
            default: () => [],
        },
        error: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            required: true
        },
        id: {
            type: String,
            required: false
        },
        options: {
            type: Array,
            required: true
        },
        col_md: {
            type: Number,
            default: 6
        },
        col: {
            type: Number,
            default: 12
        },
        refreshFunction: {
            type: Function,
            required: false
        }
    },
    computed: {
        colClass() {
            return `col-${this.col}`;
        },
        colMdClass() {
            return `col-md-${this.col_md}`;
        },
        selectedOptions: {
            get() {
                return this.options.filter(option => (this.modelValue || []).includes(option.key));
                // Map the modelValue to option objects
                //return this.options.filter(option => this.modelValue.includes(option.key));
            },
            set(options) {
                // Emit an array of selected keys
                this.$emit('update:modelValue', options.map(option => option.key));
            }
        }
    },
    methods: {
        onSelect(option) {
            this.$emit('select', option.key);
        }
    }
};
</script>
