<template>


  <Loader :loading="processing" v-if="processing"/>
  <div v-else>
    <div  class="row justify-content-center">
      <div class="col-md-12 text-right" v-if="campaign.csv || campaign.header">
        <button class="btn btn-light bg-light border text-danger" @click="removeCsv">Do you want to replace this file?</button>
      </div>
    </div>
    <div  class="row justify-content-center" v-if="!campaign.header">
      <div class="col-md-4" v-if="!csv.csv">
        <h4>Import your leads from a .csv file</h4>
        <div class="drag-drop p-5 text-center">
          <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
          <p class="small mb-0">Drag & drop file here</p>
          <p class="small mt-0">or</p>
          <label for="csv-upload" class="btn btn-primary mb-4"  >
            Choose .csv file
            <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
          </label>
          <p class="small">Import a .csv file (10,000 leads max). <a href="/leads-template.csv" download="leads-template.csv">Start with our template</a></p>
        </div>
      </div>
      <div class="col-md-4" v-else>
        <div class="btn-group my-5" role="group" aria-label="Basic example">
          <button class="btn btn-primary " @click="storeCsv"><i class="pi pi-upload"></i> Upload <span v-if="csv.name">{{csv.name}}</span></button>
          <button class="btn btn-light border text-muted" @click="cancelCsv">Cancel</button>
        </div>
      </div>
    </div>
    <div v-if="campaign.header">
      <div  class="row justify-content-center mb-4">
        <div class="logo-sm pt-3 col-md-4 border text-center rounded py-5 mb-3">
          <img src="../../../../assets/img/csv.png" alt="" height="80" class="mt-5">
          <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
          <FormControlSelect2 class="mb-4 mt-3" v-model="mapping.value" :col_md="12" :options="mapping.option" label="Select Email Column " @select="updateEmailMapping"/>
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
  name:'Step5CSV',
  components: {Pagination, RecordOptions, FormControlSelect2, Loader, FormInputTooltip},
  data(){
    return {
      processing:false,
      progress:{
        total:0,
        uploaded:0,
        percentage:0,
      },
      csv:{
        id:this.$route.params.id,
        csv:'',
        name:'',
      },
      show_popup:true,
      campaign:{
        id:this.$route.params.id,
      },
      mapping:{
        id:this.$route.params.id,
        option:[],
        value:'',
      },
      rows:[],
      table: {
        records: 10,
      },
    }
  },
  methods: {
    number_format,
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
    onCsvChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.csv.csv=file;
        this.csv.name=file.name;
      }else{
        notify('Something went wrong! Please try again later!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
      }
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
    storeCsv(){
      this.processing=true;
      const data=FORM_DATA(this.csv);
      this.axios.post('user/campaigns/csv',data).then(({ data }) => {
        this.edit();
        this.paginationRows();
        this.cancelCsv();
        //this.showPopup();
        //this.csvProgress();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    cancelCsv(){
      this.csv={
        id:this.$route.params.id,
        csv:'',
        name:'',
      }
    },
    removeCsv(){
      this.processing=true;
      this.axios.post('user/campaigns/remove-csv',{id:this.$route.params.id}).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
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

        }
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    paginationRows(page=1){
      this.axios.post('user/campaigns/pagination-rows',{id:this.$route.params.id, records: this.table.records,page:page}).then(({ data }) => {
        this.rows=data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    updateEmailMapping(){
      this.axios.post('user/campaigns/email-mapping',this.mapping).then(({ data }) => {
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    list(page = 1) {
      this.paginationRows(page);
    },
  },
  created() {
    //this.csvProgress();
    this.edit();
    this.paginationRows();

  }
}
</script>
