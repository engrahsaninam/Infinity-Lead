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
                <h4>Lead Information</h4>
                <Breadcrumb :items="[ROUTE_USER_SALES_CRM(),ROUTE_USER_SALES_CRM_SHOW(this.$route.params.id)]"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <table class="table table-lg table-borderless">
                    <tbody>
                      <tr>
                      <td>
                        <h2>{{lead.first_name}} {{lead.last_name}}</h2>
                      </td>
                      <td colspan="100%" class="text-right">
                        <span class="badge badge-soft-warning mr-2 p-2" v-if="lead.status === LEAD_STATUS_NOT_CONTACTED()">Not Contacted</span>
                        <span class="badge badge-soft-success mr-2 p-2" v-if="lead.status === LEAD_STATUS_CONTACTED()">Contacted</span>
                        <div class="dropdown show float-right" >
                          <a class="small" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="pi pi-ellipsis-h"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" @click.prevent="openCreateLeadDialog">Edit</a>
                            <a class="dropdown-item" @click="clear_notification">Clear Notification</a>
                            <a class="dropdown-item" @click.prevent="deleteLead">Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr class="text-muted">
                      <td><i class="pi pi-envelope"></i> {{lead.email}}</td>
                      <td colspan="100%" class="text-right">
                        <i class="pi pi-clock"></i>
                        Created on {{lead.created_on['d']}}
                        {{lead.created_on['F']}},
                        {{lead.created_on['Y']}}
                      </td>
                    </tr>
                    <tr class="text-muted">
                      <td>
                        <div class="card p-3">
                          <h6>Phone Number</h6>
                          <span class="small">{{lead.phone ?? '-'}}</span>
                        </div>
                      </td>
                      <td>
                        <div class="card p-3">
                          <h6>City</h6>
                          <span class="small">{{lead.city ?? '-'}}</span>
                        </div>
                      </td>
                      <td>
                        <div class="card p-3">
                          <h6>Current Company</h6>
                          <span class="small">{{lead.company ?? '-'}}</span>
                        </div>
                      </td>
                      <td>
                        <div class="card p-3">
                          <h6>Company Website</h6>
                          <span class="small">{{lead.website ?? '-'}}</span>
                        </div>
                      </td>
                      <td>
                        <div class="card p-3">
                          <h6>LinkedIn Profile</h6>
                          <span class="small">{{lead.linkedin ?? '-'}}</span>
                        </div>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="card-body">
                  <p v-if="lead?.sender" class="float-right"><span class="text-muted">Conversation sender email account </span> <{{lead?.sender?.email}}></p>
                  <div class="btn-group rounded-0">
                    <button class="btn tab-camp rounded-0 btn-outline-light bg-white active-tab-camp"> Conversation History</button>
                  </div>
                  <div class="p-3" v-if="lead.chat && lead.chat.length>0">
                    <div  v-for="message in lead.chat" class="py-2 border-bottom " >
                      <div :class="{'text-left':lead?.sender?.email === message.from , 'text-right':lead?.sender?.email !== message.from}">
                        <p class="m-0 small text-muted">From <{{message.from}}></p>
                        <div class=" "  >
                          <p class="small text-muted m-0" >
                            {{message.created_on['D']}},
                            {{message.created_on['d']}}
                            {{message.created_on['M']}},
                            {{message.created_on['Y']}}
                            ({{message.created_on['human_diff']}} )
                          </p>
                        </div>
                        <p class="m-0 small text-muted">to <<span >{{message.to}}</span>></p>
                        <p class="m-0 small text-muted" v-if="message.cc">cc < <span v-for="cc in message.cc" >{{to}}</span>></p>
                        <p>{{message.subject}}</p>
                        <div v-html="message.message" class="message-box text-muted"></div>
                      </div>
                    </div>
                    <a href="#compose-email" class="btn btn-outline-primary rounded-pill px-4 mt-3" @click="compose"><i class="pi pi-reply"></i> Reply</a>
                  </div>

                  <div class="text-center p-5" v-if="!compose_email && lead.chat.length === 0">
                    <img src="../../../assets/img/no-chat.png" width="150" alt="">
                    <h6 class="text-muted mb-0 mt-5">There is no conversation</h6>
                    <h6 class="text-muted mb-0"> history for this lead.</h6>
                    <button class="btn btn-primary btn-sm my-3" @click="compose"><i class="pi pi-pencil"></i> Compose Email</button>
                  </div>
                  <div v-if="compose_email" class="row justify-content-center p-md-5" id="compose-email">
                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header text-center">
                          <p class="p-0 m-0">New Email</p>
                        </div>
                        <div class="card-body  ">
                          <FormControlSelect2 v-if="!lead.sender" class="mt-3" :col_md="12" :options="emails_options" v-model="chat.from" label="From" layout="v"/>
                          <FormControlSelect2 v-else class="mt-3" :col_md="12" :options="[{key:lead?.sender?.id,value:lead?.sender?.email}]" v-model="chat.from" label="From" layout="v"/>

                          <div class="col-md-12 mb-3 ">
                            <div class="row">
                              <div class="col-md-1">
                                <label class="form-label">To</label>
                              </div>
                              <div class="col-md-11">
                                <input v-model="chat.to" disabled readonly class="form-control  ">
                              </div>
                            </div>
                          </div>
                          <FormControlTagsInput :col_md="12" v-model="chat.cc" :show_placeholder="false" label="Cc" layout="v"/>
                          <div class="col-md-12 ">
                            <div class="row">
                              <div class="col-md-1">
                                <label class="form-label">Subject</label>
                              </div>
                              <div class="col-md-11">
                                <input v-model="chat.subject" id="subject" class="form-control no-outline ">
                              </div>
                            </div>
                          </div>
                          <FroalaEditor class="pt-2" v-model="chat.message" :show_ai="false" :show_fields_dropdown="false"/>
                          <div class="col-md-12 ">
                            <button class="btn float-left btn-outline-primary  my-3 " @click="compose_email=false">Cancel </button>
                            <button class="btn float-right btn-primary  my-3 " @click="composeEmail" :disabled="processing">
                              <span v-if="!processing"><i class="pi pi-send"></i> Send</span>
                              <span v-else><i class="pi pi-spin pi-spinner"></i> Processing ...</span>
                            </button>
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

      </div>
    </div>
  </div>

  <Transition name="bounce">
    <dialog ref="dialog" @click.self="closeCreateLeadDialog" v-if="toggleCreateLeadDialog" class="col-md-6 top-0">
      <div class="container ">
        <form @submit.prevent="createLead(lead)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left">Update Lead</h5>
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
                <span v-if="!processing">Update Lead</span>
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
  LEAD_STATUS_CONTACTED,
  LEAD_STATUS_NOT_CONTACTED,
  ROUTE_USER_SALES_CRM, ROUTE_USER_SALES_CRM_SHOW
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import QuillEditorComponent from "../../components/QuillEditorComponent.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import FormControlTagsInput from "../../components/FormControlTagsInput.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import FroalaEditor from "../../components/FroalaEditor.vue";
export default {
  name: 'SalesCrmShow',
  components: {
    FormControlInput,
    FormControlTagsInput,
      FormControlSelect2, FroalaEditor, FormInputTooltip, Shimmer, Breadcrumb, Sidebar, Header},
  data() {
    return {
      chat:{
        lead_id:this.$route.params.id,
        from:'',
        to:'',
        subject:'',
        cc:[],
        message:'',
        reply:false,
      },
      compose_email:false,
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
      processing:false,
      shimmer:true,
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
      toggleCreateLeadDialog:false,
      emails_options:[],
    }
  },
  methods: {
    clear_notification(){
      const id=this.$route.params.id;
      this.axios.post('user/leads/clear_notification',{id:id}).then((response)=>{
        location.reload();
      });
    },
    LEAD_STATUS_CONTACTED() {
      return LEAD_STATUS_CONTACTED
    },
    LEAD_STATUS_NOT_CONTACTED() {
      return LEAD_STATUS_NOT_CONTACTED
    },
    deleteLead(){
      this.axios.post('user/leads/delete', {id:this.$route.params.id}).then((response) => {
        this.$router.push(this.ROUTE_USER_SALES_CRM());
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    openCreateLeadDialog() {
      this.toggleCreateLeadDialog = true;
      this.$nextTick(() => {
        this.$refs.dialog?.showModal();
      });
    },
    closeCreateLeadDialog() {
      this.toggleCreateLeadDialog = false;
      this.$refs.dialog?.close();
    },
    ROUTE_USER_SALES_CRM_SHOW(id) {
      return ROUTE_USER_SALES_CRM_SHOW.replace(':id',id)
    },

    ROUTE_USER_SALES_CRM() {
      return ROUTE_USER_SALES_CRM
    },

    compose() {
      this.compose_email = true;
      const chatData = this.lead.chat?.[0];
      if (!chatData) return;
      if (this.emails_options){
        this.chat.from = this.emails_options[0].key;
      }
      this.chat.to = chatData.to;
      this.chat.cc = chatData.cc;
      this.chat.subject = (chatData ?'Re: ':'')+chatData.subject;
    },
    fetch(){
      this.shimmer=true;
      this.axios.post('user/leads/fetch', {id:this.$route.params.id}).then((response) => {
        this.lead=response.data.data;
        this.chat.to=this.lead.email;

      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.shimmer=false;
      });
    },
    syncReplies(){
      this.shimmer=true;
      this.axios.post('user/chat/replies', {id:this.$route.params.id}).then((response) => {
        this.fetch();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.shimmer=false;
      });
    },

    createLead(lead){
      this.processing=true;
      this.axios.post('user/leads/update', lead).then((response) => {
        this.fetch();
        this.closeCreateLeadDialog();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.processing=false;
      });
    },
    composeEmail(){
      this.processing=true;
      this.axios.post('user/chat/compose', this.chat).then((response) => {
        this.fetch();
        this.chat={
          lead_id:this.$route.params.id,
          from:'',
          to:'',
          subject:'',
          cc:[],
          message:'',
          reply:false,
        }
        this.compose_email=false;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(item=>{
        this.processing=false;
      });
    },

    async fetchEmailAccounts() {
      await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
        this.emails_options =data.data.map(item => ({
          key: item.id,
          value: item.email
        }));
        if (this.emails_options.length){
          this.chat.from=this.emails_options[0].key;
        }
      });
    },
  },
  watch: {
    '$route'(to, from) {
      if (to.query.reload !== from.query.reload) {
        this.chat.lead_id = to.params.id;
        this.fetch();
      }
    }
  },
  created() {
    this.fetch();
    this.fetchEmailAccounts();
  }
}
</script>
