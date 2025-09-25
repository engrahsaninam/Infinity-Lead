<template>
    <div class="loader-div"  v-if="loading">
        <div class="loader"></div>
    </div>
</template>
<script>
export default {
    name: 'ContentLoader',
    props: {
        loading: {
            type: Boolean,
            required: true,
        },
    },
}
</script>
