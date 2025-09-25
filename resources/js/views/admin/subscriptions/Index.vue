<template>
  <div id="layout-wrapper">
    <Header />
    <AdminSidebar />
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer" />
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Subscriptions</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_SUBSCRIPTIONS()]" />
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-sm-3">
                      <div class="position-relative">
                        <input type="text" class="form-control search-input" v-model="table.search"
                          placeholder="Search here..." @input="this.list(true)">
                        <i class="pi pi-search search-icon"></i>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <tr v-if="subscriptions?.data?.length === 0">
                        <td colspan="100%" class="text-center">
                          <i class="text-muted">No subscriptions found.</i>
                        </td>
                      </tr>
                      <tbody v-for="list in subscriptions.data" :key="list.id" style="cursor: pointer;">

                        <tr v-if="replenish.id === list.id">
                          <td colspan="100%">
                            <div class="row align-items-center">
                              <FormControlInput v-model="replenish.credits" :col_md="6" />
                              <button class="btn btn-success" @click="updateReplenish">
                                Update
                              </button>
                              <button class="btn btn-secondary" @click="cancelReplenish">
                                Cancel
                              </button>

                            </div>
                          </td>
                        </tr>
                        <tr v-else>
                          <td>
                            <b>{{ list?.plan?.name }}</b>
                            <br>
                            <span class="text-muted">Subscribed by <b>{{ list?.user?.name }}</b></span>
                          </td>
                          <td>
                            {{ list.start_on['Y'] }}-{{ list.start_on['m'] }}-{{ list.start_on['d'] }}
                            {{ list.start_on['H'] }}:{{ list.start_on['i'] }} <br>
                            <span class="text-muted">Subscribed on</span>
                          </td>
                          <td>
                            {{ list.end_on['human_diff'] }} <br>
                            <span class="text-muted">Ends at</span>
                          </td>
                          <td>
                            {{ list.credits }}
                            <br>
                            <span class="text-muted">Credits</span>
                          </td>
                          <td>
                            {{ list.price }}
                            <br>
                            <span class="text-muted">{{ list.code }}</span>
                          </td>
                          <td>
                            {{ list?.payment_method?.name }}
                            <br>
                            <span class="text-muted">Method</span>
                          </td>
                          <td>
                            <div v-if="list.status === SUBSCRIPTION_PENDING()">
                              <button class="btn btn-sm btn-success"
                                @click="manageActionPopup('approve', list)">Approve</button>
                              |
                              <button class="btn btn-sm btn-danger"
                                @click="manageActionPopup('reject', list)">Reject</button>
                            </div>
                            <small v-else class="badge "
                              :class="{ 'btn-secondary': list.status === 'cancelled', 'btn-success': list.status != 'cancelled' }">
                              {{ list.status.toUpperCase() }}
                            </small>
                          </td>
                          <td>
                            <a target="_blank"
                              :href="'/invoice/print/' + list.id + '/' + list?.user?.created_at + '/' + list?.user?.id"
                              class="font-weight-bold">
                              {{ list.cid }}
                            </a>
                          </td>
                          <td>
                            <div v-if="list.status === 'active'">
                              <a class="btn btn-danger text-light float-left btn-sm ml-2" href="#"
                                v-if="list.recurring === 1" @click="_recurring(list.id, 'disable')">
                                <i class="pi pi-ban"></i>
                                Disable Recurring
                              </a>
                              <a class="btn btn-secondary text-light float-left btn-sm ml-2" href="#"
                                v-if="list.recurring === 0" @click="_recurring(list.id, 'enable')">
                                <i class="pi pi-play"></i>
                                Enable Recurring
                              </a>
                            </div>
                            <div class="dropdown float-left ml-2 " v-if="list.status === 'active'">
                              <button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-chevron-down"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="right: 0;">
                                <a class="dropdown-item" href="#" @click="_replenish(list)"
                                  v-if="list.status === 'active'">
                                  <i class="pi pi-plus"></i>
                                  Replenish Credits
                                </a>
                                <a class="dropdown-item" href="#" @click="_terminate(list)"
                                  v-if="list.status === 'active'">
                                  <i class="pi pi-ban"></i>
                                  Terminate
                                </a>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-header border-bottom-0">
                  <Pagination :list="page" :data="subscriptions" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Confirm :show="popup.show" heading="Warning" :message="popup.message" @confirm="actionSubscription"
    @cancel="cancelPopup" />

  <Confirm :show="recurring.toggle" heading="Warning" :message="recurring.message" @confirm="actionRecurring"
    @cancel="_closeRecurring" />
  <Confirm :show="terminate.toggle" heading="Warning" :message="terminate.message" @confirm="actionTerminate"
    @cancel="_closeTerminate" />

