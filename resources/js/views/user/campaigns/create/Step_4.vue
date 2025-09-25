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
                            <ProgressBar :step="4" />
                        </div>
                        <div class="card-body camp-card-h">
                            <Loader :loading="processing" v-if="processing" />
                            <div class="row" v-else>
                                <div class="col-md-12">
                                    <h4 class="float-left">Select a list of subscribers</h4>
                                    <router-link :to="ROUTE_USER_LISTS()" class="float-right btn btn-primary text-light"><i class="pi pi-plus-circle"></i> Add new List</router-link>
                                </div>
                                <FormControlSelect2 :col_md="6" :options="LISTS" v-model="campaign.list_id" label="" />
                            </div>
                        </div>
                        <div class="card-footer">
                            <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_3()" class="btn btn-primary float-left"> <i
                                    class="pi pi-arrow-left"></i> Back </router-link>
                            <button class="btn btn-primary float-right" @click="validateList"> Next <i
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
    ROUTE_USER_CAMPAIGNS_STEP_3,
    ROUTE_USER_CAMPAIGNS_STEP_4,
    ROUTE_USER_CAMPAIGNS_STEP_5,
    ROUTE_USER_LISTS,
} from "../../../../constants.js";
import Crumb from "./Crumb.vue";
import ProgressBar from "./ProgressBar.vue";
import FormControlSelect2 from "../../../components/FormControlSelect2.vue";

export default {
    name: 'Step4',
    data() {
        return {
            processing: false,
            update_mode: Boolean(this.$route.params.id),
            campaign: {
                id: this.$route.params.id,
                list_id: '',
            },
            LISTS:[],

        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_STEP_3() {
            return ROUTE_USER_CAMPAIGNS_STEP_3.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_CAMPAIGNS_STEP_5() {
            return ROUTE_USER_CAMPAIGNS_STEP_5.replace(':id', this.$route.params.id);
        },
        ROUTE_USER_LISTS() { return ROUTE_USER_LISTS;},

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

        validateList() {
            if (!this.campaign.list_id) {
                notify('List selection is required!', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
                return false;
            }
            this.processing = true;
            this.axios.post('user/campaigns/list', this.campaign).then(({ data }) => {
                this.$router.push(this.ROUTE_USER_CAMPAIGNS_STEP_5())
            }).catch((error) => {
                this.$flattenErrors(error);
                return false;
            }).finally((data) => {
                this.processing = false;
            });
        },
        fetchLists() {
            this.processing = true;
            this.axios.post('user/campaigns/lists').then((response) => {
                this.LISTS=[];
                response.data.data.forEach(element => {
                    this.LISTS.push({
                        key: element.id,
                        value:
                        element.name + ' '
                            // + element.created_on['d'] + ' '
                            // + element.created_on['F'] + ' '
                            // + element.created_on['y']+' '
                        ,
                    })
                });
                console.log(response.data.data);
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    components: { FormControlSelect2, ProgressBar, Crumb, Loader, Breadcrumb, Sidebar, Header },
    created() {
        if (this.update_mode) this.edit();
        this.fetchLists();
    }
}
</script>
