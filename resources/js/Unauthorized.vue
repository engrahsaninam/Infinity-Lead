<template>
    <div class="container">
        <div class="row justify-content-center align-items-center login-div">
            <div class="col-md-4  ">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="pi pi-lock text-danger mt-5" style="font-size: 150px"></i>
                        <h1 class="text-grey mt-5">401 - Unauthorized</h1>
                        <router-link :to="ROUTE_LOGIN()" class="btn btn-primary mt-5 px-5"><i class="pi pi-external-link"></i> Login</router-link>
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

import {ROUTE_LOGIN} from "./constants.js";

export default {
    name: 'Unauthorized',
    methods: {
      ROUTE_LOGIN() {
        return ROUTE_LOGIN
      }
    },
    data() {

    },
}
</script>