</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  notify, ROUTE_ADMIN_SUBSCRIPTIONS, ROUTE_USER_BLACKLIST,
  SUBSCRIPTION_ACTIVE,
  SUBSCRIPTION_PENDING,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Pagination from "../../layout/Paignation.vue";
import AdminSidebar from "../../layout/admin/AdminSidebar.vue";
import FormControlInput from "../../components/FormControlInput.vue";

export default {
  name: 'AdminSubscriptions',
  components: {
    AdminSidebar,
    Pagination,
    FormControlInput,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header
  },
  data() {
    return {
      replenish: {
        id: '',
        credits: '',
      },

      popup: {
        message: '',
        action: '',
        subscription: '',
        show: false,
      },
      showPopup: false,
      isLineLoading: false,
      shimmer: true,
      table: {
        records: 10,
        search: '',
        currentPage: 1,
        page: 1,
      },
      processing: false,
      recurring: {
        id: '',
        toggle: false,
        message: '',
      },
      terminate: {
        id: '',
        toggle: false,
        message: 'You\'re about to cancel subscription before its due date.Are you sure you want to cancel it? This action cannot be undone',
      },

    }
  },
  methods: {
    cancelReplenish() {
      this.replenish = {
        id: '',
        credits: '',
      };
    },
    _replenish(subscription) {
      this.replenish.id = subscription.id;
      this.replenish.credits = subscription.credits;
    },
    
    _terminate(subscription) {
      this.terminate.id = subscription.id;
      this.terminate.toggle=true;
    },

    manageActionPopup(action, subscription) {
      var message = '';
      if (action === 'approve') {
        message = 'Are you sure you want to approve this subscription?';
      } else if (action === 'reject') {
        message = 'Are you sure you want to reject this subscription?';
      }
      this.popup = {
        message: message,
        action: action,
        subscription: subscription,
        show: true,
      };
    },
    cancelPopup() {
      this.popup = {
        message: '',
        action: '',
        subscription: '',
        show: false,
      };
    },
    ROUTE_ADMIN_SUBSCRIPTIONS() {
      return ROUTE_ADMIN_SUBSCRIPTIONS
    },
    SUBSCRIPTION_PENDING() {
      return SUBSCRIPTION_PENDING
    },
    page(page = 1) {
      this.table.page = page;
      this.list();
    },


    async list(line = false) {
      if (line) { this.isLineLoading = true } else { this.shimmer = true; }
      await this.axios.post(`admin/subscriptions/fetch`, this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.subscriptions = data;
      }).finally(() => {
        this.isLineLoading = false;
        this.shimmer = false;
      });
    },
    actionSubscription() {
      this.processing = true;
      this.axios.post(`admin/subscriptions/action`, {
        id: this.popup.subscription.id,
        action: this.popup.action,
      }).then((response) => {
        notify(response.data.message);
        this.list();
      }).catch(({ error }) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
        this.cancelPopup();
      });
    },

    actionRecurring() {
      this.processing = true;
      this.axios.post(`admin/subscriptions/recurring`, this.recurring).then((response) => {
        notify(response.data.message);
        this.list();
        this._closeRecurring();
      }).catch(({ error }) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
        this._closeRecurring();
      });
    },
    
    actionTerminate() {
      this.processing = true;
      this.axios.post(`admin/subscriptions/terminate`, this.terminate).then((response) => {
        notify(response.data.message);
        this.list();
        this._closeTerminate();
      }).catch(({ error }) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
        this._closeTerminate();
      });
    },
    
    updateReplenish() {
      this.processing = true;
      this.axios.post(`admin/subscriptions/replenish`, this.replenish).then((response) => {
        notify(response.data.message);
        this.list();
        this.cancelReplenish();
      }).catch(({ error }) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
        this.cancelReplenish();
      });
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

    _closeRecurring() {
      this.recurring = {
        id: '',
        toggle: false,
        message: '',
      };
    },
    
    _closeTerminate() {
      this.terminate.id = '';
      this.terminate.toggle = false;
    }

  },
  created() {
    this.list();
  }
}
</script>
