<template>

  <Loader :loading="processing" v-if="processing"/>
  <div v-else>

    <div  class="row justify-content-center">
      <div class="col-md-12 text-right" v-if="campaign.csv || campaign.header">
        <button class="btn btn-light bg-light border text-danger" @click="removeCsv">Do you want to replace this file?</button>
      </div>
    </div>

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
      <div class="row">
        <FormControlSelect2 :options="google_sheets" v-model="campaign.spreadsheet_id" label="Your Google Spreadsheets List" @select="onGoogleSheetSelection"/>
        <FormControlSelect2 v-model="campaign.sheet_id" :options="sheets" label="Select Sheet to Download from Google SpreadSheets" @select="onSheetSelection"/>
      </div>
      <div class="row justify-content-center">
        <div class="logo-sm pt-3 col-md-4 border text-center rounded py-5 mb-3">
          <img src="../../../../assets/img/google.png" alt="" height="80" class="mt-5">
          <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
          <FormControlSelect2 class="mb-4 mt-3" v-model="mapping.value" :col_md="12" :options="mapping.option" label="Select Email Column " @select="updateEmailMapping"/>
        </div>
      </div>


      <div class="table-responsive"  v-if="campaign.header && campaign.header.headers">
        <RecordOptions v-model="table.records" @change="list" class="col-md-2 float-left"/>
        <button class="btn btn-primary float-right" @click="csvProgress"><i class="pi pi-spinner-dotted"></i> Refresh Rows</button>

        <table class="table table-sm table-bordered">
          <tbody>
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
          </tbody>
        </table>
        <Pagination :data="rows" :list="list"/>
      </div>


    </div>
  </div>
</template>
<script >
import {FORM_DATA, notify, NOTIFY_TYPE_ERROR, number_format} from "../../../../constants.js";
import FormInputTooltip from "../../../components/FormInputTooltip.vue";
import Loader from "../../../components/Loader.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import RecordOptions from "../../../components/RecordOptions.vue";
import Pagination from "../../../layout/Paignation.vue";

export default {
  name:'Step5Sheet',
  components: {Pagination, RecordOptions, FormControlSelect2, Loader, FormInputTooltip},
  data(){
    return {
      campaign:{
        id:this.$route.params.id,
      },
      excelClientId:'',
      processing:false,
      sheets:[],
      google_sheets:[],
      master_google_sheets:[],
      progress:{
        total:0,
        uploaded:0,
        percentage:0,
      },
      show_popup:false,
      rows:[],
      table: {
        records: 10,
      },
      mapping:{
        id:this.$route.params.id,
        option:[],
        value:'',
      },
    }
  },
  methods: {
    number_format,
    async getListOfSpreadsheets(){
      this.processing=true;
      await this.axios.post('user/google-sheets/fetch',{id:this.$route.params.id}).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    onGoogleSheetSelection(selectedSpreadSheet) {
      this.sheets=[];
      const record = this.master_google_sheets.find(SS => SS.id === selectedSpreadSheet);
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
      const data = {
        spreadsheet:spreadSheet,
        sheet:this.campaign.sheet_id,
        id:this.campaign.id
      };
      this.axios.post('user/campaigns/read-sheet',data).then(({ data }) => {
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
    connectGoogleSheet() {
      const user = JSON.parse(localStorage.getItem('user'));
      window.location.href='/google/sheet-access?campaign='+this.$route.params.id+'&user_id='+user.id;
    },
    async fetchExcelClientId() {
      await this.axios.post('excel-client-id').then(({ data }) => {
        this.excelClientId = data.data;
      });
    },
    async edit() {
      this.processing=true;
      await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
        this.campaign = response.data.data;
        this.google_sheets = [];
        this.master_google_sheets = this.campaign.google_sheets;
        this.master_google_sheets.forEach((sheet)=>{
          this.google_sheets.push({
            key:sheet.id,
            value:sheet.name,
          });
        });
        if (this.campaign.spreadsheet_id){
          this.onGoogleSheetSelection(this.campaign.spreadsheet_id)
        }
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

        }

      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    showPopup(){
      this.show_popup=true;
      this.$nextTick(() => {
        this.$refs.dialog?.showModal();
      });
    },
    closePopup() {
      this.show_popup = false;
      this.$refs.dialog?.close();
    },



    csvProgress() {
      this.axios.post('user/campaigns/csv-progress', { id: this.$route.params.id }).then(({ data }) => {
        this.progress.total = data.data.total_rows;
        this.progress.uploaded = data.data.total_uploaded;
        if (this.progress.total && this.progress.uploaded){
          this.progress.percentage = (this.progress.uploaded / this.progress.total * 100).toFixed(2);
        }else{
          this.progress.percentage=0;
        }
        this.paginationRows();

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
    removeCsv(){
      this.processing=true;
      this.axios.post('user/campaigns/remove-csv',{id:this.$route.params.id}).then(({ data }) => {
        this.edit();
        this.csvProgress();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    updateEmailMapping(){
      this.axios.post('user/campaigns/email-mapping',this.mapping).then(({ data }) => {
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
  },
  created() {
    this.fetchExcelClientId();
    this.getListOfSpreadsheets();
    this.csvProgress();
    this.paginationRows();
  }
}
</script>