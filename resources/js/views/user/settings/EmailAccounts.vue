<template>
  <div id="layout-wrapper">
    <Header/>
    <Sidebar/>
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Settings </h4>
                <Breadcrumb :items="[ROUTE_USER_EMAIL_ACCOUNTS()]"/>
              </div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-md-12" v-if="!add_email_account_mode">
              <div class="card" >
                <div class="card-body">
                  <Pills/>
                  <div class="row mb-5 p-md-3 mt-5">
                    <div class="col-md-6">
                      <h5>Email Accounts</h5>
                      <p class="text-muted">Tell us which email accounts you want to use to send campaigns from.
                      </p>
                    </div>
                    <div class="col-md-2 text-right">
                      <button class="btn btn-primary" @click="toggleMode" type="button" ><i class="pi pi-plus"></i> Connect your email</button>
                    </div>
                  </div>
                  <div class="row" v-for="account in accounts">
                    <div class="col-md-6 " >
                      <div class="d-flex align-items-center  border rounded p-3">
                        <div class="flex-shrink-0 p-2">
                          <img class="rounded-circle avatar-sm " width="50" v-if="account.profile" :src="account.profile"  :alt="account.email">
                          <img v-else class=" avatar-sm " src="../../../assets/img/mail.png" alt="" width="50" height="50" >
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0 pb-0">{{account.name}}
                            <img v-if="account.type === EMAIL_TYPE_GOOGLE()" class="rounded-circle avatar-sm " src="../../../assets/img/google.png" alt="" width="20" height="20" >
                          </h6>
                          <p class=" p-0 m-0"><small class="text-muted">{{account.email}} : {{account.host}}</small></p>
                        </div>

                        <div class="flex-shrink-0 p-2">
                          <button v-if="account.type === EMAIL_TYPE_SMTP()"   class="btn btn-sm btn-success mr-1" @click="editSmtpAccount(account)"> <i class="pi pi-pencil"></i></button>
                          <button  class="btn btn-sm btn-outline-danger ms-1" @click="showPop(account.id)">Disconnect email <i class="pi pi-trash"></i></button>
                        </div>

                      </div>
                      <p class="text-muted small p-0">This email account will send a daily randomized volume that is between:
                        {{account.per_minute}} min / {{account.volume}} max
                      </p>

                    </div>
                    <div class="col-md-6" v-if="account.type === EMAIL_TYPE_GOOGLE()">
                      <h6 class="">Control sending volume</h6>
                      <div class="form-group">
                        <label for="min">Per Minute</label>
                        <VueSlider
                            id="min"
                            v-model="account.per_minute"
                            :min="0"
                            :max="40"
                            :tooltip="'always'"
                            @change="updateRange(account,'m' ,$event)"
                        />
                      </div>
                      <div class="form-group">
                        <label for="volume">Volume</label>
                        <VueSlider
                            id="volume"
                            v-model="account.volume"
                            :min="0"
                            :max="40"
                            @change="updateRange(account,'v' ,$event)"
                        />
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="col-md-4" v-else>
              <div class="card" v-if="!moveNext">
                <div class="card-body">
                  <h4 class="mb-4">Connect a new email account</h4>
                  <div class="col-md-12 border rounded  cursor-pointer mt-5" @click="setEmailType(EMAIL_TYPE_GOOGLE())" :class="{'border-primary':emailType===EMAIL_TYPE_GOOGLE()}">
                    <div class="row ">
                      <div class="col">
                        <img class="mt-4" src="../../../assets/img/google.png" alt="" width="30" height="30" >
                      </div>
                      <div class="col-md-10 pt-3 pl-4">
                        <h5 class="m-0">Gmail / Google Workspace</h5>
                        <p class="text-muted small">OAuth (less disconnects - recommended)</p>
                      </div>
                      <div class="">
                        <p class="float-right mt-4 mr-3"><i class="pi pi-arrow-right"></i></p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 border rounded cursor-pointer  my-2" @click="setEmailType(EMAIL_TYPE_SMTP())" :class="{'border-primary':emailType===EMAIL_TYPE_SMTP()}">
                    <div class="row ">
                      <div class="col">
                        <img class="mt-4" src="../../../assets/img/mail.png" alt="" width="30" height="30" >
                      </div>
                      <div class="col-md-10 pt-3 pl-4">
                        <h5 class="m-0">SMTP</h5>
                        <p class="text-muted small"></p>
                      </div>
                      <div class="">
                        <p class="float-right mt-4 mr-3"><i class="pi pi-arrow-right"></i></p>
                      </div>
                    </div>
                  </div>

                  <h6 class="text-muted mt-5">Important:</h6>
                  <ul class="text-muted small">
                    <li>
                      We recommend that you connect Google & Microsoft email accounts as they have the best deliverability.
                    </li>
                    <li>Don’t connect personal email accounts (like @gmail.com and @smtp.com).</li>
                    <li>We recommend that you don’t send cold emails from your primary domain. Read our article to learn why.</li>
                  </ul>
                  <div class="col-md-12">
                    <button class="btn float-left btn-light border" @click="cancel" type="button" >Cancel</button>
                    <button class="btn btn-primary float-right" @click="next" type="button" >Next <i class="pi pi-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="card" v-else>
                <div v-if="this.emailType===EMAIL_TYPE_GOOGLE()" class="card-body">
                  <i class="pi pi-times-circle float-right cursor-pointer" @click="cancel"></i>
                  <div class="row ">
                    <div class="col">
                      <img class="mt-4" src="../../../assets/img/google.png" alt="" width="30" height="30" >
                    </div>
                    <div class="col-md-10 pt-3 pl-4">
                      <h5 class="m-0">Connect your Google account</h5>
                      <p class="text-muted small">Gmail / Google Workspace</p>
                    </div>
                  </div>

                  <div class="row mb-5">
                    <div class="col-md-12 text-center">
                      <h6 class="small m-0">Allow InfinityLead to access your Google Workspace.</h6>
                      <p class="small text-success">
                        You only need to do this once per domain.
                      </p>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <ol class="small">
                        <li>Watch our <a  class="font-weight-bold" target="_blank" href="https://www.youtube.com/watch?v=6Lxt8EgyTUI">video tutorial</a>.</li>
                        <li>Log into your <a  class="font-weight-bold" target="_blank" href="https://admin.google.com/">Google Workspace Admin Panel</a>.</li>
                        <li>On the left side, click:
                          <br>
                          <b>Show more --> Security --> Access and data control --> API controls --> Manage Third-Party App Access.</b>
                          You can also <a class="font-weight-bold" target="_blank" href="https://admin.google.com/u/0/ac/owl/list?tab=configuredApps">click here</a> to be taken directly to this page.</li>
                        <li>Click “Configure new app”.</li>
                        <li>
                          Use the following Client ID to search for InfinityLead:
                          <div class="bg-light border p-2 rounded cursor-pointer" @click="copyToClipboard(googleClientId)">
                            {{googleClientId}}
                          </div>
                        </li>
                        <li>Select and approve InfinityLead to access your Google Workspace. Make sure you select “Trusted” in the “Access to Google Data” section. Remember to click “Finish” at the end.</li>
                        <li>
                          Click the “Connect button” below:
                        </li>
                        <li>
                          <!--href="/google/gmail-readonly"-->
                          <a  href @click="connectGoogle" class="btn btn-primary">Connect</a>
                        </li>
                      </ol>
                    </div>
                  </div>
                </div>
                <div class="card-footer" v-else>
                  <h5 class="mb-5">Connect SMTP Account</h5>
                  <form class="row g-3" method="POST" @submit.prevent="createSmtp">
                    <FormControlInput :col_md="12" v-model="smtp.name" label="From Name" placeholder="i.e, John Doe" />
                    <FormControlInput :col_md="12" v-model="smtp.username" label="Username" placeholder="" />
                    <FormControlInput :col_md="12" v-model="smtp.email" label="Email" placeholder="i.e, user@domain.com" />
                    <FormControlInput :col_md="12" v-model="smtp.password" label="Password" placeholder="***********" />
                    <FormControlInput :col_md="6" v-model="smtp.host" label="SMTP Host" placeholder="" />
                    <FormControlInput :col_md="6" v-model="smtp.port" label="SMTP Port" placeholder="" />
                    <FormControlInput :col_md="6" v-model="smtp.encryption" label="Encryption" placeholder="i.e, ssl, tls" />

                    <div class="col-md-12">
                      <button class="btn btn-light border float-left" @click="cancel"> Cancel </button>
                      <button class="btn btn-primary float-right" :disabled="isDisabled">
                        <span v-if="!isDisabled">
                          <span v-if="smtp.id">Update</span>
                          <span v-else>Save</span>
                        </span><span v-else >
                        <i class="pi pi-spinner-dotted pi-spin"></i> Authenticating  </span>
                      </button>
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
  <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete this item?"
      @confirm="deleteAcc()"
      @cancel="showPopup = false"
  />
