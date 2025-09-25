<template>
    <router-link :to="link" class="btn btn-sm btn-primary"><i class="pi pi-pencil"></i></router-link>
</template>
<script>
import feather from 'feather-icons'

export default {
    name:'EditButton',
    props:{
        link:{
            default:'#',
            required:true,
            type:String,
        }
    },
}
</script>
