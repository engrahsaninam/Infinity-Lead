<template>
  <div id="layout-wrapper">
    <Header />
    <Sidebar />
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Settings </h4>
                <Breadcrumb :items="[ROUTE_USER_BILLING_INFORMATION()]" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <Pills />
                  <div class="container">
                    <form @submit.prevent="updateBillingInfo()" class="row py-5">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h5>2. Billing information</h5>
                          <p>
                            Enter your billing information here and click next
                          </p>
                        </div>

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

                        <div class="col-md-12">
                          <button class="btn btn-success float-right" type="submit" :disabled="processing">
                            <span v-if="!processing"><i class="pi pi-refresh"></i> Update</span>
                            <span v-else>Processing ...</span>
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Pills from "./components/Pills.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import { notify, ROUTE_USER_BILLING_INFORMATION, ROUTE_USER_CHANGE_PASSWORD, ROUTE_USER_CHANGE_PROFILE } from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";

export default {
  name: 'Billing',
  components: {Breadcrumb, FormControlInput, Pills, Sidebar, Header},
  data() {
    return {
      processing:false,
      billing_user: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        country: ''
      },
    }
  },
  methods: {
    ROUTE_USER_BILLING_INFORMATION() {
      return ROUTE_USER_BILLING_INFORMATION
    },
    async fetchBillingInfo() {
      this.axios.post("/user/billing-information/fetch").then((response) => {
        this.billing_user = response.data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      })
    },
    async updateBillingInfo() {
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


  },
  created() {
    this.fetchBillingInfo();
  }
}
</script>