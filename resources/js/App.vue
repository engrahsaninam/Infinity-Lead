<template>
    <router-view></router-view>
</template>
<script>
import store from './store';
export default {
    name:'App',
    created() {
        store.dispatch('setAuthUser');
    }
};
</script>
<style>
.multiselect__option--highlight.multiselect__option::after{
  display: none !important;
  content: none !important;
}
</style>

