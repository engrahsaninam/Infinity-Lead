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
                                <h4 v-if="update_mode">Update Tag</h4>
                                <h4 v-else>Create Tag</h4>
                                <Breadcrumb :items="[ROUTE_USER_TAGS(), ROUTE_USER_TAGS_CREATE()]" />
                            </div>
                            <LineLoader v-if="isLineLoading" />
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-footer border-bottom border-top-0">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input class="form-control" placeholder="Tag" label="Tag" v-model="tag.name" />
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
export default {
    name: 'CreateTag',
    data() {
        return {
            show_signature: false,
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            tag: {
                id: '',
                name: '',
            },
            template_error: {
                id: '',
                name: '',
            }
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
    },
    components: { Sidebar, Header, Loader, Breadcrumb, FormControlInput, QuillEditorComponent, FroalaEditor },
    created() {
        if (this.update_mode) this.edit();

    }
}
</script>
