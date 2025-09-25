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
              <ProgressBar :step="12"/>
            </div>
            <div class="card-body camp-card-h">
              <Loader :loading="processing" v-if="processing"/>
              <div class="col-md-12" v-else>
                <div class="row justify-content-center">
                  <div class="col-md-8">
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Campaign name</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <p>{{campaign.name}}</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Email accounts that you're sending with in this campaign</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_2()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <p v-for="email in campaign.accounts">
                          <img class="rounded-circle avatar-sm" src="../../../../assets/img/checkbox.png" alt="" width="20" height="20">
                          {{ email_accounts.find(em => em.id === email)?.email || '' }}
                        </p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Control who you email</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_3()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <table class="table table-borderless">
                          <tr class="text-muted small" v-for="(option, index) in controlOptions" :key="index">
                            <td width="70%">{{ option.label }}</td>
                            <td width="30%" class="text-right">
                              <span v-if="option.note" class="mr-2 text-primary">{{ option.note }}</span>
                              <div class="switch">
                                <input
                                    type="checkbox"
                                    :id="'switches' + index"
                                    v-model="campaign.controls"
                                    :value="option.value"
                                    disabled
                                />
                                <label :for="'switches' + index">.</label>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Who you will be emailing</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_4()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <div v-if="campaign.sheet_type === SHEET_TYPE_CSV()">
                          <div class="logo-sm pt-3 col-md-4 py-5 border text-center rounded py-3">
                            <img src="../../../../assets/img/csv.png" alt="" height="80">
                            <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">When do you want your campaign to send?</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_6()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col text-center" v-for="day in week_days" :key="day">
                            <p class="week-days text-capitalize border w2-step-4" :class="{ 'border-success-w2-step-4': campaign.days.includes(day) }">
                              {{ day }}
                              <span v-if="campaign.days.includes(day)" class="check-mark"><i class="pi pi-check-circle"></i></span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Time zone</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_7()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <FormControlSelect2
                            :disabled="true"
                            readonly
                            :col_md="6"
                            :options="timezones"
                            v-model="campaign.timezone"
                            label=""
                        />
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">At what time?</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_8()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div v-for="option in scheduleOptions" :key="option.key" class="col-md-6 mb-4">
                            <div class="border p-3 w2-step-4" :class="{ 'border-success-w2-step-4': campaign.day === option.key }">
                              <h5>{{ option.icon }} {{ option.label }}</h5>
                              <div class="p-3">
                                <div class="row">
                                  <div class="col border p-3 text-center pt-4">
                                    <h6>Start</h6>
                                    <p v-if="option.key !== 'custom'">{{ option.start }}</p>
                                    <input
                                        v-else
                                        type="time"
                                        v-model="campaign.start"
                                        class="form-control"
                                        disabled
                                    />
                                  </div>
                                  <div class="col border p-3 text-center pt-4">
                                    <h6>End</h6>
                                    <p v-if="option.key !== 'custom'">{{ option.end }}</p>
                                    <input
                                        v-else
                                        type="time"
                                        v-model="campaign.end"
                                        class="form-control"
                                        disabled
                                    />
                                  </div>
                                  <div class="col border p-3 text-center pt-4">
                                    <h6>Every</h6>
                                    <p>5 to 15 min</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom mb-3">
                        <h4 class="float-left">Control sending volume</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_9()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>

                      <div v-for="account in email_accounts" :key="account.id" class="card mx-3 my-1" >
                        <div class="card-body " v-if="campaign.accounts.includes(account.id)">
                          <h6>{{ account.name }} <small class="text-muted">({{ account.email }})</small></h6>

                          <div class="form-group">
                            <label>Per Minute</label>
                            <VueSlider
                                v-model="account.per_minute"
                                :min="0"
                                disabled
                                :max="40"
                                :tooltip="'always'"
                            />
                          </div>
                          <div class="form-group">
                            <label>Volume</label>
                            <VueSlider
                                v-model="account.volume"
                                :min="0"
                                disabled
                                :max="40"
                            />
                          </div>

                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body border-bottom">
                        <h4 class="float-left">Email sequence</h4>
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_10()" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                      </div>
                      <div class="card-body">
                        <div class="card message-body">
                          <div class="card-header ">
                            <h5 class="pt-2">{{campaign.subject}}</h5>
                          </div>

                          <div class="card-body">
                            <h6 v-html="campaign.message"></h6>
                            <div v-if="campaign.attachment" class="float-right">
                              <a :href="campaign.attachment" target="_blank" class="badge p-2 badge-soft-primary cursor-pointer">
                                <span class=""><i class="pi pi-external-link"></i> {{campaign.o_attachment}}</span>
                              </a>
                            </div>

                            <div class="row justify-content-end">
                              <div class="col-md-4" v-if="campaign.show_signature" >
                                <span v-html="campaign.signature"></span>
                              </div>
                            </div>
                            <div class="card mt-2" v-for="(followup) in campaign.followups">
                              <div class="card-body">
                                <h5>Every {{followup.days}} days If no reply</h5>
                                <span v-html="followup.message"></span>
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
            <div class="card-footer">
              <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_11()" class="btn bg-white border shadow-2 px-3 float-left rounded-pill"> <i class="pi pi-arrow-left"></i> Back </router-link>
              <button class="btn btn-primary border shadow-2 px-4 rounded-pill float-right" @click="launchCampaign"> Launch <i class="mdi mdi-rocket"></i> </button>
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
  NOTIFY_TYPE_ERROR, ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_STEP_1, ROUTE_USER_CAMPAIGNS_STEP_10,
  ROUTE_USER_CAMPAIGNS_STEP_11,
  ROUTE_USER_CAMPAIGNS_STEP_2,
  ROUTE_USER_CAMPAIGNS_STEP_3,
  ROUTE_USER_CAMPAIGNS_STEP_4,
  ROUTE_USER_CAMPAIGNS_STEP_5,
  ROUTE_USER_CAMPAIGNS_STEP_6,
  ROUTE_USER_CAMPAIGNS_STEP_7,
  ROUTE_USER_CAMPAIGNS_STEP_8,
  ROUTE_USER_CAMPAIGNS_STEP_9, SHEET_TYPE_CSV,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import moment from "moment-timezone";

