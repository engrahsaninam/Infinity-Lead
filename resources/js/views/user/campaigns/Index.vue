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
                <h4>Campaigns</h4>
                <Breadcrumb :items="[ROUTE_USER_CAMPAIGNS()]"/>
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
                        <button class="btn btn-danger mr-2" v-if="selectedCampaigns.length && show_checkbox" @click="manageDeletePopup"><i class="mdi mdi-delete-outline mb-2 me-2"></i> Delete {{selectedCampaigns.length}} Selected</button>

                        <button class="btn btn-light border text-muted mr-2" @click="show_checkbox=true;" v-if="!show_checkbox && !this.selectedCampaigns.length"><i class="mdi mdi-check-box-outline me-1"></i> Select Campaigns</button>
                        <button class="btn btn-light border text-muted mr-2" @click="show_checkbox=false;selectedCampaigns=[];" v-else> Cancel </button>

                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1()" class="btn btn-primary btn-rounded waves-effect waves-light text-light"><i class="mdi mdi-plus me-1"></i> Create Campaign</router-link>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <tr v-if="campaigns.data.length === 0">
                        <td colspan="4" class="text-center">
                          <i class="text-muted">No campaigns found.</i>
                        </td>
                      </tr>
                      <tr v-for="campaign in campaigns.data" >
                        <td>
                          <label class="il-checkbox-label" v-if="show_checkbox">
                            <input
                                type="checkbox"
                                :checked="selectedCampaigns.includes(campaign.id)"
                                @change="selectCampaign(campaign.id)"
                            >
                            <span class="checkmark"></span>
                          </label>

                          <h5 class="m-0 " :class="{'ml-4':this.show_checkbox}" style="margin-top: -4px!important;"><router-link :to="
                          (campaign.status === CAMPAIGN_STATUS_DRAFT())
                          ? ROUTE_USER_CAMPAIGNS_STEP_1(campaign.id)
                          :ROUTE_USER_CAMPAIGNS_SHOW(campaign.id)
                          ">{{campaign.name}}

                          </router-link></h5>
                          <p class="text-muted small m-0">Created on {{campaign.created_on['d']}} {{campaign.created_on['F']}}, {{campaign.created_on['Y']}}</p>
                        </td>
                        <td>
                          <span class="badge badge-pill font-weight-light badge-soft-secondary font-size-15" v-if="campaign.status === CAMPAIGN_STATUS_DRAFT()">Draft</span>
                          <span class="badge badge-pill font-weight-light badge-soft-success font-size-15" v-if="campaign.status === CAMPAIGN_STATUS_LAUNCHED()">Active</span>
                          <span class="badge badge-pill font-weight-light badge-secondary font-size-15" v-if="campaign.status === CAMPAIGN_STATUS_PAUSED()">Paused</span>
                          <span class="badge badge-pill font-weight-light badge-success font-size-15" v-if="campaign.status === CAMPAIGN_STATUS_COMPLETED()">Completed</span>
                        </td>

                        <td>
                          <h6><i class="pi pi-user"></i> {{campaign.total_sent_count}} <i class="pi pi-reply"></i> {{campaign.total_replied_count}}</h6>
                        </td>
                        <td>
                          <span v-if="campaign.status === CAMPAIGN_STATUS_LAUNCHED() || campaign.status === CAMPAIGN_STATUS_PAUSED()">
                          {{campaign.total_sent_count}} / {{campaign.total_count}}
                          </span>
                          <span v-if="campaign.status === CAMPAIGN_STATUS_COMPLETED()">{{campaign.total_count}}</span>
                        </td>
                        <td>
                          <button @click="duplicate(campaign.id)" class="btn btn-sm border mr-2"><i class="pi pi-copy"></i></button>
                          <button @click="updateStatus(campaign.id)" class="btn btn-sm border mr-2" v-if="campaign.status === CAMPAIGN_STATUS_LAUNCHED()"><i class="mdi mdi-pause"></i></button>
                          <button @click="updateStatus(campaign.id)" class="btn btn-sm border mr-2"  v-if="campaign.status === CAMPAIGN_STATUS_PAUSED()"><i class="mdi mdi-play"></i></button>

                          <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1(campaign.id)" v-if="campaign.status < CAMPAIGN_STATUS_COMPLETED()" class="btn btn-sm border"><i class="mdi mdi-pencil "></i></router-link>

                          <button @click="revise(campaign.id)" v-if="campaign.status !== CAMPAIGN_STATUS_DRAFT()" class="btn btn-sm border ml-2">
                            <i :class="['mdi', 'mdi-refresh', { 'mdi-spin': revise_loading_id === campaign.id }]"></i>
                          </button>

                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="card-header border-bottom-0">
                  <Pagination :list="page" :data="campaigns"/>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container " v-else>
          <div class="row justify-content-center align-items-center" style="min-height: 80vh">
            <div class="col-md-6 text-center ">
              <h4>Create your first campaign</h4>
              <p class="text-muted">Build custom campaigns to automate emails, set more meetings, and convert leads into paying customers.</p>
              <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1()" class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i class="mdi mdi-plus me-1"></i> Create Campaign</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete these campaigns?"
      @confirm="deleteCampaign"
      @cancel="showPopup = false"
  />
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  CAMPAIGN_STATUS_COMPLETED,
  CAMPAIGN_STATUS_DRAFT, CAMPAIGN_STATUS_LAUNCHED, CAMPAIGN_STATUS_PAUSED,
  notify,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_CAMPAIGNS_SHOW, ROUTE_USER_CAMPAIGNS_STEP_1,
  ROUTE_USER_CAMPAIGNS_UPDATE
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import Pagination from "../../layout/Paignation.vue";

