<template>
  <div class="btn-group col-12 mt-4">
    <router-link :class="isActive(ROUTE_USER_PROFILE()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_PROFILE()" class="btn"><i class="pi pi-user"></i> Profile</router-link>
    <router-link :class="isActive(ROUTE_USER_EMAIL_ACCOUNTS()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_EMAIL_ACCOUNTS()" class="btn"><i class="pi pi-inbox"></i> Email Accounts</router-link>
    <router-link :class="isActive(ROUTE_USER_API_KEYS()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_API_KEYS()" class="btn"><i class="pi pi-key"></i> Api Keys</router-link>
    <router-link :class="isActive(ROUTE_USER_API_SUBSCRIPTIONS()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_API_SUBSCRIPTIONS()" class="btn"><i class="pi pi-refresh"></i> Subscription</router-link>
    <router-link :class="isActive(ROUTE_USER_BILLING_INFORMATION()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_BILLING_INFORMATION()" class="btn"><i class="pi pi-dollar"></i> Billing Information</router-link>
    <router-link :class="isActive(ROUTE_USER_HELP()) ? 'btn-primary active' : 'btn-link border bg-white'"
      :to="ROUTE_USER_HELP()" class="btn"><i class="pi pi-question-circle"></i> Help</router-link>
  </div>
</template>
<script>
import {
  ROUTE_USER_API_DOMAINS,
  ROUTE_USER_API_KEYS,
  ROUTE_USER_EMAIL_ACCOUNTS,
  ROUTE_USER_API_SUBSCRIPTIONS,
  ROUTE_USER_HELP,
  ROUTE_USER_PROFILE,
  ROUTE_USER_BILLING_INFORMATION
} from "../../../../constants.js";

export default {
  name: 'Pills',
  methods: {
    ROUTE_USER_API_DOMAINS() {
      return ROUTE_USER_API_DOMAINS
    },
    ROUTE_USER_API_KEYS() {
      return ROUTE_USER_API_KEYS
    },
    ROUTE_USER_HELP() {
      return ROUTE_USER_HELP
    },
    ROUTE_USER_EMAIL_ACCOUNTS() {
      return ROUTE_USER_EMAIL_ACCOUNTS
    },
    ROUTE_USER_PROFILE() {
      return ROUTE_USER_PROFILE
    },
    
    ROUTE_USER_API_SUBSCRIPTIONS() {
      return ROUTE_USER_API_SUBSCRIPTIONS
    },
    
    ROUTE_USER_BILLING_INFORMATION() {
      return ROUTE_USER_BILLING_INFORMATION
    },


    isActive(route) {
      return this.$route.path === route;
    },
  },
}
</script>
