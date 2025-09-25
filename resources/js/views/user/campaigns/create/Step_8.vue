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
                            <ProgressBar :step="8" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>


                                <div class="col-md-12">
                                    <h5>Control Sending Volume Per Email Account</h5>



                                    <div v-for="(account, ind) in email_accounts" :key="account.id" class="col-md-6 p-0">
                                        <p v-if="ind === 0 && account.type === EMAIL_TYPE_GOOGLE()">
                                            Set a per-minute and volume limit for each selected email account.
                                        </p>
                                        <div class="card p-3" v-if="campaign.accounts.includes(account.id)">
                                            <h6>{{ account.name }} <small class="text-muted">({{ account.email
                                                    }})</small></h6>
                                            <div class="form-group" v-if="account.type === EMAIL_TYPE_GOOGLE()">
                                                <label>Per Minute</label>
                                                <VueSlider v-model="account.per_minute" :min="0" :max="40"
                                                    :tooltip="'always'"
                                                    @change="(val) => updateSliderRange(account, 'm', val)" />
                                            </div>
                                            <div class="form-group" v-if="account.type === EMAIL_TYPE_GOOGLE()">
                                                <label>Volume</label>
                                                <VueSlider v-model="account.volume" :min="0" :max="40"
                                                    @change="(val) => updateSliderRange(account, 'v', val)" />
                                            </div>
                                            <div class="mt-5" v-else>
                                                <p class="m-0 form-label">You're connected via SMTP.</p>
                                                <p class="m-0 form-label">SMTP allows high-volume sending. Your emails
                                                    will be sent as quickly as your server allows.</p>
                                                <p class="m-0 form-label"> No limits are applied here, but we recommend
                                                    monitoring your bounce and spam rates.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_7()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validatePerMinVolume"> Next <i
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
    EMAIL_TYPE_GOOGLE,
    notify,
    NOTIFY_TYPE_ERROR,
    ROUTE_USER_CAMPAIGNS_STEP_10,
    ROUTE_USER_CAMPAIGNS_STEP_7,
    ROUTE_USER_CAMPAIGNS_STEP_9,
    ROUTE_USER_CAMPAIGNS_STEP_8,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import VueSlider from '@vueform/slider';
import '@vueform/slider/themes/default.css';
export default {
    name: 'Step8',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
            },
            email_accounts: [],
        }
    },
    methods: {
        EMAIL_TYPE_GOOGLE() {
            return EMAIL_TYPE_GOOGLE
        },
        ROUTE_USER_CAMPAIGNS_STEP_7() {
            return ROUTE_USER_CAMPAIGNS_STEP_7.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_9() {
            return ROUTE_USER_CAMPAIGNS_STEP_9.replace(':id', this.$route.params.id);
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

        updateSliderRange(account, type, value) {
            if (type === 'm') {
                account.per_minute = value;
            } else {
                account.volume = value;
            }
            this.axios.post('user/email-accounts/range', account);
        },
        syncAccountInCampaign(id) {
            if (!this.campaign.accounts) {
                this.campaign.accounts = [];
            }
            const index = this.campaign.accounts.indexOf(id);
            if (index === -1) {
                this.campaign.accounts.push(id);
            } else {
                this.campaign.accounts.splice(index, 1);
            }
        },
        validatePerMinVolume() {
            let invalid = false;
            const selectedAccounts = this.email_accounts.filter(acc =>
                this.campaign.accounts.includes(acc.id)
            );
            selectedAccounts.forEach(account => {
                if(account.type !== 'smtp'){
                    if (!account.per_minute) {
                        notify(`Per minute selection is required for ${account.email}`, { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                        invalid = true;
                    }
                    if (!account.volume) {
                        notify(`Volume selection is required for ${account.email}`, { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                        invalid = true;
                    }
                }
                
            });
            if (invalid) return;
            this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_9());

        },
        async fetchEmailAccounts() {
            await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
                this.email_accounts = data.data;
            });
        },
    },
    components: { ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header, VueSlider },
    created() {
        this.edit(); this.fetchEmailAccounts();

    }
}
</script>
