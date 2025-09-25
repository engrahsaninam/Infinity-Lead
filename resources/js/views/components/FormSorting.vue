<template>
    <div class="">
        <div v-for="option in options" :key="getOptionValue(option)" class="badge-wrapper">
            <span class="my-badge p-1 px-2" :class="{'my-badge-primary': getOptionValue(option) === column}" @click="sort(option)">
                {{ getOptionTitle(option).toUpperCase() }}
                <span v-if="getOptionValue(option) === column">
                    <span v-if="dir === 'ASC'" class="pi pi-sort-down"></span>
                    <span v-else class="pi pi-sort-up"></span>
                </span>
                <span v-else>
                    <span class="pi pi-sort"></span>
                </span>
            </span>
        </div>
    </div>
</template>


<script>
export default {
    props: {
        options: Array,
        dir: String,
        column: String
    },
    methods: {
        getOptionValue(option) {
            return typeof option === 'object' ? Object.keys(option)[0] : option;
        },
        getOptionTitle(option) {
            return typeof option === 'object' ? Object.values(option)[0] : option;
        },
        sort(option) {
            const optionValue = this.getOptionValue(option);
            const newDir = (this.column === optionValue && this.dir === 'ASC') ? 'DESC' : 'ASC';
            this.$emit('update-sort', {column: optionValue, dir: newDir});
        }
    }
};
</script>


<style scoped>
.badge-wrapper {
    display: inline-block;
    margin-right: 8px;
    cursor: pointer;
}

</style>
