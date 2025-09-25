<template>
  <div id="layout-wrapper">
    <Header />
    <AdminSidebar />

    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer" />
      </div>

      <div class="page-content" v-else>
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>{{ title }}</h4>
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <div class="row" v-if="_data && _data.value">
                      <div class="col-md-12" v-html="_data.value"></div>
                    </div>
                    <div v-else class="row">
                      <div class="col-md-12 p-5">
                        <p class="text-muted text-center"><i>No data found</i></p>
                      </div>
                    </div>

                    <button class="btn float-right btn-sm btn-success mr-1" @click="edit(_data)">
                      Edit <i class="pi pi-pencil"></i>
                    </button>

                    <button v-if="_data && _data.value" class="btn float-right btn-sm btn-outline-danger mr-1" @click="showPop(_data.id)">
                      <i class="pi pi-trash"></i>
                    </button>
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
        <div class="container">
          <form @submit.prevent="store(_datum)">
            <div class="row">
              <div class="col-md-12 py-3 mb-3 border-bottom">
                <h5 class="float-left">Update {{ title }}</h5>
                <h5 class="float-right" @click="closeModal"><i class="pi pi-times"></i></h5>
              </div>

              <QuillEditorComponent v-model="_datum.value" :label="title" :show_counter="false" :show_spam="false" :show_ai="false" :show_fields_dropdown="false" :headerOptions="[]" />

              <div class="col-md-12 text-right py-2 pb-3">
                <button class="btn btn-primary px-4 float-right rounded-pill" type="submit" :disabled="processing">
                  <span v-if="!processing">Update</span>
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
  </div>
</template>

<script>
import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import AdminSidebar from "../../layout/admin/AdminSidebar.vue";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import QuillEditorComponent from "../../components/QuillEditorComponent.vue";
import { notify } from "../../../constants.js";

export default {
  name: "ContentWeb",
  props: {
    title: {
      type: String,
      required: true,
    },
    keyName: {
      type: String,
      required: true,
    },
  },
  components: {
    Header,
    Sidebar,
    AdminSidebar,
    Breadcrumb,
    Shimmer,
    LineLoader,
    Confirm,
    QuillEditorComponent,
  },
  data() {
    return {
      shimmer: true,
      isLineLoading: false,
      processing: false,
      showPopup: false,
      delete_id: '',
      toggle_modal: false,
      _data: '',
      _datum: {
        id: '',
        value: '',
      },
    };
  },
  methods: {
    fetch(line = false) {
      if (line) this.isLineLoading = true;
      else this.shimmer = true;

      this.axios
        .post("admin/settings/fetch", { key: this.keyName })
        .then(({ data }) => {
          this._data = data;
        })
        .finally(() => {
          this.isLineLoading = false;
          this.shimmer = false;
        });
    },
    store(data) {
      data.key = this.keyName;
      this.processing = true;
      this.axios
        .post("admin/settings/store", data)
        .then(({ data }) => {
          this.fetch();
          this.closeModal();
          notify(data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    edit(data) {
      this._datum = { ...data };
      this.openModal();
    },
    showPop(id) {
      this.showPopup = true;
      this.delete_id = id;
    },
    _delete() {
      this.axios
        .post("admin/settings/delete", { id: this._data.id })
        .then((response) => {
          notify(response.data.message);
          this.fetch();
          this.showPopup = false;
        });
    },
    openModal() {
      this.toggle_modal = true;
      this.$nextTick(() => {
        this.$refs.modal?.showModal();
      });
    },
    closeModal() {
      this.toggle_modal = false;
      this.$refs.modal?.close();
    },
  },
  created() {
    this.fetch();
  },
};
</script>
