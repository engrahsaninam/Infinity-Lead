<template>
    <div class="container">
        <div class="row justify-content-center align-items-center restricted-div">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="pi pi-lock text-danger mt-2" style="font-size: 120px"></i>
                        <h1 class="text-danger mt-3">Restricted</h1>
                        <p class="text-muted">You do not have permission to access this account.</p>
                        <p>Please contact your system administrator for access.</p>
                        <router-link :to="ROUTE_LOGIN()" class="btn btn-outline-primary mt-3">
                            <i class="pi pi-sign-in"></i> Go to Login
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.restricted-div {
    min-height: 100vh;
}
</style>

<script>
import { ROUTE_LOGIN } from './constants.js'

export default {
    name: 'Restricted',
    methods: {
        ROUTE_LOGIN() {
            return ROUTE_LOGIN;
        },
    },
}
</script>
  
