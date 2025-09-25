<template>
  <div id="layout-wrapper">
    <Header />
    <Sidebar />
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <Crumb/>
          <div class="card">
            <div class="card-header">
              <ProgressBar :step="5"/>
            </div>
            <div class="card-body camp-card-h">
              <Loader :loading="processing" v-if="processing"/>
              <div class="col-md-12" v-else>
                <div v-if="rows && rows.data && rows.data.length > 0" class="row justify-content-center mb-4">
                  <div class="logo-sm pt-3 col-md-4 border text-center rounded py-5 mb-3">
                    <img src="../../../../assets/img/csv.png" v-if="campaign.sheet_type===SHEET_TYPE_CSV()" alt="" height="80" class="mt-5">
                    <img src="../../../../assets/img/sheet.png" v-else alt="" height="80" class="mt-5">
                    <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
                    <button class="btn btn-light bg-light border text-danger" @click="removeCsv">Delete</button>
                    <FormControlSelect2 class="mb-4 mt-3" v-model="mapping.value" :col_md="12" :options="mapping.option" label="Select Email Column " @select="updateEmailMapping"/>
                  </div>
                  <div class="table-responsive"  v-if="campaign.header && campaign.header.headers">
                    <RecordOptions v-model="table.records" @change="list" class="col-md-2"/>
                    <table class="table table-sm table-bordered">
                      <tr class="text-muted small">
                        <th v-for="header in campaign.header.headers" :key="header">{{ header }}</th>
                      </tr>
                      <tr v-if="rows && rows.data && rows.data.length > 0" v-for="(record,index) in rows.data" :key="index">
                        <td v-for="(_, colIndex) in campaign.count_header" :key="colIndex">
                          {{record['column_'+(colIndex+1)]}}
                        </td>
                      </tr>
                      <tr v-else>
                        <td align="center" colspan="100%"><i>No record found.</i></td>
                      </tr>
                    </table>
                    <Pagination :data="rows" :list="list"/>
                  </div>
                </div>
                <div v-if="campaign.sheet_type===SHEET_TYPE_CSV()">
                  <div  class="row justify-content-center">
                    <div class="col-md-4" v-if="!csv.csv">
                      <h4>Import your leads from a .csv file</h4>
                      <div class="drag-drop p-5 text-center">
                        <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
                        <p class="small mb-0">Drag & drop file here</p>
                        <p class="small mt-0">or</p>
                        <label for="csv-upload" class="btn btn-primary mb-4"  >
                          Upload .csv file
                          <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
                        </label>
                        <p class="small">Import a .csv file (10,000 leads max). <a href="/leads-template.csv" download="leads-template.csv">Start with our template</a></p>
                      </div>
                    </div>
                    <div class="col-md-4" v-else>
                      <button class="btn btn-primary my-5" @click="storeCsv">
                        <i class="pi pi-upload"></i>
                        Upload <span v-if="csv.name">{{csv.name}}</span>
                      </button>
                      <button class="btn" @click="csvProgress">Check progress {{uploadPercentage}}%</button>
                    </div>
                  </div>
                </div>
                <div class="col-md-12" v-if="campaign.sheet_type=== SHEET_TYPE_GOOGLE()">
                  <div v-if="!google_sheets || google_sheets.length===0" class="row justify-content-center">
                    <div class="col-md-5">
                      <div class="card">
                        <div class="card-body">
                          <div class="row justify-content-center">
                            <div class="col-md-10 pt-3 pl-4">
                              <div class="">
                                <img class="float-left " src="../../../../assets/img/google.png" alt="" width="40" height="40" >
                                <div class="float-left text-start ml-2">
                                  <h5 class="m-0">Connect your Google Spreadsheet</h5>
                                  <p class="text-muted small ">Gmail / Google Workspace</p>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row ">
                            <div class="col-md-12 text-center">
                              <h6 class="small m-0">Allow InfinityLead to access your Google Workspace.</h6>
                              <p class="small text-success">
                                You only need to do this once per domain.
                              </p>
                            </div>
                          </div>
                          <hr>

                          <div class="row">
                            <div class="col-md-12">
                              <ol class="small text-muted">
                                <li>Watch our <a  class="" target="_blank" href="https://www.youtube.com/watch?v=6Lxt8EgyTUI">video tutorial</a>.</li>
                                <li>Log into your <a  class="" target="_blank" href="https://admin.google.com/">Google Workspace Admin Panel</a>.</li>
                                <li>On the left side, click:
                                  <br>
                                  <b>Show more --> Security --> Access and data control --> API controls --> Manage Third-Party App Access.</b>
                                  You can also <a class="font-weight-bold" target="_blank" href="https://admin.google.com/u/0/ac/owl/list?tab=configuredApps">click here</a> to be taken directly to this page.</li>
                                <li>Click “Configure new app”.</li>
                                <li>
                                  Use the following Client ID to search for InfinityLead:
                                  <div class="bg-light border p-2 rounded cursor-pointer" @click="copyToClipboard(excelClientId)">
                                    {{excelClientId}}
                                  </div>
                                </li>
                                <li>Select and approve InfinityLead to access your Google Workspace. Make sure you select “Trusted” in the “Access to Google Data” section. Remember to click “Finish” at the end.</li>
                                <li>
                                  Click the “Connect button” below:
                                </li>
                              </ol>
                              <center>
                                <a  @click="connectGoogleSheet" class="btn btn-outline-primary w-75">Connect</a>
                              </center>
                              <center>
                                <button @click="getListOfSpreadsheets" class="btn btn-link"><i class="pi pi-refresh"></i> Already connected? Refresh here.!</button>
                              </center>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="" v-else>
                    <FormControlSelect2 :options="google_sheets" v-model="campaign.spreadsheet_id" label="Select Spreadsheet" @select="onGoogleSheetSelection"/>
                    <FormControlSelect2 v-if="sheets.length>0" v-model="campaign.sheet_id" :options="sheets" label="Select Sheet" @select="onSheetSelection"/>
                    <button class="btn" @click="onSheetSelection">Show Sheet Data</button>
                    <div class="table-responsive" v-if="campaign.header">
                      <table class="table table-sm table-bordered">
                        <tr class="text-muted small">
                          <th v-for="header in campaign.header.headers" :key="header">{{ header }}</th>
                        </tr>
                        <tr v-for="(option, rowIndex) in campaign.rows" :key="rowIndex">
                          <td v-for="(_, colIndex) in campaign.count_header" :key="colIndex">
                            {{option['column_'+(colIndex+1)]}}
                          </td>
                        </tr>
                      </table>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_4()" class="btn btn-primary float-left"> <i class="pi pi-arrow-left"></i> Back </router-link>
              <button class="btn btn-primary float-right" @click="validateCsv"> Next <i class="pi pi-arrow-right"></i> </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script >
