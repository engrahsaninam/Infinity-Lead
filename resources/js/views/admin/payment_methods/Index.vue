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
                <h4>Payment Methods</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_PAYMENT_METHODS()]" />
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row mb-2">
                    
                  </div>
                  <div class="table-responsive">
                    <table class="table table-borderless table-hover ">
                      <tbody>
                        <tr v-if="faqs.data.length === 0">
                          <td colspan="100%" class="text-center">
                            <i class="text-muted">No data found.</i>
                          </td>
                        </tr>
                        <tr v-for="list in faqs.data" :key="list.id" style="cursor: pointer;">
                          <td>
                            <img :src="list.icon" class="img-fluid" width="30" height="30" alt="">
                          </td>
                          <td>{{ list.name }}</td>
                          <td>{{ list.description }}</td>
                          <td>
                            <span v-if="list.status === 1" class="badge p-2 badge-soft-success">Active</span>
                            <span v-else class="badge p-2 badge-soft-danger">Disabled</span>
                          </td>
                          <td>
                            <button class="btn btn-sm btn-primary mr-1" @click="edit(list)"> <i
                                class="pi pi-cog"></i></button>
                            <button v-if="list.status === 1" class="btn btn-sm btn-danger"
                              @click="disable(list.id)">Disable</button>
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
  <Transition name="bounce">
    <dialog ref="modal" @click.self="closeModal" v-if="toggle_modal" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="store(method)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">{{ method.name }}</h5>
              <h5 class="float-right" @click="closeModal"><i class="pi pi-times"></i></h5>
            </div>
            <FroalaEditor v-show="method.slug === 'offline'" :col_md="12" v-model="method.instructions"
              label="Payment Instructions" :showCounter="false" :showSpam="false" :showAI="false"
              :showFieldsDropdown="false" />
            <FormControlInput v-if="method.slug === 'stripe'" :col_md="12" v-model="method.publishable_key"
              label="Publishable Key" />
            <FormControlInput v-if="method.slug === 'stripe'" :col_md="12" v-model="method.secret_key"
              label="Secret Key" />
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                Update
              </button>
            </div>

          </div>
        </form>
      </div>
    </dialog>
  </Transition>

</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  CURRENCY_CODES,
  FORM_DATA,
  notify,
  ROUTE_ADMIN_PAYMENT_METHODS,
  ROUTE_ADMIN_PLANS,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Pagination from "../../layout/Paignation.vue";
import AdminSidebar from "../../layout/admin/AdminSidebar.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormInputFile from "../../components/FormInputFile.vue";
import FroalaEditor from "../../components/FroalaEditor.vue";

export default {
  name: 'PaymentMethods',
  components: {
    AdminSidebar,
    Pagination,
    FormControlInput,
    FormInputFile,
    FormControlTextarea,
    FormControlSelect2, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header, FroalaEditor
  },
  data() {
    return {
      showPopup: false,
      delete_id: '',
      update_mode: false,
      toggle_modal: false,
      isLineLoading: false,
      shimmer: true,
      table: {
        records: 10,
        search: '',
        currentPage: 1,
        page: 1,
      },
      CURRENCIES: [],
      method: {
        id: '',
        name: '',
        description: '',
      },
      processing: false,
      CURRENCY_CODE_OPTIONS: CURRENCY_CODES.map((item) => {
        return { key: item, value: item };
      }),
    }
  },
  methods: {
    ROUTE_ADMIN_PAYMENT_METHODS() {
      return ROUTE_ADMIN_PAYMENT_METHODS
    },
    page(page = 1) {
      this.table.page = page;
      this.list();
    },
    edit(data) {
      this.method = data;
      this.update_mode = true;
      this.openModal();
    },
    createMode() {
      this.method = {
        id: '',
        name: '',
        description: '',
        price: '',
        currency_id: '',
        credits: '',
      };
      this.update_mode = false;
    },
    showPop(id) {
      this.showPopup = true;
      this.delete_id = id;
    },
    hidePop() {
      this.showPopup = false;
      this.delete_id = '';
    },



    async list(line = false) {
      if (line) { this.isLineLoading = true } else { this.shimmer = true; }
      await this.axios.post(`admin/payment-methods/fetch`, this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.faqs = data;
      }).finally(() => {
        this.isLineLoading = false;
        this.shimmer = false;
      });
    },
    async store(data) {
      this.processing = true
      await this.axios.post(`admin/payment-methods/update`, FORM_DATA(data)).then(({ data }) => {
        this.list();
        this.closeModal();
        this.createMode();
        notify(data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },

    closeModal() {
      this.toggle_modal = false;
      this.$refs.modal?.close();
    },
    openModal() {
      this.toggle_modal = true;
      this.$nextTick(() => {
        this.$refs.modal?.showModal();
      });
    },
    openCreateModal() {
      this.createMode();
      this.openModal();
    },
    disable(id) {
      this.axios.post("admin/payment-methods/disable", { id: id }).then((response) => {
        notify(response.data.message);
        this.list();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    
  },
  created() {
    this.list();
  }
}
</script>