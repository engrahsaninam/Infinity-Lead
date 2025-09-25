<template>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class=" justify-content-center py-4">
                                <a href="/" class="logo d-flex align-items-center w-auto">
                                  <img src="/logo.png" alt="" width="250">
                                </a>
                            </div>
                            <div class="card mb-3 w-100">
                                <div class="card-header py-3 pt-4">
                                  <div class=" pb-2">
                                    <h4 class="text-center ">
                                      <img class="rounded-circle avatar-sm " src="../../assets/img/google.png" alt="" width="28" height="28" >
                                      Login account not found!
                                    </h4>
                                  </div>
                                </div>
                                <div class="card-body py-5">
                                    <div class="row  d-flex justify-content-center">
                                      <div class="col-md-12 pb-5 text-center">
                                        <i class="pi pi-exclamation-triangle text-warning" style="font-size: 100px"></i>
                                      </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <router-link class="btn btn-sm btn-outline-primary" :to="ROUTE_LOGIN()">Redirect to Login</router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>
<script>
import {ROUTE_LOGIN, ROUTE_USER_HOME} from "../../constants.js";
import store from "../../store.js";
export default {
    name: 'GoogleLoginAccountNotFound',
    data() {
        return {
            isLoading:true,
        }
    },
    methods: {
        ROUTE_LOGIN() {
            return ROUTE_LOGIN
        }
    },
    created() {

    }
}
</script>