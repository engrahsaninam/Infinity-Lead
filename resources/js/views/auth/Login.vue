<template>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-md-5 d-flex flex-column align-items-center justify-content-center">
                            <div class=" justify-content-center py-4">
                              <a href="/" class="logo d-flex align-items-center w-auto">
                                <img src="/logo.png" alt="" width="250">
                              </a>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body p-5">
                                    <div class="pt-4 pb-2">
                                        <h4 class=" text-center pb-0 fs-4">Login</h4>
                                        <p class="text-center ">Welcome back 👋</p>
                                    </div>
                                    <form class="row " method="POST" @submit.prevent="login(form)">
                                        <form-control-input v-model="form.email" label="Email" :col_md="12" id="email"/>
                                        <div class="col-md-12">
                                          <div class="form-group mt-2">
                                            <label class="form-label" for="Password">Password</label>
                                            <div class="input-group">
                                              <input v-model="form.password" :type="passType" id="password" placeholder="****************" class="form-control">
                                              <div class="input-group-append">
                                                    <span class="input-group-text rounded-0" style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;" @click="togglePasswordVisibility()" >
                                                        <i class="pi " :class="{'pi-eye':passType === 'password', 'pi-eye-slash':passType === 'text'}"></i>
                                                    </span>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      <div class="col-md-12">
                                        <router-link :to="ROUTE_FORGOT_PASSWORD()" class="float-right">Forgot Password?</router-link>
                                      </div>
                                      <div class="col-md-12">
                                        <button class="btn btn-primary w-100 sign-in-button" :disabled="processing">
                                          <span v-if="!processing">Sign in</span>
                                          <span v-else><i class="pi pi-spinner-dotted pi-spin"></i> Processing</span>
                                        </button>
                                      </div>
                                        <div class="col-md-12">
                                            <div class="separator">
                                                <div class="line"></div>
                                                <h6 class="px-3 small mt-2 text-muted" >OR</h6>
                                                <div class="line"></div>
                                            </div>
                                        </div>
                                      <div class="col-md-12">
                                        <a href="/google/login/redirect" class="btn bg-white w-100 border py-2 px-5"><img src="./../../assets/img/google.png" width="20" height="20" alt=""> Login with Google</a>
                                      </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <p class=" mb-0">Don't have account? <router-link :to="ROUTE_REGISTER()">Create an account</router-link></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>

<script>
import {ROUTE_FORGOT_PASSWORD, ROUTE_USER_HOME, ROUTE_REGISTER,notify, ROLE_ADMIN, ROUTE_ADMIN_HOME, ROLE_USER} from "../../constants.js";
import { mapActions } from 'vuex';
import store from "../../store.js";
import FormControlInput from "../components/FormControlInput.vue";
import Loader from "../components/Loader.vue";

export default {
    name: 'Login',
    components: {Loader, FormControlInput},
    data() {
        return {
            isLoading:true,
            passType: 'password',
            form: {
                email: '',
                password: '',
            },
            errors: '',
            processing:false,
        }
    },
    methods: {
        ROUTE_FORGOT_PASSWORD() {
            return ROUTE_FORGOT_PASSWORD
        },
        ROUTE_REGISTER() {
            return ROUTE_REGISTER
        },
        togglePasswordVisibility() {
            this.passType = this.passType === 'password' ? 'text' : 'password';
        },
        //...mapActions(['setAuthUser']),
        login(data) {
            this.errors = '';
            this.processing=true;
            this.axios.post('login', data).then((response) => {
                localStorage.removeItem('impersonation_token');
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
                    if(role === ROLE_ADMIN){
                        self.$router.push(ROUTE_ADMIN_HOME);
                    }
                    if(role === ROLE_USER){
                        self.$router.push(ROUTE_USER_HOME);
                    }
                },1000);
            }).catch((error) => {
                this.processing=false;
                this.errors = this.$flattenErrors(error);
            });
        },
    },
    created() {

    }
}
</script>
<style>
.separator{
    display:flex;
    align-items: center;
}

.separator .line{
    height: 1px;
    flex: 1;
    background-color: #dee2e6;
}
</style>
