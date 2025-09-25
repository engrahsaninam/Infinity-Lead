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
                                <h4 v-if="update_mode">Update Template</h4>
                                <h4 v-else>Create Template</h4>
                                <Breadcrumb :items="[ROUTE_USER_TEMPLATES(), ROUTE_USER_TEMPLATES_CREATE()]" />
                            </div>
                            <LineLoader v-if="isLineLoading" />
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-footer border-bottom border-top-0">
                            <div class="row">
                                <FormControlInput label="Template Name" v-model="template.name" />
                                <FormControlSelect2 label="Type" :col_md="3"
                                    :options="[{ key: 'html', value: 'HTML' }, { key: 'text', value: 'Plain Text' }]"
                                    v-model="template.type" />
                            </div>

                        </div>


                        <div class="card-body ">
                            <FroalaEditor v-show="(template.type == 'html')" v-model="template.body"
                                :headerOptions="tags" />
                            <FormControlTextarea ref="textarea" v-show="(template.type == 'text')" v-model="template.body" :col_md="12"
                                :rows="10" placeholder="Write text here..." />
                            <div class="col-md-12 mb-2" v-show="(template.type == 'text')">
                                <span 
                                    v-for="tag in tags" 
                                    :key="tag.key" 
                                    class="badge badge-info mr-2 cursor-pointer"
                                    @click="copyToClipboard(tag.value)"
                                    style="cursor: pointer;"
                                    title="Click to copy"
                                >
                                    {{ tag.value }}
                                </span>
                            </div>
                            <button class="btn btn-primary float-right my-3 mr-2" v-if="!show_signature"
                                @click="showSignature"><i class="pi pi-plus-circle"></i> Add Signature</button>
                            <div class="row" v-if="show_signature">
                                <div class="col-md-4">
                                    <h5>Signature:</h5>
                                    <p class="text-muted small">(Appended at the end of all outgoing messages)</p>
                                    <p class="text-muted small">Read our article How To Create The Perfect Email
                                        Signature.</p>
                                    <button class="btn btn-outline-light border bg-light text-dark"
                                        @click="removeSignature"><i class="pi pi-trash"></i> Remove Signature</button>
                                </div>
                                <div class="col-md-8">
                                    <FroalaEditor v-model="template.signature"
                                        :header-options="[{ key: 'Domain', value: 'Domain' }, { key: 'Email', value: 'Email' }]"
                                        :showAI="false" :showCounter="false" :showSpam="false" />
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary float-right" @click="create">
                                <i class="pi pi-save"></i>
                                <span v-if="update_mode">Update</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FormControlTextarea from '../../components/FormControlTextarea.vue';
import { ROUTE_USER_TEMPLATES_CREATE, ROUTE_USER_TEMPLATES, notify } from '../../../constants';
import Breadcrumb from '../../components/Breadcrumb.vue';
import FormControlInput from '../../components/FormControlInput.vue';
import Loader from '../../components/Loader.vue';
import QuillEditorComponent from '../../components/QuillEditorComponent.vue';
import FroalaEditor from '../../components/FroalaEditor.vue';
import Header from '../../layout/Header.vue';
import Sidebar from '../../layout/Sidebar.vue';
import FormControlSelect2 from '../../components/FormControlSelect2.vue';
export default {
    name: 'CreateTemplate',
    data() {
        return {
            show_signature:false,
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            template: {
                id: '',
                name: 'Untitled Template',
                body: '',
                type: 'text',
                signature: '',
            },
            template_error: {
                id: '',
                name: '',
                body: '',
                signature: '',
            },
            tags:[],
        }
    },
    methods: {
        showSignature() {
            this.show_signature = true;
        },
        removeSignature() {
            this.template.signature='';
            this.show_signature=false;
        },
        ROUTE_USER_CAMPAIGNS_STEP_2(id) {
            return ROUTE_USER_CAMPAIGNS_STEP_2.replace(':id', id);
        },
        ROUTE_USER_TEMPLATES_CREATE() {
            return ROUTE_USER_TEMPLATES_CREATE;
        },
        ROUTE_USER_TEMPLATES() {
            return ROUTE_USER_TEMPLATES;
        },
        async create() {
            this.processing = true;
            await this.axios.post('user/templates/store', this.template).then((response) => {
                notify(response.data.message);
                this.$router.push(this.ROUTE_USER_TEMPLATES());
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        async edit() {
            this.processing = true;
            await this.axios.post('user/templates/edit', { id: this.$route.params.id }).then((response) => {
                this.template = response.data.data;
                this.show_signature = !!this.template.signature;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        async fetchTags() {
            this.processing = true;
            await this.axios.post('user/tags/fetch').then((response) => {
                this.tags=[];
                response.data.forEach(tag=>{
                    this.tags.push({
                        key: tag.name,
                        value: tag.name,
                    })
                });
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        copyToClipboard(tag){
            if (!tag) return;
            // Create a temporary textarea to copy the tag value
            const textarea = document.createElement('textarea');
            textarea.value = "{" + tag+"}";
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                notify('Copied to clipboard');
            } catch (err) {
                notify('Failed to copy');
            }
            document.body.removeChild(textarea);

        },
    },
    components: {
    FormControlTextarea, Sidebar, Header, Loader, Breadcrumb, FormControlInput, QuillEditorComponent, FroalaEditor,FormControlSelect2, },
    created() {
        if (this.update_mode) this.edit();
        this.fetchTags();

    }
}
</script>
