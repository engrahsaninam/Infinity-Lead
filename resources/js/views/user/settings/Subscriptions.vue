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
                <Breadcrumb :items="[ROUTE_USER_API_SUBSCRIPTIONS()]" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <Pills />
                  <div class="col-md-6">
                    <h5 class="pt-5">Your subscription</h5>
                    <p>
                      Thank you for being with us, you are currently subscribed to <b> {{
                        subscriptions?.active?.plan?.name }} </b> plan.
                      Your next invoice will be billed in <b>{{ subscriptions?.active?.end_on['human_diff'] }}</b> , on
                      <b>
                        {{ subscriptions?.active?.end_on['Y'] }}-{{ subscriptions?.active?.end_on['m'] }}-{{
                        subscriptions?.active?.end_on['d'] }}
                        {{ subscriptions?.active?.end_on['h'] }}: {{ subscriptions?.active?.end_on['i'] }} {{
                        subscriptions?.active?.end_on['A'] }}
                      </b>.
                    </p>
                    <button class="btn btn-danger mb-4 mr-2" @click="cancel=true"><i class="pi pi-disable"></i> Cancel
                      now</button>
                    <a class="btn btn-danger  mb-4" href="#" v-if="subscriptions?.active?.recurring === 1"
                      @click="_recurring(subscriptions?.active?.id, 'disable')">
                      <i class="pi pi-ban"></i>
                      Disable Recurring
                    </a>
                    <a class="btn btn-secondary  mb-4" href="#" v-if="subscriptions?.active?.recurring === 0"
                      @click="_recurring(subscriptions?.active?.id, 'enable')">
                      <i class="pi pi-play"></i>
                      Enable Recurring
                    </a>
                  </div>
                  <div class="col-md-12 table-responsive ">
                    <table class="table table-stripped" v-if="subscriptions.all">
                      <thead>
                        <tr>
                          <th>Plan</th>
                          <th>Subscribed at</th>
                          <th>Subscription end</th>
                          <th>Price</th>
                          <th>Method</th>
                          <th>Status</th>
                          <th>Credits</th>
                          <th class="text-right">Invoice</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="subscription in subscriptions.all">
                          <td><b>{{ subscription?.plan?.name }}</b></td>
                          <td>{{ subscription.start_on['human_diff'] }}</td>
                          <td>{{ subscription.end_on['human_diff'] }}</td>
                          <td>{{ subscription.price }} {{ subscription.code }}</td>
                          <td>{{ subscription?.payment_method?.name }}</td>
                          <td>
                            <small :class="{'text-success': subscription?.status === 'active' }">
                              <b>{{ subscription?.status.toUpperCase() }}</b>
                            </small>
                          </td>
                          <td>
                            {{ subscription?.plan?.credits }}
                          </td>
                          <td class="text-right">
                            <a target="_blank"
                              :href="'/invoice/print/' + subscription.id + '/' + authUser.created_at + '/' + authUser.id"
                              class="font-weight-bold">
                              {{ subscription.cid }}
                            </a>
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
    </div>
  </div>

  <Confirm :show="recurring.toggle" heading="Warning" :message="recurring.message" @confirm="actionRecurring"
    @cancel="_closeRecurring" />

  <Confirm :show="cancel" heading="Warning" message="You're about to cancel subscription before its due date. Are you sure you want to cancel it? This action cannot be undone" @confirm="cancelSubscription" @cancel="cancel=false" />

</template>
<script>
import store from "../../../store.js";
import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Pills from "./components/Pills.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import { notify, ROUTE_USER_API_SUBSCRIPTIONS, ROUTE_USER_CHANGE_PASSWORD, ROUTE_USER_CHANGE_PROFILE, ROUTE_USER_HELP, ROUTE_USER_INACTIVE } from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import { mapGetters } from "vuex";
import Confirm from "../../components/Confirm.vue";

export default {
  name: 'Subscriptions',
  components: { Breadcrumb, FormControlInput, Pills, Sidebar, Header,Confirm },
  data() {
    return {
      cancel:false,
      subscriptions:[],
      showThisPage: true,
      processing: false,
      edit_profile_mode: false,
      payment_methods: [],
      selectedPaymentMethod: '',
      newSubForm: '',
      card: {},
      stripe: '',
      recurring: {
        id: '',
        toggle: false,
        message: '',
      },
    }
  },
  methods: {
    ROUTE_USER_API_SUBSCRIPTIONS() {
      return ROUTE_USER_API_SUBSCRIPTIONS
    },

    _closeRecurring() {
      this.recurring = {
        id: '',
        toggle: false,
        message: '',
      };
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
    fetchBanks() {
      var self = this;
      this.axios.post('user/bank-accounts/banks').then((response) => {
        this.payment_methods = response.data.data;
        this.payment_methods.forEach(function (method) {
          if (method.preferred == 1) {
            self.selectedPaymentMethod = method.id;
          }
        });
      })
    },
    fetchSubscriptions(){
      this.processing = true;
      this.axios.post('user/subscriptions/fetch').then((response) => {
        this.subscriptions = response.data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },

    actionRecurring() {
      this.processing = true;
      this.axios.post(`user/subscriptions/recurring`, this.recurring).then((response) => {
        notify(response.data.message);
        this.fetchSubscriptions();
        this._closeRecurring();
      }).catch(({ error }) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
        this._closeRecurring();
      });
    },

    ROUTE_USER_INACTIVE() {
      return ROUTE_USER_INACTIVE;
    },
    _recurring(id, action) {
      this.recurring.id = id;
      this.recurring.toggle = true;
      if (action == 'disable') {
        this.recurring.message = 'Are you sure you want to disable recurring?';
      }
      if (action == 'enable') {
        this.recurring.message = 'Are you sure you want to enable recurring?';
      }
    },
    cancelSubscription(){
      this.processing = true;
      this.axios.post('user/subscriptions/cancel').then((response) => {
        notify(response.data.message);
        this.fetchSubscriptions();
        store.dispatch('setAuthUser');
        this.$router.push(this.ROUTE_USER_INACTIVE());
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    }
  },
  computed: {
    ...mapGetters(['authUser']),
  },
  mounted() {
    this.fetchBanks();
    this.fetchSubscriptions();
  },
}
</script>