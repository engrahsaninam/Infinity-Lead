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
                <h4>Manage Sending Server</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_SENDING_SERVER(), ROUTE_ADMIN_SENDING_SERVER_UPDATE()]" />
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">

              <div class="container ">
                <form @submit.prevent="store(sending_server)">
                  <div class="row " v-if="!sending_server.type">
                    <div class="col-md-12">
                      <h5 class="float-left">Select Sending Server Type</h5>
                    </div>
                    <table class="table table-stripped table-hover table-borderless">
                      <tbody v-for="server in servers">
                        <tr @click="updateType(server)">
                          <td width="10%">
                            <img :src="server.logo" alt="" class="img-fluid" style="height: 50px;object-fit: cover;">
                          </td>
                          <td width="90%">
                            <h5 class="pt-3">{{ server.title }}</h5>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="row " v-if="sending_server.type">
                    <div class="col-md-12 ">
                      <h5 class="float-left">
                        Configure {{ sending_server.type.toUpperCase() }}
                      </h5>
                      <button class="btn btn-dark float-right" @click="sending_server.type = ''"><i
                          class="pi pi-refresh"></i> Change
                        Server?</button>
                    </div>
                    <FormControlInput :col_md="6" v-model="sending_server.domain" label="Domain" />
                    <FormControlInput :col_md="6" v-model="sending_server.host" label="Host" />
                    <FormControlInput :col_md="6" v-model="sending_server.username" label="Username" />
                    <FormControlInput :col_md="6" v-model="sending_server.password" label="Password" />
                    <FormControlInput :col_md="3" v-model="sending_server.port" label="Port" />
                    <FormControlInput :col_md="3" v-model="sending_server.encryption" label="Encryption" />

                    <div class="col-md-12 mt-">
                      <h5>Configuration settings</h5>
                      <p>Provide more configuration settings for your sending server</p>
                    </div>
                    <FormControlInput :col_md="6" v-model="sending_server.name" label="From Name" />
                    <FormControlInput :col_md="6" v-model="sending_server.email" label="From Email" />

                  </div>
                  <div class="row">
                    <div class="col-md-12 text-right py-2 pb-3">
                      <button class="btn btn-success px-4 float-right " @click="testConnection" :disabled="processing">
                        Test Connection
                      </button>
                      <button class="btn btn-primary px-4 float-right " type="submit" :disabled="processing">
                        <span v-if="!processing">
                          <i class="pi pi-save"></i>
                          <span v-if="update_mode"> Update</span>
                          <span v-else> Save</span>
                        </span>
                        <span v-else> Processing ...</span>
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

  <Confirm :show="showPopup" heading="Warning" message="Are you sure you want to delete?" @confirm="_delete"
    @cancel="showPopup = false" />
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  FORM_DATA,
  notify,
  ROUTE_ADMIN_SENDING_SERVER_UPDATE,
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

export default {
  name: 'SendingServerUpdate',
  components: {
    AdminSidebar,
    Pagination,
    FormControlInput,
    FormInputFile,
    FormControlTextarea,
    FormControlSelect2, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header
  },
  data() {
    return {
      showPopup: false,
      delete_id: '',
      update_mode: true,
      isLineLoading: false,
      shimmer: true,
      sending_server: {
        id: '',
        type: '',
        domain: '',
        host: '',
        username: '',
        password: '',
        port: '',
        encryption: '',
      },
      servers: [
        { type: 'sendgrid', logo: '/servers/sendgrid.png', title: 'Send Grid', },
        { type: 'mailgun', logo: '/servers/mailgun.png', title: 'Mail Gun', },
        { type: 'sparkpost', logo: '/servers/sparkpost.png', title: 'Spark Post', },
        { type: 'elasticsmtp', logo: '/servers/elasticsmtp.png', title: 'Elastic Smtp', },
        { type: 'smtp', logo: '/servers/smtp.png', title: 'SMTP', },
        { type: 'blastengine', logo: '/servers/blastengine.png', title: 'Blast Engine', },

      ],

      processing: false,

    }
  },
  methods: {
    ROUTE_ADMIN_SENDING_SERVER_UPDATE() {
      return ROUTE_ADMIN_SENDING_SERVER_UPDATE;
    },
    ROUTE_ADMIN_SENDING_SERVER() {
      return ROUTE_ADMIN_SENDING_SERVER_UPDATE;
    },

    updateType(server) {
      this.sending_server.type = server.type;
    },
    showUnlimited(val) {
      return val == -1 ? 'Unlimited' : '';
    },

    async edit() {
      this.shimmer = true;
      await this.axios.post(`admin/sending_servers/edit`, { id: this.$route.params.id }).then(({ data }) => {
        this.sending_server = data;
      }).finally(() => {
        this.shimmer = false;
      });
    },
    servers: [
      { type: 'sendgrid', logo: '/servers/sendgrid.png', title: 'Send Grid', },
      { type: 'mailgun', logo: '/servers/mailgun.png', title: 'Mail Gun', },
      { type: 'sparkpost', logo: '/servers/sparkpost.png', title: 'Spark Post', },
      { type: 'elasticsmtp', logo: '/servers/elasticsmtp.png', title: 'Elastic Smtp', },
      { type: 'smtp', logo: '/servers/smtp.png', title: 'SMTP', },
      { type: 'blastengine', logo: '/servers/blastengine.png', title: 'Blast Engine', },

    ],
    async store(data) {
      this.processing = true
      await this.axios.post(`admin/sending_servers/store`, FORM_DATA(data)).then(({ data }) => {
        this.edit();
        notify(data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    },
    testConnection(){
      this.processing = true;
      this.axios.post(`admin/sending_servers/test-connection`,{id:this.$route.params.id}).then(({ data }) => {
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    }
  },
  created() {
    this.edit();
  }
}
</script>