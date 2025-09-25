<template>
    <div class="vertical-menu">
        <div data-simplebar class="h-100">
            <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title">FEATURES</li>
                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_HOME() }">
                        <router-link :to="ROUTE_ADMIN_HOME()" class="text-light-sidebar">
                            <i class="mdi mdi-home "></i>
                            <span>Dashboard</span>
                        </router-link>
                    </li>
                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-2" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_ADMIN_CUSTOMERS(), this.ROUTE_ADMIN_SUBSCRIPTIONS()].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-2" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Customer</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_CUSTOMERS() }"
                                    :to="ROUTE_ADMIN_CUSTOMERS()">Customers</router-link>
                            </li>
                            <li>
                                <router-link
                                    :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_SUBSCRIPTIONS() }"
                                    :to="ROUTE_ADMIN_SUBSCRIPTIONS()">Subscriptions</router-link>
                            </li>

                        </ul>
                    </li>
                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-3" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_ADMIN_CURRENCY(), this.ROUTE_ADMIN_PLANS(), this.ROUTE_ADMIN_PAYMENT_METHODS()].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-3" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Plans</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_PLANS() }"
                                    :to="ROUTE_ADMIN_PLANS()">Manage Plans</router-link>
                            </li>
                            <li>
                                <router-link
                                    :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_PAYMENT_METHODS() }"
                                    :to="ROUTE_ADMIN_PAYMENT_METHODS()">Payment Methods</router-link>
                            </li>
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_CURRENCY() }"
                                    :to="ROUTE_ADMIN_CURRENCY()">Currency</router-link>
                            </li>


                        </ul>
                    </li>



                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-4" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_ADMIN_ADMINS(), ].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-4" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Admin</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_ADMINS() }"
                                    :to="ROUTE_ADMIN_ADMINS()">Admins</router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-5" class="dropdown-checkbox" hidden
                            :checked="[this.ROUTE_ADMIN_SENDING_SERVER(),].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-5" class="waves-effect dropdown-label">
                            <i class="mdi mdi-snowflake "></i>
                            <span>Server</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_SENDING_SERVER() }"
                                    :to="ROUTE_ADMIN_SENDING_SERVER()">Sending Server</router-link>
                            </li>
                        </ul>
                    </li>



                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_WEBSITES() }">
                        <router-link :to="ROUTE_ADMIN_WEBSITES()" class=" waves-effect">
                            <i class="mdi mdi-link "></i>
                            <span>Websites</span>
                        </router-link>
                    </li>
                    <li class="dropdown-parent">
                        <input type="checkbox" id="dropdown-toggle-1" class="dropdown-checkbox" hidden :checked="[
                            this.ROUTE_ADMIN_TESTIMONIALS(),
                            this.ROUTE_ADMIN_TEAM(),
                            this.ROUTE_ADMIN_FAQ(),
                            this.ROUTE_ADMIN_TERMS_CONDITIONS(),
                            this.ROUTE_ADMIN_PRIVACY_POLICY()
                        ].includes(this.$route.fullPath)" />
                        <label for="dropdown-toggle-1" class="waves-effect dropdown-label">
                            <i class="mdi mdi-text-box-outline "></i>
                            <span>Pages</span>
                            <b class="mdi mdi-chevron-right float-right"></b>
                        </label>
                        <ul class="submenu">
                            <li> <router-link
                                    :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_TESTIMONIALS() }"
                                    :to="ROUTE_ADMIN_TESTIMONIALS()">Testimonials</router-link></li>
                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_TEAM() }"
                                    :to="ROUTE_ADMIN_TEAM()">Team</router-link>
                            </li>

                            <li>
                                <router-link :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_FAQ() }"
                                    :to="ROUTE_ADMIN_FAQ()">FAQ</router-link>
                            </li>
                            <li>
                                <router-link
                                    :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_TERMS_CONDITIONS() }"
                                    :to="ROUTE_ADMIN_TERMS_CONDITIONS()">Terms & Conditions</router-link>
                            </li>
                            <li>
                                <router-link
                                    :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_PRIVACY_POLICY() }"
                                    :to="ROUTE_ADMIN_PRIVACY_POLICY()">Privacy Policy</router-link>
                            </li>
                        </ul>
                    </li>
                    <li :class="{ 'mm-active': this.$route.fullPath === ROUTE_ADMIN_NAME_VALIDATION() }">
                        <router-link :to="ROUTE_ADMIN_NAME_VALIDATION()" class=" waves-effect">
                            <i class="pi pi-star"></i>
                            <span>Name Validation</span>
                        </router-link>
                    </li>

                    <li>
                        <a href="#" class=" waves-effect" @click="LOGOUT(this)">
                            <i class="pi pi-sign-out"></i>
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
    ROUTE_ADMIN_WEBSITES, LOGOUT, ROUTE_ADMIN_HOME, ROUTE_ADMIN_CUSTOMERS, ROUTE_ADMIN_TESTIMONIALS, ROUTE_ADMIN_FAQ, ROUTE_ADMIN_TEAM, ROUTE_ADMIN_TERMS_CONDITIONS,
    ROUTE_ADMIN_PRIVACY_POLICY,
    ROUTE_ADMIN_NAME_VALIDATION,
    ROUTE_ADMIN_SUBSCRIPTIONS,
    ROUTE_ADMIN_CURRENCY,
    ROUTE_ADMIN_PAYMENT_METHODS,
    ROUTE_ADMIN_PLANS,
    ROUTE_ADMIN_ADMINS,
    ROUTE_ADMIN_SENDING_SERVER
} from '../../../constants.js';

export default {

    name: 'AdminSidebar',
    methods: {
        ROUTE_ADMIN_WEBSITES() {
            return ROUTE_ADMIN_WEBSITES
        },
        ROUTE_ADMIN_CUSTOMERS() {
            return ROUTE_ADMIN_CUSTOMERS
        },
        ROUTE_ADMIN_ADMINS(){
            return ROUTE_ADMIN_ADMINS;
        },
        
        ROUTE_ADMIN_SENDING_SERVER(){
            return ROUTE_ADMIN_SENDING_SERVER;
        },

        ROUTE_ADMIN_SUBSCRIPTIONS() {
            return ROUTE_ADMIN_SUBSCRIPTIONS
        },
        ROUTE_ADMIN_CURRENCY() {
            return ROUTE_ADMIN_CURRENCY
        },
        ROUTE_ADMIN_PLANS() {
            return ROUTE_ADMIN_PLANS
        },
        ROUTE_ADMIN_PAYMENT_METHODS() {
            return ROUTE_ADMIN_PAYMENT_METHODS
        },



        ROUTE_ADMIN_HOME() {
            return ROUTE_ADMIN_HOME
        },
        ROUTE_ADMIN_TESTIMONIALS() {
            return ROUTE_ADMIN_TESTIMONIALS
        },
        ROUTE_ADMIN_FAQ() {
            return ROUTE_ADMIN_FAQ
        },
        ROUTE_ADMIN_TEAM() { return ROUTE_ADMIN_TEAM },
        ROUTE_ADMIN_TERMS_CONDITIONS() { return ROUTE_ADMIN_TERMS_CONDITIONS },
        ROUTE_ADMIN_PRIVACY_POLICY() { return ROUTE_ADMIN_PRIVACY_POLICY },
        ROUTE_ADMIN_NAME_VALIDATION() { return ROUTE_ADMIN_NAME_VALIDATION },
        LOGOUT
    },
}
</script>
