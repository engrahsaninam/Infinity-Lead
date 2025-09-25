<template>
  <div id="layout-wrapper">
    <Header/>
    <Sidebar/>
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer"/>
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid" >
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Sales CRM</h4>
                <Breadcrumb :items="[ROUTE_USER_SALES_CRM()]"/>
              </div>
              <LineLoader v-if="isLineLoading"/>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-3">
              <div class="position-relative">
                <input type="text" class="form-control search-input" v-model="table.search" placeholder="Search name, email or phone..." @input="this.list(true)">
                <i class="pi pi-search search-icon"></i>
              </div>
            </div>
            <div class="col-md-2">
              <div class="dropdown float-left">
                <button class="btn btn-link dropdown-toggle" type="button" id="leads" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Display Leads by Status
                </button>
                <div class="dropdown-menu leads-filter-dropdown" aria-labelledby="leads">
                  <a v-for="(value, key) in filters" :key="key"
                     class="dropdown-item"
                     :class="{ 'bg-success text-white': table.filter === key }"
                     @click.prevent="rowsFilter(key)">
                    {{ value.label }}
                  </a>
                </div>
              </div>
            </div>
            <div class="col-sm-7">
              <div class="float-right">

                <button class="btn btn-danger mr-2" v-if="selected_leads.length && show_checkbox" @click="manageDeletePopup"><i class="mdi mdi-delete-outline mb-2 me-2"></i> Delete {{selected_leads.length}} Selected</button>
                <button class="btn btn-light border text-muted mr-2" @click="show_checkbox=true;" v-if="!show_checkbox && !this.selected_leads.length"><i class="mdi mdi-check-box-outline me-1"></i> Select Leads</button>
                <button class="btn btn-light border text-muted mr-2" @click="show_checkbox=false;selected_leads=[];" v-else> Cancel </button>


                <button @click="openImportModal" class="btn btn-success btn-rounded waves-effect waves-light "><i class="mdi mdi-import me-1"></i> Import</button>
                <button @click="openCreateLeadDialog" class="btn btn-primary btn-rounded waves-effect ml-2 waves-light  "><i class="mdi mdi-plus me-1"></i> Create Lead</button>
              </div>
            </div>
          </div>
          <div class="row gap-0">
            <div class="scrollable-row w-100">
              <div class="col-md-3" v-for="(col, index) in columns" :key="index">
                <div class="card bg-light">
                  <div class="card-body" v-if="col.isEditing">
                    <input type="text" v-model="col.name" @blur="updateColumnName(col)" class="form-control"/>
                  </div>
                  <div v-else class="card m-2 mb-0">
                    <div class="card-body">
                      <h5 class="card-title float-left">
                        <p class="p-0 m-0" :style="{ color: col.color }">{{ col.name }}</p>
                        <p class="p-0 m-0 small">{{ col.leads.length }} Lead{{ col.leads.length > 1 ? 's' : '' }}</p>
                      </h5>
                      <div class="dropdown show float-right" v-if="!col.isColorDropdownVisible">
                        <a class="small" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="pi pi-ellipsis-h"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                          <a class="dropdown-item" @click.prevent="editColumnName(col)">Edit Column Name</a>
                          <a class="dropdown-item" @click.prevent="toggleColorDropdown(col)">Edit Column Color</a>
                          <a class="dropdown-item" :href="'/column-lead/export/'+col.id">Export Columns Lead</a>
                          <a class="dropdown-item" @click.prevent="deleteColumn(col.id)">Delete Column</a>
                        </div>
                      </div>
                      <div class="dropdown" :class="{'show': col.isColorDropdownVisible}">
                        <div class="dropdown-menu p-4" :class="{'show': col.isColorDropdownVisible}">
                          <div class="row">
                            <div v-for="(color, colorIndex) in colors" :key="colorIndex" class="mb-2">
                              <div class="color-option rounded-circle" :style="{ backgroundColor: color }" @click="selectColor(col, color)"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div style="height:60vh" class="table-responsive">
                    <draggable
                        :list="col.leads"
                        group="leads"
                        @end="onLeadDrop(col, $event)"
                        @start="onDragStart($event)"
                        item-key="id"
                        class="list-group"
                        :data-index="index"
                    >
                      <div class="card m-2" style="border-style: dashed" v-if="draggingItem">
                        <div class="card-body text-center">
                          <h4 class="card-title text-muted mb-0"><i class=""></i> Drop here</h4>
                        </div>
                      </div>
                      <div class="card m-2 draggable-item" v-for="item in col.leads" :key="item.id" :data-id="item.id" :data-col="col.id" :draggable="true" v-if="col.leads.length>0" :class="{'dragging': draggingItem === item.id}">
                        <div class="card-body" @click="routeToViewPage(item.id)" :class="{'bg-soft-primary':selected_leads.includes(item.id)}">
                          <span class="badge badge-soft-primary float-right">
                            <i class="mdi mdi-email"></i>
                            <span>{{item.chat.length}}</span>
                          </span>
                          <div class="col-md-12" v-if="item.first_name || item.last_name">
                            <h6>{{ item.first_name }} {{ item.last_name }}</h6>
                          </div>
                          <div class="col-md-12 pt-4">
                            <p class="text-muted small m-0"><i class="pi pi-envelope"></i> {{ item.email }}</p>
                            <p class="text-muted small m-0"><i class="pi pi-building"></i> {{ item.company }}</p>
                            <p class="text-muted small m-0"><i class="pi pi-reply"></i> {{ item.created_on['d'] }} {{ item.created_on['M'] }}, {{ item.created_on['y'] }}</p>
                          </div>
                        </div>
                      </div>
                    </draggable>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card border-0 pt-1">
                  <div class="row">
                    <div class="col-md-12" v-if="!show_add_form">
                      <button class="btn btn-primary w-100" @click="show_add_form=true;">Add new Column</button>
                    </div>
                    <div class="col-md-12" v-else>
                      <form @submit.prevent="saveColumn(column)" class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <input type="text" v-model="column.name" placeholder="Add new column" class="form-control border-primary shadow-sm"/>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <button type="submit" class="btn ml-2 btn-primary border float-right"><i class="pi pi-save"></i> Create</button>
                          <button type="button" @click="show_add_form=false;" class="btn btn-light border float-right"><i class="pi pi-times"></i> Cancel</button>
                        </div>
                      </form>
                    </div>
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
    <dialog ref="dialog" @click.self="closeCreateLeadDialog" v-if="toggleCreateLeadDialog" class="col-md-6 top-0">
      <div class="container ">
        <form @submit.prevent="createLead(lead)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">Create Lead</h5>
              <h5 class="float-right" @click="closeCreateLeadDialog"><i class="pi pi-times"></i></h5>
            </div>
            <FormInputTooltip v-model="lead.first_name" label="First Name" placeholder="First Name" :error="lead_errors.first_name" />
            <FormInputTooltip v-model="lead.middle_name" label="Middle Name" placeholder="Middle Name" :error="lead_errors.middle_name" />
            <FormInputTooltip v-model="lead.last_name" label="Last Name" placeholder="Last Name" :error="lead_errors.last_name" />
            <FormInputTooltip v-model="lead.job_title" label="Job Title" placeholder="Job Title" :error="lead_errors.job_title" />
            <FormInputTooltip v-model="lead.email" label="Email" placeholder="Email" :error="lead_errors.email" />
            <FormInputTooltip v-model="lead.phone" label="Phone Number" placeholder="Phone Number" :error="lead_errors.phone" />
            <FormInputTooltip v-model="lead.city" label="City" placeholder="City" :error="lead_errors.city" />
            <FormInputTooltip v-model="lead.company" label="Current Company" placeholder="Current Company" :error="lead_errors.company" />
            <FormInputTooltip v-model="lead.website" label="Company Website" placeholder="Company Website" :error="lead_errors.website" />
            <FormInputTooltip v-model="lead.linkedin" label="LinkedIn Profile" placeholder="LinkedIn Profile" :error="lead_errors.linkedin" />

            
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary  float-right rounded-pill" type="submit" :disabled="processing">
                <span v-if="!processing">Create Lead</span>
                <span v-else>Processing ...</span>
              </button>
            </div>

          </div>
        </form>
      </div>
    </dialog>
  </Transition>


  <Transition name="bounce">
    <dialog ref="import_dialog" @click.self="closeImportDialog" v-if="toggle_import_dialog" class="col-md-5 top-0">
      <div class="container">
        <form @submit.prevent="submitImport">
          <div class="row">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-right" @click="closeImportDialog"><i class="pi pi-times"></i></h5>

              <div v-if="mapping?.options?.length>0">
                <FormControlSelect2 :options="COLUMN_OPTIONS" v-model="mapping.column_id" label="Select Column to Import Leads"/>
                <table class="table table-bordered" >
                  <thead>
                  <tr>
                    <th>Field Name</th>
                    <th>Select CSV Column</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(label, key) in mapping.fields" :key="key">
                    <td>{{ label }}</td>
                    <td>
                      <FormControlSelect2 v-model="mapping.selected[key]" :options="mapping.options" :col_md="12" placeholder="Select column"/>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="col-md-12">
                <h4>Import leads from a .csv file</h4>
                <div class="drag-drop p-5 text-center">
                  <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
                  <p class="small mb-0">Drag & drop file here</p>
                  <p class="small mt-0">or</p>
                  <label for="csv-upload" class="btn btn-primary mb-4">
                    Choose .csv file
                    <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
                  </label>
                </div>
              </div>

            </div>
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-light px-4 mr-2 rounded-pill" type="button" v-if="mapping?.options?.length>0" @click="cancelImport">Cancel</button>

              <button class="btn btn-primary px-4 float-right rounded-pill" v-if="mapping?.options?.length>0" type="submit" :disabled="processing">
                <span v-if="!processing">Save</span>
                <span v-else>Processing ...</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </dialog>
  </Transition>

  <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete these leads?"
      @confirm="deleteLeads"
      @cancel="showPopup = false"
  />
