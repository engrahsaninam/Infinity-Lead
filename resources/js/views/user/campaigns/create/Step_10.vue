<template>
    <div id="layout-wrapper">
        <Header />
        <Sidebar />
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <Crumb />
                    <div class="card">
                        <div class="card-header">
                            <ProgressBar :step="10" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>

                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <h4 class="text-center">Create your sequence</h4>
                                        <p class="text-center">Read our article <a href="">How To Write A Cold Email
                                                That Gets Responses.</a></p>
                                        <div class="card">
                                            <div class="card-header text-center">
                                                <p class="p-0 m-0"> New Email</p>
                                            </div>

                                            <div class="card-body p-0 m-0">
                                                <div class="input-group p-1">
                                                    <div class="input-group-append"><span
                                                            class="input-group-text bg-white  border-0">Subject</span>
                                                    </div>
                                                    <input v-model="campaign.subject" @blur="saveSubject" id="subject"
                                                        class="form-control no-outline border-0">
                                                </div>



                                                <div class="row px-3">
                                                    <FormControlSelect2 :col_md="6" :options="templates"
                                                        v-model="campaign.template_id"
                                                        label="Select an Email Template" />

                                                    <div class="col-md-6 text-right p-2 my-auto">


                                                        <a href="javascript:void(0);" v-if="campaign.template_id"
                                                            class="btn btn-warning mr-2"
                                                            @click="openTemplate(campaign.template_id)">
                                                            Preview
                                                        </a>

                                                        <router-link :to="ROUTE_USER_TEMPLATES_CREATE()"
                                                            class="btn btn-primary text-light"><i class="pi pi-plus-circle"></i>
                                                            Add new Template?</router-link>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 ">
                                                        <button class="btn btn-primary float-right my-3 mr-3"
                                                            v-if="!campaign.followups || (campaign.followups && campaign.followups.length < 2)"
                                                            @click="addFollowUp"><i class="pi pi-plus-circle"></i> Add
                                                            Followup</button>

                                                        <button class="btn btn-primary float-right my-3 mr-2"
                                                            v-if="!show_attachment" @click="showAttachment"><i
                                                                class="pi pi-plus-circle"></i> Add Attachment</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body border-top"
                                                v-for="(followup, index) in campaign.followups">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="row d-flex align-items-center px-3">
                                                            <p>Follow up after</p>
                                                            <FormControlSelect2 :col_md="4"
                                                                :options="follow_up_days_options"
                                                                v-model="followup.days" label="" :show_label="false" />
                                                            <p>If no reply</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button
                                                            class="btn btn-outline-light bg-light border text-dark float-right"
                                                            @click="removeFollowUp(index, followup.id)"><i
                                                                class="pi pi-trash"></i> Remove Followup</button>
                                                    </div>
                                                </div>
                                                <div class="border-top">
                                                    <FroalaEditor v-model="followup.message" :headerOptions="[]" />
                                                </div>
                                            </div>


                                            <div class="card-body border-top" v-show="show_attachment">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <h5>Attachment:</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div v-if="!campaign.attachment">
                                                            <div v-if="attachment" class="float-left">
                                                                <span
                                                                    class="text-primary font-semibold badge badge-soft-primary ">{{
                                                                    attachment.name }}</span>
                                                                <span class="badge badge-soft-danger cursor-pointer"
                                                                    @click="attachment = '';">
                                                                    <i class="pi pi-times-circle"></i>
                                                                </span>
                                                            </div>
                                                            <div v-else class="float-left row">
                                                                <FormInputFile v-model="attachment" :col_md="12"
                                                                    label="Select attachment to upload"
                                                                    class="float-left" />
                                                            </div>
                                                            <button class="btn btn-primary float-right"
                                                                @click="uploadFile"><i class="pi pi-upload"></i>
                                                                Upload</button>
                                                        </div>
                                                        <div v-else>
                                                            <a :href="campaign.attachment" target="_blank"
                                                                class="badge p-2 badge-soft-primary cursor-pointer">
                                                                <span class=""><i class="pi pi-external-link"></i>
                                                                    {{ campaign.o_attachment }}</span>
                                                            </a>
                                                            <span class="badge p-2 badge-soft-danger cursor-pointer"
                                                                v-if="campaign.attachment" @click="deleteAttachment"><i
                                                                    class="pi pi-trash"></i> Remove Attachment</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_9()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateEmailContent"> Next <i
                                    class="pi pi-arrow-right"></i> </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Sidebar from "../../../layout/Sidebar.vue";
