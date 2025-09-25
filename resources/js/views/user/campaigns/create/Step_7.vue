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
                            <ProgressBar :step="7" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>

                                <div class="row">

                                    <div class="col-md-12  mb-3">
                                        <h4 class="float-left">At what time?</h4>
                                        <button class="float-right btn btn-outline-primary rounded-pill px-4"
                                            @click="setWholeDayForCustom">
                                            Want to send whole day?
                                        </button>
                                    </div>
                                    <div v-for="option in scheduleOptions" :key="option.key" class="col mb-4">
                                        <div class="border p-4 w2-step-4"
                                            :class="{ 'border-success-w2-step-4': campaign.day === option.key }"
                                            @click="manage(option)">
                                            <h5>{{ option.icon }} {{ option.label }}</h5>
                                            <div class="p-3">
                                                <div class="row">
                                                    <div class="col-12 border p-3 text-center pt-4">
                                                        <h6 class="form-label">Start</h6>
                                                        <p v-if="option.key !== 'custom'">{{ option.start }}</p>
                                                        <input v-else type="time" v-model="campaign.start"
                                                            class="form-control" />
                                                    </div>
                                                    <div class="col-12 border p-3 text-center pt-4">
                                                        <h6 class="form-label">End</h6>
                                                        <p v-if="option.key !== 'custom'">{{ option.end }}</p>
                                                        <input v-else type="time" v-model="campaign.end"
                                                            class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_6()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateDayTime"> Next <i
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
    ROUTE_USER_CAMPAIGNS_STEP_7,
    ROUTE_USER_CAMPAIGNS_STEP_8, ROUTE_USER_CAMPAIGNS_STEP_9,
    SHEET_TYPE_CSV,
    SHEET_TYPE_GOOGLE
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import moment from "moment-timezone";

export default {
    name: 'Step7',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
            },
            scheduleOptions: [
                { key: "full", icon: "⏰", label: "Full Day", start: "08:00 AM", end: "06:00 PM" },
                { key: "morning", icon: "☀️", label: "Morning", start: "08:00 AM", end: "11:45 AM" },
                { key: "afternoon", icon: "🌄", label: "Afternoon", start: "01:00 PM", end: "04:45 PM" },
                { key: "custom", icon: "📝", label: "Custom", start: "", end: "" }
            ],
        }
    },
    methods: {
        manage(option) {
            this.campaign.day = option.key
            if (option.key !== 'custom') {
                this.campaign.start = option.start
                this.campaign.end = option.end
            }
        },
        ROUTE_USER_CAMPAIGNS_STEP_6() {
            return ROUTE_USER_CAMPAIGNS_STEP_6.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_8() {
            return ROUTE_USER_CAMPAIGNS_STEP_8.replace(':id', this.$route.params.id);
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

        validateDayTime() {
            if (!this.campaign.day) {
                notify('Select a box', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }
            if (!this.campaign.start) {
                notify('Start Time is missing', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }
            if (!this.campaign.start) {
                notify('End Time is missing', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }
            this.processing = true;
            this.axios.post('user/campaigns/time', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_8())
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        setWholeDayForCustom() {
            const customOption = this.scheduleOptions.find(opt => opt.key === 'custom');
            if (customOption) {
                this.campaign.day = 'custom';
                this.campaign.start = '00:00';
                this.campaign.end = '23:59';
                customOption.start = '12:00 AM';
                customOption.end = '11:59 PM';
            }
        },
    },
    components: { FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header },
    created() {
        if (this.update_mode) this.edit();
    }
}
</script>
