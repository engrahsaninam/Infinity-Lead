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
                <h4>{{ update_mode ? 'Update' : 'Create' }} Campaigns</h4>
                <Breadcrumb :items="[ROUTE_USER_CAMPAIGNS(), ROUTE_USER_CAMPAIGNS_CREATE()]" />
              </div>
            </div>
          </div>
          <div class="row">
            <Loader :loading="processing" />
            <Shimmer column="12" height="400" :onLoading="wizardLoading" />
            <div class="col-lg-12" v-show="!wizardLoading">
              <div class="card">
                <div class="card-body">
                  <form-wizard ref="wizard" @on-complete="launchCampaign" finishButtonText="Launch Campaign 🚀" shape="circle" :startIndex="currentStep" color="#007bff" step-size="xs" error-color="#e74c3c">
                    <tab-content :before-change="validateName">
                      <div class="form-group">
                        <label class="form-label h4 font-weight-light">Name your campaign</label>
                        <div class="input-group position-relative">
                          <input
                              type="text"
                              v-model="campaign.name"
                              placeholder="Type your campaign name here ..."
                              class="form-control form-control-lg col-md-4"
                              :class="{ 'is-invalid': campaign_error.name }"
                              @input="campaign_error.name = ''"
                          />
                          <div v-if="campaign_error.name" class="d-block invalid-tooltip">
                            {{ campaign_error.name }}
                          </div>
                        </div>
                      </div>
                    </tab-content>
                    <tab-content :before-change="validateEmailAccounts" >
                      <h4>Select email accounts</h4>
                      <div class="row" v-for="(account, index) in email_accounts" :key="index" v-if="email_accounts.length>0">
                        <div class="col-md-6 pb-2">
                          <div class="d-flex align-items-center border rounded p-3 cursor-pointer" :class="{'border-primary':this.campaign.accounts?.includes(account.id)}"  @click="syncAccountInCampaign(account.id)">
                            <div class="flex-shrink-0 p-2">
                              <img class="rounded-circle avatar-sm" width="50" :src="account.profile" :alt="account.email">
                            </div>
                            <div class="flex-grow-1">
                              <h5 class="font-size-14 mb-0">{{ account.name }} <img class="rounded-circle avatar-sm" src="../../../assets/img/google.png" alt="" width="20" height="20"></h5>
                              <small>{{ account.email }}</small>
                            </div>
                            <div class="flex-shrink-0 p-2">
                              <img class="rounded-circle avatar-sm" v-if="this.campaign.accounts?.includes(account.id)" src="../../../assets/img/checkbox.png" alt="" width="35" height="35">

                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row" v-else>
                        <div class="col-md-6 border border-primary rounded p-5 mx-3 my-3">
                          <p class="m-0 text-muted float-left"><i class="pi pi-exclamation-circle"></i> You have no sender email accounts connected. This is a mandatory setting for your campaign to run.</p>
                          <router-link :to="ROUTE_USER_EMAIL_ACCOUNTS()" class="btn btn-primary float-right ">Connect email now</router-link>
                          <button @click="fetchEmailAccounts" class="btn btn-light float-right "><i class="pi pi-refresh"></i></button>
                        </div>
                      </div>
                      <div v-if="email_accounts.length>0">
                        <p class="m-0">You can send up to {{(campaign.accounts?.length) * 40}} emails per day. Connect more email accounts to increase volume.</p>
                        <p class="m-0"><b>Important:</b> You must <b class="text-primary">upgrade</b> to a paid plan to connect more than one sender email account.</p>
                      </div>

                    </tab-content>
                    <tab-content :before-change="validateControls">
                      <h4>Control who you email</h4>
                      <table class="table table-borderless">
                        <tr class="text-muted small" v-for="(option, index) in controlOptions" :key="index">
                          <td>{{ option.label }}</td>
                          <td class="text-right">
                            <span v-if="option.note" class="mr-2 text-primary">{{ option.note }}</span>
                            <div class="switch">
                              <input
                                  :disabled="option.note && option.note === 'Mandatory'"
                                  type="checkbox"
                                  :id="'switch' + index"
                                  v-model="campaign.controls"
                                  :value="option.value"
                              />
                              <label :for="'switch' + index">.</label>
                            </div>
                          </td>
                        </tr>
                      </table>

                    </tab-content>
                    <tab-content :before-change="validateSheetType"  >
                      <h4>How do you want to email your leads?</h4>
                      <div class="row cursor-pointer mt-5">
                        <div class="col-md-4">
                          <div class="text-center border rounded p-5" :class="{ 'border-success': campaign.sheet_type === SHEET_TYPE_CSV() }" @click="selectSheetType(SHEET_TYPE_CSV())">
                            <div class="logo-sm pt-3">
                              <img src="../../../assets/img/csv.png" alt="" height="80">
                            </div>
                            <h5 class="mt-3">From a .csv file</h5>
                            <p class="pb-3">Uploading a .csv file allows you to email more than one lead at the same time.</p>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="text-center border rounded p-5" :class="{ 'border-success': campaign.sheet_type === SHEET_TYPE_GOOGLE() }" @click="selectSheetType(SHEET_TYPE_GOOGLE())">
                            <div class="logo-sm pt-3">
                              <img src="../../../assets/img/sheet.png" alt="" height="80">
                            </div>
                            <h5 class="mt-3">Google Sheets</h5>
                            <p class="pb-3">When a new lead is added to your spreadsheet, it is added to your campaign.</p>
                          </div>
                        </div>
                      </div>
                    </tab-content>
                    <tab-content :before-change="validateCsv">
                      <div v-if="campaign.sheet_type===SHEET_TYPE_CSV()">
                        <div v-if="!campaign.csv" class="row justify-content-center">
                          <div class="col-md-4   ">
                            <h4>Import your leads from a .csv file</h4>
                            <div class="drag-drop p-5 text-center">
                              <p class="h1"><i class="pi pi-cloud-upload text-primary"></i></p>
                              <p class="small mb-0">Drag & drop file here</p>
                              <p class="small mt-0">or</p>
                              <label for="csv-upload" class="btn btn-primary mb-4"  v-if="!campaign.csv">
                                Upload .csv file
                                <input type="file" id="csv-upload" class="d-none" accept=".csv" @change="onCsvChange">
                              </label>
                              <p class="small">Import a .csv file (10,000 leads max). <a href="leads-template.csv" download="leads-template.csv">Start with our template</a></p>
                              </div>
                            </div>
                        </div>
                        <div v-else>
                          <div class="row justify-content-center mb-4">
                            <div class="logo-sm pt-3 col-md-4 border text-center rounded py-5 mb-3">
                              <img src="../../../assets/img/csv.png" alt="" height="80" class="mt-5">
                              <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
                              <button class="btn btn-light bg-light border text-danger" @click="removeCsv">Delete</button>
                              <FormControlSelect2 class="mb-4 mt-3" v-model="mapping.value" :col_md="12" :options="mapping.option" label="Select Email Column " @select="updateEmailMapping"/>
                            </div>
                            <div class="table-responsive"  v-if="campaign.header && campaign.header.headers">
                              <table class="table table-sm table-bordered">
                                <tr class="text-muted small">
                                  <th v-for="header in campaign.header.headers" :key="header">{{ header }}</th>
                                </tr>
                                <tr v-for="(option, rowIndex) in campaign.rows" :key="rowIndex">
                                  <td v-for="(_, colIndex) in campaign.count_header" :key="colIndex">
                                   {{option['column_'+(colIndex+1)]}}
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12" v-else>
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
                                      <li>Watch our <a  class="" target="_blank" href="https://www.youtube.com/watch?v=6Lxt8EgyTUI">video tutorial</a>.</li>
                                      <li>Log into your <a  class="" target="_blank" href="https://admin.google.com/">Google Workspace Admin Panel</a>.</li>
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
                          <FormControlSelect2 :options="google_sheets" v-model="campaign.spreadsheet_id" label="Select Spreadsheet" @select="onGoogleSheetSelection"/>
                          <FormControlSelect2 v-if="sheets.length>0" v-model="campaign.sheet_id" :options="sheets" label="Select Sheet" @select="onSheetSelection"/>
                          <button class="btn" @click="onSheetSelection">Show Sheet Data</button>
                          <div class="table-responsive" v-if="campaign.header">
                            <table class="table table-sm table-bordered">
                              <tr class="text-muted small">
                                <th v-for="header in campaign.header.headers" :key="header">{{ header }}</th>
                              </tr>
                              <tr v-for="(option, rowIndex) in campaign.rows" :key="rowIndex">
                                <td v-for="(_, colIndex) in campaign.count_header" :key="colIndex">
                                  {{option['column_'+(colIndex+1)]}}
                                </td>
                              </tr>
                            </table>
                          </div>

                        </div>
                      </div>
                    </tab-content>
                    <tab-content :before-change="validateWeekDays">
                      <h4>When do you want your campaign to send?</h4>
                      <div class="row">
                        <div class="col text-center" v-for="day in week_days" :key="day" @click="manageDay(day)">
                          <p class="week-days text-capitalize border rounded" :class="{ 'selected-day': campaign.days.includes(day) }">
                            {{ day }}
                            <span v-if="campaign.days.includes(day)" class="check-mark">
                              <i class="pi pi-check-circle"></i>
                            </span>
                          </p>
                        </div>
                      </div>
                    </tab-content>
                    <tab-content :before-change="validateTimezone">
                      <h4>In which time zone?</h4>
                      <FormControlSelect2
                          :col_md="6"
                          :options="timezones"
                          v-model="campaign.timezone"
                          label=""
                      />
                    </tab-content>
                    <tab-content :before-change="validateDayTime">
                      <div class="row">
                        <div class="col-md-12">
                          <h4>At what time?</h4>
                        </div>
                        <div v-for="option in scheduleOptions" :key="option.key" class="col-md-6 mb-4">
                          <div class="border p-3 rounded" :class="{ 'border-primary': campaign.day === option.key }" @click="campaign.day = option.key">
                            <h5>{{ option.icon }} {{ option.label }}</h5>
                            <div class="p-3">
                              <div class="row">
                                <div class="col border p-3 text-center pt-4">
                                  <h6>Start</h6>
                                  <p v-if="option.key !== 'custom'">{{ option.start }}</p>
                                  <input
                                      v-else
                                      type="time"
                                      v-model="campaign.start"
                                      class="form-control"
                                  />
                                </div>
                                <div class="col border p-3 text-center pt-4">
                                  <h6>End</h6>
                                  <p v-if="option.key !== 'custom'">{{ option.end }}</p>
                                  <input
                                      v-else
                                      type="time"
                                      v-model="campaign.end"
                                      class="form-control"
                                  />
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
                    </tab-content>
                    <tab-content :before-change="validatePerMinVolume">
                      <div class="col-md-6">
                        <h6 class="">Control sending volume</h6>
                        Set a minimum / maximum range. For example: 5 min / 15 max. All email accounts that are connected to this campaign will send a daily randomized volume that is between the range you set. This updates on an email account level (not campaign level). All campaigns that use the same email accounts will be updated.
                        <div class="form-group">
                          <label for="min">Per Minute</label>
                          <VueSlider
                              id="min"
                              v-model="campaign.per_minute"
                              :min="0"
                              :max="40"
                              :tooltip="'always'"
                              @change="updateSliderRange(campaign,'m' ,$event)"
                          />
                        </div>
                        <div class="form-group">
                          <label for="volume">Volume</label>
                          <VueSlider
                              id="volume"
                              v-model="campaign.volume"
                              :min="0"
                              :max="40"
                              @change="updateSliderRange(campaign,'v' ,$event)"
                          />
                        </div>
                      </div>

                    </tab-content>
                    <tab-content :before-change="validateDeliverability">
                      <h4>Optimize your deliverability</h4>
                      <table class="table table-borderless">
                        <tr class="text-muted small" v-for="(option, index) in deliverabilityOptions" :key="index">
                          <td>{{ option.label }}</td>
                          <td class="text-right">
                            <span class="mr-2 text-primary">{{ option.note }}</span>
                            <div class="switch">
                              <input
                                  type="checkbox"
                                  :id="'switches' + index"
                                  :value="option.value"
                                  checked
                                  disabled
                              />
                              <label :for="'switches' + index">.</label>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </tab-content>
                    <tab-content :before-change="validateEmailContent">
                      <div class="row justify-content-center">
                        <div class="col-md-8">
                          <h4 class="text-center">Create your sequence</h4>
                          <p class="text-center">Read our article <a href="">How To Write A Cold Email That Gets Responses.</a></p>
                          <div class="card">
                            <div class="card-header text-center">
                              <p class="p-0 m-0"> New Email</p>
                            </div>

                            <div class="card-body p-0 m-0">
                              <div class="input-group p-1">
                                <div class="input-group-append">
                                  <span class="input-group-text bg-white  border-0" >
                                    Subject
                                  </span>
                                </div>
                                <input v-model="campaign.subject" id="subject" class="form-control no-outline border-0">
                              </div>
                              <QuillEditorComponent v-model="campaign.message" :header-options="headerOptions"/>
                              <div class="row">
                                <div class="col-md-12 ">
                                  <button class="btn btn-primary float-right my-3 mr-3" @click="addFollowUp"><i class="pi pi-plus-circle"></i> Add Followup</button>
                                  <button class="btn btn-primary float-right my-3 mr-2" v-if="!campaign.show_signature" @click="campaign.show_signature=true"><i class="pi pi-plus-circle"></i> Add Signature</button>
                                </div>
                              </div>
                            </div>
                            <div class="card-body border-top" v-for="(followup,index) in campaign.followups">
                              <div class="row">
                                <div class="col-md-8">
                                  <div class="row d-flex align-items-center px-3">
                                    <p>Follow up after</p>
                                    <FormControlSelect2 :col_md="4" :options="follow_up_days_options" v-model="followup.days" label="" :show_label="false"  />
                                    <p>If no reply</p>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <button class="btn btn-outline-light bg-light border text-dark float-right" @click="removeFollowUp(index,followup.id)"><i class="pi pi-trash"></i> Remove Followup</button>
                                </div>
                              </div>
                              <div class="border-top">
                                <QuillEditorComponent v-model="followup.message" :header-options="headerOptions"/>
                              </div>
                            </div>

                            <div class="card-body border-top" v-show="campaign.show_signature">
                              <div class="row">
                                <div class="col-md-4">
                                  <h5>Signature:</h5>
                                  <p class="text-muted small">(Appended at the end of all outgoing messages)</p>
                                  <p class="text-muted small">Read our article How To Create The Perfect Email Signature.</p>
                                  <button class="btn btn-outline-light border bg-light text-dark" @click="deleteSignature()"><i class="pi pi-trash"></i> Remove Signature</button>
                                </div>
                                <div class="col-md-8">
                                  <QuillEditorComponent class="border" v-model="campaign.signature" :header-options="[{key:'Domain',value:'Domain'},{key:'Email',value:'Email'}]" :show_counter="false" :show_spam="false"/>
                                </div>
                              </div>
                            </div>


                          </div>
                        </div>
                      </div>
                    </tab-content>
                    <tab-content>
                      <div class="row justify-content-center">
                        <div class="col-md-8">
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Campaign name</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,1)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <p>{{campaign.name}}</p>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Email accounts that you're sending with in this campaign</h4>
                              <button @click="goToStep(1)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</button>
                            </div>
                            <div class="card-body">
                              <p v-for="email in campaign.accounts">
                                <img class="rounded-circle avatar-sm" src="../../../assets/img/checkbox.png" alt="" width="20" height="20">
                                {{ email_accounts.find(em => em.id === email)?.email || '' }}
                              </p>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Control who you email</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,3)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <table class="table table-borderless">
                                <tr class="text-muted small" v-for="(option, index) in controlOptions" :key="index">
                                  <td width="70%">{{ option.label }}</td>
                                  <td width="30%" class="text-right">
                                    <span v-if="option.note" class="mr-2 text-primary">{{ option.note }}</span>
                                    <div class="switch">
                                      <input
                                          type="checkbox"
                                          :id="'switches' + index"
                                          v-model="campaign.controls"
                                          :value="option.value"
                                          disabled
                                      />
                                      <label :for="'switches' + index">.</label>
                                    </div>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Who you will be emailing</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,4)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <div v-if="campaign.sheet_type === SHEET_TYPE_CSV()">
                                <div class="logo-sm pt-3 col-md-6 border text-center rounded py-3">
                                  <img src="../../../assets/img/csv.png" alt="" height="80">
                                  <p class="text-muted small mt-3">{{campaign.csv_name}}</p>
                                  <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,4)" class=" btn btn-light bg-light border text-danger"><i class="pi pi-pencil"></i> Preview</router-link>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">When do you want your campaign to send?</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,5)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col text-center" v-for="day in week_days" :key="day">
                                  <p class="week-days text-capitalize border rounded" :class="{ 'selected-day': campaign.days.includes(day) }">
                                    {{ day }}
                                    <span v-if="campaign.days.includes(day)" class="check-mark"><i class="pi pi-check-circle"></i></span>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Time zone</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,6)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <FormControlSelect2
                                  :disabled="true"
                                  readonly
                                  :col_md="6"
                                  :options="timezones"
                                  v-model="campaign.timezone"
                                  label=""
                              />
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">At what time?</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,7)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div v-for="option in scheduleOptions" :key="option.key" class="col-md-12 mb-4">
                                  <div class="border p-3 rounded" :class="{ 'border-primary': campaign.day === option.key }">
                                    <h5>{{ option.icon }} {{ option.label }}</h5>
                                    <div class="p-3">
                                      <div class="row">
                                        <div class="col border p-3 text-center pt-4">
                                          <h6>Start</h6>
                                          <p v-if="option.key !== 'custom'">{{ option.start }}</p>
                                          <input
                                              v-else
                                              type="time"
                                              v-model="campaign.start"
                                              class="form-control"
                                              disabled
                                          />
                                        </div>
                                        <div class="col border p-3 text-center pt-4">
                                          <h6>End</h6>
                                          <p v-if="option.key !== 'custom'">{{ option.end }}</p>
                                          <input
                                              v-else
                                              type="time"
                                              v-model="campaign.end"
                                              class="form-control"
                                              disabled
                                          />
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

                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Control sending volume</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,8)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <div class="form-group">
                                <label for="min">Per Minute</label>
                                <VueSlider
                                    disabled
                                    id="min"
                                    v-model="campaign.per_minute"
                                    :min="0"
                                    :max="40"
                                    :tooltip="'always'"
                                    @change="updateSliderRange(campaign,'m' ,$event)"
                                />
                              </div>
                              <div class="form-group">
                                <label for="volume">Volume</label>
                                <VueSlider
                                    disabled
                                    id="volume"
                                    v-model="campaign.volume"
                                    :min="0"
                                    :max="40"
                                    @change="updateSliderRange(campaign,'v' ,$event)"
                                />
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-body border-bottom">
                              <h4 class="float-left">Email sequence</h4>
                              <router-link :to="ROUTE_USER_CAMPAIGNS_UPDATE(campaign.id,10)" class="float-right btn btn-outline-primary"><i class="pi pi-pencil"></i> Edit</router-link>
                            </div>
                            <div class="card-body">
                              <div class="card message-body">
                                <div class="card-header ">
                                  <h5 class="pt-2">{{campaign.subject}}</h5>
                                </div>
                                <div class="card-body">
                                  <h6 v-html="campaign.message"></h6>
                                  <div class="row justify-content-end">
                                    <div class="col-md-4" v-if="campaign.show_signature" >
                                      <span v-html="campaign.signature"></span>
                                    </div>
                                  </div>
                                  <div class="card mt-2" v-for="(followup) in campaign.followups">
                                    <div class="card-body">
                                      <h5>Every {{followup.days}} days If no reply</h5>
                                      <span v-html="followup.message"></span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>


                          </div>

                        </div>
                      </div>
                    </tab-content>
                  </form-wizard>
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
import { FormWizard, TabContent } from 'vue3-form-wizard';
import 'vue3-form-wizard/dist/style.css';
import Breadcrumb from "../../components/Breadcrumb.vue";
import Shimmer from "../../components/Shimmer.vue";
import Loader from "../../components/Loader.vue";