import Sidebar from "../../../layout/Sidebar.vue";
import Loader from "../../../components/Loader.vue";
import Header from "../../../layout/Header.vue";
import Breadcrumb from "../../../components/Breadcrumb.vue";
import {
  FORM_DATA,
  notify,
  NOTIFY_TYPE_ERROR,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE,
  ROUTE_USER_CAMPAIGNS_STEP_2,
  ROUTE_USER_CAMPAIGNS_STEP_3,
  ROUTE_USER_CAMPAIGNS_STEP_4, ROUTE_USER_CAMPAIGNS_STEP_5, ROUTE_USER_CAMPAIGNS_STEP_6,
  SHEET_TYPE_CSV,
  SHEET_TYPE_GOOGLE
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import RecordOptions from "../../../components/RecordOptions.vue";
import Pagination from "../../../layout/Paignation.vue";

export default {
  name:'Step5',
  data(){
    return {
      progressInterval:null,
      uploadPercentage: 0,
      sheets:[],
      google_sheets:[],
      master_google_sheets:[],
      processing:false,
      update_mode: Boolean(this.$route.params.id),
      campaign:{
        id:this.$route.params.id,
      },
      mapping:{
        id:this.$route.params.id,
        option:[],
        value:'',
      },
      table: {
        records: 10,
      },
      rows:[],
      csv:{
        id:this.$route.params.id,
        csv:'',
        name:'',
      },
    }
  },
  methods: {
    SHEET_TYPE_GOOGLE() {
      return SHEET_TYPE_GOOGLE
    },
    ROUTE_USER_CAMPAIGNS_STEP_4() {
      return ROUTE_USER_CAMPAIGNS_STEP_4.replace(':id',this.$route.params.id);
    },
    SHEET_TYPE_CSV() {
      return SHEET_TYPE_CSV
    },

    ROUTE_USER_CAMPAIGNS_STEP_3() {
      return ROUTE_USER_CAMPAIGNS_STEP_3.replace(':id',this.$route.params.id);
    },

    ROUTE_USER_CAMPAIGNS_STEP_6() {
      return ROUTE_USER_CAMPAIGNS_STEP_6.replace(':id',this.$route.params.id);
    },

    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },

    async edit() {
      this.processing=true;
      await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
        this.campaign = response.data.data;
        if (this.campaign && this.campaign.header) {
          this.mapping.option = Object.values(this.campaign.header.headers).map((header, index) => ({
            key: 'column_' + (index + 1),
            value: header
          }));
          if (this.campaign.header.mapping){
            Object.entries(this.campaign.header.mapping).forEach(([key, value]) => {
              this.mapping.value=key;
            });
          }
          this.headerOptions = Object.values(this.campaign.header.headers).map((header) => ({
            key: header,
            value: header
          }));
        }
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    onCsvChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.csv.csv=file;
        this.csv.name=file.name;
      }else{
        notify('Something went wrong! Please try again later!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
      }
    },
    storeCsv(){
      const data=FORM_DATA(this.csv);
      this.axios.post('user/campaigns/csv',data).then(({ data }) => {
        this.edit();
        this.csvProgress(); // Start progress checking every second
        this.paginationRows();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    updateEmailMapping(){
      this.axios.post('user/campaigns/email-mapping',this.mapping).then(({ data }) => {
        //this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    removeCsv(){
      this.axios.post('user/campaigns/remove-csv',this.campaign).then(({ data }) => {
        this.paginationRows();
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    paginationRows(page=1){
      this.axios.post('user/campaigns/pagination-rows',{id:this.$route.params.id, records: this.table.records,page:page}).then(({ data }) => {
        this.rows=data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    list(page = 1) {
      this.paginationRows(page);
    },
    validateCsv(){
      if (this.campaign.rows !== ''){
        if (!this.mapping.value){
          notify('Select email column for mapping!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
          return false;
        }
      }else{
        if (this.campaign.sheet_type===SHEET_TYPE_CSV){
          notify('CSV data not readable', {autoClose: 3000},NOTIFY_TYPE_ERROR);
          return false;
        }
      }
      this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_6())
    },
    connectGoogleSheet() {
      const user = JSON.parse(localStorage.getItem('user'));
      window.location.href='/google/sheet-access?campaign='+this.campaign.id+'&user_id='+user.id;
    },
    async getListOfSpreadsheets(){
      this.processing=true;
      await this.axios.post('user/google-sheets/fetch').then(({ data }) => {
        this.edit();
        this.google_sheets = [];
        this.master_google_sheets = data;
        data.forEach((sheet)=>{
          this.google_sheets.push({
            key:sheet.id,
            value:sheet.name,
          });
        })
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    onGoogleSheetSelection(selectedSpreadSheet) {
      this.sheets=[];
      var record = this.master_google_sheets.find(SS => SS.id === selectedSpreadSheet);
      record.sheets.forEach((sheet)=>{
        this.sheets.push({
          key: sheet.id,
          value: sheet.name
        });
      });
    },
    onSheetSelection() {
      this.processing=true;
      const spreadSheet = this.master_google_sheets.find(SS => SS.id === this.campaign.spreadsheet_id);
      this.axios.post('user/campaigns/read-sheet',{spreadsheet:spreadSheet,sheet:this.campaign.sheet_id,id:this.campaign.id}).then(({ data }) => {
        this.edit();
        this.paginationRows();
      }).catch((error) => {
        this.google_sheets='';
        this.master_google_sheets='';
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    copyToClipboard(text){
      navigator.clipboard.writeText(text).then(() => {
        notify('Copied to clipboard');
      }).catch(() => {
        alert('Failed to copy URL. Please try again.');
      });
    },


    csvProgress() {
      this.axios.post('user/campaigns/csv-progress', { id: this.$route.params.id }).then(({ data }) => {
        this.uploadPercentage = (data.data.uploaded / data.data.length * 100).toFixed(2);
        if (this.uploadPercentage < 100) {
          this.csvProgress();
        }
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    }

  },
  components: {Pagination, RecordOptions, FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar,Header},
  created() {
    this.edit();
    this.paginationRows();
  }
}
</script>