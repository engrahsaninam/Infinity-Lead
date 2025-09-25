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
                <h4>Admin</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_ADMINS()]" />
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
                      <router-link :to="ROUTE_ADMIN_ADMINS_CREATE()"
                        class="btn btn-primary btn-rounded waves-effect waves-light text-light"><i class="mdi mdi-plus me-1"></i>
                        Create Admin</router-link>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <thead>
                        <tr>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Profile</th>
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
                            <img :src="list.profile" class="img-fluid img-thumbnail" width="50" height="50"
                              v-if="list.profile" alt="user-profile">
                          </td>
                          <td>
                            <router-link :to="ROUTE_ADMIN_ADMINS_CREATE(list.id)" class="btn btn-sm btn-success text-light"><i
                                class="pi pi-pencil"></i></router-link>

                            
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

</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  notify, ROLE_ADMIN, ROLE_USER, ROUTE_ADMIN_ADMINS, ROUTE_ADMIN_ADMINS_CREATE, ROUTE_ADMIN_CUSTOMERS, ROUTE_ADMIN_CUSTOMERS_CREATE,
  ROUTE_ADMIN_HOME,
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

export default {
  name: 'Admins',
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

      processing: false,
    }
  },
  methods: {
    ROUTE_ADMIN_ADMINS() {
      return ROUTE_ADMIN_ADMINS
    },

    ROUTE_ADMIN_ADMINS_CREATE(id='') {
      return ROUTE_ADMIN_ADMINS_CREATE.replace(':id?',id)
    },

    page(page = 1) {
      this.table.page = page;
      this.list();
    },


    async list(line = false) {
      if (line) { this.isLineLoading = true } else { this.shimmer = true; }
      await this.axios.post(`admin/admins/fetch`, this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.users = data;
      }).finally(() => {
        this.isLineLoading = false;
        this.shimmer = false;
      });
    },
    
  },
  created() {
    this.list();
  }
}
</script>
