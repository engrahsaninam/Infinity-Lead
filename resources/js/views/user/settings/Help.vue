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
                <Breadcrumb :items="[ROUTE_USER_HELP()]" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <Pills/>
                  <form @submit.prevent="updateProfile" class=" mt-5 p-md-3">
                    <div class="row mb-5 ">
                      <div class="col-md-12">
                        <h5>Help</h5>
                        <p class="text-muted">Complete the form below and we will respond within the next 24-hours!</p>
                      </div>
                    </div>
                    <div class="container">
                      <div class="form-group row">
                        <label class="form-label  ">First Name *</label>
                        <div class="input-group position-relative ">
                          <input type="text" v-model="user.first_name" placeholder="First Name" class="form-control  col-md-8 form-control-lg" :class="{ 'is-invalid': user_error.first_name}" />
                          <div v-if="user_error.first_name" class=" d-block invalid-tooltip">
                            {{ user_error.first_name }}
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="form-label  ">Last Name *</label>
                        <div class="input-group position-relative ">
                          <input type="text" v-model="user.last_name" placeholder="Last Name" class="form-control col-md-8 form-control-lg" :class="{ 'is-invalid': user_error.last_name }" />
                          <div v-if="user_error.last_name" class=" d-block invalid-tooltip">
                            {{ user_error.last_name }}
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="form-label  ">Business Email *</label>
                        <div class="input-group position-relative ">
                          <input type="text" v-model="user.email" placeholder="you@company.com" class="form-control form-control-lg  col-md-8"  :class="{ 'is-invalid': user_error.email }"  />
                          <div v-if="user_error.email" class=" d-block invalid-tooltip">
                            {{ user_error.email }}
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="form-label  ">Phone number *</label>
                        <div class="input-group position-relative ">
                          <input type="tel" v-model="user.phone" placeholder="(884) 567-8901" class="form-control form-control-lg  col-md-8"  :class="{ 'is-invalid': user_error.phone }"  />
                          <div v-if="user_error.phone" class=" d-block invalid-tooltip">
                            {{ user_error.phone }}
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="form-label  ">Message *</label>
                        <div class="input-group position-relative ">
                          <textarea type="tel" v-model="user.message" placeholder="How can we help you?" rows="4" class="form-control form-control-lg  col-md-8" :class="{ 'is-invalid': user_error.message }" >
                          </textarea>

                          <div v-if="user_error.message" class=" d-block invalid-tooltip">
                            {{ user_error.message }}
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <button class="btn btn-primary  btn-lg col-md-8" type="submit" :disabled="processing">
                          <span v-if="!processing">Submit</span>
                          <span v-else>Processing</span>

                        </button>
                      </div>
                      <div class="form-group row">
                        <div class="col-md-8 text-center">
                          By submitting this form, you are agreeing to InfinityLead's
                          <a href="/privacy-policy">Privacy Policy</a> and <a href="/terms-and-conditions">Terms of Service </a>.
                        </div>
                      </div>
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
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Pills from "./components/Pills.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import {notify, ROUTE_USER_CHANGE_PASSWORD, ROUTE_USER_CHANGE_PROFILE, ROUTE_USER_HELP} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";

export default {
  name: 'Help',
  components: {Breadcrumb, FormControlInput, Pills, Sidebar, Header},
  data() {
    return {
      processing:false,
      edit_profile_mode:false,
      user:{
        first_name:'',
        last_name:'',
        email:'',
        phone:'',
        message:'',
      },
      user_error:{
        first_name:'',
        last_name:'',
        email:'',
        phone:'',
        message:'',
      },
    }
  },
  methods: {
    ROUTE_USER_HELP() {
      return ROUTE_USER_HELP
    },
    async updateProfile() {
      this.processing = true;
      this.user_error = true;
      await this.axios.post('user/profile/help',this.user).then(({ data }) => {
        notify(data.message);
        this.toggleMode();
      }).catch((error) => {
        this.user_error=this.$flattenErrors(error);
      }).finally(()=>{
        this.processing=false;
      });
    },

  },
  created() {
  }
}
</script>