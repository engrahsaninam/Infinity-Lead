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
                            <ProgressBar :step="6" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="col-md-12" v-else>


                                <h4>In which time zone?</h4>
                                <FormControlSelect2 :col_md="6" :options="timezones" v-model="campaign.timezone"
                                    label="" />

                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_5()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateTimezone"> Next <i
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
    ROUTE_USER_CAMPAIGNS_STEP_4,
    ROUTE_USER_CAMPAIGNS_STEP_5,
    ROUTE_USER_CAMPAIGNS_STEP_6,
    ROUTE_USER_CAMPAIGNS_STEP_7,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";
import moment from "moment-timezone";

export default {
    name: 'Step6',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
                timezone: '',
            },
            timezones: this.getTimezones(),

        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_STEP_5() {
            return ROUTE_USER_CAMPAIGNS_STEP_5.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_7() {
            return ROUTE_USER_CAMPAIGNS_STEP_7.replace(':id', this.$route.params.id);
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
        getTimezones() {
            return moment.tz.names().map((tz) => {
                const offset = moment.tz(tz).utcOffset();
                const formattedOffset = `UTC${offset >= 0 ? "+" : ""}${(offset / 60).toFixed(0)}`;
                return { key: tz, value: `${formattedOffset} - ${tz}` };
            });
        },
        validateTimezone() {
            if (!this.campaign.timezone) {
                notify('Timezone is required!', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }
            this.processing = true;
            this.axios.post('user/campaigns/timezone', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_7())
            }).catch((error) => {
                this.$flattenErrors(error);
                return false;
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    components: { FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header },
    created() {
        if (this.update_mode) this.edit();
    }
}
</script>
