<template>
  <Transition name="bounce">
    <dialog ref="dialog" v-if="show_popup" class="col-md-8 top-0 h-75">
      <div class="d-flex align-items-center justify-content-center h-100">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <h5 class="float-left">Processing...</h5>
              <h5 class="float-right">
                <i class="pi pi-spinner-dotted pi-spin"></i>
              </h5>
            </div>
          </div>
          <div class="progress">
            <div
                class="progress-bar progress-bar-striped"
                role="progressbar"
                :style="{ width: progress.percentage + '%' }"
                :aria-valuenow="progress.percentage"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
          </div>
          <div class="float-right"> {{ number_format(progress.uploaded) }} / {{ number_format(progress.total) }} </div>
          <div class="float-left"> {{ progress.percentage }} % </div>
        </div>
      </div>
    </dialog>
  </Transition>
  <Loader :loading="processing" v-if="processing"/>
  <div class="container pt-5" v-else>
    <div  class="row justify-content-center">
      <div class="col-md-5" v-if="!csv.csv">
        <h4>Import more leads into exisiting campaign</h4>
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
      <div class="col-md-4" v-else>
        <div class="btn-group my-5" role="group" aria-label="Basic example">
          <button class="btn btn-primary " @click="storeCsv"><i class="pi pi-upload"></i> Upload <span v-if="csv.name">{{csv.name}}</span></button>
          <button class="btn btn-light border text-muted" @click="cancelCsv">Cancel</button>
        </div>
      </div>
    </div>
    <div >
      <div  class="row justify-content-center mb-4">
        <div class="table-responsive"  v-if="campaign.header && campaign.header.headers" >
          <h3 class="d-flex align-items-center">
            <img src="../../../../assets/img/csv.png" alt="" height="40" class="mr-3">
            <span class="text-muted small mt-3">{{campaign.csv_name}}</span>
          </h3>
          <table class="table table-sm table-bordered">
            <tbody>
              <tr class="text-muted small">
                <th v-for="header in campaign.header.headers" :key="header">{{ header }}</th>
              </tr>
              <tr>
                <td colspan="100%" class="text-danger">Note: Make sure uploaded file should have same headers as listed above.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>



</template>
<script >
import {
  FORM_DATA,
  notify,
  NOTIFY_TYPE_ERROR,
  number_format,
  ROUTE_USER_CAMPAIGNS, ROUTE_USER_CAMPAIGNS_SHOW,
} from "../../../../constants.js";
import FormInputTooltip from "../../../components/FormInputTooltip.vue";
import Loader from "../../../components/Loader.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import RecordOptions from "../../../components/RecordOptions.vue";
import Pagination from "../../../layout/Paignation.vue";

export default {
  name:'AddMoreLeads',
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

    }
  },
  methods: {
    ROUTE_USER_CAMPAIGNS_SHOW() {
      return ROUTE_USER_CAMPAIGNS_SHOW.replace(':id',this.$route.params.id)
    },
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
        if (this.progress.percentage < 100) {
          this.csvProgress();
        }else{
          this.closePopup();
          this.edit();
          this.cancelCsv();
          this.$router.push(this.ROUTE_USER_CAMPAIGNS_SHOW());
        }
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    storeCsv(){
      this.processing=true;
      const data=FORM_DATA(this.csv);
      this.axios.post('user/campaigns/more-csv',data).then(({ data }) => {
        this.showPopup();
        this.csvProgress();
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
    async edit() {
      this.processing=true;
      await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
        this.campaign = response.data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },

  },
  created() {
    this.edit();
  }
}
</script>