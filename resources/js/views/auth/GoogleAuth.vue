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
                            <div class="card w-100">
                              <div class="card-header py-3 pt-4">
                                <div class=" pb-2">
                                  <h4 class="text-center">
                                    <img class="rounded-circle avatar-sm " src="../../assets/img/google.png" alt="" width="25" height="25" >
                                    Google Sign In
                                  </h4>
                                </div>
                              </div>
                                <div class="card-body py-5">
                                    <div class=" pb-2">
                                        <p class="text-center ">Processing! Please wait</p>
                                    </div>
                                    <div class="row g-3  ">
                                        <div class="col-md-12 text-center">
                                          <div id="loading"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pt-3">
                                        <div class="separator">
                                            <div class="line"></div>
                                            <h6 class="px-3 small mt-2 text-muted" >OR</h6>
                                            <div class="line"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mt-4">
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
    name: 'GoogleAuth',
    data() {
        return {
            isLoading:true,
        }
    },
    methods: {
        ROUTE_LOGIN() {
            return ROUTE_LOGIN
        },
        makingLoggedIn(){
          this.axios.post(`google-auth`,{'token':this.$route.params.token}).then(( response) => {
            localStorage.removeItem('user');
            localStorage.removeItem('sanctum_token');
            localStorage.removeItem('expiration_date');
            var user=JSON.stringify(response.data.user);
            localStorage.setItem('user', user);

            var role=response.data.user.role;
            localStorage.setItem('role', role);

            localStorage.setItem('sanctum_token', response.data.token);
            var self=this;
            store.dispatch('setAuthUser');
            setTimeout(function (){
              self.$router.push(ROUTE_USER_HOME);
            },1000);
          }).finally(()=>{
            this.isLoading=false;
          });
        }
    },
    created() {
        this.makingLoggedIn();
    }
}
</script>
<style>

#loading {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 3px solid rgb(65 84 241);
    border-radius: 50%;
    border-top-color: #0884e8;
    animation: spin 1s ease-in-out infinite;
    -webkit-animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { -webkit-transform: rotate(360deg); }
}
@-webkit-keyframes spin {
    to { -webkit-transform: rotate(360deg); }
}
</style>
