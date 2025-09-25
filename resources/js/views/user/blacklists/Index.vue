<template>
  <div id="layout-wrapper">
    <Header/>
    <Sidebar/>
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer"/>
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid" v-if="count">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Blacklists</h4>
                <Breadcrumb :items="[ROUTE_USER_BLACKLIST()]"/>
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
                        <button class="btn btn-danger mr-2" @click="manageDeletePopup('all')"><i class="mdi mdi-delete-outline me-1"></i> Delete All Blacklisted</button>
                        <button class="btn btn-danger mr-2" v-if="selectedBlacklists.length>0" @click="manageDeletePopup('selected')"><i class="mdi mdi-delete-outline me-1"></i> Delete {{selectedBlacklists.length}} Selected</button>
                        <button class="btn btn-light border text-muted mr-2" @click="selectedBlacklists=[];" v-if="selectedBlacklists.length>0"> Cancel </button>

                        <button @click="openCreateModal" class="btn btn-primary btn-rounded waves-effect waves-light "><i class="mdi mdi-plus me-1"></i> Create</button>
                        <button @click="openImportModal" class="btn btn-success btn-rounded waves-effect waves-light ml-2"><i class="mdi mdi-import me-1"></i> Import</button>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <tr v-if="blacklists.data.length === 0">
                        <td colspan="2" class="text-center">
                          <i class="text-muted">No blacklist found.</i>
                        </td>
                      </tr>
                      <tr v-for="list in blacklists.data" :key="list.id" :class="{ 'table-active': selectedBlacklists.includes(list.id) }" @click="toggleRowSelection(list.id)" style="cursor: pointer;">
                        <td>
                          {{ list.email }}
                          <small class="float-right" v-if=" list.description" >{{ list.description }}</small>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="card-header border-bottom-0">
                  <Pagination :list="page" :data="blacklists"/>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container " v-else>
          <div class="row justify-content-center align-items-center" style="min-height: 80vh">
            <div class="col-md-6 text-center ">
              <h4>Create your Blacklist </h4>
              <p class="text-muted">
                Add emails or domains you want to exclude from your campaigns. Prevent accidental outreach, improve targeting, and stay compliant.</p>
                <button @click="openCreateModal" class="btn btn-primary btn-rounded waves-effect waves-light mb-2 mr-2"><i class="mdi mdi-plus me-1"></i> Create Blacklist</button>
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
    <dialog ref="dialog" @click.self="createBlacklistDialog" v-if="toggle_blacklist_dialog" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="createBlacklist(blacklist)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">Add Blacklist</h5>
              <h5 class="float-right" @click="createBlacklistDialog"><i class="pi pi-times"></i></h5>
            </div>
            <FormInputTooltip class="col-md-12" v-model="blacklist.email" label="Email" :error="blacklist_error.email" />
            <FormControlTextarea :col_md="12" v-model="blacklist.description" label="Description" :error="blacklist_error.description" />


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
    <dialog ref="import_dialog" @click.self="createBlacklistDialog" v-if="toggle_import_dialog" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="submitImport">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-right" @click="createBlacklistDialog"><i class="pi pi-times"></i></h5>

              <div class="col-md-12">
                <h4>Import blacklist emails from a .csv file</h4>
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
                  label="Select Email Column"
                  @select="updateEmailMapping"
              />
              <FormControlSelect2
                  v-if="mapping.option.length"
                  class="mb-4 mt-3"
                  v-model="reason_mapping.value"
                  :col_md="12"
                  :options="mapping.option"
                  label="Select Reason Column (Optional)"
                  @select="updateReasonMapping"
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
  notify, ROUTE_USER_BLACKLIST,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Pagination from "../../layout/Paignation.vue";

export default {
  name: 'Blacklist',
  components: {
    Pagination,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header},
  data() {
    return {
      toggle_blacklist_dialog: false,
      toggle_import_dialog: false,
      revise_loading_id:'',
      delete_campaign_id:'',
      showPopup:false,
      isLineLoading:false,
      shimmer:true,
      count:0,
      blacklists:'',
      table:{
        records:10,
        search:'',
        currentPage:1,
        page:1,
      },
      blacklist:{
        id:'',
        email:'',
        description:'',
      },
      blacklist_error:{
        id:'',
        email:'',
        description:'',
      },
      processing:false,
      selectedBlacklists: [],
      mapping: {
        value: null,
        option: [],
        emails: []
      },
      reason_mapping: {
        value: null,
      },

      csvFile: null,
      uploadError: '',
      delete_all:false,
    }
  },
  methods: {
    ROUTE_USER_BLACKLIST() {
      return ROUTE_USER_BLACKLIST
    },
    page(page=1){
      this.table.page=page;
      this.list();
    },
    openCreateModal() {
      this.toggle_blacklist_dialog = true;
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

    createBlacklistDialog() {
      this.toggle_blacklist_dialog = false;
      this.$refs.dialog?.close();
      this.toggle_import_dialog=false;
      this.$refs.import_dialog?.close();

    },
    manageDeletePopup(type) {
      if(type === 'all'){
        this.delete_all=true;
      }
      if(type === 'selected'){
        if (this.selectedBlacklists.length === 0) return;
        this.delete_all=false;
      }
      this.showPopup = true;
    },
    async list(line=false) {
      if (line){this.isLineLoading=true}else{this.shimmer=true;}
      await this.axios.post(`user/blacklists/fetch`,this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.blacklists = data;
        this.countBlacklist();
      }).finally(() => {
        this.isLineLoading=false;
        this.shimmer=false;
      });
    },
    toggleRowSelection(id) {
      const index = this.selectedBlacklists.indexOf(id);
      if (index === -1) {
        this.selectedBlacklists.push(id);
      } else {
        this.selectedBlacklists.splice(index, 1);
      }
    },
    deleteEmails() {
      const payload ={ ids: this.selectedBlacklists,all:this.delete_all }
      this.axios.post('user/blacklists/delete', payload).then((response) => {
            notify(response.data.message);
            this.list();
            this.showPopup = false;
            this.selectedBlacklists = [];
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    createBlacklist(data){
      this.processing=true;
      this.axios.post('user/blacklists/store', data).then((response) => {
        notify(response.data.message);
        this.list();
        this.toggle_blacklist_dialog=false;
      }).catch((error) => {
        this.blacklist_error=this.$flattenErrors(error);
      }).finally((res)=>{
        this.processing=false;
      });
    },
    countBlacklist(){
      this.axios.post('user/blacklists/count').then((response) => {
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
      this.axios.post('/user/blacklists/parse-csv', formData)
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

    updateEmailMapping(selected) {
      this.mapping.value = selected;
    },
    updateReasonMapping(selected) {
      this.reason_mapping.value = selected;
    },


    submitImport() {
      if (!this.csvFile || !this.mapping.value) {
        this.uploadError = "CSV file and email column are required.";
        return;
      }

      const formData = new FormData();
      formData.append('file', this.csvFile);
      formData.append('email_column', this.mapping.value);
      formData.append('reason_column', this.reason_mapping.value);

      this.processing = true;
      this.axios.post('/user/blacklists/import', formData)
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
    }
  },
  created() {
    this.list();
    this.countBlacklist();
  }
}
</script>
