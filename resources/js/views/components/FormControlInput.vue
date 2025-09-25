<template>
    <div :class="[ col , col_md ]">
        <div class="form-group">
            <label :for="id" class="form-label" v-if="label">{{label}}</label>
            <div class="input-group">
                <input :type="type" :name="id" :id="id" v-model="inputData" :min="min" :max="max" :placeholder="placeholder ? placeholder : label" class="form-control" :class="{ 'is-invalid': error }"  />
                <div class="input-group-append" v-if="append">
                    <span class="input-group-text" style="border-top-left-radius: 0;border-bottom-left-radius: 0" id="basic-addon1">{{append}}</span>
                </div>
            </div>
            <div v-if="error" class="invalid-feedback">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormControlInput",
    props: {
        modelValue:[String,Object],
        min:{
            type: Number,
            default:'',
        },
        max:{
            type: Number,
            default:'',
        },

        type: {
            type: String,
            default: 'text'
        },
        id: {
            type: String,
            required: false
        },
        append: {
            type: String,
            required: false,
            default:'',
        },

        label: {
            type: String,
            required: false
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
