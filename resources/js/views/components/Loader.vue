<template>
    <div  v-if="loading" class="main-wrap-white"> <div class="simple-loader"></div> </div>
</template>
<script>
export default {
    name: 'Loader',
    props: {
        loading: {
            type: Boolean,
            required: true,
        },
    },
}
</script>
