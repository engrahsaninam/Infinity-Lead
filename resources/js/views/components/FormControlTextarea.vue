<template>
    <div :class="[ col , col_md ]">
        <div class="form-group">
            <label :for="id" class="form-label" v-if="label">{{label}}</label>
            <textarea :id="id" v-model="inputData" :placeholder="placeholder ? placeholder : label" class="form-control" :rows="rows" :class="{ 'is-invalid': error }" ></textarea>
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormControlTextarea",
    props: {
        modelValue: String,

        rows: {
            type: Number,
            default:2,
        },
        id: {
            type: String,
            required: false
        },

        label: {
            type: String,
            required: false,
        },
        placeholder: {
            type: String,
            default: ''
        },
        col_md:{
            type: Number,
            default:6
        },
        col:{
            type: Number,
            default:12
        },
        error: {
            type: String,
            default: null,
        }

    },
    computed: {
        col(){
            return `col-${this.col}`;
        },
        col_md(){
            return `col-md-${this.col_md}`;
        },

        inputData: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    }
};
</script>
