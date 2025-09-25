<template>
    <div id="layout-wrapper">
        <Header />
        <Sidebar />
        <div class="main-content">
            <div class="page-content" v-if="shimmer">
                <Shimmer column="12" height="1000" :onLoading="shimmer" />
            </div>
            <div class="page-content" v-else>
                <div class="container-fluid" v-if="count">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4>Templates</h4>
                                <Breadcrumb :items="[ROUTE_USER_TEMPLATES()]" />
                            </div>
                            <LineLoader v-if="isLineLoading" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-3">
                                            <div class="position-relative">
                                                <input type="text" class="form-control search-input"
                                                    v-model="table.search" placeholder="Search here..."
                                                    @input="this.list(true)">
                                                <i class="pi pi-search search-icon"></i>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="float-right">
                                                <router-link :to="ROUTE_USER_TEMPLATES_CREATE()"
                                                    @click="openCreateModal"
                                                    class="btn btn-primary btn-rounded waves-effect waves-light "><i
                                                        class="mdi mdi-plus me-1"></i> Create</router-link>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table  table-hover ">
                                            <tr v-if="lists.data.length === 0">
                                                <td colspan="2" class="text-center">
                                                    <i class="text-muted">No list found.</i>
                                                </td>
                                            </tr>
                                            <tr v-for="__list in lists.data" :key="__list.id">
                                                <td>
                                                    {{ __list.name }}
                                                </td>
                                                <td>
                                                    {{ __list.type.toUpperCase() }}
                                                </td>

                                                <td class="text-right">
                                                    <small class="text-muted small m-0">
                                                        Created on
                                                        {{ __list.created_on['d'] }}
                                                        {{__list.created_on['F']}},
                                                        {{ __list.created_on['Y'] }}
                                                    </small>

                                                </td>
                                                <td>
                                                    <router-link :to="ROUTE_USER_TEMPLATES_CREATE(__list.id)"
                                                        @click="openCreateModal" class="btn btn-success btn-sm ml-2"><i
                                                            class="mdi mdi-pencil"></i></router-link>

                                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm ml-2"
                                                        @click="openTemplate(__list.id)">
                                                        <i class="pi pi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm ml-2"
                                                        @click="deletePopup(__list.id)">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-header border-bottom-0">
                                    <Pagination :list="page" :data="lists" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container" v-else>
                    <div class="row justify-content-center align-items-center" style="min-height: 80vh">
                        <div class="col-md-6 text-center ">
                            <h4>Create Template </h4>
                            <p class="text-muted"> Add emails or domains you want to exclude from your campaigns.
                                Prevent accidental outreach, improve targeting, and stay compliant.</p>



                            <router-link :to="ROUTE_USER_TEMPLATES_CREATE()" @click="openCreateModal"
                                class="btn btn-primary btn-rounded waves-effect waves-light "><i
                                    class="mdi mdi-plus me-1"></i> Create</router-link>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Confirm :show="showPopup" heading="Warning" message="Are you sure you want to delete?" @confirm="delete"
        @cancel="showPopup = false" />

</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
    LIST_STATUS_DRAFTED,
    notify,
    ROUTE_USER_TEMPLATES,
    ROUTE_USER_TEMPLATES_CREATE,
    SHEET_TYPE_CSV,
    SHEET_TYPE_GOOGLE,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import Confirm from "../../components/Confirm.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import FormControlTextarea from "../../components/FormControlTextarea.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Pagination from "../../layout/Paignation.vue";
import FormControlInput from "../../components/FormControlInput.vue";

export default {
    name: 'Templates',
    components: {
        Pagination,
        FormControlSelect2,
        FormControlTextarea, FormInputTooltip, Confirm, LineLoader, Shimmer, Breadcrumb, Sidebar, Header
    },
    data() {
        return {
            toggle_list_dialog: false,
            toggle_import_dialog: false,
            revise_loading_id: '',
            delete_list_id: '',
            showPopup: false,
            isLineLoading: false,
            shimmer: true,
            count: 0,
            lists: '',
            table: {
                records: 10,
                search: '',
                currentPage: 1,
                page: 1,
            },
            list: {
                id: '',
                name: '',
            },
            list_error: {
                id: '',
                name: '',
            },
            processing: false,
            TYPES: [
                { key: SHEET_TYPE_GOOGLE, value: SHEET_TYPE_GOOGLE },
                { key: SHEET_TYPE_CSV, value: SHEET_TYPE_CSV },
            ],
        }
    },
    methods: {
        ROUTE_USER_TEMPLATES_CREATE(id='') {
            return ROUTE_USER_TEMPLATES_CREATE.replace(':id?',id)
        },

        ROUTE_USER_TEMPLATES() {
            return ROUTE_USER_TEMPLATES;
        },

        LIST_STATUS_DRAFTED() {
            return LIST_STATUS_DRAFTED
        },

        page(page = 1) {
            this.table.page = page;
            this.__list();
        },
        openCreateModal() {
            this.toggle_list_dialog = true;
            this.$nextTick(() => {
                this.$refs.dialog?.showModal();
            });
        },
        openTemplate(id) {
            window.open(`/template/_view/${id}`, '_blank', 'width=600,height=600,top=100,left=100');
        },
        edit(list){
            this.list=list;
        },
        cancelEdit(){
            this.list={
                id: '',
                name: '',
            };
        },

        createListDialog() {
            this.toggle_list_dialog = false;
            this.$refs.dialog?.close();

        },
        deletePopup(id) {
            this.delete_list_id = id;
            this.showPopup = true;
        },
        async __list(line = false) {
            if (line) { this.isLineLoading = true } else { this.shimmer = true; }
            await this.axios.post(`user/templates/fetch`, this.table).then(({ data }) => {
                this.table.currentPage = data.current_page
                this.lists = data;
                this.countList();
            }).finally(() => {
                this.isLineLoading = false;
                this.shimmer = false;
            });
        },

        delete() {
            const payload = { id: this.delete_list_id }
            this.axios.post('user/template/delete', payload).then((response) => {
                notify(response.data.message);
                this.__list();
                this.showPopup = false;
                this.delete_list_id = '';
            }).catch((error) => {
                this.$flattenErrors(error);
            });
        },
        createList(data) {
            this.processing = true;
            this.axios.post('user/lists/store', data).then((response) => {
                notify(response.data.message);
                this.__list();
                this.toggle_list_dialog = false;
                this.list = {
                    id: '',
                    name: '',
                };
            }).catch((error) => {
                this.list_error = this.$flattenErrors(error);
            }).finally((res) => {
                this.processing = false;
            });
        },
        countList() {
            this.axios.post('user/templates/count').then((response) => {
                this.count = response.data.data;
            });
        },

    },
    created() {
        this.__list();
        this.countList();
    }
}
</script>
