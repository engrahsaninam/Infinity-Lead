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
              <ProgressBar :step="2"/>
            </div>
            <div class="card-body camp-card-h" >
              <Loader :loading="processing" v-if="processing"/>
              <div class="" v-else>
                <h4>Select email accounts</h4>
                <div class="row" v-for="(account, index) in email_accounts" :key="index" v-if="email_accounts.length>0">
                  <div class="col-md-6 pb-2">
                    <div class="d-flex align-items-center border rounded p-3 cursor-pointer" :class="{'border-primary':this.campaign.accounts?.includes(account.id)}"  @click="syncAccountInCampaign(account.id)">
                      <div class="flex-shrink-0 p-2">
                        <img class="rounded-circle avatar-sm " width="50" v-if="account.profile" :src="account.profile"  :alt="account.email">
                        <img v-else class="rounded-circle avatar-sm " src="../../../../assets/img/mail.png" alt="" width="50" height="50" >

                      </div>
                      <div class="flex-grow-1">
                        <h5 class="font-size-14 mb-0">{{ account.name }} <img v-if="account.type==='google'" class="rounded-circle avatar-sm" src="../../../../assets/img/google.png" alt="" width="20" height="20"></h5>
                        <small>{{ account.email }}</small>
                      </div>
                      <div class="flex-shrink-0 p-2">
                        <img class="rounded-circle avatar-sm" v-if="this.campaign.accounts?.includes(account.id)" src="../../../../assets/img/checkbox.png" alt="" width="35" height="35">

                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-md-6 border border-primary rounded p-5 mx-3 my-3">
                    <p class="m-0 text-muted float-left"><i class="pi pi-exclamation-circle"></i> You have no sender email accounts connected. This is a mandatory setting for your campaign to run.</p>
                    <router-link :to="ROUTE_USER_EMAIL_ACCOUNTS()" class="btn btn-primary float-right ">Connect email now</router-link>
                    <button @click="fetchEmailAccounts" class="btn btn-light float-right "><i class="pi pi-refresh"></i></button>
                  </div>
                </div>
                <div v-if="email_accounts.length>0">
                  <p class="m-0">You can send up to {{(campaign.accounts?.length) * 40}} emails per day. Connect more email accounts to increase volume.</p>
                  <p class="m-0"><b>Important:</b> You must <b class="text-primary">upgrade</b> to a paid plan to connect more than one sender email account.</p>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1()" class="btn btn-primary float-left"> <i class="pi pi-arrow-left"></i> Back </router-link>
              <button class="btn btn-primary float-right" @click="validateEmailAccounts"> Next <i class="pi pi-arrow-right"></i> </button>
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
  notify,
  NOTIFY_TYPE_ERROR,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_CAMPAIGNS_STEP_1, ROUTE_USER_CAMPAIGNS_STEP_2, ROUTE_USER_CAMPAIGNS_STEP_3,
  ROUTE_USER_EMAIL_ACCOUNTS
} from "../../../../constants.js";
import ProgressBar from "./ProgressBar.vue";
import Crumb from "./Crumb.vue";

export default {
  name:'Step2',
  data(){
    return {
      processing:false,
      update_mode: Boolean(this.$route.params.id),
      email_accounts:[],
      campaign:{
        id:this.$route.params.id,
        name:'',
      },
      campaign_error:{
        name:'',
      }
    }
  },
  methods: {
    ROUTE_USER_CAMPAIGNS_STEP_1() {
      return ROUTE_USER_CAMPAIGNS_STEP_1.replace(':id',this.$route.params.id)
    },
    ROUTE_USER_CAMPAIGNS_STEP_3() {
      return ROUTE_USER_CAMPAIGNS_STEP_3.replace(':id',this.$route.params.id)
    },
    ROUTE_USER_EMAIL_ACCOUNTS() {
      return ROUTE_USER_EMAIL_ACCOUNTS
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    async validateEmailAccounts(){
      if (!this.campaign.accounts){
        this.campaign.accounts=[];
      }
      if (this.campaign.accounts.length > 0){
        this.processing=true;
        await this.axios.post('user/campaigns/accounts', this.campaign).then((response) => {
          this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_3())

        }).catch((error) => {
          this.$flattenErrors(error);
        }).finally((final)=>{
          this.processing=false;
        });
      }else{
        notify('Please select at least one email account!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
      }
    },
    async fetchEmailAccounts() {
      await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
        this.email_accounts = data.data;
      });
    },
    syncAccountInCampaign(id) {
      if (!this.campaign.accounts) {
        this.campaign.accounts = [];
      }
      const index = this.campaign.accounts.indexOf(id);
      if (index === -1) {
        this.campaign.accounts.push(id);
      } else {
        this.campaign.accounts.splice(index, 1);
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
  components: {Crumb, ProgressBar, Loader, Breadcrumb, Sidebar,Header},
  created() {
    this.fetchEmailAccounts();
    this.edit();
  }
}
</script>