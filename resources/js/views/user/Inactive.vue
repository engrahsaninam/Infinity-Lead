<template>
  <div id="layout-wrapper">
    <Header />
    <div class="container pt-5 mt-5" v-if="showPendingPage === true">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body p-5">
              <h3 class="mt-3">Payment verification is in progress </h3>
              <p class="text-muted pt-4">
                Thank you for the confirmation. We are going to check your payment shortly. Once the payment is
                verified, your account will be immediately activated and you can get started with the application
                features
              </p>
              <p class="text-right mt-3">
                <button class="btn btn-secondary" @click="refreshOfflineStatus">Refresh Transaction Status</button>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h4 class="text-primary ct-card-header">Your order</h4>
              <p>
                You’re subscribing to plan "<strong>{{ selectedPlan?.name }}</strong>", and your subscription
                will
                be due on {{ new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0,
                16).replace('T', ' ') }}
              </p>

              <hr>
              <table class="table table-borderless table-hover">
                <tbody>
                  <tr>
                    <td>Price</td>
                    <td class="text-right">{{ selectedPlan?.currency?.format.replace('{PRICE}', selectedPlan?.price) }}
                    </td>
                  </tr>
                  <tr>
                    <td>Credits</td>
                    <td class="text-right">{{ selectedPlan?.credits }}</td>
                  </tr>
                  <tr>
                    <td><b>Total</b></td>
                    <td class="text-right">{{ selectedPlan?.currency?.format.replace('{PRICE}', selectedPlan?.price) }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <div class="alert alert-success">
                        <span class="mdi mdi-alert-circle-outline"></span>
                        By clicking the Proceed with payment button, you agree to our <b>Term of Use</b> and
                        <b>Privacy
                          Policy</b>.
                      </div>
                    </td>
                  </tr>

                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container pt-5 mt-5" v-show="!showPendingPage">
      <div class="row" v-if="!selectedPlan">
        <div class="col-12">
          <div class="page-title-box ">
            <h4>Choose a plan to subscribe </h4>
            <p>
              You need to subscribe to a plan in order to experience the application. Choose one of plans
              below to
              subscribe for using the app:
            </p>
          </div>
        </div>
        <div class="col-md-4" v-for="plan in PLANS" v-if="PLANS.length > 0" :key="plan.id">
          <div class="card mb-3" v-if="plan.currency">
            <div class="card-header">
              <h5 class="card-title pt-3 h4">{{ plan.name }}</h5>
            </div>
            <div class="card-body">
              <p class="card-text">{{ plan.description }}</p>
              <center class="mb-5">
                <p class="h1 text-muted">
                  {{ plan.currency.format.replace('{PRICE}', plan.price) }} {{ plan.symbol }}
                </p>
                <span class="rounded p-2 h4">{{ plan.currency.code }}</span>
              </center>
            </div>
            <div class="card-footer">
              <button class="btn btn-secondary w-100" @click="selectedPlan = plan">Select Plan</button>
            </div>
          </div>
        </div>
        <div v-else class="col-md-12">
          <div class="card" style="height: 200px;">
            <div class="card-body text-center d-flex align-items-center justify-content-center">
              <h5 class="text-italic font-weight-normal">
                <i class="text-muted">
                  <span class="mdi mdi-alert-circle-outline"></span>
                  No plan found</i>
              </h5>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row" v-show="selectedPlan">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body pb-0">
                <h5 class="float-left">1. You have chosen "<strong>{{ selectedPlan?.name }}</strong>" plan.</h5>
                <button class="btn btn-danger float-right" :disabled="assigned_by_admin" @click="changePlan()">Change Plan?</button>
              </div>
              <div class="card-body pt-0">
                <p class="text-muted">
                  You’re subscribing to plan "<strong>{{ selectedPlan?.name }}</strong>", and your subscription
                  will
                  be due on {{ new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0,
                  16).replace('T', ' ') }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-12">

            <div class="card">
              <div class="card-body">




                <form @submit.prevent="updateBillingInfo()">

                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5>2. Billing information</h5>
                      <p>
                        Enter your billing information here and click next
                      </p>
                    </div>
                    <button class="btn btn-success float-right" type="submit" :disabled="processing">
                      <span v-if="!processing">Update</span>
                      <span v-else>Processing ...</span>
                    </button>
                  </div>

                  <div class="row">
                    <form-control-input :col_md="6" v-model="billing_user.first_name" label="First Name"
                      placeholder="Enter your first name"></form-control-input>
                    <form-control-input :col_md="6" v-model="billing_user.last_name" label="Last Name"
                      placeholder="Enter your last name"></form-control-input>
                    <form-control-input :col_md="6" v-model="billing_user.email" label="Email"
                      placeholder="Enter your email"></form-control-input>
                    <form-control-input :col_md="6" v-model="billing_user.phone" label="Phone"
                      placeholder="Enter your phone"></form-control-input>
                    <form-control-input :col_md="6" v-model="billing_user.address" label="Address"
                      placeholder="Enter your  address"></form-control-input>
                    <FormControlSelect2 :col_md="6" v-model="billing_user.country" label="Country"
                      :options="_countries" />


                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-12" v-if="billing_user.id">
            <div class="card">
              <div class="card-body">
                <h5>3. Payment method</h5>
                <h6>Choose your preferred payment method</h6>
                <table class="table table-striped mt-5">
                  <tr v-for="method in METHODS" :key="method.id" style="cursor: pointer;"
                    @click="selectedMethod = method" :class="{ 'bg-light': selectedMethod.id === method.id }">
                    <td>
                      <img :src="method.icon" class="img-fluid" width="30" height="30" alt="">
                    </td>
                    <td>{{ method.name }}</td>
                    <td>{{ method.description }}</td>
                    <td>
                      <span v-if="selectedMethod.id === method.id"><i
                          class="mdi mdi-check-circle text-success h4"></i></span>
                    </td>
                  </tr>
                  <tr v-if="METHODS.length === 0">
                    <td class="text-center text-muted pt-5">
                      <span class="mdi mdi-alert-circle-outline"></span>
                      No payment method found!
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8" v-show="selectedMethod && billing_user.id">
            <div v-if="selectedMethod.slug === 'offline'">
              <div class="card">
                <div class="card-body">
                  <h5>Payment Instructions</h5>
                  <p v-html="selectedMethod.instructions"></p>
                </div>
              </div>
            </div>
            <div v-show="selectedMethod.slug === 'stripe'">

              <div class="mt-5 d-flex justify-content-between  p-2 ">
                <div>
                  <button class="btn btn-sm mr-2 " @click="changeOption('select')"
                    :class="(addOrSelectPm === 'select' ? 'btn-primary ' : 'btn-light')">Select Card</button>
                  <button @click="changeOption('add')" class="btn btn-sm"
                    :class="(addOrSelectPm === 'add' ? 'btn-primary' : 'btn-light')">Add Card</button>
                </div>

              </div>
              <hr class="m-0">
              <div class="container" v-show="addOrSelectPm === 'select'">
                <div class="row border-bottom p-2 cursor-pointer align-items-center"
                  :class="selectedBank == payment_method.id ? 'bg-light' : 'bg-white'"
                  v-for="(payment_method, i) in payment_methods" @click="selectPaymentMethod(payment_method)">
                  <div class="col-md-9 ct-p">
                    <img src="../../assets/Visa.png" v-if="payment_method.card_type === 'Visa'" width="30">
                    <img src="../../assets/Mastercard.png" v-if="payment_method.card_type === 'MasterCard'" width="30">
                    <img src="../../assets/AmericanExpress.png" v-if="payment_method.card_type === 'American Express'"
                      width="30">
                    <h6 class="m-0 pr-5 font-weight-normal">{{ payment_method.card_number }}</h6>
                  </div>
                  <div class="col-md-3 d-flex justify-content-end">
                    <p class="m-0 float-end" style="color: grey">{{ payment_method.expiry }}</p>
                    <i class="fa fa-check-circle mt-1 ml-3 "
                      :class="selectedBank == payment_method.id ? 'text-success' : 'text-white'"></i>
                  </div>
                </div>
                <div class=" col-md-12 text-center">
                  <p v-if="payment_methods.length == 0" class="mt-3">No payment method.</p>
                </div>
              </div>

              <div class="container" v-show="addOrSelectPm === 'add'">
                <form @submit.prevent="addPaymentMethod()">
                  <div class="row py-5">
                    <div class="col-12">
                      <div id="card-element"></div>
                    </div>
                    <div class="col-12 text-right mt-2">
                      <button class="btn btn-primary ct-p btn-sm" :disabled="isLoading">{{ isLoading ? 'Loading...' : 'Save' }}</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-4" v-if="selectedMethod && selectedPlan && billing_user.id">
            <div class="card">
              <div class="card-body">
                <h4 class="text-primary ct-card-header">Your order</h4>
                <p>
                  You’re subscribing to plan "<strong>{{ selectedPlan.name }}</strong>", and your subscription
                  will
                  be due on {{ new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0,
                  16).replace('T', ' ') }}
                </p>

                <hr>
                <table class="table table-borderless table-hover">
                  <tbody>
                    <tr>
                      <td>Price</td>
                      <td class="text-right">{{ selectedPlan.currency.format.replace('{PRICE}', selectedPlan.price) }}
                      </td>
                    </tr>
                    <tr>
                      <td>Credits</td>
                      <td class="text-right">{{ selectedPlan.credits }}</td>
                    </tr>
                    <tr>
                      <td><b>Total</b></td>
                      <td class="text-right">{{ selectedPlan.currency.format.replace('{PRICE}', selectedPlan.price) }}
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <div class="alert alert-success">
                          <span class="mdi mdi-alert-circle-outline"></span>
                          By clicking the Proceed with payment button, you agree to our <b>Term of Use</b> and
                          <b>Privacy
                            Policy</b>.
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" class="text-right">
                        <button class="btn  btn-primary " @click="subscribe()" :disabled="isLoadingSubBtn">
                          <span v-if="selectedMethod.slug === 'offline'">
                            <span v-if="selectedPlan.price === 0">Get Started</span>
                            <span v-else>Claim Payment</span>
                          </span>
                          <span v-if="selectedMethod.slug === 'stripe'">
                            Checkout
                          </span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Header from "../layout/Header.vue";
