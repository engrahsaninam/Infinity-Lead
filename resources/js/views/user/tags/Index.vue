<template>

    <div id="layout-wrapper">
        <Header />
        <Sidebar />
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4>Tag</h4>
                                <Breadcrumb :items="[ROUTE_USER_TAGS()]" />
                            </div>
                            <LineLoader v-if="isLineLoading" />
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-footer border-bottom border-top-0">
                            <div class="col-md-6 py-3">
                                <label for="form-label"><i class="pi pi-plus-circle"></i> Add a new Tag</label>
                                <div class="input-group">
                                    <input class="form-control" placeholder="Tag" v-model="tag.name"
                                        @keyup.enter="create" />
                                    <div class="input-group-append">
                                        <button class="btn btn-primary float-right" @click="create">
                                            <i class="pi pi-save"></i>
                                            <span v-if="update_mode"> Update</span>
                                            <span v-else> Save</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <FormControlSelect2 :col_md="6" :options="LISTS" v-model="mapping.list_id"
                                    label="Select a List to Populate Columns" @select="fetchColumns" />
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-sm table-stripped table-hover" v-if="COLUMNS && mapping.list_id">
                                <tr>
                                    <th width="15%">Tag</th>
                                    <th width="85%">List Columns</th>
                                </tr>
                                <tr v-for="_tag in tags" :key="_tag.id">
                                    <td>
                                        <span>{{ _tag.name }}</span>
                                    </td>
                                    <td class="form-padding-0">
                                        <FormControlSelect2 class="w-auto" v-model="_tag.value" :options="COLUMNS" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">
                                        <button class="btn btn-success" @click="saveMapping(tags)">
                                            <i class="pi pi-save"></i> Save
                                        </button>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { ROUTE_USER_TAGS_CREATE, ROUTE_USER_TAGS, notify } from '../../../constants';
import Breadcrumb from '../../components/Breadcrumb.vue';
import FormControlInput from '../../components/FormControlInput.vue';
import Loader from '../../components/Loader.vue';
import QuillEditorComponent from '../../components/QuillEditorComponent.vue';
import FroalaEditor from '../../components/FroalaEditor.vue';
import Header from '../../layout/Header.vue';
import Sidebar from '../../layout/Sidebar.vue';
import FormControlSelect2 from '../../components/FormControlSelect2.vue';
import { Alert } from 'bootstrap';
export default {
    name: 'CreateTag',
    data() {
        return {
            show_signature: false,
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            tags: [],
            tag: {
                id: '',
                name: '',
            },
            template_error: {
                id: '',
                name: '',
            },
            LISTS: [],
            mapping: {
                list_id: '',
                tag_id: '',
            },
            COLUMNS: [],
        }
    },
    methods: {
        showSignature() {
            this.show_signature = true;
        },
        removeSignature() {
            this.tag.signature = '';
            this.show_signature = false;
        },
        ROUTE_USER_CAMPAIGNS_STEP_2(id) {
            return ROUTE_USER_CAMPAIGNS_STEP_2.replace(':id', id);
        },
        ROUTE_USER_TAGS_CREATE() {
            return ROUTE_USER_TAGS_CREATE;
        },
        ROUTE_USER_TAGS() {
            return ROUTE_USER_TAGS;
        },
        async create() {
            this.processing = true;
            await this.axios.post('user/tags/store', this.tag).then((response) => {
                notify(response.data.message);
                this.tag = {
                    id: '',
                    name: ''
                };
                this.fetch();
                this.$router.push(this.ROUTE_USER_TAGS());
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        async edit() {
            this.processing = true;
            await this.axios.post('user/templates/edit', { id: this.$route.params.id }).then((response) => {
                this.tag = response.data.data;
                this.show_signature = !!this.tag.signature;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        async fetch() {
            this.processing = true;
            await this.axios.post('user/tags/fetch').then((response) => {
                this.tags = response.data;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        fetchLists() {
            this.processing = true;
            this.axios.post('user/campaigns/lists').then((response) => {
                this.LISTS = [];
                response.data.data.forEach(element => {
                    this.LISTS.push({
                        key: element.id,
                        value:
                            element.name,
                    })
                });
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        fetchColumns() {
            this.axios.post('user/tags/fetch_column', { list_id: this.mapping.list_id }).then((response) => {
                const { headers, mappings } = response.data.data;
                this.COLUMNS = headers.map((h) => ({ key: h, value: h }));
                this.tags = this.tags.map(tag => ({
                    ...tag,
                    value: mappings[tag.id] ?? ''
                }));
            }).catch(this.$flattenErrors);
        },
        saveMapping(tags) {
            this.processing = true;
            this.axios.post('user/tags/mapping', {
                list_id: this.mapping.list_id,
                tags: tags.map(tag => ({ id: tag.id, value: tag.value })),
            }).then((res) => {
                notify(res.data.message);
            }).catch(this.$flattenErrors).finally(() => {
                this.processing = false;
            });
        }
    },
    components: { Sidebar, Header, Loader, Breadcrumb, FormControlInput, QuillEditorComponent, FroalaEditor, FormControlSelect2 },
    created() {
        if (this.update_mode) this.edit();
        this.fetch();
        this.fetchLists();
    }
}
</script>
<style>
.form-padding-0 .form-group{
    margin-bottom: 0!important;
}
</style>
