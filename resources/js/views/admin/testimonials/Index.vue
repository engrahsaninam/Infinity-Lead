<template>
  <div id="layout-wrapper">
    <Header/>
    <AdminSidebar/>
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer"/>
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Testimonials</h4>
                <Breadcrumb :items="[ROUTE_ADMIN_TESTIMONIALS()]"/>
              </div>
              <LineLoader v-if="isLineLoading"/>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-sm-3">
                      <div class="position-relative">
                        <input type="text" class="form-control search-input" v-model="table.search" placeholder="Search here..." @input="this.list(true)">
                        <i class="pi pi-search search-icon"></i>
                      </div>
                    </div>
                    <div class="col-sm-9 text-right">
                        <button @click="openCreateModal" class="btn btn-primary btn-rounded "><i class="mdi mdi-plus me-1"></i> Create</button>
                    </div>

                  </div>

                  <div class="table-responsive">
                    <table class="table  table-hover ">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Title</th>
                          <th>Rating</th>
                          <th>Feedback</th>
                          <th>Profile</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="testimonials.data.length === 0">
                        <td colspan="100%" class="text-center">
                          <i class="text-muted">No testimonial found.</i>
                        </td>
                      </tr>
                      <tr v-for="list in testimonials.data" :key="list.id" style="cursor: pointer;">
                        <td>{{ list.name }}</td>
                        <td>{{ list.title }}</td>
                        <td>{{ list.rating }}</td>
                        <td>{{ list.feedback }}</td>
                        <td>
                          <a :href="list.profile" target="_blank" >
                            <img :src="list.profile" alt="profile" width="100" height="100" class="img-thumbnail img-fluid">
                          </a>
                        </td>
                        <td>
                          <button  class="btn btn-sm btn-success mr-1" @click="edit(list)"> <i class="pi pi-pencil"></i></button>
                          <button  class="btn btn-sm btn-outline-danger mr-1" @click="showPop(list.id)"><i class="pi pi-trash"></i></button>

                        </td>
                        
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-header border-bottom-0">
                  <Pagination :list="page" :data="testimonials"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>





  <Transition name="bounce">
    <dialog ref="modal" @click.self="closeModal" v-if="toggle_modal" class="col-md-5 top-0">
      <div class="container ">
        <form @submit.prevent="store(testimonial)">
          <div class="row ">
            <div class="col-md-12 py-3 mb-3 border-bottom">
              <h5 class="float-left" v-if="update_mode">Update Testimonial</h5>
              <h5 class="float-left" v-else>Add Testimonial</h5>
              <h5 class="float-right" @click="closeModal"><i class="pi pi-times"></i></h5>
            </div>
            <FormControlInput v-model="testimonial.name" label="Name"/>
            <FormControlInput v-model="testimonial.title" label="Designation"/>
            <FormControlInput v-model="testimonial.rating" type="number" label="Rating" placeholder="0-5"/>
            <FormInputFile v-model="testimonial.file" :col_md="12" label="Profile Picture"/>
            <FormControlTextarea v-model="testimonial.feedback" :col_md="12" label="Feedback"/>
            <div class="col-md-12 text-right py-2 pb-3">
              <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                <span v-if="!processing">
                  <span v-if="update_mode">Update</span> 
                  <span v-else>Save</span> 
                </span>
                <span v-else>Processing ...</span>
              </button>
            </div>

          </div>
        </form>
      </div>
    </dialog>
  </Transition>
<Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete?"
      @confirm="_delete"
      @cancel="showPopup = false"
  />
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
  FORM_DATA,
  notify, ROUTE_ADMIN_TESTIMONIALS, ROUTE_USER_BLACKLIST,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Pagination from "../../layout/Paignation.vue";
import AdminSidebar from "../../layout/admin/AdminSidebar.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormInputFile from "../../components/FormInputFile.vue";

export default {
  name: 'Testimonial',
  components: {
    AdminSidebar,
    Pagination,
    FormControlInput,
    FormInputFile,
    FormControlTextarea,
    FormControlSelect2, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header},
  data() {
    return {
      showPopup:false,
      delete_id:'',
      update_mode:false,
      toggle_modal:false,
      isLineLoading:false,
      shimmer:true,
      table:{
        records:10,
        search:'',
        currentPage:1,
        page:1,
      },
      testimonial:{
        id:'',
        title:'',
        name:'',
        rating:'',
        feedback:'',
        profile:'',
      },
      processing:false,
    }
  },
  methods: {
    ROUTE_ADMIN_TESTIMONIALS() {
      return ROUTE_ADMIN_TESTIMONIALS
    },
    page(page=1){
      this.table.page=page;
      this.list();
    },
    edit(data){
      this.testimonial=data;
      this.update_mode=true;
      this.openModal();
    },
    createMode(){
      this.testimonial={
        id:'',
        title:'',
        name:'',
        rating:'',
        feedback:'',
        profile:'',
      };
      this.update_mode=false;
    },
    showPop(id){
      this.showPopup=true;
      this.delete_id=id;
    },
    hidePop(){
      this.showPopup=false;
      this.delete_id='';
    },
    
    
    
    async list(line=false) {
      if (line){this.isLineLoading=true}else{this.shimmer=true;}
      await this.axios.post(`admin/testimonials/fetch`,this.table).then(({ data }) => {
        this.table.currentPage = data.current_page
        this.testimonials = data;
      }).finally(() => {
        this.isLineLoading=false;
        this.shimmer=false;
      });
    },
    async store(data) {
      this.processing=true
      await this.axios.post(`admin/testimonials/store`,FORM_DATA(data)).then(({ data }) => {
        this.list();
        this.closeModal();
        this.createMode();
        notify(data.message);
      }).finally(() => {
        this.processing=false;
      });
    },
    
    closeModal() {
      this.toggle_modal = false;
      this.$refs.modal?.close();
    },
    openModal() {
      this.toggle_modal = true;
      this.$nextTick(() => {
        this.$refs.modal?.showModal();
      });
    },
    openCreateModal(){
      this.createMode();
      this.openModal();
    },
    _delete() {
      this.axios.post("admin/testimonials/delete", { id:this.delete_id }).then((response) => {
          notify(response.data.message);
          this.list();
          this.hidePop();
        }).catch((error) => {
          this.$flattenErrors(error);
      });
    },
  },
  created() {
    this.list();
  }
}
</script>