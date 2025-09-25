<template>
    <div :class="[colClass, colMdClass]">
        <div class="form-group ">
          <div :class="{'row align-items-center':layout === 'v'}">
            <div :class="{'col-md-1':layout === 'v'}" class="d-flex justify-content-between ">
              <label :for="id" v-if="label && show_label" class="form-label mb-1 ">{{ label }}</label>
              <label v-if="refreshFunction" @click="refreshFunction" class="form-label">
                <strong><i class="pi pi-refresh"></i></strong>
              </label>
            </div>
            <div :class="{'col-md-11':layout === 'v'}">
              <multiselect
                  v-model="selectedOption"
                  :options="options"
                  :id="id"
                  :class="{ 'is-invalid': error }"
                  :placeholder="placeholderText"
                  track-by="key"
                  label="value"
                  @select="onSelect"
                  allowEmpty="true"
                  :disabled="disabled"
              >
              </multiselect>
            </div>
          </div>
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
    name: "FormControlSelect2",
    components: { Multiselect },
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
        error: {
            type: String,
            default: ''
        },
        layout: {
            type: String,
            default: '',
        },

        label: {
            type: String,
            required: false
        },
        id: {
            type: String,
            required: false
        },
        show_placeholder: {
            type: Boolean,
            default: true,
            required: false
        },
        show_label: {
            type: Boolean,
            default: true,
            required: false,
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
        },
        disabled: {
          type: Boolean,
          default: false
        }
    },
    computed: {
        colClass() {
            return `col-${this.col}`;
        },
        colMdClass() {
            return `col-md-${this.col_md}`;
        },
        selectedOption: {
            get() {
                return this.options.find(option => option.key === this.modelValue) || null;
            },
            set(option) {
                this.$emit('update:modelValue', option ? option.key : null);
            }
        },
        placeholderText() {
            return this.show_placeholder ? `${this.label || '-- Select an option'}` : '';
        }
    },
    methods: {
        onSelect(option) {
            this.$emit('select', option.key);
        }
    },

};
</script>
