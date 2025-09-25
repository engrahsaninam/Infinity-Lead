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
                                <h4>Campaigns <i class="pi pi-chevron-right" style="font-size: 14px"></i> <span
                                        class="text-primary">{{ campaign.name }}</span></h4>
                                <Breadcrumb :items="[ROUTE_USER_CAMPAIGNS(), ROUTE_USER_CAMPAIGNS_SHOW()]" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <Loader :loading="processing" />
                        <div class="col-md-12 ">
                            <div class="btn-group rounded-0">
                                <button v-for="m in menus"
                                    :class="{ 'active-tab-camp': m === menu, 'text-secondary border-0 ': m !== menu }"
                                    @click="manageMenu(m)"
                                    class="btn tab-camp rounded-0 btn-outline-light bg-white ">{{ m }}</button>
                            </div>
                            <div class="card mt-4" v-if="menu === 'Leads'">
                                <div class="card-header">
                                    <div class="dropdown float-left">
                                        <button class="btn btn-link dropdown-toggle" type="button" id="leads"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ filter.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }} Leads
                                        </button>
                                        <div class="dropdown-menu leads-filter-dropdown" aria-labelledby="leads">
                                            <a v-for="(value, key) in filters" :key="key" class="dropdown-item"
                                                :class="{ 'bg-success text-white': filter === key }"
                                                @click.prevent="rowsFilter(key)">
                                                {{ value.label }} ( {{ analytic[key] }} )
                                            </a>
                                        </div>
                                    </div>
                                    <a :href="'/campaigns/download/'+this.campaign.id+'/'+this.filter" 
                                        class="float-right text-white btn btn-success"><i class="pi pi-file-excel"></i>
                                        Export </a>
                                    <router-link :to="ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS()"
                                        class="float-right btn btn btn-primary d-none"><i class="pi pi-plus-circle"></i>
                                        Add more leads</router-link>
                                </div>
                                <div class="card-body">
                                    <RecordOptions v-model="records" @change="pagination" />

                                    <div class="table-responsive pt-3">
                                        <table class="table table-bordered table-sm table-hover ">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th v-for="header in campaign?.list?.csv?.headers" :key="header">{{
                                                        header }}</th>
                                                    <th>Error</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="rows && rows.data && rows.data.length > 0"
                                                    v-for="(cc, index) in rows.data" :key="index">
                                                    <td v-for="(_, colIndex) in campaign?.list?.csv?.columns"
                                                        :key="colIndex">
                                                        <span
                                                            class="small text-muted">{{ cc.subscriber['column_' + (colIndex + 1)] }}</span>
                                                    </td>
                                                    <td>
                                                        {{ cc.error }}
                                                        <small v-if="cc.blacklisted" title="Blacklisted"
                                                            class="text-center text-muted"><i
                                                                class="pi pi-ban text-danger"></i></small>
                                                    </td>
                                                    <td>
                                                        <span v-if="cc.skipped"><i
                                                                class="pi pi-times-circle text-danger"></i></span>
                                                        <span class=" text-warning" v-if="!cc.sent && !cc.skipped"><i
                                                                class="pi pi-circle"></i></span>
                                                        <span class="badge " title="Check reply/bounce"
                                                            v-if="cc.sent && !cc.replied && !cc.bounced"><i
                                                                class="pi pi-check"></i></span>
                                                        <span v-if="cc.sent && cc.replied" class="text-success"><i
                                                                class="pi pi-reply"></i></span>
                                                        <span v-if="cc.sent && cc.bounced"><i
                                                                class="pi pi-wave-pulse"></i></span>
                                                    </td>
                                                </tr>
                                                <tr v-else>
                                                    <td align="center" colspan="100%" class="text-muted italic"><i>No
                                                            record found.</i></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-header border-bottom-0">
                                    <Pagination :list="pagination" :data="rows" />
                                </div>
                            </div>
                            <div class="card mt-4" v-if="menu === 'Sequence'">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 pb-3">
                                            <h4 class="float-left pr-4">Email sequence</h4>
                                            <button v-if="!edit_subject" @click="edit_subject = true;"
                                                class="btn btn-outline-primary float-left"><i class="pi pi-pencil"></i>
                                                Edit</button>
                                            <button v-if="edit_subject" @click="edit_subject = false;"
                                                class="btn btn-light border float-left"><i
                                                    class="pi pi-times-circle"></i> Cancel</button>
                                            <button v-if="edit_subject" @click="saveSubject()"
                                                class="btn ml-2 btn-primary border float-left"><i
                                                    class="pi pi-save"></i> Save</button>
                                        </div>
                                        <div class="col-md-12" v-if="!edit_subject">
                                            <div class="card message-body">
                                                <div class="card-body border-bottom">
                                                    <p class="pt-2 font-weight-bold">{{ campaign.subject }}</p>
                                                </div>
                                                <div class="card-body">
                                                    <p class="" v-html="campaign.template.body"
                                                        v-if="campaign.template.body"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 border mb-5 pb-4" v-else>
                                            <div class="input-group border-bottom p-1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white text-muted border-0">
                                                        Subject
                                                    </span>
                                                </div>
                                                <input v-model="campaign.subject" id="subject"
                                                    class="form-control no-outline border-0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pb-3">
                                            <h4 class="float-left pr-4">Signature</h4>
                                            <button v-if="edit_signature" @click="saveSignature()"
                                                class="btn ml-2 btn-primary border float-left"><i
                                                    class="pi pi-save"></i> Save</button>
                                        </div>
                                        <div class="col-md-6" v-if="!edit_signature">
                                            <div class="card message-body">
                                                <div class="card-body">
                                                    <p v-html="campaign.template.signature" v-if="campaign.template">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pb-3">
                                            <h4 class="float-left pr-4">Attachment</h4>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div v-if="!campaign.attachment">
                                                        <FormInputFile v-model="attachment" :col_md="10"
                                                            label="Select attachment to upload" class="float-left" />
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
                            <div class="card mt-4" v-if="menu === 'Schedule'">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>Name your campaign</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group position-relative">
                                                    <input type="text" v-model="campaign.name"
                                                        placeholder="Type your campaign name here ..."
                                                        class="form-control col-md-4" @input="validateName" />

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4 class="float-left">Email accounts that you're sending with in this
                                                campaign</h4>
                                            <router-link :to="ROUTE_USER_EMAIL_ACCOUNTS()"
                                                class="btn btn-primary float-right"><i class="pi pi-plus-circle"></i>
                                                Connect Email</router-link>
                                        </div>
                                        <div class="col-md-7 " v-for="email in campaign.accounts">
                                            <div
                                                class="border p-3 d-flex align-items-center rounded justify-content-between">
                                                <div class="d-flex">
                                                    <div class="p-3">
                                                        <img class="rounded-circle avatar-sm " width="30"
                                                            v-if="email.profile" :src="email.profile"
                                                            :alt="email.email">
                                                        <img v-else class="rounded-circle avatar-sm "
                                                            src="../../../assets/img/mail.png" alt="" width="30"
                                                            height="30">


                                                    </div>
                                                    <div class=" pt-2">
                                                        <h5 class="p-0 m-0">{{email_accounts.find(em => em.id ===
                                                            email)?.name || '' }}</h5>
                                                        <p class="p-0 m-0 text-muted">{{email_accounts.find(em => em.id
                                                            === email)?.email || '' }}</p>
                                                    </div>
                                                </div>
                                                <div class="p-2">
                                                    <img class="rounded-circle avatar-sm float-left"
                                                        src="../../../assets/img/checkbox.png" alt="" width="40"
                                                        height="40">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>Control who you email</h4>
                                            <table class="table table-borderless ">
                                                <tbody>
                                                    <tr class="text-muted small"
                                                        v-for="(option, index) in controlOptions" :key="index">
                                                        <td>{{ option.label }}</td>
                                                        <td class="text-right">
                                                            <span v-if="option.note" class="mr-2 text-primary">{{
                                                                option.note }}</span>
                                                            <div class="switch">
                                                                <input
                                                                    :disabled="option.note && option.note === 'Mandatory'"
                                                                    type="checkbox" :id="'switch' + index"
                                                                    v-model="campaign.controls" :value="option.value" />
                                                                <label :for="'switch' + index">.</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="100%" class="text-right">
                                                            <button class="btn btn-outline-primary"
                                                                @click="updateControls"><i class="pi pi-save"></i>
                                                                Save</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>When do you want your campaign to send?</h4>
                                            <div class="row">
                                                <div class="col text-center" v-for="day in week_days" :key="day"
                                                    @click="manageDay(day)">
                                                    <p class="week-days text-capitalize border rounded"
                                                        :class="{ 'selected-day': campaign.days.includes(day) }">
                                                        {{ day }}
                                                        <span v-if="campaign.days.includes(day)" class="check-mark">
                                                            <i class="pi pi-check-circle"></i>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>In which time zone?</h4>
                                            <FormControlSelect2 :col_md="6" :options="timezones"
                                                v-model="campaign.timezone" label="" @select="updateTimezone" />
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>At what time?</h4>
                                        </div>
                                        <div v-for="option in scheduleOptions" :key="option.key" class="col-md-5 mb-4">
                                            <div class="border p-3 rounded"
                                                :class="{ 'border-primary': campaign.day === option.key }"
                                                @click="updateDayTime(option)">
                                                <h5>{{ option.icon }} {{ option.label }}</h5>
                                                <div class="p-3">
                                                    <div class="row">
                                                        <div class="col border p-3 text-center pt-4">
                                                            <h6>Start</h6>
                                                            <p v-if="option.key !== 'custom'">{{ option.start }}</p>
                                                            <input v-else type="time" v-model="campaign.start"
                                                                class="form-control" />
                                                        </div>
                                                        <div class="col border p-3 text-center pt-4">
                                                            <h6>End</h6>
                                                            <p v-if="option.key !== 'custom'">{{ option.end }}</p>
                                                            <input v-else type="time" v-model="campaign.end"
                                                                class="form-control" />
                                                        </div>
                                                        <div class="col border p-3 text-center pt-4">
                                                            <h6>Every</h6>
                                                            <p>5 to 15 min</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" mb-4">
                                        <div class="col-md-12">
                                            <h5 class="">Control sending volume</h5>
                                            Set a minimum / maximum range. For example: 5 min / 15 max. All email
                                            accounts that are connected to this campaign will send a daily randomized
                                            volume that is between the range you set. This updates on an email account
                                            level (not campaign level). All campaigns that use the same email accounts
                                            will be updated.
                                        </div>
                                        <div v-for="account in email_accounts" :key="account.id" class="col-md-6 p-0">
                                            <div class="p-3" v-if="campaign.accounts.includes(account.id)">
                                                <h6>{{ account.name }} <small class="text-muted">({{ account.email
                                                        }})</small></h6>
                                                <div class="form-group">
                                                    <label>Per Minute</label>
                                                    <VueSlider v-model="account.per_minute" :min="0" :max="40"
                                                        :tooltip="'always'"
                                                        @change="(val) => updateSliderRange(account, 'm', val)" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Volume</label>
                                                    <VueSlider v-model="account.volume" :min="0" :max="40"
                                                        @change="(val) => updateSliderRange(account, 'v', val)" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>Optimize your deliverability</h4>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr class="text-muted small"
                                                        v-for="(option, index) in deliverabilityOptions" :key="index">
                                                        <td>{{ option.label }}</td>
                                                        <td class="text-right">
                                                            <span class="mr-2 text-primary">{{ option.note }}</span>
                                                            <div class="switch">
                                                                <input type="checkbox" :id="'sdfdsfsd' + index"
                                                                    :value="option.value" checked disabled />
                                                                <label :for="'sdfdsfsd' + index">.</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card mt-4" v-if="menu === 'Analytics'">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-success p-2 "><i class="pi pi-user"></i>
                                                </span> <span class="font-size-16"> Leads that have been
                                                    contacted</span></h5>
                                            <h3>{{ campaign.total_sent_count ?? 0 }}</h3>
                                        </div>
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-warning p-2 "><i class="pi pi-reply"></i>
                                                </span> <span class="font-size-16"> Leads that have replied </span></h5>
                                            <h3>{{ analytic.replied ?? 0 }}</h3>
                                            <h6>{{ analytic.reply_rate }}% replied rate</h6>

                                        </div>
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-success p-2 "><i
                                                        class="pi pi-face-smile"></i> </span> <span
                                                    class="font-size-16"> Leads that have replied positively </span>
                                            </h5>
                                            <h3>0</h3>
                                            <h6>0% of replies are positive</h6>
                                        </div>
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-danger p-2 "><i
                                                        class="pi pi-exclamation-triangle"></i> </span> <span
                                                    class="font-size-16"> Leads that have bounced </span></h5>
                                            <h3>{{ analytic.bounced ?? 0 }}</h3>
                                            <h6>{{ analytic.bounced_rate }}% bounce rate</h6>
                                        </div>
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-info p-2 "><i class="pi pi-send"></i>
                                                </span> <span class="font-size-16"> Total Emails </span></h5>
                                            <h3>{{ campaign.total_count ?? 0 }}</h3>
                                        </div>
                                        <div class="p-4 col-md-4">
                                            <h5> <span class="badge badge-soft-warning p-2 "><i
                                                        class="pi pi-exclamation-circle"></i> </span> <span
                                                    class="font-size-16"> Campaign progress </span></h5>
                                            <h3>{{ analytic.progress ?? 0 }}%</h3>
                                        </div>
                                        <div class="col-md-12 mt-5">
                                            <p class="text-muted">Read our article to learn why we don’t track email
                                                open rates (tracking this hurts your deliverability and lands your
                                                emails in spam).</p>
                                        </div>
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


