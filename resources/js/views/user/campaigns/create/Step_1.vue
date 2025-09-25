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
              <ProgressBar :step="1"/>
            </div>
            <div class="card-body camp-card-h">

              <Loader :loading="processing" v-if="processing"/>
              <div class="col-md-4" v-else>
                <div class="form-group">
                  <label class="form-label h4 font-weight-light">Name your campaign</label>
                  <div class="input-group position-relative">
                    <input
                        type="text"
                        v-model="campaign.name"
                        placeholder="Type your campaign name here ..."
                        class="form-control form-control-lg col-md-12"
                        :class="{ 'is-invalid': campaign_error.name }"
                        @input="campaign_error.name = ''"
                    />
                    <div v-if="campaign_error.name" class="d-block invalid-tooltip">
                      {{ campaign_error.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button class="btn btn-primary float-right" @click="validateName"> Next <i class="pi pi-arrow-right"></i> </button>
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
import {ROUTE_USER_CAMPAIGNS, ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_CAMPAIGNS_STEP_2} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";

export default {
  name:'Step1',
  data(){
    return {
      processing:false,
      update_mode: Boolean(this.$route.params.id),
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
    ROUTE_USER_CAMPAIGNS_STEP_2(id) {
      return ROUTE_USER_CAMPAIGNS_STEP_2.replace(':id',id);
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    async validateName() {
      if (!this.campaign.name) {
        this.campaign_error.name = 'Please insert a campaign name.';
        return false;
      }
      try {
        this.processing = true;
        const response = await this.axios.post('user/campaigns/name', this.campaign);
        const id = response.data.data.id;
        this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_2(id))
        return true;
      } catch (error) {
        this.$flattenErrors(error);
        return false;
      } finally {
        this.processing = false;
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
  components: { ProgressBar, Crumb, Loader, Breadcrumb, Sidebar,Header},
  created() {
    if (this.update_mode) this.edit();

  }
}
</script>