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
                            <ProgressBar :step="5"/>
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>
                                <h4>When do you want your campaign to send?</h4>
                                <div class="row">
                                    <div class="col text-center" v-for="day in week_days" :key="day"
                                        @click="manageDay(day)">
                                        <p class="week-days text-capitalize border w2-step-4"
                                            :class="{ 'border-success-w2-step-4': campaign.days.includes(day) }">
                                            {{ day }}
                                            <span v-if="campaign.days.includes(day)" class="check-mark"><i
                                                    class="pi pi-check-circle"></i></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_4()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateWeekDays"> Next <i
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
    NOTIFY_TYPE_ERROR,
    ROUTE_USER_CAMPAIGNS,
    ROUTE_USER_CAMPAIGNS_CREATE,
    ROUTE_USER_CAMPAIGNS_STEP_2,
    ROUTE_USER_CAMPAIGNS_STEP_3,
    ROUTE_USER_CAMPAIGNS_STEP_4,
     ROUTE_USER_CAMPAIGNS_STEP_5,
     ROUTE_USER_CAMPAIGNS_STEP_6,
    SHEET_TYPE_CSV,
    SHEET_TYPE_GOOGLE
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";

export default {
    name: 'Step6',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
                name: '',
                controls: '',
            },
            week_days: ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',],
            manageDay(day) {
                const index = this.campaign.days.indexOf(day);
                if (index === -1) {
                    this.campaign.days.push(day); // Add day if not already selected
                } else {
                    this.campaign.days.splice(index, 1); // Remove day if selected
                }
            },
        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_STEP_4() {
            return ROUTE_USER_CAMPAIGNS_STEP_4.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_5() {
            return ROUTE_USER_CAMPAIGNS_STEP_5.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_6() {
            return ROUTE_USER_CAMPAIGNS_STEP_6.replace(':id', this.$route.params.id);
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
        validateWeekDays() {
            this.processing = true;
            this.axios.post('user/campaigns/days', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_6())
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    components: { ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header },
    created() {
        if (this.update_mode) this.edit();
    }
}
</script>
<style></style>