import Loader from "../../../components/Loader.vue";
import Header from "../../../layout/Header.vue";
import Breadcrumb from "../../../components/Breadcrumb.vue";
import {
    FORM_DATA,
    notify,
    NOTIFY_TYPE_ERROR,
    ROUTE_USER_CAMPAIGNS_STEP_10, ROUTE_USER_CAMPAIGNS_STEP_11,
    ROUTE_USER_CAMPAIGNS_STEP_9,
    ROUTE_USER_TEMPLATES_CREATE,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import QuillEditorComponent from "../../../components/QuillEditorComponent.vue";
import FormControlInput from "../../../components/FormControlInput.vue";
import FormInputFile from "../../../components/FormInputFile.vue";
import FroalaEditor from "../../../components/FroalaEditor.vue";

export default {
    name: 'Step10',
    data() {
        return {
            attachment: '',
            show_attachment: true,
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
                followups: [],
                template_id: '',
            },
            headerOptions: [],
            follow_up_days_options: Array.from({ length: 27 }, (_, i) => ({
                key: i + 4,
                value: `${i + 4} days`
            })),
            templates: [],
        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_STEP_10() {
            return ROUTE_USER_CAMPAIGNS_STEP_10.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_11() {
            return ROUTE_USER_CAMPAIGNS_STEP_11.replace(':id', this.$route.params.id);
        },
        
        ROUTE_USER_CAMPAIGNS_STEP_9() {
            return ROUTE_USER_CAMPAIGNS_STEP_9.replace(':id', this.$route.params.id);
        },

        ROUTE_USER_TEMPLATES_CREATE() {
            return ROUTE_USER_TEMPLATES_CREATE.replace(':id?','');
        },
        async edit(processing = true) {
            if (processing) {
                this.processing = true;
            }
            await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
                this.campaign = response.data.data;
                if (this.campaign && this.campaign.header) {
                    this.headerOptions = Object.values(this.campaign.header.headers).map((header) => ({
                        key: header,
                        value: header
                    }));
                }
                if (!this.campaign.followups) {
                    this.campaign.followups = [];
                }
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                if (processing) {
                    this.processing = false;
                }
            });
        },
        addFollowUp() {
            if (!this.campaign.followups || (this.campaign.followups && this.campaign.followups.length < 2)) {
                this.campaign.followups.push({ id: '', message: '', days: 4, });
            }
        },
        removeFollowUp(index, id) {
            this.processing = true;
            this.axios.post('user/campaigns/delete-followup', { id: id }).then(({ data }) => {
            }).catch((error) => {
                //this.$flattenErrors(error);
            }).finally((data) => {
                if (this.campaign.followups.length) {
                    this.campaign.followups.splice(index, 1);
                }
                this.processing = false;
            });
        },
        deleteSignature() {
            this.processing = true;
            this.axios.post('user/campaigns/delete-signature', this.campaign).then(({ data }) => {
                this.edit();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },

        deleteAttachment() {
            this.processing = true;
            this.axios.post('user/campaigns/delete-attachment', this.campaign).then(({ data }) => {
                this.edit();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },


        saveSubject() {
            if (this.campaign.subject) {
                this.axios.post('user/campaigns/subject', this.campaign).then(({ data }) => {

                });
            }
        },
        saveMessage() {
            this.axios.post('user/campaigns/save-message', this.campaign)
        },
        syncFollowups() {
            this.axios.post('user/campaigns/sync-followups', this.campaign).then((data) => {
                this.edit(false);
            })
        },

        saveSignature() {
            this.axios.post('user/campaigns/save-signature', this.campaign)
        },
        showSignature() {
            this.campaign.show_signature = true;
            this.saveSignature();
        },
        showAttachment() {
            this.show_attachment = true;
        },

        validateEmailContent() {
            if (!this.campaign.subject) {
                notify('Subject is missing!', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }

            this.processing = true;
            this.axios.post('user/campaigns/email-content', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_11());
            }).catch((error) => {
                this.$flattenErrors(error);
                return false;
            }).finally((data) => {
                this.processing = false;
            });
            return true;
        },
        uploadFile() {
            this.processing = true;
            const data = {
                id: this.campaign.id,
                attachment: this.attachment,
            }
            this.axios.post('user/campaigns/attachment', FORM_DATA(data)).then(({ data }) => {
                notify(data.message);
                this.edit();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        openTemplate(id) {
            window.open(`/template/_view/${id}`, '_blank', 'width=600,height=600,top=100,left=100');
        },

        fetchTemplates() {
            this.processing = true;
            this.axios.post('user/campaigns/templates').then((response) => {
                this.templates = [];
                response.data.data.forEach(element => {
                    this.templates.push({
                        key: element.id,
                        value:
                            element.name + ' '
                            + element.created_on['d'] + ' '
                            + element.created_on['F'] + ' '
                            + element.created_on['y'] + ' '
                        ,
                    })
                });
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    components: {
        FroalaEditor,
        FormInputFile,
        FormControlInput,
        QuillEditorComponent, FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header
    },
    created() {
        if (this.update_mode) this.edit();
        this.fetchTemplates();
    }
}
</script>
