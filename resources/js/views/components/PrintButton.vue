<template>
    <a :href="link" target="_blank" class="btn btn-dark me-1"> <i class="pi " :class="icon" v-if="show_icon"></i> {{title}}</a>
</template>
<script>
export default {
    name:'PrintButton',
    props:{
        title:{
            default:'',
            required:false,
            type:String,
        },
        link:{
            default:'#',
            required:true,
            type:String,
        },
        show_icon:{
            default:true,
            required:false,
            type:Boolean,
        },
        icon:{
            default:'pi-print',
            required:false,
            type:String,
        },


    },
}
</script>
