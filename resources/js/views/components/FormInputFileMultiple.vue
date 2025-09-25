<template>
    <div :class="[colClass, colMdClass]">
        <div class="form-group">
            <label :for="id" class="form-label">{{ label }}</label>
            <input
                type="file"
                :id="id"
                class="form-control"
                multiple
                @change="onFileChange"
                :class="{ 'is-invalid': error }"
            />
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormInputFileMultiple",
    props: {
        modelValue: {
            type: Array,  // Array to handle multiple files
            default: () => []
        },
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
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
        }
    },
    methods: {
        onFileChange(event) {
            const files = Array.from(event.target.files);
            this.$emit('update:modelValue', files);
        }
    }
};
</script>
