<template>
  <div id="layout-wrapper">
    <Header/>
    <AdminSidebar/>
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer"/>
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid" v-if="count">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Websites</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_WEBSITES()]"/>
              </div>
              <LineLoader v-if="isLineLoading"/>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-sm-3">
                      <div class="position-relative">
                        <input type="text" class="form-control search-input" v-model="table.search" placeholder="Search here..." @input="this.list(true)">
                        <i class="pi pi-search search-icon"></i>
                      </div>
                    </div>
                    <div class="col-sm-9">
                      <div class="float-right">
                        <button class="btn btn-danger mr-2" v-if="selectedWebsites.length>0" @click="manageDeletePopup"><i class="mdi mdi-delete-outline me-1"></i> Delete {{selectedWebsites.length}} Selected</button>
                        <button class="btn btn-light border text-muted mr-2" @click="selectedWebsites=[];" v-if="selectedWebsites.length>0"> Cancel </button>

                        <button @click="openCreateModal" class="btn btn-primary btn-rounded waves-effect waves-light "><i class="mdi mdi-plus me-1"></i> Create</button>
                        <button @click="openImportModal" class="btn btn-success btn-rounded waves-effect waves-light ml-2"><i class="mdi mdi-import me-1"></i> Import</button>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive">

                    <table class="table table-hover">
                      <tr v-if="websites.data.length === 0">
                        <td colspan="4" class="text-center">
                          <i class="text-muted">No websites found.</i>
                        </td>
                      </tr>

                      <template v-for="list in websites.data" :key="list.id">
                        <tr v-if="editId !== list.id" :class="{ 'table-active': selectedWebsites.includes(list.id) }">
                          <td @click="toggleRowSelection(list.id)" style="cursor: pointer;">
                            <small class="text-muted mr-2"><i class="pi pi-th-large"></i></small>
                            {{ list.name }}
                          </td>
                          <td>
                            {{ list.domain }}
                            <a :href="formatWebsite(list.domain)" target="_blank">
                              <small class="pi pi-external-link"></small>
                            </a>
                          </td>
                          <td >
                            <span title="Invalid Format" :class="{'bg-soft-danger':!checkFormat(list.format)}">
                            {{ list.format }}
                            </span>
                          </td>
                          <td>
                            <button class="btn btn-sm" @click="editWebsite(list)">
                              <i class="pi pi-pencil"></i>
                            </button>
                          </td>
                        </tr>
                        <tr v-else>
                          <td colspan="4">
                            <div class="row pt-2">
                              <div class="col-md-3 p-0 m-0">
                                <FormInputTooltip
                                    label=""
                                    class="col-md-12"
                                    v-model="website.name"
                                    :error="website_error.name"
                                />
                              </div>
                              <div class="col-md-3 p-0 m-0">
                                <FormInputTooltip
                                    label=""
                                    class="col-md-12"
                                    v-model="website.domain"
                                    :error="website_error.domain"
                                />
                              </div>
                              <div class="col-md-3 p-0 m-0">
                                <FormControlSelect2
                                    :options="FORMATS"
                                    class="col-md-12"
                                    v-model="website.format"
                                    :error="website_error.format"
                                />
                              </div>
                              <div class="col-md-2 p-0 m-0 text-right">
                                <button class="btn btn-success" @click="update">Update</button>
                                <button class="btn btn-light border" @click="cancelEdit">Cancel</button>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </template>
                    </table>
                  </div>
                </div>
                <div class="card-header border-bottom-0">
                  <Pagination :list="page" :data="websites"/>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container " v-else>
          <div class="row justify-content-center align-items-center" style="min-height: 80vh">
            <div class="col-md-6 text-center ">
              <h4>Add Websites & Domains</h4>
              <p class="text-muted">
                Add emails or domains you want to exclude from your campaigns. Prevent accidental outreach, improve targeting, and stay compliant.</p>
                <button @click="openCreateModal" class="btn btn-primary btn-rounded waves-effect waves-light mb-2 mr-2"><i class="mdi mdi-plus me-1"></i> Add Websites</button>
                <button @click="openImportModal" class="btn btn-success btn-rounded waves-effect waves-light mb-2 "><i class="mdi mdi-import me-1"></i> Import</button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete?"
      @confirm="deleteEmails"
      @cancel="showPopup = false"
  />
  <Transition name="bounce">
    <dialog ref="dialog" @click.self="createWebsiteDialog" v-if="toggle_website_dialog" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="createWebsite(website)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">Add Websites & Domains</h5>
              <h5 class="float-right" @click="createWebsiteDialog"><i class="pi pi-times"></i></h5>
            </div>
            <FormInputTooltip class="col-md-12" v-model="website.name" label="Name" :error="website_error.name" />
            <FormInputTooltip class="col-md-12" v-model="website.domain" label="Domain" :error="website_error.domain" />
            <FormControlSelect2 :options="FORMATS" class="col-md-12" v-model="website.format" label="Format" :error="website_error.format" />


            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                <span v-if="!processing">Save</span>
                <span v-else>Processing ...</span>
              </button>
            </div>

          </div>
        </form>
      </div>
    </dialog>
  </Transition>
  <Transition name="bounce">
    <dialog ref="import_dialog" @click.self="createWebsiteDialog" v-if="toggle_import_dialog" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="submitImport">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-right" @click="createWebsiteDialog"><i class="pi pi-times"></i></h5>

              <div class="col-md-12">
                <h4>Import websites and domains from a .csv file</h4>
                <div class="drag-drop p-5 text-center">
                  <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
                  <p class="small mb-0">Drag & drop file here</p>
                  <p class="small mt-0">or</p>
                  <label for="csv-upload" class="btn btn-primary mb-4"  >
                    Choose .csv file
                    <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
                  </label>
                </div>
              </div>

              <FormControlSelect2
                  v-if="mapping.option.length"
                  class="mb-4 mt-3"
                  v-model="mapping.value"
                  :col_md="12"
                  :options="mapping.option"
                  label="Select Website Name Column"
                  @select="updateWebsiteName"
              />
              <FormControlSelect2
                  v-if="mapping.option.length"
                  class="mb-4 mt-3"
                  v-model="website_domain.value"
                  :col_md="12"
                  :options="mapping.option"
                  label="Select Website Domain Column"
                  @select="updateWebsiteDomain"
              />
              <FormControlSelect2
                  v-if="mapping.option.length"
                  class="mb-4 mt-3"
                  v-model="website_format.value"
                  :col_md="12"
                  :options="mapping.option"
                  label="Select Company Email Format Column"
                  @select="updateWebsiteFormat"
              />

              <p v-if="uploadError" class="text-danger">{{ uploadError }}</p>

            </div>
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                <span v-if="!processing">Save</span>
                <span v-else>Processing ...</span>
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
  EMAIL_FORMATS,
  notify, ROUTE_ADMIN_WEBSITES,
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
  name: 'Website',
  components: {
    AdminSidebar,
    Pagination,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header},
  data() {
    return {
      editId:'',
      toggle_website_dialog: false,
      toggle_import_dialog: false,
      revise_loading_id:'',
      delete_campaign_id:'',
      showPopup:false,
      isLineLoading:false,
      shimmer:true,
      count:0,
      websites:'',
      table:{
        records:10,
        search:'',
        currentPage:1,
        page:1,
      },
      website:{
        id:'',
        name:'',
        domain:'',
        format:'',
      },
      website_error:{
        id:'',
        name:'',
        format:'',
        domain:'',
      },
      FORMATS:EMAIL_FORMATS.map((item)=> ( {key:item, value:item})),
      processing:false,
      selectedWebsites: [],
      mapping: {
        value: null,
        option: [],
        emails: []
      },
      website_domain: {
        value: null,
      },
      website_format: {
        value: null,
      },


      csvFile: null,
      uploadError: '',
    }
  },
  methods: {
    EMAIL_FORMATS() {
      return EMAIL_FORMATS
    },
    ROUTE_ADMIN_WEBSITES() {
      return ROUTE_ADMIN_WEBSITES
    },
    formatWebsite(url) {
      if (!url) return '#';
      if (!url.match(/^https?:\/\//)) {
        return 'https://' + url;
      }
      return url;
    },
    page(page=1){
      this.table.page=page;
      this.list();
    },
    openCreateModal() {
      this.toggle_website_dialog = true;
      this.$nextTick(() => {
        this.$refs.dialog?.showModal();
      });
    },
    openImportModal() {
      this.toggle_import_dialog = true;
      this.$nextTick(() => {
        this.$refs.import_dialog?.showModal();
      });
    },

    createWebsiteDialog() {
      this.toggle_website_dialog = false;
      this.$refs.dialog?.close();
      this.toggle_import_dialog=false;
      this.$refs.import_dialog?.close();

    },
    manageDeletePopup() {
      if (this.selectedWebsites.length === 0) return;
      this.showPopup = true;
    },
    async list(line=false) {
      if (line){this.isLineLoading=true}else{this.shimmer=true;}
      await this.axios.post(`user/websites/fetch`,this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.websites = data;
        this.countBlacklist();
      }).finally(() => {
        this.isLineLoading=false;
        this.shimmer=false;
      });
    },
    toggleRowSelection(id) {
      const index = this.selectedWebsites.indexOf(id);
      if (index === -1) {
        this.selectedWebsites.push(id);
      } else {
        this.selectedWebsites.splice(index, 1);
      }
    },
    deleteEmails() {
      const payload ={ ids: this.selectedWebsites }
      this.axios.post('user/websites/delete', payload).then((response) => {
            notify(response.data.message);
            this.list();
            this.showPopup = false;
            this.selectedWebsites = [];
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    createWebsite(data){
      this.processing=true;
      this.axios.post('user/websites/store', data).then((response) => {
        notify(response.data.message);
        this.list();
        this.toggle_website_dialog=false;
      }).catch((error) => {
        this.website_error=this.$flattenErrors(error);
      }).finally((res)=>{
        this.processing=false;
      });
    },
    update(){
      this.processing=true;
      this.axios.post('user/websites/update', this.website).then((response) => {
        notify(response.data.message);
        this.list();
        this.cancelEdit();
        this.toggle_website_dialog=false;
      }).catch((error) => {
        this.website_error=this.$flattenErrors(error);
      }).finally((res)=>{
        this.processing=false;
      });
    },

    countBlacklist(){
      this.axios.post('user/websites/count').then((response) => {
        this.count=response.data.data;
      });
    },



    onCsvChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.csvFile = file;
      const formData = new FormData();
      formData.append('file', file);
      this.uploadError = '';
      this.processing = true;
      this.axios.post('/user/websites/parse-csv', formData)
          .then(({ data }) => {
            this.mapping.option = data.headers.map((header, index) => ({
              key: header,
              value: header
            }));
          })
          .catch(error => {
            this.$flattenErrors(error);
          })
          .finally(() => {
            this.processing = false;
          });
    },

    updateWebsiteName(selected) {
      this.mapping.value = selected;
    },
    updateWebsiteDomain(selected) {
      this.website_domain.value = selected;
    },
    updateWebsiteFormat(selected) {
      this.website_format.value = selected;
    },



    submitImport() {
      if (!this.csvFile || !this.mapping.value) {
        this.uploadError = "CSV file and email column are required.";
        return;
      }

      const formData = new FormData();
      formData.append('file', this.csvFile);
      formData.append('website', this.mapping.value);
      formData.append('domain', this.website_domain.value);
      formData.append('format', this.website_format.value);

      this.processing = true;
      this.axios.post('/user/websites/import', formData)
          .then(res => {
            notify(res.data.message);
            this.list();
            this.toggle_import_dialog = false;
          })
          .catch(err => {
            this.uploadError = 'Import failed. Please check your file.';
            console.error(err);
          })
          .finally(() => {
            this.processing = false;
          });
    },
    editWebsite(website) {
      this.editId = website.id;
      this.website = { ...website };
      this.website_error = {};
    },
    cancelEdit() {
      this.editId = null;
      this.website = { id: null, name: '', domain: '', format: '' };
    },
    checkFormat(format){
      return EMAIL_FORMATS.includes(format);
    },
  },
  created() {
    this.list();
    this.countBlacklist();
    console.log(EMAIL_FORMATS);
  }
}
</script>