</template>
<script>
import VueSlider from '@vueform/slider';
import '@vueform/slider/themes/default.css';


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Pills from "./components/Pills.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import {
  confirmDialog,
  EMAIL_TYPE_GOOGLE,
  EMAIL_TYPE_SMTP,
  notify,
  ROUTE_USER_EMAIL_ACCOUNTS,
  ROUTE_USER_HOME
} from "../../../constants.js";
import Confirm from "../../components/Confirm.vue";
import Breadcrumb from "../../components/Breadcrumb.vue";

export default {
  name: 'EmailAccounts',
  components: {Breadcrumb, Confirm, FormControlInput, Pills, Sidebar, Header,VueSlider},
  data() {
    return {
      isDisabled:false,
      delete_account_id:'',
      showPopup:false,
      value: [5, 20],
      add_email_account_mode:false,
      emailType:'',
      moveNext:false,
      googleClientId:'',
      accounts:[],
      smtp:{
        id:'',
        name:'',
        email:'',
        password:'',
        host:'',
        port:'',
        username:'',
        encryption:'',
      }
    }
  },
  methods: {
    ROUTE_USER_EMAIL_ACCOUNTS() {
      return ROUTE_USER_EMAIL_ACCOUNTS
    },
    ROUTE_USER_HOME() {
      return ROUTE_USER_HOME
    },
    EMAIL_TYPE_SMTP() {
      return EMAIL_TYPE_SMTP
    },
    EMAIL_TYPE_GOOGLE() {
      return EMAIL_TYPE_GOOGLE
    },
    showPop(id){
      this.showPopup=true;
      this.delete_account_id=id;
    },
    editSmtpAccount(account){
      this.toggleMode();
      this.emailType=this.EMAIL_TYPE_SMTP;
      this.next();
      this.smtp=account;
    },
    updateRange(account,type,value) {
      if (type==='m'){
        account.per_minute = value;
      }else{
        account.volume = value;
      }
      this.axios.post('user/email-accounts/range',account);
    },
    async connectGoogle() {
      try {
        const response = await this.axios.get('/google/gmail-readonly');
        if (response.data.url) {
          window.location.href = response.data.url;
        } else {
          console.error("No URL returned from backend");
        }
      } catch (error) {
        console.error("Error connecting to Google:", error);
      }
    },

    copyToClipboard(text){
      navigator.clipboard.writeText(text).then(() => {
        notify('Copied to clipboard');
      }).catch(() => {
        alert('Failed to copy URL. Please try again.');
      });
    },
    toggleMode(){
      this.add_email_account_mode=!this.add_email_account_mode;
    },
    cancel(){
      this.add_email_account_mode=false;
      this.emailType='';
      this.moveNext=false;
      this.smtp={
        id:'',
        name:'',
        email:'',
        password:'',
        host:'',
        port:'',
        username:'',
        encryption:'',
      }
    },

    async fetchGoogleClientId() {
      await this.axios.post('google-client-id').then(({ data }) => {
        this.googleClientId = data.data;
      });
    },
    async fetchEmailAccounts() {
      await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
        this.accounts = data.data;
        this.cancel();
      });
    },

    setEmailType(type){this.emailType=type},
    next(){
      this.moveNext = !!this.emailType;
    },


    deleteAcc() {
      this.axios.post("user/email-accounts/delete", { id:this.delete_account_id })
          .then((response) => {
            notify(response.data.message);
            this.fetchEmailAccounts();
            this.delete_account_id='';
            this.showPopup=false;
          })
          .catch((error) => {
            this.$flattenErrors(error);
          });
    },

    async createSmtp() {
      this.processing=true;
      this.isDisabled=true;
      await this.axios.post('user/email-accounts/smtp-create', this.smtp).then((response) => {
        notify(response.data.message);
        this.fetchEmailAccounts();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.isDisabled=false;
        this.processing=false;
      });
    },
  },
  watch: {
    value(newValue) {
      console.log('Slider value changed:', newValue);
    },
  },
  created() {
    this.fetchGoogleClientId();
    this.fetchEmailAccounts();
  }
}
</script>
