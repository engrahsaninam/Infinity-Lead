<template>
    <div class="form-group float-start d-flex align-items-center">
        <label for="" class="mr-1 mt-1 text-muted">Per page</label>
        <select v-model="selectedValue" @change="onSelect" class="form-control form-select bg-transparent form-control-sm w-auto">
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
        </select>
    </div>
</template>

<script>
export default {
    name: 'RecordOptions',
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
    },
    computed: {
        selectedValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    methods: {
        onSelect(event) {
            this.$emit('change', event.target.value);
        }
    }
}
</script>
