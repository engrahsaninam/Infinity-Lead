<template>
  <div id="layout-wrapper">
    <Header/>
    <Sidebar/>
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>DNS Configuration</h4>
                <Breadcrumb :items="[ROUTE_USER_API_DOMAINS()]" />
              </div>
            </div>
          </div>

          <Loader :loading="processing" />
          <div class="card">
            <div class="card-body">
              <Pills/>

              <form @submit.prevent="addDomain" class="row pt-5">
                <FormControlInput label="Domain" v-model="form.domain" placeholder="e.g. domain.com"/>
                <div class="col-md-6 mt-auto mb-3">
                  <button class="btn btn-primary " :disabled="processing">
                    <i class="pi pi-plus-circle"></i> Add Domain
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="row mt-4" v-if="domains.length">
            <div class="col-12" v-for="domain in domains" :key="domain.id">
              <div class="card mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                      <div class="float-left">
                        <h4 class="form-label m-0 ">{{ domain.domain }}</h4>
                        <p class="text-danger m-0 " v-if="!domain.verified"><small>Please add the following DNS records to your domain before verifying:</small></p>
                      </div>
                      <button class="btn btn-sm float-right font-weight-light text-light" :class="domain.verified ? 'btn-success' : 'btn-warning'">{{ domain.verified ? 'Verified' : 'Not Verified' }}</button>
                      <button v-if="!domain.verified" class="btn btn-sm mr-2 btn-outline-success float-right" @click="confirmVerification(domain.id)" :disabled="verifyingId === domain.id">
                        <span v-if="verifyingId === domain.id">
                          <i class="fa fa-spinner fa-spin"></i> Verifying...
                        </span>
                        <span v-else>Click to verify your DNS records</span>
                      </button>

                      <button class="btn btn-sm btn-outline-danger float-right mr-2" @click="showDeletePop(domain.id)"><i class="pi pi-trash"></i> Delete</button>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 mt-2">
                      <h6>1. SPF </h6>
                      <table class="table table-sm table-bordered table-striped">
                        <tbody>
                          <tr>
                            <th width="10%">Type</th>
                            <th width="10%">Host</th>
                            <th width="60%">Value</th>
                            <th width="10%">TTL</th>
                            <th width="10%">Status</th>
                          </tr>
                          <tr>
                            <td>TXT</td>
                            <td>@</td>
                            <td>
                              <code class="float-left">{{server.spf}}</code>
                              <i class="pi pi-clipboard float-right"  @click="copyToClipboard(server.spf)" ></i>
                            </td>
                            <td>60 min</td>
                            <td>
                              <span v-if="domain.spf"><i class="pi pi-check-circle text-success"></i></span>
                              <span v-else><i class="pi pi-times-circle text-danger"></i></span>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-12">
                      <h6>2. DKIM </h6>
                      <table class="table table-sm table-bordered table-striped">
                        <tbody>
                          <tr>
                            <th  width="10%">Type</th>
                            <th  width="10%">Host</th>
                            <th  width="60%">Value</th>
                            <th  width="10%">TTL</th>
                            <th  width="10%">Status</th>
                          </tr>
                          <tr>
                            <td>TXT</td>
                            <td>default._domainkey</td>
                            <td>
                              <code style="word-break: break-all;word-wrap: break-word" class="float-left">
                                {{server.dkim}}
                              </code>
                              <i class="pi pi-clipboard float-right"  @click="copyToClipboard(server.dkim)" ></i>
                            </td>
                            <td>60 min</td>
                            <td>
                              <span v-if="domain.dkim"><i class="pi pi-check-circle text-success"></i></span>
                              <span v-else><i class="pi pi-times-circle text-danger"></i></span>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-12">
                      <h6>3. DMARC </h6>
                      <table class="table table-sm table-bordered table-striped">
                        <tbody>
                          <tr>
                            <th width="10%">Type</th>
                            <th width="10%">Host</th>
                            <th width="60%">Value</th>
                            <th width="10%">TTL</th>
                            <th width="10%">Status</th>
                          </tr>
                          <tr>
                            <td>TXT</td>
                            <td>_dmarc</td>
                            <td>
                              <code >
                                {{ `v=DMARC1; p=none; rua=mailto:admin@${domain.domain}` }}
                              </code>
                              <i class="pi pi-clipboard float-right"  @click="copyToClipboard(`v=DMARC1; p=none; rua=mailto:admin@${domain.domain}`)"></i>

                            </td>
                            <td>60 min</td>
                            <td>

                              <span v-if="domain.dmarc"><i class="pi pi-check-circle text-success"></i></span>
                              <span v-else><i class="pi pi-times-circle text-danger"></i></span>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
