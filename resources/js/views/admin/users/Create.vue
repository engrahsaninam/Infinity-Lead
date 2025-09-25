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
                <div class="btn-group" role="group" aria-label="Basic example">
                  <button type="button" class="btn btn-secondary" :class="{ 'active': tab ==='edit'}"
                    @click="_TAB('edit')">
                    <span v-if="update_mode">Update Customer</span>
                    <span v-else>Create Customer</span>
                  </button>
                  <button v-if="update_mode" type="button" class="btn btn-secondary"
                    :class="{ 'active': tab === 'subscriptions' }" @click="_TAB('subscriptions')">Subscriptions</button>
                </div>




                <Breadcrumb :items="[ROUTE_ADMIN_CUSTOMERS(), ROUTE_ADMIN_CUSTOMERS_CREATE()]" />
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card" v-if="tab==='edit'">
                <div class="card-body">
                  <form class="row g-3 " method="POST" @submit.prevent="register(form)">
                    <div class="col-md-7 d-flex justify-content-center">
                      <div class="col-md-6">
                        <label class="form-label mt-5">Profile Image</label>
                        <div v-if="!imageSelected">
                          <UploadImages @changed="handleImages" :max="1" />
                        </div>
                        <div v-else class="text-center">
                          <img :src="imageUrl" alt="Selected Profile" class="img-thumbnail mb-2 w-100" />
                          <br />
                          <button class="btn btn-sm btn-outline-secondary" @click.prevent="cancelImage"><i
                              class="pi pi-times-circle"></i> Cancel</button>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <FormControlInput v-model="form.first_name" label="First Name" :col_md="12" />
                      <FormControlInput v-model="form.last_name" label="Last Name" :col_md="12" />
                      <FormControlInput v-model="form.email" label="Email" :col_md="12" />
                      <div class="col-md-12">
                        <div class="form-group mt-2">
                          <label class="form-label" for="Password">Password</label>
                          <div class="input-group">
                            <input v-model="form.password" :type="passType" id="password" class="form-control"
                              placeholder="****************">
                            <div class="input-group-append">
                              <span class="input-group-text rounded-0"
                                style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;"
                                @click="togglePasswordVisibility()">
                                <i class="pi "
                                  :class="{ 'pi-eye': passType === 'password', 'pi-eye-slash': passType === 'text' }"></i>
                              </span>
                            </div>
                          </div>
                          <p class="small text-muted">Your password must be at least 8 characters in length, and contain
                            at least one of each of the following: lower case letters (a-z), upper case letters (A-Z)
                            and
                            numbers (0-9).</p>
                        </div>
                      </div>
                      <div class="col-md-12 mt-3">
                        <button class="btn btn-primary float-right" :disabled="processing">
                          <span v-if="!processing">
                            <i class="pi pi-save"></i>
                            <span v-if="update_mode"> Update</span>
                            <span v-else> Save</span>
                          </span>
                          <span v-else><i class="pi pi-spinner-dotted pi-spin"></i> Processing</span>
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="card" v-if="tab === 'subscriptions'">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <thead>
                        <tr>
                          <th>Subscriber</th>
                          <th>Price</th>
                          <th>Plan</th>
                          <th>Method</th>
                          <th>Status</th>
                          <th>Invoice</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="subscriptions.data && subscriptions.data.length === 0">
                          <td colspan="100%" class="text-center">
                            <i class="text-muted">No subscriptions found.</i>
                          </td>
                        </tr>
                        <tr v-for="list in subscriptions.data" :key="list.id" style="cursor: pointer;">
                          <td>{{ list?.user?.name }}</td>
                          <td>
                            {{ list.price }} {{ list.code }}
                          </td>
                          <td>
                            <b>{{ list?.plan?.name }}</b>
                            <br>
                            {{ list?.plan?.description }}
                          </td>
                          <td>
                            {{ list?.payment_method?.name }}
                          </td>
                          <td>
                            <small>
                              {{ list.status.toUpperCase() }}
                            </small>

                            <div v-if="list.status === SUBSCRIPTION_PENDING()">
                              <button class="btn btn-sm btn-success"
                                @click="manageActionPopup('approve', list)">Approve</button>
                              |
                              <button class="btn btn-sm btn-danger"
                                @click="manageActionPopup('reject', list)">Reject</button>
                            </div>
                          </td>
                          <td>
                            <a target="_blank"
                              :href="'/invoice/print/' + list.id + '/' + list?.user?.created_at + '/' + list?.user?.id"
                              class="font-weight-bold">
                              {{ list.cid }}
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
  <Confirm :show="popup.show" heading="Warning" :message="popup.message" @confirm="actionSubscription"
    @cancel="cancelPopup" />

