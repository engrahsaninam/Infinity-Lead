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
                                <h4>Lists</h4>
                                <Breadcrumb :items="[ROUTE_USER_LISTS()]" />
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
                                                <button @click="openCreateModal"
                                                    class="btn btn-primary btn-rounded waves-effect waves-light "><i
                                                        class="mdi mdi-plus me-1"></i> Create</button>
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
                                                    <div class="input-group" v-if="list.id === __list.id">
                                                        <input type="text" class="form-control" placeholder="Edit Name"
                                                            v-model="list.name" />
                                                        <div class="input-group-append">
                                                            <button class="btn btn-success" @click="createList(list)"><i
                                                                    class="pi pi-save"></i></button>
                                                        </div>
                                                    </div>
                                                    <router-link v-else :to="ROUTE_USER_LISTS_SHOW(__list.id)" class="">
                                                        <b>{{ __list.name }}</b>
                                                    </router-link>

                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h6 class="text-muted">
                                                            Subscribers
                                                            <span>{{ __list.count_subscribers }} </span>
                                                        </h6>
                                                    </div>
                                                </td>
                                                <td class="text-right">
                                                    <small class="text-muted small m-0">
                                                        Created on
                                                        {{ __list.created_on['d'] }}
                                                        {{ __list.created_on['F'] }},
                                                        {{ __list.created_on['Y'] }}
                                                    </small>

                                                </td>
                                                <td>
                                                    <button class="btn btn-secondary btn-sm ml-2"
                                                        v-if="list.id === __list.id" @click="cancelEdit"> <i
                                                            class="mdi mdi-close"></i> </button>
                                                    <button class="btn btn-success btn-sm ml-2" v-else
                                                        @click="edit(__list)">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
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
                <div class="container " v-else>
                    <div class="row justify-content-center align-items-center" style="min-height: 80vh">
                        <div class="col-md-6 text-center ">
                            <h4>Create your List </h4>
                            <p class="text-muted"> Add emails or domains you want to exclude from your campaigns.
                                Prevent accidental outreach, improve targeting, and stay compliant.</p>
                            <button @click="openCreateModal"
                                class="btn btn-primary btn-rounded waves-effect waves-light mb-2 mr-2"><i
                                    class="mdi mdi-plus me-1"></i> Create List</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Confirm :show="showPopup" heading="Warning" message="Are you sure you want to delete?" @confirm="deleteList"
        @cancel="showPopup = false" />
    <Transition name="bounce">
        <dialog ref="dialog" @click.self="createListDialog" v-if="toggle_list_dialog" class="col-md-5 top-0">
            <div class="container ">
                <form @submit.prevent="createList(list)">
                    <div class="row ">
                        <div class="col-md-12 py-3 mb-3 border-bottom">
                            <h5 class="float-left">Add List</h5>
                            <h5 class="float-right" @click="createListDialog"><i class="pi pi-times"></i></h5>
                        </div>
                        <FormInputTooltip class="col-md-12" v-model="list.name" label="Name" :error="list_error.name" />
                        <div class="col-md-12 text-right py-2 pb-3">
                            <button class="btn btn-primary px-4 float-right rounded-pill" type="submit"
                                :disabled="processing">
                                <span v-if="!processing">Save</span>
                                <span v-else>Processing ...</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </dialog>
    </Transition>
</template>
<script>


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
    LIST_STATUS_DRAFTED,
    notify,
    ROUTE_USER_LISTS,
    ROUTE_USER_LISTS_SHOW,
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
    name: 'Lists',
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
        ROUTE_USER_LISTS() {
            return ROUTE_USER_LISTS
        },
        ROUTE_USER_LISTS_SHOW(id) {
            return ROUTE_USER_LISTS_SHOW.replace(':id', id);
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

        edit(list) {
            this.list = list;
        },
        cancelEdit() {
            this.list = {
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
            await this.axios.post(`user/lists/fetch`, this.table).then(({ data }) => {
                this.table.currentPage = data.current_page
                this.lists = data;
                this.countList();
            }).finally(() => {
                this.isLineLoading = false;
                this.shimmer = false;
            });
        },

        deleteList() {
            const payload = { id: this.delete_list_id }
            this.axios.post('user/lists/delete', payload).then((response) => {
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
            this.axios.post('user/lists/count').then((response) => {
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
