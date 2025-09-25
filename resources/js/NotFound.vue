<template>
    <div class="container">
        <div class="row justify-content-center align-items-center login-div">
            <div class="col-md-4  ">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="pi pi-exclamation-triangle text-warning mt-2" style="font-size: 150px"></i>
                        <h1 class="text-grey mt-2">404</h1>
                        <h4 class="text-grey ">Page not found</h4>
                        <router-link :to="ROUTE_LOGIN()" class="btn btn-primary mt-4 px-5 mb-3"><i class="pi pi-external-link"></i> Login</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.login-div{
    min-height: 100vh;
}
</style>
<script>
import {  ROUTE_LOGIN} from "./constants.js";

export default {
    name: 'NotFound',
    methods: {
        ROUTE_LOGIN() {
            return ROUTE_LOGIN
        },

    },
    data() {

    },
}
</script>
