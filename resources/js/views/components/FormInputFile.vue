<template>

    <div :class="[colClass, colMdClass]">
        <div class="custom-file">
            <label :for="id" class="custom-file-label form-label" v-if="label">{{ label }}</label>
            <input type="file" :id="id" class="custom-file-input" @change="onFileChange" :class="{ 'is-invalid': error }"/>
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormInputFile",
    props: {
        modelValue: {
            type: File,
            default: null,
        },
        id: {
            type: String,
            required: false
        },
        label: {
            type: String,
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
            default: null,
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
            const file = event.target.files[0];
            this.$emit('update:modelValue', file);
        },
    }
};
</script>
