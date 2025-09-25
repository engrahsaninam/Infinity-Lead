<template>
  <div id="layout-wrapper">
    <Header />
    <Sidebar />
    <Loader :loading="isLoading"/>
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                  <h4>Lead Finder</h4>
                  <p>
                    Extract leads from
                    <a href="https://www.linkedin.com/sales" target="_blank">
                      LinkedIn Sales Navigator
                    </a>
                    with their email addresses as a .csv file.
                  </p>
                </div>
                <Breadcrumb :items="[ROUTE_USER_LEAD_FINDER()]" />
              </div>
              <LineLoader v-if="isLineLoading" />
            </div>
          </div>
          <div class="row mb-4">
            <!-- Step 1 -->
            <div class="col-md-4 d-none">
              <div class="card process border-success">
                <div class="card-body">
                  <div class="col-md-12 pt-3">
                    <h5>1. Watch tutorial video
                      <span class="mdi mdi-check-circle text-success"></span>
                    </h5>
                    <p class="text-muted">Learn how to use our Lead Finder effectively.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2: Install extension -->
            <div class="col-md-4">
              <div class="card process h-100" :class="{'border-success': isExtensionInstalled}">
                <div class="card-body d-flex justify-content-center align-items-center">
                  <div>
                    <h5>
                      1. Install Chrome extension
                      <span v-if="isExtensionInstalled" class="mdi mdi-check-circle text-success"></span>
                    </h5>
                    <p class="text-muted">Add our extension to your browser.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3: Connect API key -->
            <div class="col-md-4">
              <div class="card process h-100" :class="{'border-success': verificationKey}">
                <div class="card-body d-flex justify-content-center align-items-center">
                  <div>
                    <h5>
                      2. Connect API key 
                      <span v-if="verificationKey" class="mdi mdi-check-circle text-success"></span>
                    </h5>
                    <p class="text-muted">Connect your email verification service.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 4: Extract & Upload -->
            <div class="col-md-4">
              <div class="card process h-100 border-success">
                <div class="card-body">
                  <div class="col-md-12 pt-3 pl-1 pr-1">
                    <h5>3. Extract & Upload Leads from Sales Navigator
                      <span class="mdi mdi-check-circle text-success"></span>
                    </h5>

                    <!-- Upload area -->
                    <div class="col-md-12 py-4 text-center" v-if="!uploadingFile">
                      <!-- If no files selected show dashed select button -->
                      <button
                        class="btn border text-muted"
                        style="border-style:dashed!important"
                        @click="triggerFileInput"
                        v-if="selectedFiles.length === 0"
                      >
                        <i class="pi pi-cloud-upload"></i>
                        Select files downloaded from InfinityLead export extension!
                      </button>

                      <!-- If files selected show list and upload controls -->
                      <div class="form-group" v-else>
                        <div class="mb-2 text-left">
                          <!-- Show how many files are selected -->
                          <small class="text-muted">Selected files ({{ selectedFiles.length }})</small>
                        </div>

                        <!-- List selected files with remove option -->
                        <div class="selected-files-list mb-3">
                          <ul class="list-unstyled m-0 p-0">
                            <li
                              class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded"
                              v-for="(file, idx) in selectedFiles"
                              :key="file.name + idx"
                            >
                              <div class="d-flex align-items-center">
                                <i class="pi pi-file mr-2" style="font-size: 1.1rem;"></i>
                                <div>
                                  <input
                                      type="text"
                                      v-model="file.exportName"
                                      placeholder="Please type linked export name"
                                      class="form-control"
                                  />
                                  <!-- <div class="small text-muted">{{ humanFileSize(file.size) }}</div> -->
                                </div>
                              </div>

                              <div class="d-flex align-items-center">
                                <!-- Remove a single file -->
                                <button class="btn btn-sm btn-outline-danger mr-2" @click="removeFile(idx)">
                                  <i class="pi pi-times"></i>
                                </button>
                              </div>
                            </li>
                          </ul>
                        </div>

                        <!-- Actions: Clear all / Upload -->
                        <div style="display: flex; justify-content: flex-end; gap: 5px;">
                          <button class="btn btn-sm btn-danger" title="Remove all" @click="clearAllFiles">
                            <i class="pi pi-trash"></i>
                          </button>

                            <button class="btn btn-sm btn-secondary" title="Add more" @click="triggerFileInput">
                              <i class="pi pi-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" title="Upload" @click="handleFileUpload" :disabled="selectedFiles.length === 0">
                              <i class="pi pi-upload"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" title="Combined upload" v-if="selectedFiles.length >= 2" @click="showCombinedModal">
                              Combined Upload
                            </button>

                        </div>
                      </div>
                    </div>

                    <!-- Upload spinner state -->
                    <div v-else class="col-md-12 py-4 text-center">
                      <h1 class="pi pi-spinner pi-spin text-success"></h1>
                      <div class="mt-2">Uploading files... please wait</div>
                    </div>

                    <!-- hidden multi-file input -->
                    <input
                      type="file"
                      ref="fileInput"
                      style="display: none"
                      @change="handleFileChange"
                      multiple
                      accept=".csv,.txt,.xlsx"
                    />

                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <div v-if="shimmer">
          <Shimmer :column="12" :height="500" :onLoading="shimmer" />
        </div>
        <div class="container-fluid" v-else>
          <div class="row">
            <div class="col-md-12">
              <h5>Sales Navigator extraction history</h5>
              <p>Download your previous extractions.</p>
            </div>
          </div>

          <div class="row p-2" v-if="export_leads && export_leads.length">
            <div class="col-md-3"><h5 class="m-0">Extraction Name</h5></div>
            <div class="col-md-3"><h5 class="m-0">Date</h5></div>
            <div class="col-md-3"><h5 class="m-0">Records</h5></div>
            <div class="col-md-3"><h5 class="m-0">Action</h5></div>
          </div>

          <div
            class="row single-lead-extract p-2"
            v-for="(lead, index) in export_leads"
            :key="lead.id || index"
            v-if="export_leads.length>0"
          >
            <div class="col-md-3">
              <h6 class="pt-2" style="cursor: pointer" @click="lead.expanded ? closeCollapse(index) : toggleCollapse(index)">
                <span class="text-capitalize">
                  <small class="pi" :class="lead.expanded ? 'pi-arrow-down' : 'pi-arrow-right'"></small>
                  {{ lead.name }}
                </span>
              </h6>
            </div>
            <div class="col-md-3">
              <h6 class="pt-2">{{ lead.created_on.m }}/{{ lead.created_on.d }}/{{ lead.created_on.Y }}</h6>
            </div>
            <div class="col-md-3">
              <h6 class="pt-2">{{ lead.count }}</h6>
            </div>
            <div class="col-md-3">
              <button class="btn btn-outline-danger btn-sm" @click="showPop(lead.id)">
                <i class="pi pi-trash"></i>
              </button>
            </div>

            <div class="col-md-12" v-if="lead.expanded">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <button v-if="lead.company_sync===0" class="btn btn-sm btn-outline-primary mr-1" @click="syncCompanies(lead.id,index)"><i class="pi pi-sync"></i> Sync Company</button>
                      <button disabled class="btn btn-sm btn-outline-success mr-1" v-else>Companies Synced <i class="pi pi-check-circle"></i></button>
                      <button v-if="lead.company_sync===1 && lead.generate_email_sync === 0" class="btn ml-1 btn-sm btn-outline-primary mr-1" @click="generateEmails(lead.id,index)"><i class="mdi mdi-cog"></i> Generate Emails</button>
                      <button disabled class="btn btn-sm btn-outline-success mr-1" v-else>Emails Generated <i class="pi pi-check-circle"></i></button>
                      <button v-if="lead.generate_email_sync===1 && lead.verify_email_sync === 0" class="btn btn-sm btn-outline-primary mr-1" @click="verifyEmails(lead.id,index)"><i class="pi pi-refresh"></i> Verify Emails</button>
                      <button disabled class="btn btn-sm btn-outline-success mr-1" v-else>Emails Verified <i class="pi pi-check-circle"></i></button>

                      <div class="dropdown-css float-right" tabindex="0">
                        <div class="d-flex align-items-center p-0 rounded-0">
                          <span class="badge">Download <small><i class="pi pi-ellipsis-v"></i></small></span>
                        </div>
                        <div class="dropdown-css-content mt-2 float-right">
                          <a class="p-1 px-2 border-bottom font-size-12" target="_blank" :href="'/export/linked-in/records/'+lead.id+'/valid'" ><i class="pi pi-check-square text-success"></i> Verified Only</a>
                          <a class="p-1 px-2 border-bottom font-size-12" target="_blank" :href="'/export/linked-in/records/'+lead.id+'/invalid'" ><i class="pi pi-times text-danger"></i> Invalid Only</a>
                          <a class="p-1 px-2 border-bottom font-size-12" target="_blank" :href="'/export/linked-in/records/'+lead.id+'/skipped'" ><i class="pi pi-exclamation-triangle text-warning"></i> Skipped Only</a>
                          <a class="p-1 px-2 border-bottom font-size-12" target="_blank" :href="'/export/linked-in/records/'+lead.id+'/all'" ><i class="pi pi-list text-purple"></i>  All</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-3">
                    <div class="col-md-3 mb-2" v-for="item in analytics" :key="item.key">
                      <div class="card analytics-card  rounded shadow-sm p-3">
                        <div class="card-body text-center mt-3">
                          <h4 class="fw-bold ">{{ item.value }}</h4>
                          <p class="text-muted ">{{ item.label }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive NTRC-table mt-2">
                    <table class="table table-bordered table-sm table-hover">
                      <thead >
                        <tr>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Emails</th>
                          <th>Designation</th>
                          <th>Company</th>
                          <th>Linkedin</th>
                          <th>Region</th>
                          <th>Profile</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="lead.records && lead.records.data && lead.records.data.length > 0" v-for="(record,index) in lead.records.data" :key="index">
                          <td>{{ record.first_name }}</td>
                          <td>{{ record.last_name }}</td>
                          <td>
                            <p class="p-0 m-0" v-for="(email) in record.emails" :key="email.email">
                              <span class="badge" :class="{'badge-soft-primary':email.status==='valid','badge-soft-danger':(email.status==='invalid' || !email.status || email.status ==='guessed')}">
                                {{email.email}}
                                <span v-if="email.status==='valid'"><i class="pi pi-check-circle"></i></span>
                                <span v-else><i class="pi pi-times-circle"></i></span>
                              </span>
                            </p>
                          </td>
                          <td>{{ record.title }}</td>
                          <td>{{ record.company }}
                            <a :href="formatWebsite(record.website)" v-if="record.website" target="_blank" rel="noopener noreferrer">
                              <i class="pi pi-external-link"></i>
                            </a>
                          </td>
                          <td>
                            <a :href="record.url" target="_blank">{{ record.name }}</a>
                          </td>
                          <td>{{ record.region }}</td>
                          <td>
                            <img :src="record.profile " alt="" width="25" height="25" class="img-fluid rounded-pill">
                          </td>
                        </tr>
                        <tr v-else>
                          <td align="center" colspan="100%"><i>No record found.</i></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="card-footer">
                  <RecordOptions v-model="table.records" @change="list" class="col-md-2"/>
                  <Pagination :data="lead.records" :list="list"/>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12 border pt-5 pb-4 mt-4" v-else>
            <p class="text-center text-muted"><i>No export found</i></p>
          </div>
        </div>
      </div>
    </div>

    <Confirm
      :show="showPopup"
      heading="Warning"
      message="Are you sure you want to delete this export?"
      @confirm="deleteLead"
      @cancel="showPopup = false"
    />

    <!-- Combined Upload Modal -->
    <div class="modal fade" :class="{show: showCombinedUploadModal}" tabindex="-1" role="dialog"
         :style="{display: showCombinedUploadModal ? 'block' : 'none'}">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Combine Files</h5>
            <button type="button" class="close" @click="showCombinedUploadModal = false">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Would you like to combine all the selected files?</p>
            <div class="form-group">
              <label for="combinedFileName">Combined File Name</label>
              <input type="text" class="form-control" id="combinedFileName" v-model="combinedFileName" placeholder="Enter combined file name">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showCombinedUploadModal = false">Cancel</button>
            <!-- <button type="button" class="btn btn-primary" @click="handleCombinedUpload">Yes, Combine</button> -->
            <button
              type="button"
              class="btn btn-primary"
              :disabled="uploadingFile"
              @click="handleCombinedUpload"
            >
              <span v-if="uploadingFile"><i class="pi pi-spin pi-spinner"></i> Uploading...</span>
              <span v-else>Yes, Combine</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show" v-if="showCombinedUploadModal"></div>

  </div>
</template>

<script>
import { VueDraggableNext } from "vue-draggable-next";
import Header from "../../layout/Header.vue";
import Sidebar from "../../layout/Sidebar.vue";
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import LineLoader from "../../components/LineLoader.vue";
import FormInputTooltip from "../../components/FormInputTooltip.vue";
import {
  FORM_DATA, notify,
  ROUTE_USER_LEAD_FINDER,
} from "../../../constants.js";
import RecordOptions from "../../components/RecordOptions.vue";
import FormControlInput from "../../components/FormControlInput.vue";
import SortableHeader from "../../components/SortableHeader.vue";
import Pagination from "../../layout/Paignation.vue";
import Confirm from "../../components/Confirm.vue";
import Loader from "../../components/Loader.vue";

export default {
  name: 'LeadFinder',
  components: {
    Loader,
    Confirm,
    Pagination,
    SortableHeader,
    FormControlInput,
    RecordOptions,
    Header,
    Sidebar,
    Breadcrumb,
    Shimmer,
    LineLoader,
    FormInputTooltip,
    draggable: VueDraggableNext
  },
  data() {
    return {
      analytics: '',
      showPopup: false,
      showCombinedUploadModal: false,
      combinedFileName: '',
      shimmer: false,
      isLineLoading: false,
      isLoading: false,
      isExtensionInstalled: false,
      verificationKey: false,
      uploadingFile: false,
      selectedFiles: [],
      export_leads: '',
      table: {
        search: '',
        records: 10,
      },
      selectedLead: '',
      delete_export_id: '',
    };
  },
  methods: {
    ROUTE_USER_LEAD_FINDER() {
      return ROUTE_USER_LEAD_FINDER;
    },
    closeCollapse(index) {
      this.export_leads[index].expanded = !this.export_leads[index].expanded;
    },
    toggleCollapse(index) {
      this.export_leads.forEach((lead) => {
        lead.expanded = false;
      });
      this.export_leads[index].expanded = true;
      if (this.export_leads[index].expanded) {
        this.selectedLead = this.export_leads[index];
        this.fetchLeadRecords(this.selectedLead, 1);
      }
    },

    initLeadTable(lead) {
      lead.table = {
        search: '',
        records: 10
      };
    },
    async checkExtensionInstalled() {
      const extensionId = "olfhccldmohnmpieafcbcgcagnmnbhkg";
      try {
        await fetch(`chrome-extension://${extensionId}/logo.png`, { method: 'GET', mode: 'no-cors' });
        this.isExtensionInstalled = true;
      } catch {
        this.isExtensionInstalled = false;
      }
    },
    async getVerificationToolKey() {
      await this.axios.post('user/api-keys/fetch/verification-tool').then(({ data }) => {
        this.verificationKey = !!data.data;
      });
    },
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileChange(event) {
      const files = Array.from(event.target.files || []);
      const validExtensions = ['csv', 'txt', 'xlsx'];
      let duplicateFiles = [];
      let invalidFiles = [];
      let addedFiles = [];

      files.forEach((f) => {
        const extension = f.name.split('.').pop().toLowerCase();
        if (!validExtensions.includes(extension)) {
          invalidFiles.push(f.name);
          return;
        }

        const exists = this.selectedFiles.some(sf =>
          sf.name === f.name && sf.size === f.size
        );

        if (exists) {
          duplicateFiles.push(f.name);
        } else {
          const fileName = f.name.split('.').slice(0, -1).join('.') || f.name;
          f.exportName = fileName;
          this.selectedFiles.push(f);
          addedFiles.push(f.name);
        }
      });
      if (addedFiles.length > 0) notify(`${addedFiles.length} file(s) added: ${this.formatFileList(addedFiles)}`, 'success');
      if (duplicateFiles.length > 0) notify(`${duplicateFiles.length} duplicate(s) skipped: ${this.formatFileList(duplicateFiles)}`, 'warning');
      if (invalidFiles.length > 0) notify(`${invalidFiles.length} invalid file(s): ${this.formatFileList(invalidFiles)}`, 'error');
      this.$refs.fileInput.value = null;
    },

    formatFileList(files, maxLength = 80) {
      if (files.length === 0) return '';

      const joined = files.join(', ');
      if (joined.length <= maxLength) return joined;

      let truncated = '';
      let fileCount = 0;

      for (const file of files) {
        const nextPart = fileCount === 0 ? file : `, ${file}`;
        if ((truncated + nextPart).length > maxLength - 10) break;
        truncated += nextPart;
        fileCount++;
      }

      const remaining = files.length - fileCount;
      return remaining > 0 ? `${truncated} and ${remaining} more` : truncated;
    },

    removeFile(index) {
      this.selectedFiles.splice(index, 1);
    },

    clearAllFiles() {
      this.selectedFiles = [];
      this.combinedFileName = '';
      this.fetch();
    },

    humanFileSize(size) {
      if (size === 0) return '0 B';
      const i = Math.floor(Math.log(size) / Math.log(1024));
      const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
      return (size / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    },

    async handleFileUpload() {
      if (this.selectedFiles.length === 0) {
        notify('Please select at least one file to upload');
        return;
      }

      this.uploadingFile = true;

      const formData = new FormData();
      this.selectedFiles.forEach((file) => {
        formData.append('files[]', file, file.name);
      });

      try {
        const res = await this.axios.post('user/lead-finder/upload-multiple', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        notify(`${this.selectedFiles.length} files uploaded successfully!`);
        this.selectedFiles = [];
        this.fetch();
      } catch (error) {
        if (this.$flattenErrors) {
          this.$flattenErrors(error);
        } else {
          console.error(error);
          notify('Upload failed. Please check console for details.');
        }
      } finally {
        this.uploadingFile = false;
      }
    },

    showCombinedModal() {
      this.showCombinedUploadModal = true;
    },

    async handleCombinedUpload() {
      if (!this.combinedFileName.trim()) {
        notify('Please enter a name for the combined file');
        return;
      }

      this.uploadingFile = true;

      const formData = new FormData();
      this.selectedFiles.forEach((file) => { formData.append('files[]', file); });

      formData.append('combined', 'true');
      formData.append('combined_name', this.combinedFileName);

      try {
        const res = await this.axios.post('user/lead-finder/upload-multiple', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        notify(`Files combined and uploaded successfully as "${this.combinedFileName}"!`);
        this.selectedFiles = [];
        this.combinedFileName = '';
        this.fetch();
        this.showCombinedUploadModal = false;
      } catch (error) {
        if (this.$flattenErrors) {
          this.$flattenErrors(error);
        } else {
          const msg = error.response?.data?.message || 'Combined upload failed!';
          notify(msg, 'error');
        }
      } finally {
        this.uploadingFile = false;
      }
    },

    fetch() {
      this.shimmer = true;
      this.axios.post('user/lead-finder/fetch').then((response) => {
        // ensure each lead has expanded flag
        this.export_leads = (response.data.data || []).map(l => ({ ...l, expanded: false }));
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.shimmer = false;
      });
    },

    fetchLeadRecords(lead, page = 1) {
      this.initLeadTable(lead);
      const { search, sort, records } = lead.table || {};
      this.axios.post(`/user/lead-finder/records`, { page: page, search: search, records: this.table.records, id: lead.id }).then(({ data }) => {
        lead.records = data.data.records;
        this.analytics = data.data.analytics;
      }).catch(err => {
        this.$flattenErrors(err);
      });
    },

    list(page = 1) {
      this.fetchLeadRecords(this.selectedLead, page);
    },

    formatWebsite(url) {
      if (!url) return '#';
      if (!url.match(/^https?:\/\//)) {
        return 'https://' + url;
      }
      return url;
    },

    deleteLead() {
      this.axios.post("user/lead-finder/delete", { id: this.delete_export_id }).then((response) => {
        notify(response.data.message);
        this.delete_export_id = '';
        this.showPopup = false;
        this.fetch();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },

    syncCompanies(id) {
      this.axios.post("user/lead-finder/sync-companies", { id: id }).then((response) => {
        notify(response.data.message);
        this.fetch();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },

    syncCompaniesDatabase(id, index) {
      this.isLoading = true;
      this.axios.post("user/lead-finder/sync-companies-database", { id: id }).then((response) => {
        this.toggleCollapse(index);
        this.export_leads[index].company_sync = 1;
        notify(response.data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.isLoading = false;
      });
    },

    generateEmails(id, index) {
      this.isLoading = true;
      this.axios.post("user/lead-finder/generate-emails", { id: id }).then((response) => {
        this.toggleCollapse(index);
        this.export_leads[index].generate_email_sync = 1;
        notify(response.data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.isLoading = false;
      });
    },

    verifyEmails(id, index) {
      this.isLoading = true;
      this.axios.post("user/lead-finder/verify-emails", { id: id }).then((response) => {
        this.toggleCollapse(index);
        this.export_leads[index].verify_email_sync = 1;
        notify(response.data.message);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally(() => {
        this.isLoading = false;
      });
    },

    showPop(id) {
      this.showPopup = true;
      this.delete_export_id = id;
    },
  },
  created() {
    this.fetch();
    this.getVerificationToolKey();
  },
  mounted() {
    this.checkExtensionInstalled();
  }
};
</script>

<style scoped>
.single-lead-extract:hover {
  background-color: #e4e8fc;
}
.card.border-success {
  border-width: 2px;
  background-color: #f3fff9;
}
.card.process {
  border-radius: 20px;
}

/* small styles for selected file list */
.selected-files-list ul li {
  background: #fff;
}
</style>

<style>
.analytics-card {
  background-color: #f0f8ff !important; /* soft light blue on hover */
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.analytics-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  background-color: #f9f9f9!important;
}
</style>
