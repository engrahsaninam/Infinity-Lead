<template>
    <div :class="[colClass, colMdClass]" class="mt-auto pt-2">
        <div class="form-check form-group">
            <input
                class="form-check-input border border-dark"
                type="checkbox"
                :id="id"
                :value="value"
                v-model="inputData"
            >
            <label class="form-check-label" :for="id">
                {{ label }}
            </label>

            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormCheckbox",
    props: {
        modelValue: {
            type: [Boolean, Array, String],
            required: true
        },
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: false
        },
        value: {
            type: [String, Number],
            required: false
        },
        col_md: {
            type: Number,
            default: 6
        },
        col: {
            type: Number,
            default: 12
        },
        error: {
            type: String,
            default: null
        }
    },
    computed: {
        colClass() {
            return `col-${this.col}`;
        },
        colMdClass() {
            return `col-md-${this.col_md}`;
        },
        inputData: {
            get() {
                return this.modelValue; // This allows v-model to reflect the array
            },
            set(value) {
                this.$emit('update:modelValue', value); // Emit the change
            }
        }
    }
};
</script>
