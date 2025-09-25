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
                <h4>Change Password </h4>
                <Breadcrumb :items="[ROUTE_USER_CHANGE_PASSWORD()]"/>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">

                  <form @submit.prevent="updatePassword" class="  p-md-3">
                    <div class="row">
                      <div class="col-md-12 mb-5">
                        <router-link :to="ROUTE_USER_PROFILE()"><i class="pi pi-arrow-left"></i></router-link>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="form-label offset-md-1 col-md-2">Current Password</label>
                      <div class="input-group position-relative col-md-4">
                        <input :type="passType" v-model="user.old_password" placeholder="************" class="form-control  col-md-12" :class="{ 'is-invalid': user_error.old_password}" />
                        <div class="input-group-append">
                          <span class="input-group-text rounded-0" style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;" @click="togglePasswordVisibility()" >
                            <i class="pi " :class="{'pi-eye':passType === 'password', 'pi-eye-slash':passType === 'text'}"></i>
                          </span>
                        </div>
                        <div v-if="user_error.old_password" class=" d-block invalid-tooltip">
                          {{ user_error.old_password }}
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="form-label offset-md-1 col-md-2">New Password</label>
                      <div class="input-group position-relative col-md-4">
                        <input :type="passType" v-model="user.password" placeholder="************" class="form-control  col-md-12" :class="{ 'is-invalid': user_error.password}" />
                        <div class="input-group-append">
                          <span class="input-group-text rounded-0" style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;" @click="togglePasswordVisibility()" >
                            <i class="pi " :class="{'pi-eye':passType === 'password', 'pi-eye-slash':passType === 'text'}"></i>
                          </span>
                        </div>
                        <div v-if="user_error.password" class=" d-block invalid-tooltip">
                          {{ user_error.password }}
                        </div>
                        <p class="small text-muted">
                          Your password must be at least 8 characters in length, and contain at least one of each of the following: lower case letters (a-z), upper case letters (A-Z) and numbers (0-9).
                        </p>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="form-label offset-md-1 col-md-2">Confirm Password</label>
                      <div class="input-group position-relative col-md-4">
                        <input :type="passType" v-model="user.confirm_password" placeholder="************" class="form-control  col-md-12" :class="{ 'is-invalid': user_error.confirm_password}" />
                        <div class="input-group-append">
                          <span class="input-group-text rounded-0" style="border-top-right-radius: 5px!important;border-bottom-right-radius: 5px!important;" @click="togglePasswordVisibility()" >
                            <i class="pi " :class="{'pi-eye':passType === 'password', 'pi-eye-slash':passType === 'text'}"></i>
                          </span>
                        </div>
                        <div v-if="user_error.confirm_password" class=" d-block invalid-tooltip">
                          {{ user_error.confirm_password }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-7 text-right">
                      <button class="btn btn-primary" type="submit"> Update Password</button>
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
import {notify, ROUTE_USER_CHANGE_PASSWORD, ROUTE_USER_PROFILE} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";

export default {
  name: 'ChangePassword',
  components: {Breadcrumb, FormControlInput, Pills, Sidebar, Header},
  data() {
    return {
      edit_profile_mode:false,
      user:{
        old_password:'',
        password:'',
        confirm_password:'',
      },
      passType: 'password',
      user_error:{
        old_password:'',
        password:'',
        confirm_password:'',
      },
    }
  },
  methods: {
    ROUTE_USER_PROFILE() {
      return ROUTE_USER_PROFILE
    },
    ROUTE_USER_CHANGE_PASSWORD() {
      return ROUTE_USER_CHANGE_PASSWORD
    },
    async updatePassword() {
      this.user_error = true;
      await this.axios.post('user/profile/password',this.user).then(({ data }) => {
        notify(data.message);
      }).catch((error) => {
        this.user_error=this.$flattenErrors(error);
      });
    },
    togglePasswordVisibility() {
      this.passType = this.passType === 'password' ? 'text' : 'password';
    },
  },
  created() {
  }
}
</script>