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

                <Breadcrumb :items="[ROUTE_USER_API_KEYS()]" />

              </div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-md-12">
              <div class="card" >
                <div class="card-body">
                  <Pills/>
                  <div class="row  p-md-3 mt-5">
                    <div class="col-md-6">
                      <h5>Api Keys</h5>
                      <p class="text-muted">Connect your API keys to InfinityLead.</p>
                    </div>
                  </div>
                  <Loader :loading="processing" />

                  <div class="row">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body">
                          <p><span class="badge" :class="{'bg-warning text-warning':create_ai_mode, 'bg-success text-success':!create_ai_mode}" style="border-radius: 100%;width: 15px;height: 15px">.</span>
                            <span v-if="create_ai_mode"> Not Connected</span>
                            <span v-else> Connected</span>
                          </p>
                          <h5>Connect your AI tool</h5>
                          <p class="p-0 small text-muted" v-if="create_ai_mode">Connect an API key from your AI tool to use our AI rewriting feature.</p>
                          <p class="p-0 small text-muted" v-else>Connect an API key from your email verification tool to use our Lead Finder and other features.</p>

                          <div class="form-group">
                            <label class="form-label">API Key</label>
                            <div class="input-group position-relative">
                              <input type="text" v-model="open_ai.key" placeholder="SA****************************NB" class="form-control  " :class="{ 'is-invalid': open_ai_error.key}"/>
                              <div v-if="!create_ai_mode" class="input-group-append" @click="deleteOpenAiKey"><span class="input-group-text rounded-0 text-danger"><i class="pi pi-trash"></i></span></div>
                              <div v-else class="input-group-append btn btn-primary rounded-0" @click="updateOpenAiKey"><span class=" ">Add Api Key</span></div>
                            </div>
                            <p class="pt-3" v-if="!create_ai_mode">Your <a href="">OpenAI</a> API key is connected.</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body">
                          <p><span class="badge" :class="{'bg-warning text-warning':create_tool_mode, 'bg-success text-success':!create_tool_mode}" style="border-radius: 100%;width: 15px;height: 15px">.</span>
                            <span v-if="create_tool_mode"> Not Connected</span>
                            <span v-else> Connected</span>
                          </p>
                          <h5>Connect your verification tool</h5>
                          <p class="p-0 small text-muted" v-if="create_tool_mode">Connect an API key from your email verification tool to use our Lead Finder and other features.</p>
                          <p class="p-0 small text-muted" v-else>Connect an API key from your email verification tool to use our Lead Finder and other features.</p>

                          <div class="row">
                            <FormControlSelect2 v-model="tool.tool" :options="VERIFICATION_TOOLS" :col_md="6" label="Select Verification Tool"/>

                          </div>

                          <div class="form-group">
                            <label class="form-label">API Key</label>
                            <div class="input-group position-relative">
                              <input type="text" v-model="tool.key" :disabled="!create_tool_mode" placeholder="Paste your key here" class="form-control  " :class="{ 'is-invalid': tool_error.key}"/>
                              <div v-if="!create_tool_mode" class="input-group-append" @click="deleteVerificationKey"><span class="input-group-text rounded-0 text-danger"><i class="pi pi-trash"></i></span></div>
                              <div v-else class="input-group-append btn btn-primary rounded-0" @click="updateVerificationKey"><span class=" ">Add Api Key</span></div>
                            </div>
                            <p class="pt-3" v-if="!create_tool_mode">Your <a href=""><span class="text-capitalize">{{tool.tool}}</span></a> API key is connected.</p>
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
</template>
<script>

import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Pills from "./components/Pills.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import {
  notify,
  ROUTE_USER_API_KEYS,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Loader from "../../components/Loader.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";

export default {
  name: 'ApiKeys',
  components: {FormControlSelect2, Loader, Breadcrumb, FormControlInput, Pills, Sidebar, Header},
  data() {
    return {
      processing:false,
      create_ai_mode:true,
      create_tool_mode:true,
      open_ai:{
        key:'',
        type:'open_ai',
      },
      open_ai_error:{
        key:'',
        type:'',
      },
      tool:{
        key:'',
        type:'verification_tool',
        tool:'reoon',
      },
      tool_error:{
        key:'',
        type:'',
        tool:'',
      },
      VERIFICATION_TOOLS:[
        {key:'emailable',value:'Emailable'},
        {key:'zerobounce',value:'Zero Bounce'},
        {key:'neverbounce',value:'Never Bounce'},
        {key:'millionverifier',value:'Million Verifier'},
        {key:'reoon',value:'Reoon'},

      ],
    }
  },
  methods: {
    ROUTE_USER_API_KEYS() {
      return ROUTE_USER_API_KEYS
    },

    async getOpenAiApiKey() {
      await this.axios.post('user/api-keys/fetch/open-ai').then(({ data }) => {
        if (data.data){
          this.create_ai_mode=false;
          this.open_ai = data.data;
        }else{
          this.create_ai_mode=true;
        }
      });
    },
    async getVerificationToolKey() {
      await this.axios.post('user/api-keys/fetch/verification-tool').then(({ data }) => {
        if (data.data){
          this.create_tool_mode=false;
          this.tool = data.data;
        }else{
          this.create_tool_mode=true;
        }
      });
    },

    updateOpenAiKey(){
      this.processing=true;
      this.axios.post('user/api-keys/update/open-ai',this.open_ai).then(({ data }) => {
        notify(data.message);
        this.getOpenAiApiKey();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    deleteOpenAiKey(){
      this.processing=true;
      this.axios.post('user/api-keys/delete/open-ai',this.open_ai).then(({ data }) => {
        notify(data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },

    updateVerificationKey(){
      this.processing=true;
      this.axios.post('user/api-keys/update/verification-tool',this.tool).then(({ data }) => {
        notify(data.message);
        this.getVerificationToolKey();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    deleteVerificationKey(){
      this.processing=true;
      this.axios.post('user/api-keys/delete/verification-tool',this.tool).then(({ data }) => {
        notify(data.message);
        this.getVerificationToolKey();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    }

  },
  created() {
    this.getOpenAiApiKey();
    this.getVerificationToolKey();
  }
}
</script>
