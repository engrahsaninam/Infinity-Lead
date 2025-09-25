<template>
  <main>
    <div class="container">
      <section class="section reset-password min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
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
                    <h4 class="text-center pb-0 fs-4">Reset Your Password</h4>
                    <p class="text-center">Enter your new password below. 🔑</p>
                  </div>

                  <form class="row g-3" method="POST" @submit.prevent="resetPassword">
                    <div class="form-group col-md-12">
                      <label class="form-label font-weight-light">New Password</label>
                      <div class="input-group position-relative">
                        <input
                            type="password"
                            v-model="form.password"
                            placeholder="Type your new password"
                            class="form-control"
                            :class="{ 'is-invalid': errors.password }"
                            @input="errors.password = ''"
                        />
                        <div v-if="errors.password" class="d-block invalid-tooltip">
                          {{ errors.password }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-12">
                      <label class="form-label font-weight-light">Confirm Password</label>
                      <div class="input-group position-relative">
                        <input
                            type="password"
                            v-model="form.password_confirmation"
                            placeholder="Confirm your password"
                            class="form-control"
                            :class="{ 'is-invalid': errors.password_confirmation }"
                            @input="errors.password_confirmation = ''"
                        />
                        <div v-if="errors.password_confirmation" class="d-block invalid-tooltip">
                          {{ errors.password_confirmation }}
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12 mt-3">
                      <button class="btn btn-primary w-100 sign-in-button" :disabled="processing">
                        <span v-if="!processing">Reset Password</span>
                        <span v-else>Processing</span>
                      </button>
                    </div>

                    <div class="col-12 text-center mt-4">
                      <p class="mb-0">Back to log in? <router-link :to="ROUTE_LOGIN()">Login</router-link></p>
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
import { ROUTE_LOGIN,notify } from "../../constants.js";

export default {
  name: 'PasswordReset',
  data() {
    return {
      isLoading: true,
      passType: 'password',
      form: {
        email: '',
        password: '',
        password_confirmation: ''
      },
      errors: {},
      processing: false,
      token: '', // Store the token for password reset
      email: '', // Store the email
    };
  },
  methods: {
    ROUTE_LOGIN() {
      return ROUTE_LOGIN;
    },
    async resetPassword() {
      this.processing = true;

      const resetData = {
        token: this.token,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation
      };

      try {
        const response=await this.axios.post('reset-password', resetData);
        notify(response.data.message);
        setTimeout(() => {
          this.$router.push(ROUTE_LOGIN);
        }, 2500);
      } catch (error) {
        this.errors = this.$flattenErrors(error);
      } finally {
        this.processing = false;
      }
    }
  },
  created() {
    // Extract email and token from the URL query parameters
    this.token = this.$route.params.token;
    if (!this.token ) {
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
/* You can add custom styles here */
</style>