<!--
                    <div class="col-md-4 mt-4">
                      <h6>MX</h6>
                      <p><b>Mail Server:</b> mail.infinitylead.io</p>
                      <p><b>Priority:</b> 10</p>
                    </div>-->

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="alert alert-info mt-3">No domains connected yet.</div>
        </div>
      </div>
    </div>
  </div>

  <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete this domain?"
      @confirm="deleteDomain()"
      @cancel="showPopup = false"
  />
</template>

<script>
import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Loader from "../../components/Loader.vue";
import {notify, ROUTE_USER_API_DOMAINS} from "../../../constants.js";
import FormControlInput from "../../components/FormControlInput.vue";
import Pills from "./components/Pills.vue";
import Confirm from "../../components/Confirm.vue";

export default {
  name: "DomainVerification",
  components: {
    Confirm,
    Pills,
    FormControlInput,
    Header,
    Sidebar,
    Breadcrumb,
    Loader,
  },
  data() {
    return {
      delete_domain_id:'',
      showPopup:false,
      server:{
        spf:'v=spf1 include:infinitylead.io ip4:185.197.250.109 ~all',
        dkim:'v=DKIM1; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAw6GkzlUD8wy7NjTdp4ZndWglu6HCye84GBANDw6O+UrL5xNvZNfjhYZCfLXF4lofKo1NUEtPX1+UjVElhkmkn5gkNu17E4oNJUJ7HtBZ4eiirEPjQ1ZyL/lvvb6pxApC92jKNTlEkWU/qpwC9gQLK1gJr5b9L15EXwKqp8Jk2ntRLoDWAYvXtyg358p596xoQYMWixxCBCjK06w8XhcOpsCyVlie9p6MOZKrfm5ehYaF+f/2LmIZ4a48Qm5rMSPh8ibCotidcprxcG01CGtx1LwBWTmm6OJrL2jZZT7lpeLYVNbuYj2/p0OiJXDeuJhZQKEvxYKraVSBJVY7Nj6nKwIDAQAB;',
        dmarc:'v=DMARC1; p=none; rua=mailto:admin@domain.com',
      },
      form: {
        domain: "",
      },
      processing: false,
      verifyingId: null,
      domains: [],
    };
  },
  methods: {
    ROUTE_USER_API_DOMAINS() {
      return ROUTE_USER_API_DOMAINS
    },
    copyToClipboard(text){
      navigator.clipboard.writeText(text).then(() => {
        notify('Copied to clipboard');
      }).catch(() => {
        alert('Failed to copy URL. Please try again.');
      });
    },
    async fetchDomains() {
      this.processing = true;
      try {
        const { data } = await this.axios.post("/user/domains/fetch");
        this.domains = data.data;
      } catch (error) {
        this.$flattenErrors(error);
      } finally {
        this.processing = false;
      }
    },
    async addDomain() {
      this.processing = true;
      try {
        const { data } = await this.axios.post("/user/domains/store", this.form);
        notify("Domain added successfully");
        this.form.domain = "";
        this.fetchDomains();
      } catch (error) {
        this.$flattenErrors(error);
      } finally {
        this.processing = false;
      }
    },
    async confirmVerification(id) {
      this.verifyingId = id;
      try {
        const { data } = await this.axios.post(`/user/domains/${id}/verify`);
        notify(data.message || "Domain verified successfully");
        this.fetchDomains();
      } catch (error) {
        this.$flattenErrors(error);
      } finally {
        this.verifyingId = null;
      }
    },
    deleteDomain() {
      this.axios.post("user/domains/delete", { id:this.delete_domain_id })
          .then((response) => {
            notify(response.data.message);
            this.fetchDomains();
            this.delete_account_id='';
            this.showPopup=false;
          })
          .catch((error) => {
            this.$flattenErrors(error);
          });
    },
    showDeletePop(id){
      this.showPopup=true;
      this.delete_domain_id=id;
    },
  },
  created() {
    this.fetchDomains();
  },
};
</script>
<style>
.table th{
  background: #efefef;
}
</style>
