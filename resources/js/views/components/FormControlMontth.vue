<template>
    <div :class="[ col_md ]">
        <div class="form-group">
            <input type="month" v-model="inputData" :min="min"  class="form-control w-auto float-right mb-1 bg-white" />
        </div>
    </div>
</template>

<script>
export default {
    name: "FormControlMonth",
    props: {
        modelValue: [String, Object],
        min: {
            type: String,
            default: '2021-06',
        },
        col_md:{
            type: Number,
            default:6
        },
    },
    computed: {
        inputData: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        col_md(){
            return `col-md-${this.col_md}`;
        },

    }
};
</script>