</template>
<script>
import { VueDraggableNext } from "vue-draggable-next";


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  notify,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_SALES_CRM, ROUTE_USER_SALES_CRM_SHOW
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";

import { ref } from "vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import LineLoader from "../../components/LineLoader.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Confirm from "../../components/Confirm.vue";


export default {
  name: 'SalesCrm',
  components: {
    Confirm,
    FormControlSelect2, LineLoader, FormInputTooltip, ref, Shimmer, Breadcrumb, Sidebar, Header,draggable: VueDraggableNext},
  data() {
    return {
      show_checkbox:false,
      selected_leads:[],
      isLineLoading:false,
      enabled: true,
      filters:{
        'all': { label: 'All Leads' },
        'contacted': { label: 'Contacted' },
        'not-contacted': { label: 'Not Contacted' },
        'responded': { label: 'Responded' },
        'not-responded': { label: 'Not Responded' },
      },

      dragging: false,

      processing:false,
      toggleCreateLeadDialog:false,
      show_add_form:false,
      shimmer:true,
      columns:'',
      column:{
        name:'',
      },
      mapping: {
        options: [],
        fields: [],
        selected: {},
        column_id: '',
      },
      showPopup:false,
      colors: [
        '#FF0000', '#FF7F00', '#FFFF00', '#00FF00', '#0000FF', '#4B0082', '#8A2BE2', '#A52A2A',
        '#FFC0CB', '#800000', '#FF6347', '#FFFFE0', '#90EE90', '#ADD8E6', '#D3D3D3', '#808080',
        'black', '#08bdbd', '#FFD700'
      ],

      lead:{
        first_name:'',
        last_name:'',
        middle_name:'',
        job_title:'',
        email:'',
        phone:'',
        city:'',
        company:'',
        website:'',
        linkedin:'',
      },
      COLUMN_OPTIONS:[],
      toggle_import_dialog:false,
      lead_errors:{
        first_name:'',
        last_name:'',
        middle_name:'',
        job_title:'',
        email:'',
        phone:'',
        city:'',
        company:'',
        website:'',
        linkedin:'',
      },
      table:{
        search:'',
        filter:'',
      },
      draggingItem: null,
    }
  },
  methods: {
    selectLead(id){
      const index = this.selected_leads.indexOf(id);
      if (index === -1) {
        this.selected_leads.push(id);
      } else {
        this.selected_leads.splice(index, 1);
      }
    },
    manageDeletePopup() {
      if (this.selected_leads.length === 0) return;
      this.showPopup = true;
    },
    onDragStart(event) {
      const draggedElement = event.item._underlying_vm_;
      if (draggedElement) {
        this.draggingItem = draggedElement.id;
      }
    },
    routeToViewPage(id){
      if (this.show_checkbox){
        this.selectLead(id)
      }else{
        this.$router.push(this.ROUTE_USER_SALES_CRM_SHOW(id))
      }
    },

    closeImportDialog() {
      this.toggle_import_dialog=false;
      this.$nextTick(() => {
        this.$refs.import_dialog?.close();
      });
    },
    onLeadDrop(col, event) {
      this.draggingItem = null;
      const droppedId = Number(event.item.dataset.id);
      this.axios.post('/user/leads/index', {
        column: this.columns[event.to.dataset.index],
        id: droppedId
      }).catch(error => {
      }).then((response)=>{
        this.list();
      });
    },
    deleteLeads(){
      this.isLineLoading=true;
      this.axios.post('user/leads/delete', {'id':this.selected_leads}).then((response) => {
        notify(response.data.message);
        this.list();
        this.selected_leads=[];
        this.showPopup=false;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((proc)=>{
        this.isLineLoading=false;
      });
    },

    ROUTE_USER_SALES_CRM_SHOW(id) {
      return ROUTE_USER_SALES_CRM_SHOW.replace(':id',id)
    },
    openCreateLeadDialog() {
      this.toggleCreateLeadDialog = true;
      this.$nextTick(() => {
        this.$refs.dialog?.showModal();
      });
    },
    cancelImport() {
      this.csvFile = null;
      this.mapping = {
        options: [],
        fields: {},
        selected: {}
      };
    },
    closeCreateLeadDialog() {
      this.toggleCreateLeadDialog = false;
      this.$refs.dialog?.close();
    },
    ROUTE_USER_SALES_CRM() {
      return ROUTE_USER_SALES_CRM
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    async list(line=false) {
      if (line){this.isLineLoading=true}else{this.shimmer=true;}
      await this.axios.post(`user/column/fetch`,this.table).then(({ data }) => {
        this.columns = data.data;
        this.COLUMN_OPTIONS=[];
        this.columns.forEach((col,index)=>{
          this.COLUMN_OPTIONS.push({
            key:col.id,
            value:col.name,
          })
        })
      }).finally(() => {
        this.isLineLoading=false;
        this.shimmer=false;
      });
    },
    editColumnName(col) {
      col.isEditing = true;
    },
    saveColumn(column){
      this.axios.post('user/column/create', column).then((response) => {
        this.list();
        this.column={
          name:'',
        };
        this.show_add_form=false;
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    deleteColumn(id){
      this.axios.post('user/column/delete', {id:id}).then((response) => {
        this.list();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },

    openImportModal() {
      this.toggle_import_dialog = true;
      this.$nextTick(() => {
        this.$refs.import_dialog?.showModal();
      });
    },
    toggleColorDropdown(col) {
      col.isColorDropdownVisible = !col.isColorDropdownVisible;
    },
    async rowsFilter(option){
      this.table.filter=option;
      await this.list(true);
    },
    updateColumnName(column){
      column.isEditing=false;
      this.axios.post('user/column/update', column).then((response) => {
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    selectColor(col, color) {
      col.color = color;
      col.isColorDropdownVisible = false;
      this.axios.post('user/column/update', col).then((response) => {

      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    createLead(lead){
      this.processing=true;
      this.axios.post('user/leads/create', lead).then((response) => {
        this.list();
        this.closeCreateLeadDialog();
        this.lead={
          first_name:'',
          last_name:'',
          middle_name:'',
          job_title:'',
          email:'',
          phone:'',
          city:'',
          company:'',
          website:'',
          linkedin:'',
        };
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.processing=false;
      });
    },
    onCsvChange(e) {
      const file = e.target.files[0];
      if (!file) return;

      this.csvFile = file;
      const formData = new FormData();
      formData.append('file', file);

      this.processing = true;

      this.axios.post('/user/leads/parse-csv', formData)
          .then(({ data }) => {
            this.mapping.options = data.headers.map(h => ({ key: h, value: h }));
            this.mapping.fields = data.columns;
            this.mapping.selected = {};
            for (const key in data.columns) {
              this.mapping.selected[key] = data.suggestions?.[key] || '';
            }
          })
          .catch(error => {
            this.$flattenErrors(error);
          })
          .finally(() => {
            this.processing = false;
          });
    },
    submitImport() {
      if (!this.csvFile) {
        this.uploadError = "Please upload a CSV file.";
        return;
      }
      const formData = new FormData();
      formData.append('file', this.csvFile);
      formData.append('mapping', JSON.stringify(this.mapping.selected));
      formData.append('column_id', this.mapping.column_id);
      this.processing = true;
      this.axios.post('/user/leads/import', formData).then(() => {
        notify('Leads imported successfully.');
        this.list();
        this.cancelImport();
        this.closeImportDialog();
      }).catch(error => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.processing = false;
      });
    }

  },
  created() {
    this.list();
  }
}
</script>

<style>
.draggable-item.dragging{
  opacity: 0.5;
  background: #f1f1f1;
  transform: rotate(2deg)!important;
}
</style>