import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import {
    FORM_DATA,
    notify,
    NOTIFY_TYPE_ERROR,
    ROUTE_USER_CAMPAIGNS,
    ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS,
    ROUTE_USER_CAMPAIGNS_SHOW,
    ROUTE_USER_CAMPAIGNS_UPDATE,
    ROUTE_USER_EMAIL_ACCOUNTS,
} from "../../../constants.js";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Loader from "../../components/Loader.vue";
import QuillEditorComponent from "../../components/QuillEditorComponent.vue";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import moment from "moment-timezone";



import VueSlider from '@vueform/slider';
import '@vueform/slider/themes/default.css';
import Pagination from "../../layout/Paignation.vue";
import FormInputFile from "../../components/FormInputFile.vue";
import RecordOptions from "../../components/RecordOptions.vue";

export default {
    name: 'CampaignShow',
    components: {
        RecordOptions,
        FormInputFile,
        Pagination, FormControlSelect2, QuillEditorComponent, Loader, Breadcrumb, Sidebar, Header, VueSlider
    },
    data() {
        return {
            records: 10,
            attachment: '',
            headers: [],
            filter: 'all',
            filters: {
                'all': { label: 'All Leads' },
                'contacted': { label: 'Contacted' },
                'not_contacted': { label: 'Not Contacted' },
                'replied': { label: 'Response Received' },
                'not_replied': { label: 'No Response Received' },
                'skipped': { label: 'Skipped' },
                'bounced': { label: 'Bounced' }
            },
            rows: [],
            email_accounts: [],
            timezones: this.getTimezones(),
            controlOptions: [
                { value: "skip_existing", label: "Skip leads that exist in another campaign. This doesn't apply to campaigns that have been deleted." },
                { value: "skip_invalid", label: "Skip leads that have “invalid” or “catch-all” email addresses. Activating this will ensure that you only send emails to leads with “valid” email addresses.", note: "Recommended" },
                { value: "skip_personal", label: "Skip personal email addresses (like @gmail.com). Contacting personal email addresses can negatively impact your deliverability.", note: "Recommended" },
                { value: "skip_replied", label: "Skip leads that have already replied. This does not apply to leads that have replied in other sending tools.", note: "Mandatory" },
                { value: "skip_duplicates", label: "Skip duplicate leads. If your lead list contains the same lead more than once, then we'll only include the lead once in this campaign.", note: "Mandatory" }
            ],

            deliverabilityOptions: [
                { value: "plain_text_emails", label: "Send emails as plain text (not HTML) to improve deliverability.", note: "Mandatory" },
                { value: "gradual_sending", label: "Gradually increase sending volume on newly connected email accounts for the first 8-weeks.", note: "Mandatory" },
                { value: "auto_match_inboxes", label: "Auto-match your leads’ inboxes to your sender email accounts. Gmail to Gmail. Outlook to Outlook.", note: "Mandatory" },
                { value: "avoid_bounced_emails", label: "Avoid sending emails to email accounts that have previously bounced.", note: "Mandatory" }
            ],

            editingFollowup: false,
            selectedFollowup: null,
            selectedIndex: null,
            processing: false,
            campaign: {
                id: this.$route.params.id,
            },
            menus: ['Leads', 'Sequence', 'Schedule', 'Analytics'],
            menu: 'Leads',
            edit_subject: false,
            edit_signature: false,
            headerOptions: [],
            week_days: ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',],
            scheduleOptions: [
                { key: "full", icon: "⏰", label: "Full Day", start: "08:00 AM", end: "06:00 PM" },
                { key: "morning", icon: "☀️", label: "Morning", start: "08:00 AM", end: "11:45 AM" },
                { key: "afternoon", icon: "🌄", label: "Afternoon", start: "01:00 PM", end: "04:45 PM" },
                { key: "custom", icon: "📝", label: "Custom" }
            ],
            follow_up_days_options: Array.from({ length: 27 }, (_, i) => ({
                key: i + 4,
                value: `${i + 4} days`
            })),
            analytic: {
                all: 0,
                sent: 0,
                progress: 0,
                replied: 0,
            },
        }
    },
    methods: {
        ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS() {
            return ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS.replace(':id', this.$route.params.id)
        },
        ROUTE_USER_EMAIL_ACCOUNTS() {
            return ROUTE_USER_EMAIL_ACCOUNTS
        },
        getTimezones() {
            return moment.tz.names().map((tz) => {
                const offset = moment.tz(tz).utcOffset();
                const formattedOffset = `UTC${offset >= 0 ? "+" : ""}${(offset / 60).toFixed(0)}`;
                return { key: tz, value: `${formattedOffset} - ${tz}` };
            });
        },
        editFollowup(followup, index) {
            this.selectedFollowup = { ...followup };
            this.selectedIndex = index;
            this.editingFollowup = true;
        },
        cancelFollowupEdit() {
            this.editingFollowup = false;
            this.selectedFollowup = null;
        },

        ROUTE_USER_CAMPAIGNS_UPDATE(id, step) {
            return ROUTE_USER_CAMPAIGNS_UPDATE
        },
        ROUTE_USER_CAMPAIGNS_SHOW() {
            return ROUTE_USER_CAMPAIGNS_SHOW
        },
        ROUTE_USER_CAMPAIGNS() {
            return ROUTE_USER_CAMPAIGNS
        },
        async show() {
            this.processing = true;
            await this.axios.post('user/campaigns/show', { id: this.$route.params.id }).then((response) => {
                this.campaign = response.data.data;
                if (this.campaign && this.campaign.header) {
                    this.headerOptions = Object.values(this.campaign.header.headers).map((header) => ({
                        key: header,
                        value: header
                    }));
                }
                this.analytics();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },




        manageDay(day) {
            const index = this.campaign.days.indexOf(day);
            if (index === -1) {
                this.campaign.days.push(day);
            } else {
                this.campaign.days.splice(index, 1);
            }
            this.updateWeekDays();
        },
        manageMenu(m) {
            this.menu = m;
        },
        saveSubject() {
            if (this.campaign.subject) {
                this.processing = true;
                this.axios.post('user/campaigns/subject', this.campaign).then(({ data }) => {
                    this.show();
                    this.edit_subject=false;
                }).finally((data) => {
                    this.processing = false;
                });
            }
        },
        updateSliderRange(account, type, value) {
            if (type === 'm') {
                account.per_minute = value;
            } else {
                account.volume = value;
            }
            this.axios.post('user/email-accounts/range', account);
        },
        saveSignature() {
            this.processing = true;
            this.axios.post('user/campaigns/signature', this.campaign).then(({ data }) => {
                this.show();
                this.edit_signature = false;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        deleteFollowUp(id) {
            this.processing = true;
            this.axios.post('user/campaigns/delete-followup', { id: id }).then(({ data }) => {
                this.show();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        saveFollowup(followup) {
            followup.campaign_id = this.$route.params.id;
            this.processing = true;
            this.axios.post('user/campaigns/add-followup', followup).then(({ data }) => {
                this.show();
                this.editingFollowup = false;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        async fetchEmailAccounts() {
            await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
                this.email_accounts = data.data;
            });
        },
        updateControls() {
            this.axios.post('user/campaigns/controls', { id: this.campaign.id, controls: this.campaign.controls, name: this.campaign.name }).then((response) => {
            }).catch((error) => {
                this.$flattenErrors(error);
            });
        },

        updateWeekDays() {
            this.processing = true;
            this.axios.post('user/campaigns/days', this.campaign).then(({ data }) => {
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        updateTimezone() {
            this.processing = true;
            this.axios.post('user/campaigns/timezone', this.campaign).then(({ data }) => {
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        updateDayTime(option) {
            this.campaign.day = option.key;
            this.campaign.start = option.start;
            this.campaign.end = option.end;

            this.processing = true;
            this.axios.post('user/campaigns/time', this.campaign).then(({ data }) => {
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },

        sendEmail(data) {
            this.processing = true;
            this.axios.post('user/email/send', data).then(({ data }) => {
                this.pagination();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        checkReply(data) {
            this.processing = true;
            this.axios.post('user/email/check-reply', data).then(({ data }) => {
                this.pagination();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },

        analytics() {
            this.processing = true;
            this.axios.post('user/campaigns/analytics', { id: this.campaign.id }).then(({ data }) => {
                this.analytic = data;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        async checkReplyManually() {
            console.log('checkReplyManually');
            this.processing = true;
            this.axios.post('user/campaigns/check-reply', { id: this.campaign.id }).then(({ data }) => {
                this.analytic = data;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        async validateName() {
            this.processing = true;
            try {
                await this.axios.post('user/campaigns/name', this.campaign);
            }
            catch (error) {
                this.$flattenErrors(error);
            }
            finally {
                this.processing = false;
            }
        },
        async rowsFilter(option) {
            this.filter = option;
            await this.pagination();
        },
        uploadFile() {
            const _this = this;
            _this.processing = true;
            const data = {
                id: this.campaign.id,
                attachment: this.attachment,
            }
            this.axios.post('user/campaigns/attachment', FORM_DATA(data)).then(({ data }) => {
                notify(data.message);
                _this.show();
            }).catch((error) => {
                _this.$flattenErrors(error);
            }).finally((data) => {
                _this.processing = false;
            });
        },
        deleteAttachment() {
            const _this = this;
            _this.processing = true;
            this.axios.post('user/campaigns/delete-attachment', this.campaign).then(({ data }) => {
                _this.show();
            }).catch((error) => {
                _this.$flattenErrors(error);
            }).finally((data) => {
                _this.processing = false;
            });
        },





        pagination(page = 1) {
            this.axios.post('user/campaigns/pagination', { id: this.$route.params.id, records: this.records, page: page, filter: this.filter, }).then(({ data }) => {
                this.rows = data.data;
            }).catch((error) => {
                this.$flattenErrors(error);
            });
        },
    },
    created() {
        this.analytics();
        this.show();
        this.pagination();
        this.fetchEmailAccounts();
        this.checkReplyManually();
    }
}
</script>
