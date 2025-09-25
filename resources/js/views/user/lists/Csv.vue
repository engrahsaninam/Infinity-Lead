<template>
    <Loader :loading="processing" />
    <div class="row justify-content-center" v-if="!csv.csv">
        <div class="col-md-4">
            <h4>Import subscribers from a .csv file</h4>
            <div class="drag-drop p-5 text-center">
                <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
                <label for="csv-upload" class="btn btn-primary mb-4">
                    Choose .csv file
                    <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
                </label>
                <p class="small">Import a .csv file (10,000 leads max). <a href="/leads-template.csv"
                        download="leads-template.csv">Start with our template</a></p>
            </div>
        </div>
    </div>
    <div class="row justify-content-center" v-else>
        <div class="col-md-4 mt-4">
            <div class="col-md-12 mt-2" v-if="progress > 0 && progress < 100">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                        :style="{ width: progress + '%' }" :aria-valuenow="progress" aria-valuemin="0"
                        aria-valuemax="100">
                        {{ progress }}%
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <h6 v-if="csv.name">Do you want to upload <span class="text-primary">{{ csv.name }}</span></h6>
            </div>
            <FormControlSelect2 v-model="csv.mapping" :col_md="12" :options="csv.mappings" label="Map Email Column" />
            <div class="col-md-12">
                <button class="btn btn-light border text-muted float-left" @click="cancelCsv">Cancel</button>
                <button class="btn btn-primary float-right" @click="storeCsv" :disabled="processing"><i
                        class="pi pi-upload"></i> Upload </button>
            </div>
        </div>
    </div>
</template>
<script>
import { FORM_DATA, notify, NOTIFY_TYPE_ERROR, number_format } from "../../../constants";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import Loader from "../../components/Loader.vue";
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
                mapping: '',
                mappings: [],
            },
            list: {
                id: this.$route.params.id,
            },
            progress:0,
        }
    },
    components: {
        FormControlSelect2,
        Loader,
    },
    methods: {
        number_format,
        onCsvChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.csv.csv = file;
                this.csv.name = file.name;
                this.getHeaders();
            } else {
                this.csv.csv = '';
                this.csv.name = '';
                notify('Something went wrong! Please try again later!', { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
            }
        },
        storeCsv() {
            if (this.processing) {
                return;
            }
            this.processing = true;
            const data = FORM_DATA(this.csv);
            this.axios.post('user/lists/csv', data)
                .then(({ response }) => {
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
            this.processing = true;
            this.axios.post('user/lists/csv-headers', FORM_DATA(this.csv)).then(({ data }) => {
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

    },
    created() {
        this.show();

    }
}
</script>
<style>
.w2-step-4 {
    border-radius: 20px !important;
}


</style>
