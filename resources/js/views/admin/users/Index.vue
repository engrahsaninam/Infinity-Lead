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
                <h4>Users</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_CUSTOMERS()]" />
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
                    <div class="col-md-9 text-right">
                      <router-link :to="ROUTE_ADMIN_CUSTOMERS_CREATE()"
                        class="btn btn-primary btn-rounded waves-effect waves-light text-light"><i class="mdi mdi-plus me-1"></i>
                        Create Customer</router-link>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <thead>
                        <tr>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Active Plan</th>
                          <th>Profile</th>
                          <th>Account</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="users.data.length === 0">
                          <td colspan="100%" class="text-center">
                            <i class="text-muted">No user found.</i>
                          </td>
                        </tr>
                        <tr v-for="list in users.data" :key="list.id" style="cursor: pointer;">
                          <td>{{ list.first_name }}</td>
                          <td>{{ list.last_name }}</td>
                          <td>{{ list.email }}</td>
                          <td>
                            <b v-if="list.active_subscription && list.active_subscription.plan" class="text-success">
                              {{ list.active_subscription.plan.name }}
                            </b>
                            <b v-else-if="list.assigned_subscription && list.assigned_subscription.plan"
                              class="text-secondary" title="Assigned Subscription by Admin">
                              {{ list.assigned_subscription.plan.name }}
                            </b>
                            <span v-else class="text-danger">Not subscribed</span>
                          </td>
                          <td>
                            <img :src="list.profile" class="img-fluid img-thumbnail" width="50" height="50"
                              v-if="list.profile" alt="user-profile">
                          </td>
                          <td>
                            <span v-if="list.status === 1">Active</span>
                            <span v-else-if="list.status === 2">Restricted</span>
                            <span v-else>Inactive</span>
                          </td>
                          <td>
                            <router-link :to="ROUTE_ADMIN_CUSTOMERS_CREATE(list.id)"
                              class="btn btn-sm btn-success float-left text-light"><i class="pi pi-pencil"></i></router-link>

                            <button class="btn btn-primary btn-sm ml-2 float-left " title="Login as Customer"
                              @click="login(list.id)">
                              <small> <i class="pi pi-sign-in"></i></small>
                            </button>

                            <div class="dropdown float-left ml-2 ">
                              <button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-chevron-down"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item text-success" href="#" @click="openModal(list.id)">
                                  <i class="pi pi-verified"></i>
                                  Assign Plan
                                </a>
                                <a class="dropdown-item text-danger" href="#" v-if="list.status === 1"
                                  @click="disable(list.id, 'disable')">
                                  <i class="pi pi-ban"></i>
                                  Disable
                                </a>
                                <a class="dropdown-item text-primary" href="#" v-if="list.status === 2"
                                  @click="disable(list.id, 'enable')">
                                  <i class="pi pi-play"></i>
                                  Enable
                                </a>
                                <router-link class="dropdown-item"
                                  :to="ROUTE_ADMIN_CUSTOMERS_CREATE_SUBSCRIPTION(list.id, 'subscriptions')">
                                  <i class="mdi mdi-check"></i> Subscriptions</router-link>
                                <a class="dropdown-item text-danger" href="#" @click="____delete(list.id)">
                                  <i class="pi pi-trash"></i>
                                  Delete
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
                  <Pagination :list="page" :data="users" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Transition name="bounce">
    <dialog ref="modal" @click.self="closeModal" v-if="assign.show" class="col-md-8 top-0">
      <div class="container-fluid">
        <form @submit.prevent="_assign(assign)">
          <div class="row">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">Assign Plan</h5>

              <h5 class="float-right" @click="closeModal"><i class="pi pi-times"></i></h5>
            </div>
            <div class="col-md-12 pb-3 mb-3 border-bottom">
              Assigning plan can not be un-done so make sure you want to assign. This will cancel current subscription
            </div>
            <div class="col-md-4" v-for="plan in PLANS" v-if="PLANS.length > 0" :key="plan.id" style="cursor: pointer;">
              <div class="card mb-3" v-if="plan.currency" @click="assign.plan_id = plan.id">
                <div class="card-header" :class="{ 'bg-success': plan.id === assign.plan_id }">
                  <h5 class="card-title pt-2 h4" :class="{ 'text-light': plan.id === assign.plan_id }">{{ plan.name }}
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text">
                    <span class="float-right" v-if="plan.id === assign.plan_id">
                      <i class="h1 text-success pi pi-check-circle"></i>
                    </span>
                    <br>
                    {{ plan.description }}
                  </p>
                  <center class="mb-5">
                    <p class="h1 text-muted">
                      {{ plan.currency.format.replace('{PRICE}', plan.price) }} {{ plan.symbol }}
                    </p>
                    <span class="rounded p-2 h4">{{ plan.currency.code }}</span>
                  </center>

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
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                <span v-if="!processing">
                  Assign
                </span>
                <span v-else>Processing ...</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </dialog>
  </Transition>
  <Confirm :show="action.toggle" heading="Warning" :message="action.message" @confirm="_action" @cancel="closeAction" />
  <Confirm :show="__delete.toggle" heading="Warning" :message="__delete.message" @confirm="this.____del"
    @cancel="closeDelete" />
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  notify, ROUTE_ADMIN_CUSTOMERS, ROUTE_ADMIN_CUSTOMERS_CREATE,
  ROUTE_USER_HOME,
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
import { mapActions } from 'vuex';
import store from "../../../store.js";
import { mapGetters } from "vuex";

