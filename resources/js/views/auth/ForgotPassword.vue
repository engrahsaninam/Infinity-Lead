<template>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-5 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="/" class="logo d-flex align-items-center w-auto">
                                    <img src="logo.png" alt="" width="250">
                                </a>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body p-5">
                                    <div class="pt-4 pb-2">
                                        <h4 class=" text-center pb-0 fs-4">Forgot your password?</h4>
                                        <p class="text-center ">Reset your password by entering your account's email address below. 🔑</p>
                                    </div>

                                    <form class="row g-3 " method="POST" @submit.prevent="login(form)">
                                      <div class="form-group col-md-12">
                                        <label class="form-label font-weight-light">Email</label>
                                        <div class="input-group position-relative">
                                          <input
                                              type="text"
                                              v-model="form.email"
                                              placeholder="Type your email"
                                              class="form-control  "
                                              :class="{ 'is-invalid': errors.email }"
                                              @input="errors.email = ''"
                                          />
                                          <div v-if="errors.email" class="d-block invalid-tooltip">
                                            {{ errors.email }}
                                          </div>
                                        </div>
                                      </div>
                                        <div class="col-md-12 mt-3">
                                            <button class="btn btn-primary w-100 sign-in-button" :disabled="processing">
                                                <span v-if="!processing">Send me email instructions</span>
                                                <span v-else>Processing ...</span>
                                            </button>
                                        </div>

                                        <div class="col-12 text-center mt-4">
                                            <p class=" mb-0 ">Back to log in? <router-link :to="ROUTE_LOGIN()">Click here</router-link></p>
                                        </div>
                                    </form>
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
import {ROUTE_LOGIN,notify} from "../../constants.js";
import FormControlInput from "../components/FormControlInput.vue";
import Loader from "../components/Loader.vue";

export default {
    name: 'ForgotPassword',
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
      ROUTE_LOGIN() {
        return ROUTE_LOGIN
      },
      async login(data) {
        this.processing=true;
        await this.axios.post('forgot-password', data).then((response) => {
          notify(response.data.message);
          setTimeout(() => {
            this.$router.push(ROUTE_LOGIN);
          }, 2500);
        }).catch((error) => {
          console.log(error);
          this.errors=this.$flattenErrors(error);
        }).finally((ms)=>{
          this.processing=false;
        });
      },
    },
    created() {
      console.log('forgot password');
    }
}
</script>
