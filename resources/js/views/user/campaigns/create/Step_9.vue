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
                            <ProgressBar :step="9" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>

                                <h4>Optimize your Deliverability</h4>
                                <table class="table table-borderless">
                                    <tr class="text-muted small" v-for="(option, index) in deliverabilityOptions"
                                        :key="index">
                                        <td>{{ option.label }}</td>
                                        <td class="text-right">
                                            <span class="mr-2 text-primary">{{ option.note }}</span>
                                            <div class="switch">
                                                <input type="checkbox" :id="'switches' + index" :value="option.value"
                                                    checked disabled />
                                                <label :for="'switches' + index">.</label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_8()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateDeliverability"> Next <i
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
    notify,
     ROUTE_USER_CAMPAIGNS_STEP_11,
    ROUTE_USER_CAMPAIGNS_STEP_10,
    ROUTE_USER_CAMPAIGNS_STEP_8,
    ROUTE_USER_CAMPAIGNS_STEP_7, ROUTE_USER_CAMPAIGNS_STEP_9,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";

export default {
    name: 'Step9',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
            },

            deliverabilityOptions: [
                { value: "plain_text_emails", label: "Send emails as plain text (not HTML) to improve deliverability.", note: "Mandatory" },
                { value: "gradual_sending", label: "Gradually increase sending volume on newly connected email accounts for the first 8-weeks.", note: "Mandatory" },
                { value: "auto_match_inboxes", label: "Auto-match your leads’ inboxes to your sender email accounts. Gmail to Gmail. Outlook to Outlook.", note: "Mandatory" },
                { value: "avoid_bounced_emails", label: "Avoid sending emails to email accounts that have previously bounced.", note: "Mandatory" }
            ],
        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_STEP_8() {
            return ROUTE_USER_CAMPAIGNS_STEP_8.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_10() {
            return ROUTE_USER_CAMPAIGNS_STEP_10.replace(':id', this.$route.params.id);
        },
        async edit() {
            this.processing = true;
            await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
                this.campaign = response.data.data;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },

        validateDeliverability() {
            this.deliverability = [
                'plain_text_emails',
                'gradual_sending',
                'auto_match_inboxes',
                'avoid_bounced_emails',
            ];
            this.processing = true;
            this.axios.post('user/campaigns/deliverability', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_10())
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    components: { ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header },
    created() {
        this.edit();
    }
}
</script>