import VueSlider from '@vueform/slider';
import '@vueform/slider/themes/default.css';

export default {
  name:'Step12',
  data(){
    return {
      processing:false,
      update_mode: Boolean(this.$route.params.id),
      campaign:{
        id:this.$route.params.id,
      },
      email_accounts:[],
      controlOptions: [
        { value: "skip_existing", label: "Skip leads that exist in another campaign. This doesn't apply to campaigns that have been deleted." },
        { value: "skip_invalid", label: "Skip leads that have “invalid” or “catch-all” email addresses. Activating this will ensure that you only send emails to leads with “valid” email addresses.", note: "Recommended" },
        { value: "skip_personal", label: "Skip personal email addresses (like @gmail.com). Contacting personal email addresses can negatively impact your deliverability.", note: "Recommended" },
        { value: "skip_responded", label: "Skip leads that have already responded. This does not apply to leads that have responded in other sending tools.", note: "Mandatory" },
        { value: "skip_duplicates", label: "Skip duplicate leads. If your lead list contains the same lead more than once, then we'll only include the lead once in this campaign.", note: "Mandatory" }
      ],
      week_days:['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',],
      timezones: this.getTimezones(),
      scheduleOptions: [
        { key: "full", icon: "⏰", label: "Full Day", start: "08:00 AM", end: "06:00 PM" },
        { key: "morning", icon: "☀️", label: "Morning", start: "08:00 AM", end: "11:45 AM" },
        { key: "afternoon", icon: "🌄", label: "Afternoon", start: "01:00 PM", end: "04:45 PM" },
        { key: "custom", icon: "📝", label: "Custom" }
      ],
    }
  },
  methods: {
    SHEET_TYPE_CSV() {
      return SHEET_TYPE_CSV
    },
    ROUTE_USER_CAMPAIGNS_STEP_10() {
      return ROUTE_USER_CAMPAIGNS_STEP_10.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_9() {
      return ROUTE_USER_CAMPAIGNS_STEP_9.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_8() {
      return ROUTE_USER_CAMPAIGNS_STEP_8.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_7() {
      return ROUTE_USER_CAMPAIGNS_STEP_7.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_6() {
      return ROUTE_USER_CAMPAIGNS_STEP_6.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_5() {
      return ROUTE_USER_CAMPAIGNS_STEP_5.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_4() {
      return ROUTE_USER_CAMPAIGNS_STEP_4.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_2() {
      return ROUTE_USER_CAMPAIGNS_STEP_2.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_3() {
      return ROUTE_USER_CAMPAIGNS_STEP_3.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_1() {
      return ROUTE_USER_CAMPAIGNS_STEP_1.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_11() {
      return ROUTE_USER_CAMPAIGNS_STEP_11.replace(':id',this.$route.params.id);
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
    async fetchEmailAccounts() {
      await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
        this.email_accounts = data.data;
      });
    },
    getTimezones() {
      return moment.tz.names().map((tz) => {
        const offset = moment.tz(tz).utcOffset();
        const formattedOffset = `UTC${offset >= 0 ? "+" : ""}${(offset / 60).toFixed(0)}`;
        return { key: tz, value: `${formattedOffset} - ${tz}` };
      });
    },
    launchCampaign(){
      this.processing=true;
      this.axios.post('user/campaigns/launch',this.campaign).then(({ data }) => {
        notify(data.data.message);
        this.$router.push(ROUTE_USER_CAMPAIGNS);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    }
  },
  components: {FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar,Header,VueSlider},
  created() {
    this.edit();
    this.fetchEmailAccounts();
  }
}
</script>