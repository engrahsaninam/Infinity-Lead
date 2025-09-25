<template>
    <div class="vertical-menu">
        <div data-simplebar class="h-100">
            <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="p-4">
                        <router-link :to="ROUTE_USER_CAMPAIGNS_STEP_1()"
                            class="btn btn-primary rounded text-light font-weight-normal"><i
                                class="mdi mdi-plus-box-outline small text-light"></i> Create Campaign</router-link>
                    </li>
                    <li class="menu-title">FEATURES</li>
                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_CAMPAIGNS() }">
                        <router-link :to="ROUTE_USER_CAMPAIGNS()" class="waves-effect">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Campaigns</span>
                        </router-link>
                    </li>

                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-1" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_USER_LISTS(), this.ROUTE_USER_TAGS()].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-1" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake"></i>
                            <span>Lists</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_LISTS() }"
                                    :to="ROUTE_USER_LISTS()">Lists</router-link>
                            </li>
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_TAGS() }"
                                    :to="ROUTE_USER_TAGS()">Tags</router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-2" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_USER_BLACKLIST()].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-2" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Sending</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_BLACKLIST() }"
                                    :to="ROUTE_USER_BLACKLIST()">Blacklist</router-link>
                            </li>
                        </ul>
                    </li>


                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_TEMPLATES() }">
                        <router-link :to="ROUTE_USER_TEMPLATES()" class="waves-effect">
                            <i class="mdi mdi-snowflake text-success"></i>
                            <span>Templates</span>
                        </router-link>
                    </li>




                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_SALES_CRM() }">
                        <router-link :to="ROUTE_USER_SALES_CRM()" class=" waves-effect">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Sales CRM</span>
                        </router-link>
                    </li>
                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_LEAD_FINDER() }">
                        <router-link :to="ROUTE_USER_LEAD_FINDER()" class=" waves-effect">
                            <i class="mdi mdi-snowflake text-warning"></i>
                            <span>Lead Finder</span>
                        </router-link>
                    </li>




                    <li class="menu-title">ACCOUNT</li>
                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_USER_PROFILE() }">
                        <router-link :to="ROUTE_USER_PROFILE()" class=" waves-effect">
                            <i class="pi pi-cog "></i>
                            <span>Settings</span>
                        </router-link>
                    </li>

                    <li>
                        <a href="#" class=" waves-effect" @click="LOGOUT(this)">
                            <i class="pi pi-sign-out "></i>
                            <span>Logout</span>
                        </a>
                    </li>


                </ul>
            </div>
        </div>
    </div>
</template>
<script>

import {
    LOGOUT,
    ROUTE_USER_CAMPAIGNS,
    ROUTE_USER_LISTS,
    ROUTE_USER_CAMPAIGNS_CREATE, ROUTE_USER_CAMPAIGNS_STEP_1,
    ROUTE_USER_LEAD_FINDER,
    ROUTE_USER_PROFILE,
    ROUTE_USER_SALES_CRM,
    ROUTE_USER_TEMPLATES,
    ROUTE_USER_TAGS,
    ROUTE_USER_BLACKLIST,
} from "../../constants.js";
export default {

    name: 'Sidebar',
    methods: {

        ROUTE_USER_CAMPAIGNS_STEP_1() {
            return ROUTE_USER_CAMPAIGNS_STEP_1.replace(':id?', '')
        },
        ROUTE_USER_LEAD_FINDER() {
            return ROUTE_USER_LEAD_FINDER
        },
        ROUTE_USER_SALES_CRM() {
            return ROUTE_USER_SALES_CRM
        },
        ROUTE_USER_CAMPAIGNS_CREATE() {
            return ROUTE_USER_CAMPAIGNS_CREATE
        },
        ROUTE_USER_PROFILE() {
            return ROUTE_USER_PROFILE
        },
        ROUTE_USER_CAMPAIGNS() {
            return ROUTE_USER_CAMPAIGNS
        },
        ROUTE_USER_LISTS() {
            return ROUTE_USER_LISTS
        },
        ROUTE_USER_TEMPLATES() {
            return ROUTE_USER_TEMPLATES
        },
        ROUTE_USER_TAGS() {
            return ROUTE_USER_TAGS
        },
        ROUTE_USER_BLACKLIST() {
            return ROUTE_USER_BLACKLIST
        },
        LOGOUT
    },
}
</script>
<style>

</style>
