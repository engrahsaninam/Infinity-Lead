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
                <h4>{{ list.name }}</h4>
                <Breadcrumb :items="[ROUTE_USER_LISTS(),ROUTE_USER_LISTS_SHOW()]"/>
              </div>
              <LineLoader v-if="isLineLoading"/>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card border-0">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-md-12">
                        <button v-if="csv.type"  class="btn btn-sm btn-light border float-right" @click="clickType('cancel')"><i class="pi pi-times"></i></button>
                    </div>
                  </div>
                  <div class="row justify-content-center" v-if="!list.csv">
                    <div class="col-md-3" v-if="!csv.type">
                        <div class="text-center border p-5 w2-step-4" :class="{ 'border-success-w2-step-4': csv.type === SHEET_TYPE_CSV() }" @click="clickType(SHEET_TYPE_CSV())">
                            <div class="logo-sm">
                                <img src="../../../assets/img/csv.png" width="50%" alt="" class="img-fluid">
                                <p class="mt-3">Import from CSV</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="!csv.type">
                        <div class="text-center border p-5 w2-step-4" :class="{ 'border-success-w2-step-4': csv.type === SHEET_TYPE_GOOGLE() }" @click="clickType(SHEET_TYPE_GOOGLE() )">
                            <div class="logo-sm">
                                <img src="../../../assets/img/sheet.png" width="50%"  alt="" class="img-fluid" >
                                <p class="mt-3">Import from Google Sheet</p>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="row" v-else>
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-stripped">
                                <tr>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Total Rows</th>
                                    <th>Total Uploaded</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>{{ list.csv.file_name }}</td>
                                    <td>{{ list.csv.type }}</td>
                                    <td>{{ list.csv.rows }}</td>
                                    <td>{{ list.csv.uploaded }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm"  @click="deletePopup(list.id)"><i class="mdi mdi-delete-outline"></i> </button>
                                    </td>
                                </tr>
                            </table>
                            <RecordOptions v-model="table.records" @change="___list" class="col-md-2 float-left"/>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                <tr class="text-muted small">
                                    <th v-for="header in list.csv.headers" :key="header">{{ header }}</th>
                                </tr>
                                <tr v-if="rows && rows.data && rows.data.length > 0" v-for="(record,index) in rows.data" :key="index">
                                    <td v-for="(_, colIndex) in list.csv.columns" :key="colIndex">
                                        {{record['column_'+(colIndex+1)]}}
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td align="center" colspan="100%"><i>No record found.</i></td>
                                </tr>
                                </tbody>
                            </table>
                            <Pagination :data="rows" :list="___list"/>
                        </div>
                    </div>
                <Csv v-if="csv.type === SHEET_TYPE_CSV()" :cancelType="finishUpload"/>
                <Sheet v-if="csv.type === SHEET_TYPE_GOOGLE()" :cancelType="finishUpload"/>
            </div>
          </div>
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
      @confirm="deleteCsv"
      @cancel="showPopup = false"
  />
</template>
<script>
import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
    LIST_STATUS_DRAFTED,
    notify,
    ROUTE_USER_LISTS,
    ROUTE_USER_LISTS_SHOW,
    SHEET_TYPE_CSV,
    SHEET_TYPE_GOOGLE,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import RecordOptions from "../../components/RecordOptions.vue";
import Pagination from "../../layout/Paignation.vue";
import Step_5_csv from "../campaigns/create/Step_5_csv.vue";
import Step_5_sheet from "../campaigns/create/Step_5_sheet.vue";
import Csv from "./Csv.vue";
import Sheet from "./Sheet.vue";

export default {
  name: 'ListShow',
  components: {
    Pagination,
    RecordOptions,
    Sheet,
    Csv,
    Step_5_sheet,
    FormControlSelect2,
    FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header},
    data() {
        return {
            showPopup:false,
            csv:{
                type:'',
                list_id:this.$route.params.id,
            },
            list:{
                id:this.$route.params.id,
            },
            isLineLoading:false,
            TYPES:[
                {key:SHEET_TYPE_GOOGLE,value:SHEET_TYPE_GOOGLE},
                {key:SHEET_TYPE_CSV,value:SHEET_TYPE_CSV},
            ],
            rows:[],
            table: {
                records: 10,
            },

        }
    },
  methods: {
    deletePopup() {
        this.showPopup = true;
    },
    ROUTE_USER_LISTS(){
        return ROUTE_USER_LISTS;
    },
    ROUTE_USER_LISTS_SHOW(){
        return ROUTE_USER_LISTS_SHOW;
    },
    SHEET_TYPE_CSV(){
        return SHEET_TYPE_CSV;
    },
    SHEET_TYPE_GOOGLE(){
        return SHEET_TYPE_GOOGLE;
    },
    async show() {
        this.isLineLoading=true;
        await this.axios.post(`user/lists/show`,{id:this.$route.params.id}).then(({ data }) => {
        this.list = data.data;
      }).finally(() => {
        this.isLineLoading=false;
      });
    },
    clickType(type){
        if(type === 'cancel'){
            this.csv.type='';
        }else{
            this.csv.type=type;
        }
    },
    finishUpload(){
        this.clickType('cancel');
        this.show();
        this.pagination();
    },

    ___list(page = 1) {
        this.pagination(page);
    },
    deleteCsv() {
        const payload ={ id: this.$route.params.id }
        this.axios.post('user/lists/remove-csv', payload).then((response) => {
            notify(response.data.message);
            this.showPopup = false;
            this.show();
        }).catch((error) => {
            this.$flattenErrors(error);
        });
    },
    pagination(page=1){
      this.axios.post('user/lists/pagination',{id:this.$route.params.id, records: this.table.records,page:page}).then(({ data }) => {
        this.rows=data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },

  },
  created() {
    this.show();
    this.pagination();
  }
}
</script>
