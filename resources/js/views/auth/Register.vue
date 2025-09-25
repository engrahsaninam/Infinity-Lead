<template>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="/" class="logo d-flex align-items-center w-auto">
                                    <img src="logo.png" alt="" width="250">
                                </a>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body p-5">

                                    <div class="pt-4 pb-2">
                                        <h4 class=" text-center pb-0 fs-4">Create a free account</h4>
                                        <p class="text-center ">InfinityLead can be used for free, forever. 😍</p>
                                    </div>

                                    <form class="row g-3 " method="POST" @submit.prevent="register(form)">
                                        <form-control-input  v-model="form.first_name" label="First Name" :col_md="12"/>
                                        <form-control-input  v-model="form.last_name" label="Last Name" :col_md="12"/>
                                        <form-control-input v-model="form.email" label="Email" :col_md="12"/>
                                        <div class="col-md-12">
                                          <div class="form-group mt-2">
                                            <label class="form-label" for="Password">Password</label>
                                            <div class="input-group">
                                              <input v-model="form.password" :type="passType" id="password" class="form-control" placeholder="****************">
                                              <div class="input-group-append">
                                                    <span class="input-group-text rounded-0" style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;" @click="togglePasswordVisibility()" >
                                                        <i class="pi " :class="{'pi-eye':passType === 'password', 'pi-eye-slash':passType === 'text'}"></i>
                                                    </span>
                                              </div>
                                            </div>
                                            <p class="small text-muted">Your password must be at least 8 characters in length, and contain at least one of each of the following: lower case letters (a-z), upper case letters (A-Z) and numbers (0-9).</p>
                                          </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <button 
                                                class="btn btn-primary w-100 sign-in-button" 
                                                :disabled="processing || verificationPending"
                                            >
                                                <span v-if="verificationPending">Please verify your email</span>
                                                <span v-else-if="!processing">Sign up free</span>
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
                                        <a href="/google/register/redirect" class="btn bg-white w-100 border py-2 px-5"><img src="./../../assets/img/google.png" width="20" height="20" alt=""> Sign up with Google</a>

                                      </div>
                                        <div class="col-12 text-center mt-3">
                                            <p class=" mb-0">Already have an account? <router-link to="login">Login</router-link></p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <p class=" mb-0">
                                By signing up, you agree to our
                                <a href="/terms-and-conditions">Terms of Service </a>
                                and our
                                <a href="/privacy-policy">Privacy Policy.</a>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

</template>

<script>
import {notify,  ROUTE_USER_HOME} from "../../constants.js";
import store from "../../store.js";
import FormControlInput from "../components/FormControlInput.vue";
import Loader from "../components/Loader.vue";

export default {
    name: 'Register',
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
            verificationPending: false
        }
    },
    methods: {
        togglePasswordVisibility() {
            this.passType = this.passType === 'password' ? 'text' : 'password';
        },
        //...mapActions(['setAuthUser']),
        register(data) {
            this.processing=true;
            this.axios.post('register', data).then((response) => {

            localStorage.removeItem('impersonation_token');
            if (response.data.requires_verification) {
                localStorage.removeItem('user');
                localStorage.removeItem('sanctum_token');
                localStorage.removeItem('expiration_date');
                notify(response.data.message);

                this.verificationPending = true;
                this.processing = false; 
            } else {
                notify('Account created successfully!');
                localStorage.removeItem('user');
                localStorage.removeItem('sanctum_token');
                localStorage.removeItem('expiration_date');
                const user=JSON.stringify(response.data.user);
                localStorage.setItem('user', user);
                var role=response.data.user.role;
                localStorage.setItem('role', role);
                localStorage.setItem('sanctum_token', response.data.token);
                const self=this;
                store.dispatch('setAuthUser');

                setTimeout(function (){
                    self.$router.push(ROUTE_USER_HOME);
                },1000);
            }
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally(()=>{
              this.processing=false;
            });
        },
    },
    created() {

    }
}
</script>
