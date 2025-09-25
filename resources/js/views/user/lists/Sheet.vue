<template>
    <Loader :loading="processing" />

    <div v-if="!google_sheets || google_sheets.length===0" class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10 pt-3 pl-4">
                            <div class="">
                                <img class="float-left " src="../../../assets/img/google.png" alt="" width="40" height="40" >
                                <div class="float-left text-start ml-2">
                                    <h5 class="m-0">Connect your Google Spreadsheet</h5>
                                    <p class="text-muted small ">Gmail / Google Workspace</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-12 text-center">
                            <h6 class="small m-0">Allow InfinityLead to access your Google Workspace.</h6>
                            <p class="small text-success">
                                You only need to do this once per domain.
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <ol class="small text-muted">
                                <li>Watch our <a class="" target="_blank" href="https://www.youtube.com/watch?v=6Lxt8EgyTUI">video tutorial</a>.</li>
                                <li>Log into your <a class="" target="_blank" href="https://admin.google.com/">Google Workspace Admin Panel</a>.</li>
                                <li>On the left side, click:
                                    <br>
                                    <b>Show more --> Security --> Access and data control --> API controls --> Manage Third-Party App Access.</b>
                                    You can also <a class="font-weight-bold" target="_blank" href="https://admin.google.com/u/0/ac/owl/list?tab=configuredApps">click here</a> to be taken directly to this page.</li>
                                <li>Click “Configure new app”.</li>
                                <li>
                                    Use the following Client ID to search for InfinityLead:
                                    <div class="bg-light border p-2 rounded cursor-pointer" @click="copyToClipboard(excelClientId)">
                                        {{excelClientId}}
                                    </div>
                                </li>
                                <li>Select and approve InfinityLead to access your Google Workspace. Make sure you select “Trusted” in the “Access to Google Data” section. Remember to click “Finish” at the end.</li>
                                <li>
                                    Click the “Connect button” below:
                                </li>
                            </ol>
                            <center>
                                <a @click="connectGoogleSheet" class="btn btn-outline-primary w-75">Connect</a>
                            </center>
                            <center>
                                <button @click="getListOfSpreadsheets" class="btn btn-link"><i class="pi pi-refresh"></i> Already connected? Refresh here.!</button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="" v-else>
        <div class="row">
            <FormControlSelect2 :col_md="4"  :options="google_sheets" v-model="csv.spreadsheet_id" label="Your Google Spreadsheets List" @select="onGoogleSheetSelection"/>
            <FormControlSelect2 :col_md="4"  v-model="csv.sheet_id" :options="sheets" label="Select Sheet to Download from Google SpreadSheets" @select="onSheetSelection"/>
            <FormControlSelect2 :col_md="4"  v-model="csv.mapping" :options="csv.mappings" label="Map Email Column " @select="updateEmailMapping"/>
            <div class="col-md-12">
                <button class="btn btn-success float-right" @click="onUpload" :disabled="processing"> <i class="pi pi-book"></i> Read  <i class="pi pi-upload"></i> Upload Spreadsheet</button>
            </div>
        </div>
    </div>
</template>

<script>
import Loader from "../../components/Loader.vue";
import { FORM_DATA, notify, NOTIFY_TYPE_ERROR, number_format } from "../../../constants";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
export default {
    name: 'CSV',
    props: {
        cancelType: {
            type: Function,
            required: true,
        }
    },
    data() {
        return {
            processing: false,
            csv: {
                id: this.$route.params.id,
                csv: '',
                name: '',
                spreadsheet_id: '',
                sheet_id: '',
                mapping: '',
                mappings: [],
            },
            list: {
                id: this.$route.params.id,
            },
            google_sheets: [],
            sheets: [],
            master_google_sheets: [],
        }
    },

    components: {
        FormControlSelect2,
        Loader
    },
    methods: {
        number_format,

        storeCsv() {
            if (this.processing) {
                return;
            }
            this.processing = true;
            const data = FORM_DATA(this.csv);
            this.axios.post('user/lists/csv', data).then(({ data }) => {
                notify(data.data.message);
                this.cancelCsv();
                this.cancelType('cancel');
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        cancelCsv() {
            this.csv = {
                id: this.$route.params.id,
                csv: '',
                name: '',
                mapping: '',
                mappings: [],
            }
        },
        removeCsv() {
            this.processing = true;
            this.axios.post('user/lists/remove-csv', { id: this.$route.params.id }).then(({ data }) => {
                this.edit();
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        async show() {
            this.processing = true;
            await this.axios.post('user/lists/show', { id: this.$route.params.id }).then((response) => {
                this.list = response.data.data;
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        getHeaders() {
            this.processing=true;
            const spreadSheet = this.master_google_sheets.find(SS => SS.id === this.csv.spreadsheet_id);
            const data = {
                spreadsheet: spreadSheet,
                sheet: this.csv.sheet_id,
                id: this.$route.params.id
            };
            this.axios.post('user/lists/google-headers', data).then(({ data }) => {
                this.csv.mappings = Object.values(data.data).map((header, index) => ({
                    key: 'column_' + (index + 1),
                    value: data.data[index]
                }));
            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((ms) => {
                this.processing = false;
            });
        },
        connectGoogleSheet() {
            const user = JSON.parse(localStorage.getItem('user'));
            window.location.href = '/google/sheet-access?list_id=' + this.$route.params.id + '&user_id=' + user.id;
        },
        async getListOfSpreadsheets() {
            this.processing = true;
            await this.axios.post('user/google-sheets/fetch', { id: this.$route.params.id }).then(({ data }) => {

            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        async authSheets() {
            await this.axios.post('user/google-sheets/auth', { id: this.$route.params.id }).then(({ data }) => {
                this.google_sheets = [];
                this.master_google_sheets = data.data.google_sheets;
                this.master_google_sheets.forEach((sheet) => {
                    this.google_sheets.push({
                        key: sheet.id,
                        value: sheet.name,
                    });
                });

            }).catch((error) => {
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
        onGoogleSheetSelection(selectedSpreadSheet) {
            this.sheets = [];
            const record = this.master_google_sheets.find(SS => SS.id === selectedSpreadSheet);
            record.sheets.forEach((sheet) => {
                this.sheets.push({
                    key: sheet.id,
                    value: sheet.name
                });
            });
        },
        onSheetSelection() {
            this.getHeaders();
        },
        onUpload() {
            this.processing = true;
            const spreadSheet = this.master_google_sheets.find(SS => SS.id === this.csv.spreadsheet_id);
            const data = {
                spreadsheet: spreadSheet,
                sheet: this.csv.sheet_id,
                id: this.$route.params.id,
                mapping:this.csv.mapping,
                mappings:this.csv.mappings,
            };
            this.axios.post('user/lists/read-sheet', data).then(({ data }) => {
                this.cancelType();
            }).catch((error) => {
                this.google_sheets = '';
                this.master_google_sheets = '';
                this.$flattenErrors(error);
            }).finally((data) => {
                this.processing = false;
            });
        },
    },
    created() {
        this.show();
        this.authSheets();
    }
}
</script>

<style>
.w2-step-4 {
    border-radius: 20px !important;
}

</style>
