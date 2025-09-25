<template>


  <div class="page-title-right">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><router-link :to="ROUTE_HOME()"><i class="pi pi-home"></i> Dashboard</router-link></li>
      <li class="breadcrumb-item" :class="{active: (key === items.length - 1)}" v-for="(item, key) in items" :key="key">
        <router-link v-if="key !== items.length - 1" :to="item">{{ rename(item) }}</router-link>
        <a v-else>{{ rename(item) }}</a>
      </li>
    </ol>
  </div>
</template>
<script >
import {
  ROUTE_USER_HOME,
} from "../../constants.js";

export default {
    name: 'Breadcrumb',
    methods: {
        ROUTE_HOME() {
            return ROUTE_USER_HOME
        },
        rename(item){
            item=item.replaceAll('-',' ');
            item=item.replaceAll('/',' ');
          item = item.replaceAll('user', ' ');
          item = item.replaceAll('admin', ' ');
            item=item.replaceAll(':id?',' ');
            item=item.replaceAll(':id',' ');
            item = item.replace(/\d/g, '');
            return item;
        }
    },
    props:{
        items: {
            type: Array,
            default: []
        },
    },
}
</script>
<style scoped>
.breadcrumb-item{
    text-transform: capitalize;
}
</style>