export default {
  name: 'Campaigns',
  components: {Pagination, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header},
  data() {
    return {
      show_checkbox:false,
      revise_loading_id:'',
      delete_campaign_id:'',
      showPopup:false,
      isLineLoading:false,
      shimmer:true,
      count:0,
      campaigns:'',
      table:{
        records:10,
        search:'',
        currentPage:1,
        page:1,
      },
      selectedCampaigns:[],
    }
  },
  methods: {

    selectCampaign(id){
      const index = this.selectedCampaigns.indexOf(id);
      if (index === -1) {
        this.selectedCampaigns.push(id);
      } else {
        this.selectedCampaigns.splice(index, 1);
      }
      if (!this.selectedCampaigns){
        this.show_checkbox=false;
      }
    },
    CAMPAIGN_STATUS_COMPLETED() {
      return CAMPAIGN_STATUS_COMPLETED
    },
    manageDeletePopup() {
      if (this.selectedCampaigns.length === 0) return;
      this.showPopup = true;
    },

    ROUTE_USER_CAMPAIGNS_STEP_1(id='') {
      return ROUTE_USER_CAMPAIGNS_STEP_1.replace(':id?',id??'')
    },
    ROUTE_USER_CAMPAIGNS_SHOW(id) {
      return ROUTE_USER_CAMPAIGNS_SHOW.replace(':id',id)
    },
    CAMPAIGN_STATUS_PAUSED() {
      return CAMPAIGN_STATUS_PAUSED
    },
    CAMPAIGN_STATUS_LAUNCHED() {
      return CAMPAIGN_STATUS_LAUNCHED
    },
    CAMPAIGN_STATUS_DRAFT() {
      return CAMPAIGN_STATUS_DRAFT
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    async list(line=false) {
      if (line){this.isLineLoading=true}else{this.shimmer=true;}
      await this.axios.post(`user/campaigns/fetch`,this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.campaigns = data;
      }).finally(() => {
        this.isLineLoading=false;
        this.shimmer=false;
      });
    },
    page(page=1){
      this.table.page=page;
      this.list();
    },
    deleteCampaign(){
      this.isLineLoading=true;
      this.axios.post('user/campaigns/delete', {'id':this.selectedCampaigns}).then((response) => {
        notify(response.data.message);
        this.list();
        this.selectedCampaigns=[];
        this.showPopup=false;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((proc)=>{
        this.isLineLoading=false;
      });
    },
    updateStatus(id) {
      this.axios.post('user/campaigns/play-pause', { id }).then((response) => {
        const campaign = this.campaigns.data.find(c => c.id === id);
        if (campaign) {
          if (campaign.status === this.CAMPAIGN_STATUS_LAUNCHED()) {
            campaign.status = this.CAMPAIGN_STATUS_PAUSED();
          } else if (campaign.status === this.CAMPAIGN_STATUS_PAUSED()) {
            campaign.status = this.CAMPAIGN_STATUS_LAUNCHED();
          }
        }
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    duplicate(id) {
      this.isLineLoading=true;
      this.axios.post('user/campaigns/duplicate', { id }).then((response) => {
        this.list();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.isLineLoading=false;
      });
    },
    
    countCampaigns(){
      this.axios.post('user/campaigns/count').then((response) => {
        this.count=response.data.data;
      });
    },
    revise(id){
      this.revise_loading_id=id;
      this.axios.post('user/campaigns/revise', {'id':id}).then((response) => {
        notify(response.data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(()=>{
        this.revise_loading_id='';
      });
    },
  },
  created() {
    this.list();
    this.countCampaigns();
  }
}
</script>