import VueSlider from '@vueform/slider';
import '@vueform/slider/themes/default.css';

import {
  FORM_DATA,
  notify, NOTIFY_TYPE_ERROR,
  ROUTE_USER_CAMPAIGNS,
  ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_CAMPAIGNS_UPDATE,
  ROUTE_USER_EMAIL_ACCOUNTS, SHEET_TYPE_CSV, SHEET_TYPE_GOOGLE
} from "../../../constants.js";
import FormControlSelect2 from "../../components/FormControlSelect2.vue";
import moment from "moment-timezone";
import {QuillEditor} from "@vueup/vue-quill";
import QuillEditorComponent from "../../components/QuillEditorComponent.vue";

export default {
  name: "CreateCampaigns",
  components: {
    QuillEditorComponent,
    FormControlSelect2, Header, Sidebar, FormWizard, TabContent, Breadcrumb, Shimmer, Loader, VueSlider,QuillEditor },
  data() {
    return {
      timezones: this.getTimezones(),
      excelClientId:'',
      currentStep: this.$route.params.step ? this.$route.params.step - 1 : 0,
      update_mode: Boolean(this.$route.params.id),
      processing: false,
      wizardLoading: false,
      email_accounts: [],
      headerOptions:[],
      follow_up_days_options: Array.from({ length: 27 }, (_, i) => ({
        key: i + 4,
        value: `${i + 4} days`
      })),
      mapping:{
        id:this.$route.params.id,
        option:[],
        value:'',
      },
      campaign: {

        name: '',
        csv_name: '',
        accounts: [],
        controls:[
          'skip_responded',
          'skip_duplicates',
        ],
        days:[
          'mon',
          'tue',
          'wed',
          'thu',
          'fri',
        ],
        day:'full',
        start: "08:00:00",
        end: "18:00:00",
        per_minute:'',
        volume:'',
        sheet_type: SHEET_TYPE_CSV,
        csv:'',
        rows:'',
        header:'',
        spreadsheet_id:'',
        sheet_id:'',
        timezone:'',
        deliverability: [
          'plain_text_emails',
          'gradual_sending',
          'auto_match_inboxes',
          'avoid_bounced_emails',
        ],
        subject:'',
        message:'',
        signature:'',
        show_signature:false,
        followups:[],
      },

      campaign_error: {
        name: '' ,
        accounts: '' ,
        controls:'',
      },
      controlOptions: [
        { value: "skip_existing", label: "Skip leads that exist in another campaign. This doesn't apply to campaigns that have been deleted." },
        { value: "skip_invalid", label: "Skip leads that have “invalid” or “catch-all” email addresses. Activating this will ensure that you only send emails to leads with “valid” email addresses.", note: "Recommended" },
        { value: "skip_personal", label: "Skip personal email addresses (like @gmail.com). Contacting personal email addresses can negatively impact your deliverability.", note: "Recommended" },
        { value: "skip_responded", label: "Skip leads that have already responded. This does not apply to leads that have responded in other sending tools.", note: "Mandatory" },
        { value: "skip_duplicates", label: "Skip duplicate leads. If your lead list contains the same lead more than once, then we'll only include the lead once in this campaign.", note: "Mandatory" }
      ],
      deliverabilityOptions: [
        { value: "plain_text_emails", label: "Send emails as plain text (not HTML) to improve deliverability.", note: "Mandatory" },
        { value: "gradual_sending", label: "Gradually increase sending volume on newly connected email accounts for the first 8-weeks.", note: "Mandatory" },
        { value: "auto_match_inboxes", label: "Auto-match your leads’ inboxes to your sender email accounts. Gmail to Gmail. Outlook to Outlook.", note: "Mandatory" },
        { value: "avoid_bounced_emails", label: "Avoid sending emails to email accounts that have previously bounced.", note: "Mandatory" }
      ],
      scheduleOptions: [
        { key: "full", icon: "⏰", label: "Full Day", start: "08:00 AM", end: "06:00 PM" },
        { key: "morning", icon: "☀️", label: "Morning", start: "08:00 AM", end: "11:45 AM" },
        { key: "afternoon", icon: "🌄", label: "Afternoon", start: "01:00 PM", end: "04:45 PM" },
        { key: "custom", icon: "📝", label: "Custom" }
      ],

      CSV_HEADER:"",
      CSV_DATA:"",
      google_sheets:[],
      master_google_sheets:[],
      sheets:[],
      week_days:['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',],
      selectedHeader:'',
      savedSelection:'',

    };
  },
  methods: {
    ROUTE_USER_CAMPAIGNS_UPDATE(id,step) {
      return ROUTE_USER_CAMPAIGNS_UPDATE.replace(':id',id).replace(':step?',step)
    },
    async goToStep(stepNumber) {
      this.$refs.wizard.activeTabIndex = stepNumber - 1;
    },

    SHEET_TYPE_GOOGLE() {
      return SHEET_TYPE_GOOGLE
    },
    SHEET_TYPE_CSV() {
      return SHEET_TYPE_CSV
    },
    ROUTE_USER_EMAIL_ACCOUNTS() {
      return ROUTE_USER_EMAIL_ACCOUNTS
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    ROUTE_USER_CAMPAIGNS() {
      return ROUTE_USER_CAMPAIGNS
    },
    connectGoogleSheet() {
      const user = JSON.parse(localStorage.getItem('user'));
      window.location.href='/google/sheet-access?campaign='+this.campaign.id+'&user_id='+user.id;
    },
    async validateName() {
      if (!this.campaign.name) {
        this.campaign_error.name = 'Please insert a campaign name.';
        return false;
      }
      if (!this.update_mode) {
        this.processing = true;
        try {
          const response = await this.axios.post('user/campaigns/name', this.campaign);
          const id = response.data.data.id;
          this.$router.replace({ name: 'CampaignUpdate', params: { id, step: 2 } });
          return true;
        } catch (error) {
          this.$flattenErrors(error);
          return false;
        } finally {
          this.processing = false;
        }
      }
      return true;
    },
    async validateEmailAccounts(){
      if (!this.campaign.accounts){
        this.campaign.accounts=[];
      }
      if (this.campaign.accounts.length > 0){
        this.processing=true;
        await this.axios.post('user/campaigns/accounts', this.campaign).then((response) => {
          return true;
        }).catch((error) => {
          this.$flattenErrors(error);
          return false;
        }).finally((final)=>{
          this.processing=false;
        });
        return true;
      }else{
        notify('Please select at least one email account!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
    },
    async validateControls(){
      if (this.campaign.controls.length > 0){
        await this.axios.post('user/campaigns/controls', this.campaign).then((response) => {
          return true;
        }).catch((error) => {
          this.$flattenErrors(error);
          return false;
        });
        return true;
      }else{
        notify('Please select at least one control!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
    },
    async validateSheetType(){
      if (this.campaign.sheet_type){
        await this.axios.post('user/campaigns/sheet_type', this.campaign).then((response) => {
          if (this.campaign.sheet_type === this.SHEET_TYPE_GOOGLE()){
            this.getListOfSpreadsheets();
          }
          return true;
        }).catch((error) => {
          this.$flattenErrors(error);
          return false;
        });
        return true;
      }else{
        notify('Please select sheet type!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
    },
    async edit() {
      this.processing=true;
      await this.axios.post('user/campaigns/edit', { id: this.$route.params.id }).then((response) => {
        this.campaign = response.data.data;
        if (this.campaign && this.campaign.header) {
          this.mapping.option = Object.values(this.campaign.header.headers).map((header, index) => ({
            key: 'column_' + (index + 1),
            value: header
          }));
          Object.entries(this.campaign.header.mapping).forEach(([key, value]) => {
            this.mapping.value=key;
          });
        }
        if (this.campaign && this.campaign.header){
          this.headerOptions = Object.values(this.campaign.header.headers).map((header) => ({
            key: header,
            value: header
          }));
        }


      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms)=>{
        this.processing=false;
      });
    },
    async fetchEmailAccounts() {
      await this.axios.post('user/email-accounts/fetch').then(({ data }) => {
        this.email_accounts = data.data;
      });
    },
    selectSheetType(type) {
      this.campaign.sheet_type = type;
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
    onCsvChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.campaign.csv=file;
        this.storeCsv();
      }else{
        notify('Something went wrong! Please try again later!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
      }
    },
    storeCsv(){
      var data=FORM_DATA(this.campaign);
      this.axios.post('user/campaigns/csv',data).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    removeCsv(){
      this.axios.post('user/campaigns/remove-csv',this.campaign).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
      /*this.campaign.csv='';
      this.CSV_HEADER='';
      this.CSV_DATA='';*/
    },
    updateEmailMapping(){
      this.axios.post('user/campaigns/email-mapping',this.mapping).then(({ data }) => {
        //this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      });
    },
    validateCsv(){
      if (this.campaign.rows !== ''){
        if (!this.mapping.value){
          notify('Select email column for mapping!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
          return  false;
        }
        return true;
      }else{
        if (this.campaign.sheet_type===SHEET_TYPE_CSV){
          notify('CSV data not readable', {autoClose: 3000},NOTIFY_TYPE_ERROR);
          return  false;
        }
        return true;
      }
    },
    copyToClipboard(text){
      navigator.clipboard.writeText(text).then(() => {
        notify('Copied to clipboard');
      }).catch(() => {
        alert('Failed to copy URL. Please try again.');
      });
    },
    async fetchExcelClientId() {
      await this.axios.post('excel-client-id').then(({ data }) => {
        this.excelClientId = data.data;
      });
    },
    async getListOfSpreadsheets(){
      this.processing=true;
      await this.axios.post('user/google-sheets/fetch').then(({ data }) => {
        this.edit();
        this.google_sheets = [];
        this.master_google_sheets = data;
        data.forEach((sheet)=>{
          this.google_sheets.push({
            key:sheet.id,
            value:sheet.name,
          });
        })
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    onGoogleSheetSelection(selectedSpreadSheet) {
      this.sheets=[];
      var record = this.master_google_sheets.find(SS => SS.id === selectedSpreadSheet);
      record.sheets.forEach((sheet)=>{
        this.sheets.push({
          key: sheet.id,
          value: sheet.name
        });
      });
    },
    onSheetSelection() {
      this.processing=true;
      var spreadSheet = this.master_google_sheets.find(SS => SS.id === this.campaign.spreadsheet_id);
      this.axios.post('user/campaigns/read-sheet',{spreadsheet:spreadSheet,sheet:this.campaign.sheet_id,id:this.campaign.id}).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.google_sheets='';
        this.master_google_sheets='';
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },
    manageDay(day) {
      const index = this.campaign.days.indexOf(day);
      if (index === -1) {
        this.campaign.days.push(day); // Add day if not already selected
      } else {
        this.campaign.days.splice(index, 1); // Remove day if selected
      }
    },
    validateWeekDays(){
      this.processing=true;
      this.axios.post('user/campaigns/days',this.campaign).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },
    deleteSignature(){
      this.processing=true;
      this.axios.post('user/campaigns/delete-signature',this.campaign).then(({ data }) => {
        this.edit();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    },

    getTimezones() {
      return moment.tz.names().map((tz) => {
        const offset = moment.tz(tz).utcOffset();
        const formattedOffset = `UTC${offset >= 0 ? "+" : ""}${(offset / 60).toFixed(0)}`;
        return { key: tz, value: `${formattedOffset} - ${tz}` };
      });
    },
    validateTimezone(){
      if (!this.campaign.timezone){
        notify('Timezone is required!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      this.processing=true;
      this.axios.post('user/campaigns/timezone',this.campaign).then(({ data }) => {
        this.edit();
        return true;
      }).catch((error) => {
        this.$flattenErrors(error);
        return false;
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },
    updateSliderRange(account,type,value) {
      if (type==='m'){
        account.per_minute = value;
      }else{
        account.volume = value;
      }
    },
    validateDayTime(){
      if (!this.campaign.day){
        notify('Select a box', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      if (!this.campaign.start){
        notify('Start Time is missing', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      if (!this.campaign.start){
        notify('End Time is missing', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }

      this.processing=true;
      this.axios.post('user/campaigns/time',this.campaign).then(({ data }) => {
        this.edit();
        return true;
      }).catch((error) => {
        this.$flattenErrors(error);
        return false;
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },
    validatePerMinVolume(){
      if (!this.campaign.per_minute){
        notify('Per minute selection is required', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      if (!this.campaign.volume){
        notify('Volume selection is required', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      this.processing=true;
      this.axios.post('user/campaigns/slider',this.campaign).then(({ data }) => {
        this.edit();
        return true;
      }).catch((error) => {
        this.$flattenErrors(error);
        return false;
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },
    validateDeliverability(){
      this.deliverability=[
        'plain_text_emails',
        'gradual_sending',
        'auto_match_inboxes',
        'avoid_bounced_emails',
      ];
      this.processing=true;
      this.axios.post('user/campaigns/deliverability',this.campaign).then(({ data }) => {
        this.edit();
        return true;
      }).catch((error) => {
        this.$flattenErrors(error);
        return false;
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },
    validateEmailContent(){
      if (!this.campaign.subject){
        notify('Subject is missing!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      if (!this.campaign.message){
        notify('Message is missing!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      if (this.campaign.show_signature && !this.campaign.signature){
        notify('Signature is missing!', {autoClose: 3000},NOTIFY_TYPE_ERROR);
        return  false;
      }
      this.processing=true;
      this.axios.post('user/campaigns/email-content',this.campaign).then(({ data }) => {
        this.edit();
        return true;
      }).catch((error) => {
        this.$flattenErrors(error);
        return false;
      }).finally((data)=>{
        this.processing=false;
      });
      return true;
    },

    addFollowUp(){
      if (this.campaign.followups && this.campaign.followups.length<2){
        this.campaign.followups.push({id:'',message:'',days:4,});
      }
    },
    removeFollowUp(index,id) {
      this.processing=true;
      this.axios.post('user/campaigns/delete-followup',{id:id}).then(({ data }) => {
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        if (this.campaign.followups.length) {
          this.campaign.followups.splice(index, 1);
        }
        this.processing=false;
      });
    },
    launchCampaign(){
      this.processing=true;
      this.axios.post('user/campaigns/launch',this.campaign).then(({ data }) => {
        notify(data.data.message);
        this.$router.push(ROUTE_USER_CAMPAIGNS);
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((data)=>{
        this.processing=false;
      });
    }
  },
  created() {
    if (this.update_mode) this.edit();
    if (!this.campaign.timezone){
      this.campaign.timezone=Intl.DateTimeFormat().resolvedOptions().timeZone;
    }


    this.fetchExcelClientId();
    this.fetchEmailAccounts();
  },
  watch: {
    '$route.params': {
      handler(newParams, oldParams) {
        if (newParams.id !== oldParams?.id) {
          this.update_mode = Boolean(newParams.id);
          this.currentStep = newParams.step ? Number(newParams.step) - 1 : 0;
          if (this.update_mode) {
            this.edit();
          } else {
            this.campaign = { name: '' };
          }
        }
      },
      immediate: true,
      deep: true
    },
  },
};
</script>
<style>
.message-body p{margin: 0;padding: 0}
</style>