export default {
  name: 'Users',
  components: {
    AdminSidebar,
    Pagination,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header
  },
  data() {
    return {
      isLineLoading: false,
      shimmer: true,
      table: {
        records: 10,
        search: '',
        currentPage: 1,
        page: 1,
      },
      PLANS: [],
      assign: {
        show: false,
        user_id: '',
        plan_id: '',
      },
      processing: false,
      action: {
        id: '',
        toggle: false,
        message: '',
      },
      __delete: {
        id: '',
        toggle: false,
        message: 'Are you sure to delete user?',
      },

    }
  },
  methods: {
    closeAction() {
      this.action.toggle = false;
    },

    closeDelete() {
      this.__delete.toggle = false;
    },
    ROUTE_ADMIN_CUSTOMERS_CREATE_SUBSCRIPTION(id, type) {
      let route = ROUTE_ADMIN_CUSTOMERS_CREATE.replace(':id?', id || '');
      const queryParams = new URLSearchParams();
      if (type) queryParams.append('type', type);
      return `${route}?${queryParams.toString()}`;
    },

    disable(id, action) {
      this.action.id = id;
      this.action.toggle = true;
      if (action == 'disable') {
        this.action.message = 'Are you sure you want to disable?';
      }
      if (action == 'enable') {
        this.action.message = 'Are you sure you want to enable?';
      }
    },
    ____delete(id) {
      this.__delete.id = id;
      this.__delete.toggle = true;
    },
    closeModal() {
      this.assign.show = false;
      this.$refs.modal?.close();
      this.assign.user_id = '';
    },
    openModal(id) {
      this.assign.user_id = id;
      this.assign.show = true;
      this.$nextTick(() => {
        this.$refs.modal?.showModal();
      });
    },
    ROUTE_ADMIN_CUSTOMERS() {
      return ROUTE_ADMIN_CUSTOMERS
    },

    ROUTE_ADMIN_CUSTOMERS_CREATE(id = '') {
      return ROUTE_ADMIN_CUSTOMERS_CREATE.replace(':id?', id)
    },

    page(page = 1) {
      this.table.page = page;
      this.list();
    },


    async list(line = false) {
      if (line) { this.isLineLoading = true } else { this.shimmer = true; }
      await this.axios.post(`admin/users/fetch`, this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.users = data;
      }).finally(() => {
        this.isLineLoading = false;
        this.shimmer = false;
      });
    },
    login(id) {
      this.processing = true;
      this.axios.post('admin/login-via-id', { id: id, admin_id: this.authUser.id }).then((response) => {
        notify(response.data.message);
        localStorage.removeItem('user');
        localStorage.removeItem('sanctum_token');
        localStorage.removeItem('expiration_date');
        var user = JSON.stringify(response.data.user);
        var impersonation_token = response.data.impersonation_token;
        localStorage.setItem('impersonation_token', impersonation_token);
        localStorage.setItem('user', user);
        var role = response.data.user.role;
        localStorage.setItem('role', role);
        localStorage.setItem('sanctum_token', response.data.token);
        var self = this;
        store.dispatch('setAuthUser');
        setTimeout(function () {
          self.$router.push(ROUTE_USER_HOME);
        }, 1000);
      }).catch((error) => {
        this.processing = false;
        this.errors = this.$flattenErrors(error);
      });
    },
    fetchPlans() {
      this.axios.post('user/bank-accounts/plans').then((response) => {
        this.PLANS = response.data.data;
      })
    },
    _assign(data) {
      this.processing = true;
      this.axios.post("admin/users/assign", data).then((response) => {
        notify(response.data.message);
        this.list();
        this.closeModal();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },

    _action() {
      this.processing = true;
      this.axios.post("admin/users/action", this.action).then((response) => {
        notify(response.data.message);
        this.list();
        this.closeAction();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },
    ____del() {
      this.processing = true;
      this.axios.post("admin/users/delete", this.__delete).then((response) => {
        notify(response.data.message);
        this.list();
        this.closeDelete();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },


  },
  created() {
    this.list();
    this.fetchPlans();
  },
  computed: {
    ...mapGetters(['authUser']),
  },
}
</script>