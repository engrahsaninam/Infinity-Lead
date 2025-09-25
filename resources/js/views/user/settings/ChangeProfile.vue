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
                <h4>Change Profile </h4>
                <Breadcrumb :items="[ROUTE_USER_CHANGE_PROFILE()]" />
              </div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                </div>
                <div class="card-body">
                  <form @submit.prevent="updatePassword" class="  p-md-3 py-0">
                    <div class="row">
                      <div class="col-md-2 mb-5">
                        <router-link :to="ROUTE_USER_PROFILE()"><i class="pi pi-arrow-left"></i></router-link>
                      </div>
                      <div class="col-md-10">
                        <h4>Your profile picture</h4>
                      </div>
                      <div class="col-md-12 text-center" v-if="!edit_profile_mode">
                        <div class="">
                          <img :src="user.profile" width="150" height="150" alt="">
                        </div>
                        <div class="mt-4">
                          <button class="btn btn-primary mr-3" @click="editModeOn"><i class="pi pi-file-edit"></i> Edit</button>
                          <button class="btn btn-outline-danger" @click="deleteProfile"><i class="pi pi-trash"></i> Delete</button>
                        </div>
                      </div>
                      <div v-else>
                        <UploadImages @changed="handleImages" :max="1" v-if="!imageSelected" />
                        <div v-else class="row">
                          <div class="col-md-12">
                            <img :src="imageUrl" alt="Screenshot Preview" ref="image" class="w-100 img-fluid img-thumbnail"/>
                          </div>
                          <div class="col-md-12 mt-4">
                            <button class="btn btn-outline-light float-left text-dark" @click="cancelUpload"><i class="pi pi-times-circle"></i> Cancel</button>
                            <button class="btn btn-primary float-right" @click="updateProfile"><i class="pi pi-upload"></i> Upload</button>
                          </div>
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
import {FORM_DATA, notify, ROUTE_USER_CHANGE_PROFILE, ROUTE_USER_PROFILE} from "../../../constants.js";
import UploadImages from "vue-upload-drop-images"
import Breadcrumb from "../../components/Breadcrumb.vue";


export default {
  name: 'ChangeProfile',
  components: {
    Breadcrumb, FormControlInput,
    Pills,
    Sidebar,
    Header,
    UploadImages,
  },
  data() {
    return {
      edit_profile_mode:false,
      imageSelected:false,
      profile:'',
      imageUrl:false,
      isCropping:false,
      user:{
        profile:'',
      },
      user_error:{
        profile:'',
      },
    }
  },
  methods: {
    ROUTE_USER_CHANGE_PROFILE() {
      return ROUTE_USER_CHANGE_PROFILE
    },
    ROUTE_USER_PROFILE() {
      return ROUTE_USER_PROFILE
    },
    async updateProfile() {
      this.user_error = true;
      var data=FORM_DATA({'profile':this.profile});
      await this.axios.post('user/profile/profile',data).then(({ data }) => {
        notify(data.message);
        this.fetchUserData();
        this.cancelUpload();
      }).catch((error) => {
        this.user_error=this.$flattenErrors(error);
      });
    },
    async fetchUserData() {
      await this.axios.post('auth-user').then(({ data }) => {
        this.user=data.data;
      });
    },
    editModeOn(){
      this.edit_profile_mode=true;
    },
    handleImages(files){
      if (files.length>0){
        const file = files[0];
        this.profile=file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imageUrl = e.target.result;
          this.imageSelected = true;
        };
        reader.readAsDataURL(file);
      }else{
        this.imageSelected=false;
      }
    },
    cancelUpload(){
      this.profile='';
      this.imageSelected=false;
      this.imageUrl='';
      this.edit_profile_mode=false;
    }
  },
  created() {
    this.fetchUserData();
  }
}
</script>