import { COUNTRIES, notify, ROUTE_USER_API_SUBSCRIPTIONS, ROUTE_USER_HOME } from "../../constants.js";
import FormControlInput from '../components/FormControlInput.vue';
import FormControlSelect2 from "../components/FormControlSelect2.vue";
export default {
  name: 'Inactive',
  components: { Header, FormControlInput, FormControlSelect2 },
  data() {
    return {
      payBtnLoading: false,
      showPendingPage: true,
      processing: false,
      edit_profile_mode: false,
      payment_methods: [],
      selectedBank: '',
      selectedMethod: '',
      card: {},
      stripe: '',
      PLANS: [],
      METHODS: [],
      selectedPlan: '',
      assigned_by_admin:false,
      billing_user: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        country: ''
      },
      addOrSelectPm:'add',
      PUBLISH_KEY: '',
      _countries: COUNTRIES.map((item) => {
        return { key: item, value: item };
      }),
    }
  },
  methods: {
    ROUTE_USER_API_SUBSCRIPTIONS() {
      return ROUTE_USER_API_SUBSCRIPTIONS
    },
    changePlan(){
      if(!this.assigned_by_admin){
        this.selectedPlan = ''
      }
    },
    async addPaymentMethod() {
      this.isLoading=true;
      const { token, error } = await this.stripe.createToken(this.card, {
        name: this.cardHolderName,
      });

      if (error) {
        this.isLoading = false;
        console.error(error.message);
      } else {
        this.savePaymentMethod(token);
      }
    },
    async updateProfile() {
      this.processing = true;
      this.user_error = true;
      await this.axios.post('user/profile/help', this.user).then(({ data }) => {
        notify(data.message);
        this.toggleMode();
      }).catch((error) => {
        this.user_error = this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },

    changeOption(option) {
      this.addOrSelectPm=option;
    },
    selectPaymentMethod(bank){
      this.selectedBank=bank.id;
    },
    async checkPendingOffline() {
      this.axios.post("/user/bank-accounts/offline-payment-pending").then((response) => {
        this.showPendingPage = response.data.data.status;
        this.selectedPlan =response.data.data.plan;
      });
    },
    
    async refreshOfflineStatus() {
      window.location.reload();
    },

    async savePaymentMethod(token) {
      this.isLoading = false;
      this.axios.post("/user/bank-accounts/stripe-store", token).then((response) => {
        notify('Payment method added successfully!');
        this.initializeStripe();
        this.fetchBanks();
        this.changeOption('select');
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.isLoading = false;
      });
    },

    async fetchBillingInfo() {
      this.axios.post("/user/billing-information/fetch").then((response) => {
        this.billing_user = response.data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      })
    },
    async updateBillingInfo(token) {
      this.processing = true;
      this.axios.post("/user/billing-information/update", this.billing_user).then((response) => {
        notify(response.data.message);
        this.fetchBillingInfo();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },


    fetchBanks() {
      var self = this;
      this.axios.post('user/bank-accounts/banks').then((response) => {
        this.payment_methods = response.data.data;
        this.payment_methods.forEach(function (method) {
          if (method.preferred == 1) {
            self.selectedBank = method.id;
          }
        });
      })
    },
    fetchPlans() {
      this.axios.post('user/bank-accounts/plans').then((response) => {
        this.PLANS = response.data.data;
        this.fetchAssignedPlan();
      })
    },
    fetchAssignedPlan(){
      this.axios.post('user/bank-accounts/assigned').then((response) => {
        this.selectedPlan=response.data.data.plan;
        this.assigned_by_admin=true;
      })
    },
    fetchMethods() {
      this.axios.post('user/bank-accounts/methods').then((response) => {
        this.METHODS = response.data.data;
      })
    },

    async fetchPublishableKey() {
      await this.axios.post('user/bank-accounts/publish-key').then((response) => {
        this.PUBLISH_KEY = response.data.data;
      })
    },


    async initializeStripe() {
      await this.fetchPublishableKey();
      if(!this.PUBLISH_KEY){
        console.log('not found');
        return false;
      }
      // TODO: fetch publishable key from stripe 
      this.stripe = Stripe("pk_test_51JZ9PYSECWbQyTIWVDeGpzNicEH5zTrAa0ffrHcVeuoyCdQQTv4B7eNHlcIa9w5glmIPSIBKktzXk5ucMRLoWrm1005UFACPYq");
      const elements = this.stripe.elements();

      const style = {
        base: {
          color: "#32325d",
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: "antialiased",
          fontSize: "16px",
          "::placeholder": {
            color: "#aab7c4",
          },
        },
        invalid: {
          color: "#fa755a",
          iconColor: "#fa755a",
        },
      };

      this.card = elements.create("card", { style });
      this.card.mount("#card-element");
      this.isLoading = false;
    },


    async handleSubmit() {
      this.payBtnLoading = true;
      if (this.selectedMethod.slug !== 'stripe') {
        this.subscribe();
        return;
      }
      const { token, error } = await this.stripe.createToken(this.card, {
        name: this.cardHolderName,
      });

      if (error) {
        notify(error.message);
        this.payBtnLoading = false;
      } else {
        this.subscribe(token);
      }
    },
    async subscribe(token = '') {
      const data = {
        ...token,
        plan: this.selectedPlan,
        method: this.selectedMethod,
        bank: this.selectedBank,
      };
      this.axios.post("user/bank-accounts/process-payment", data).then((response) => {
        notify(response.data.message);
        var user = JSON.stringify(response.data.data);
        localStorage.setItem('user', user);
        this.checkPendingOffline();
        this.$router.push(this.ROUTE_USER_HOME());
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.payBtnLoading = false;
      });
    },
    ROUTE_USER_HOME() {
      return ROUTE_USER_HOME;
    },

  },
  created() {
  },
  mounted() {
    const stripeScript = document.createElement("script");
    stripeScript.src = "https://js.stripe.com/v3/";
    document.head.appendChild(stripeScript);

    stripeScript.onload = () => {
      this.initializeStripe();
    };
    this.fetchBanks();
    this.fetchPlans();
    this.fetchMethods();
    this.fetchBillingInfo();
    this.checkPendingOffline();
  },
}
</script>