</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  FORM_DATA,
  notify, ROUTE_ADMIN_CUSTOMERS, ROUTE_ADMIN_CUSTOMERS_CREATE, ROUTE_USER_BLACKLIST,
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



import UploadImages from "vue-upload-drop-images"; // ✅ import


export default {
  name: 'CreateUsers',
  components: {
    UploadImages,
    AdminSidebar,
    Pagination,
    FormControlInput,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header
  },
  data() {
    return {
      tab:'edit',
      update_mode: Boolean(this.$route.params.id),
      passType: 'text',
      isLineLoading: false,
      imageSelected: false,
      imageUrl: '',
      form: {
        email: '',
        password: '',
        profile:'',
      },
      popup: {
        message: '',
        action: '',
        subscription: '',
        show: false,
      },
      processing: false,
      subscriptions:[],
      table: {
        records: 10,
        search: '',
        currentPage: 1,
        page: 1,
        auth:this.$route.params.id,
      },
    }
  },
  methods: {
    _TAB(tab){
      this.tab=tab;
    },
    ROUTE_ADMIN_CUSTOMERS() {
      return ROUTE_ADMIN_CUSTOMERS
    },
    togglePasswordVisibility() {
      this.passType = this.passType === 'password' ? 'text' : 'password';
    },

    ROUTE_ADMIN_CUSTOMERS(){
      return ROUTE_ADMIN_CUSTOMERS;
    },
    ROUTE_ADMIN_CUSTOMERS_CREATE() {
      return ROUTE_ADMIN_CUSTOMERS_CREATE;
    },
    register(data) {
      this.processing = true;
      this.axios.post('admin/users/create', FORM_DATA(data)).then((response) => {
        notify(response.data.message);
        this.$router.push(this.ROUTE_ADMIN_CUSTOMERS());
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },
    SUBSCRIPTION_PENDING() {
      return SUBSCRIPTION_PENDING
    },
    async list() {
      this.processing=true;
      await this.axios.post(`admin/subscriptions/fetch`, this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.subscriptions = data;
      }).finally(() => {
        this.processing = false;
      });
    },
    async edit() {
      if (!this.$route.params.id){
        return false;
      }
      this.processing = true;
      await this.axios.post('admin/users/edit', { id: this.$route.params.id }).then((response) => {
        this.form = response.data.data;
        if (this.form.profile) {
          this.imageUrl = this.form.profile; // should be full image path
          this.imageSelected = true;
        }
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms) => {
        this.processing = false;
      });
    },
    handleImages(files) {
      if (files.length > 0) {
        const file = files[0];
        this.form.profile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imageUrl = e.target.result;
          this.imageSelected = true;
        };
        reader.readAsDataURL(file);
      } else {
        this.imageSelected = false;
        this.form.profile = '';
        this.imageUrl = '';
      }
    },
    cancelImage() {
      this.form.profile = '';
      this.imageSelected = false;
      this.imageUrl = '';
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
  },
  created() {
    this.edit();
    this.list();
    if (this.$route.query.type ==='subscriptions'){
      this.tab ='subscriptions';
    }
  }
}
</script>
