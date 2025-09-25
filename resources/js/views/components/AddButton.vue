<template>
    <router-link :to="link" class="btn btn-light border active float-end"><i class="pi pi-plus-circle"></i> {{title}}</router-link>

</template>
<script>
export default {
    name:'AddButton',
    props:{
        title:{
            default:'',
            required:true,
            type:String,
        },
        link:{
            default:'#',
            required:true,
            type:String,
        }
    },
}
</script>
