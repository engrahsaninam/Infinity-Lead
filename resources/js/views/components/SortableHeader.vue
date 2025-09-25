<template>
    <th :width="width || 'auto'"  @click="toggleSort" class="cursor-pointer">
        <span class="">
            {{ label || formattedColumn }}
            <span class="arrows-sorting"
                  :class="{
                    'ASC': table.sort_by === column && table.sort_dir === 'ASC',
                    'DESC': table.sort_by === column && table.sort_dir === 'DESC'
                  }"
            ></span>
        </span>
    </th>
</template>
<script>
export default {
    props: {
        column: { type: String, required: true },
        label: { type: String, default: null },
        width: { type: String, default: null },
        table: { type: Object, required: false },
    },
    computed: {
        formattedColumn() {
            return this.column.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
        },
    },
    methods: {
        toggleSort() {
            this.$emit("update-sort", {
                column: this.column,
                dir: this.table.sort_dir === "ASC" ? "DESC" : "ASC",
            });
        },
    },
};
</script>
