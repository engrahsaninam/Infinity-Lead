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
              <ProgressBar :step="3"/>
            </div>
            <div class="card-body camp-card-h">
              <Loader :loading="processing" v-if="processing"/>
              <div class="col-md-12" v-else>

                <h4>Control who you email</h4>
                <table class="table table-borderless">
                  <tr class="text-muted small" v-for="(option, index) in controlOptions" :key="index">
                    <td>{{ option.label }}</td>
                    <td class="text-right">
                      <span v-if="option.note" class="mr-2 text-primary">{{ option.note }}</span>
                      <div class="switch">
                        <input
                            :disabled="option.note && option.note === 'Mandatory'"
                            type="checkbox"
                            :id="'switch' + index"
                            v-model="campaign.controls"
                            :value="option.value"
                        />
                        <label :for="'switch' + index">.</label>
                      </div>
                    </td>
                  </tr>
                </table>

              </div>
            </div>
            <div class="card-footer">
              <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_2()" class="btn btn-primary float-left"> <i class="pi pi-arrow-left"></i> Back </router-link>
              <button class="btn btn-primary float-right" @click="validateControls"> Next <i class="pi pi-arrow-right"></i> </button>
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
  notify, NOTIFY_TYPE_ERROR,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE,
  ROUTE_USER_CAMPAIGNS_STEP_2, ROUTE_USER_CAMPAIGNS_STEP_4
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";

export default {
  name:'Step3',
  data(){
    return {
      processing:false,
      update_mode: Boolean(this.$route.params.id),
      campaign:{
        id:this.$route.params.id,
        name:'',
        controls:'',
      },
      campaign_error:{
        name:'',
      },
      controlOptions: [
        { value: "skip_existing", label: "Skip leads that exist in another campaign. This doesn't apply to campaigns that have been deleted." },
        { value: "skip_invalid", label: "Skip leads that have “invalid” or “catch-all” email addresses. Activating this will ensure that you only send emails to leads with “valid” email addresses.", note: "Recommended" },
        { value: "skip_personal", label: "Skip personal email addresses (like @gmail.com). Contacting personal email addresses can negatively impact your deliverability.", note: "Recommended" },
        { value: "skip_responded", label: "Skip leads that have already responded. This does not apply to leads that have responded in other sending tools.", note: "Mandatory" },
        { value: "skip_duplicates", label: "Skip duplicate leads. If your lead list contains the same lead more than once, then we'll only include the lead once in this campaign.", note: "Mandatory" }
      ],
    }
  },
  methods: {
    ROUTE_USER_CAMPAIGNS_STEP_2() {
      return ROUTE_USER_CAMPAIGNS_STEP_2.replace(':id',this.$route.params.id);
    },
    ROUTE_USER_CAMPAIGNS_STEP_4() {
      return ROUTE_USER_CAMPAIGNS_STEP_4.replace(':id',this.$route.params.id);
    },

    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    async validateControls(){
      if (this.campaign.controls.length > 0){
        await this.axios.post('user/campaigns/controls', this.campaign).then((response) => {
          this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_4())
        }).catch((error) => {
          this.$flattenErrors(error);
          return false;
        });
        return true;
      }else{
        notify('Please select at least one control!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
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
  components: {ProgressBar, Crumb, Loader, Breadcrumb, Sidebar,Header},
  created() {
    if (this.update_mode) this.edit();
  }
}
</script>