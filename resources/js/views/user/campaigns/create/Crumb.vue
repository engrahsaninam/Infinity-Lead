<template>
  <div class="row mb-2">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4>Create Campaigns</h4>
        <Breadcrumb :items="[ROUTE_USER_CAMPAIGNS, ROUTE_USER_CAMPAIGNS_CREATE,]" />
      </div>
    </div>
  </div>
</template>
<script setup>
import {ROUTE_USER_CAMPAIGNS, ROUTE_USER_CAMPAIGNS_CREATE} from "../../../../constants.js";
import Breadcrumb from "../../../components/Breadcrumb.vue";
</script>