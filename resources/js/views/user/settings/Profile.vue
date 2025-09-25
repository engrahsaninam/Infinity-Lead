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
                <Breadcrumb :items="[ROUTE_USER_PROFILE()]" />
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
                      <div class="col-md-4">
                        <h5>Profile</h5>
                        <p class="text-muted">Update your name, password & profile picture.</p>
                      </div>
                      <div class="col-md-4 text-right">
                        <button class="btn btn-primary" @click="toggleMode" type="button" v-if="!edit_profile_mode"><i class="pi pi-user-edit"></i> Edit</button>
                        <button class="btn btn-primary" type="submit" v-else><i class="pi pi-save"></i> Save</button>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="form-label  col-md-2">First Name</label>
                      <div class="input-group position-relative col-md-4">
                        <input type="text" v-model="user.first_name" placeholder="First Name" class="form-control  col-md-12" :class="{ 'is-invalid': user_error.first_name}" :readonly="!edit_profile_mode"/>
                        <div v-if="user_error.first_name" class=" d-block invalid-tooltip">
                          {{ user_error.first_name }}
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-label  col-md-2">Last Name</label>
                        <div class="input-group position-relative col-md-4">
                          <input type="text" v-model="user.last_name" placeholder="Last Name" class="form-control  col-md-12" :class="{ 'is-invalid': user_error.last_name }" :readonly="!edit_profile_mode"/>
                          <div v-if="user_error.last_name" class=" d-block invalid-tooltip">
                            {{ user_error.last_name }}
                          </div>
                        </div>
                      </div>
                    <div class="form-group row">
                        <label class="form-label  col-md-2">Email</label>
                        <div class="input-group position-relative col-md-4">
                          <input type="text" v-model="user.email" placeholder="Email" class="form-control  col-md-12"  readonly/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-label  col-md-2">Password</label>
                        <div class="input-group position-relative col-md-4">
                          <input type="text" value="**********" placeholder="Password" class="form-control col-md-12" readonly v-if="!edit_profile_mode"/>
                          <router-link :to="ROUTE_USER_CHANGE_PASSWORD()" class="btn btn-primary" v-else>Change Password</router-link>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-label  col-md-2">Profile</label>
                        <div class="input-group position-relative col-md-4">
                          <img :src="user.profile" alt="" class="rounded-circle header-profile-user">
                          <router-link :to="ROUTE_USER_CHANGE_PROFILE()" class="btn btn-primary" v-if="edit_profile_mode">Upload new Profile Picture</router-link>
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
import {notify, ROUTE_USER_CHANGE_PASSWORD, ROUTE_USER_CHANGE_PROFILE, ROUTE_USER_PROFILE} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";

export default {
  name: 'Profile',
  components: {Breadcrumb, FormControlInput, Pills, Sidebar, Header},
  data() {
    return {
      edit_profile_mode:false,
      user:{
        first_name:'',
        last_name:'',
        email:'',
        password:'',
        profile:'',
      },
      user_error:{
        first_name:'',
        last_name:'',
        email:'',
        password:'',
        profile:'',
      },
    }
  },
  methods: {
    ROUTE_USER_PROFILE() {
      return ROUTE_USER_PROFILE
    },
    ROUTE_USER_CHANGE_PROFILE() {
      return ROUTE_USER_CHANGE_PROFILE
    },
    ROUTE_USER_CHANGE_PASSWORD() {
      return ROUTE_USER_CHANGE_PASSWORD
    },
    toggleMode(){
      this.edit_profile_mode=!this.edit_profile_mode;
    },
    async fetchUserData() {
      await this.axios.post('user/profile/fetch').then(({ data }) => {
        this.user = data.data;
      });
    },
    async updateProfile() {
      this.user_error = true;
      await this.axios.post('user/profile/update',this.user).then(({ data }) => {
        notify(data.message);
        this.toggleMode();
      }).catch((error) => {
        this.user_error=this.$flattenErrors(error);
      });
    },

  },
  created() {
    this.fetchUserData();
  }
}
</script>
<style>
.form-control[readonly],.form-control[readonly]:focus{
  border: none;
  background: none;
  border-color: transparent!important;
  outline: none!important;
  box-shadow: none!important;
}
</style>