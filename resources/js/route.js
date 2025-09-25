import { createRouter, createWebHistory } from 'vue-router';


import Login from './views/auth/Login.vue';
import Register from './views/auth/Register.vue';
import VerifyEmail from './views/auth/VerifyEmail.vue';

import {
    ROLE_ADMIN,
    ROLE_USER,
    ROUTE_ADMIN_HOME,
    ROUTE_ADMIN_WEBSITES,
    ROUTE_ADMIN_CUSTOMERS,
    ROUTE_FORGOT_PASSWORD,
    ROUTE_GOOGLE_ALREADY_REGISTERED,
    ROUTE_GOOGLE_AUTH,
    ROUTE_GOOGLE_LOGIN_ACCOUNT_NOT_FOUND,
    ROUTE_LOGIN,
    ROUTE_PASSWORD_RESET,
    ROUTE_REGISTER,
    ROUTE_VERIFY_EMAIL,
    ROUTE_UNAUTHORIZED, ROUTE_USER_API_DOMAINS,
    ROUTE_USER_API_KEYS, ROUTE_USER_BLACKLIST,
    ROUTE_USER_CAMPAIGNS, ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS,
    ROUTE_USER_CAMPAIGNS_CREATE,
    ROUTE_USER_CAMPAIGNS_SHOW,
    ROUTE_USER_CAMPAIGNS_STEP_1,
    ROUTE_USER_CAMPAIGNS_STEP_10,
    ROUTE_USER_CAMPAIGNS_STEP_11,
    ROUTE_USER_CAMPAIGNS_STEP_12,
    ROUTE_USER_CAMPAIGNS_STEP_2,
    ROUTE_USER_CAMPAIGNS_STEP_3,
    ROUTE_USER_CAMPAIGNS_STEP_4,
    ROUTE_USER_CAMPAIGNS_STEP_5,
    ROUTE_USER_CAMPAIGNS_STEP_6,
    ROUTE_USER_CAMPAIGNS_STEP_7,
    ROUTE_USER_CAMPAIGNS_STEP_8,
    ROUTE_USER_CAMPAIGNS_STEP_9,
    ROUTE_USER_CAMPAIGNS_UPDATE,
    ROUTE_USER_CHANGE_PASSWORD,
    ROUTE_USER_CHANGE_PROFILE,
    ROUTE_USER_EMAIL_ACCOUNTS,
    ROUTE_USER_HELP,
    ROUTE_USER_HOME,
    ROUTE_USER_API_SUBSCRIPTIONS,
    ROUTE_USER_LEAD_FINDER,
    ROUTE_USER_PROFILE,
    ROUTE_USER_SALES_CRM,
    ROUTE_USER_SALES_CRM_SHOW,
    ROUTE_ADMIN_TESTIMONIALS,
    ROUTE_ADMIN_FAQ,
    ROUTE_ADMIN_CURRENCY,
    ROUTE_ADMIN_TEAM,
    ROUTE_ADMIN_TERMS_CONDITIONS,
    ROUTE_ADMIN_PRIVACY_POLICY,
    ROUTE_ADMIN_NAME_VALIDATION,
    ROUTE_USER_LISTS,
    ROUTE_USER_LISTS_SHOW,
    ROUTE_USER_TAGS,
    ROUTE_USER_TEMPLATES,
    ROUTE_USER_TEMPLATES_CREATE,
    ROUTE_USER_TAGS_CREATE,
    ROUTE_ADMIN_PLANS,
    ROUTE_ADMIN_PAYMENT_METHODS,
    ROUTE_USER_INACTIVE,
    ROUTE_ADMIN_SUBSCRIPTIONS,
    ROUTE_ADMIN_CUSTOMERS_CREATE,
    ROUTE_ADMIN_ADMINS,
    ROUTE_ADMIN_ADMINS_CREATE,
    ROUTE_ADMIN_SENDING_SERVER,
    ROUTE_ADMIN_SENDING_SERVER_UPDATE,
    ROUTE_USER_RESTRICTED,
    ROUTE_USER_BILLING_INFORMATION,

} from "./constants.js";
import Unauthorized from "./Unauthorized.vue";
import NotFound from "./NotFound.vue";
import ForgotPassword from "./views/auth/ForgotPassword.vue";
import GoogleAuth from "./views/auth/GoogleAuth.vue";

import AdminDashboard from "./views/admin/Dashboard.vue";
import Website from "./views/admin/websites/Index.vue";
import Admins from "./views/admin/admins/Index.vue";
import CreateAdmins from "./views/admin/admins/Create.vue";
import SendingServer from "./views/admin/sending_servers/Index.vue";
import SendingServerUpdate from "./views/admin/sending_servers/Update.vue";
import Users from "./views/admin/users/Index.vue";
import CreateUsers from "./views/admin/users/Create.vue";
import Testimonials from "./views/admin/testimonials/Index.vue";
import Teams from "./views/admin/teams/Index.vue";
import TermsAndConditions from "./views/admin/terms_and_conditions/Index.vue";
import PrivacyPolicy from "./views/admin/privacy_policy/Index.vue";
import NameValidation from "./views/admin/name_validation/Index.vue";
import Faq from "./views/admin/faqs/Index.vue";
import Currency from "./views/admin/currencies/Index.vue";
import Plans from "./views/admin/plans/Index.vue";
import PaymentMethods from "./views/admin/payment_methods/Index.vue";
import AdminSubscriptions from './views/admin/subscriptions/Index.vue';

import Dashboard from "./views/user/Dashboard.vue";
import LeadFinder from "./views/user/lead_finder/Index.vue"
import SalesCrm from "./views/user/sales_crm/Index.vue"
import SalesCrmShow from "./views/user/sales_crm/Show.vue"
import Campaigns from "./views/user/campaigns/Index.vue"
import CampaignShow from "./views/user/campaigns/Show.vue"
import Blacklist from "./views/user/blacklists/Index.vue";
import Profile from "./views/user/settings/Profile.vue";
import ChangePassword from "./views/user/settings/ChangePassword.vue";
import ChangeProfile from "./views/user/settings/ChangeProfile.vue";
import EmailAccounts from "./views/user/settings/EmailAccounts.vue";
import Help from "./views/user/settings/Help.vue";
import ApiKeys from "./views/user/settings/ApiKeys.vue";
import AlreadyRegistered from "./views/auth/AlreadyRegistered.vue";
import GoogleLoginAccountNotFound from "./views/auth/GoogleLoginAccountNotFound.vue";
import ResetPassword from "./views/auth/ResetPassword.vue";
import Tags from "./views/user/tags/Index.vue";
import CreateTags from "./views/user/tags/Create.vue";
import Templates from "./views/user/templates/Index.vue";
import CreateTemplates from "./views/user/templates/Create.vue";
import Lists from "./views/user/lists/Index.vue";
import ListShow from "./views/user/lists/Show.vue";
import Step_1 from "./views/user/campaigns/create/Step_1.vue";
import Step_2 from "./views/user/campaigns/create/Step_2.vue";
import Step_3 from "./views/user/campaigns/create/Step_3.vue";
import Step_4 from "./views/user/campaigns/create/Step_4.vue";
import Step_5 from "./views/user/campaigns/create/Step_5.vue";
import Step_6 from "./views/user/campaigns/create/Step_6.vue";
import Step_7 from "./views/user/campaigns/create/Step_7.vue";
import Step_8 from "./views/user/campaigns/create/Step_8.vue";
import Step_9 from "./views/user/campaigns/create/Step_9.vue";
import Step_10 from "./views/user/campaigns/create/Step_10.vue";
import Step_11 from "./views/user/campaigns/create/Step_11.vue";
import Step_12 from "./views/user/campaigns/create/Step_12.vue";
import AddMoreLeads from "./views/user/campaigns/create/AddMoreLeads.vue";
import Domains from "./views/user/settings/Domains.vue";
import Subscriptions from './views/user/settings/Subscriptions.vue';
import Inactive from './views/user/Inactive.vue';
import Restricted from './Restricted.vue';
import Billing from './views/user/settings/Billing.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: ROUTE_UNAUTHORIZED, component: Unauthorized, name: Unauthorized, meta: { loginRequired: false } },
        { path: ROUTE_LOGIN, component: Login, name: Login, meta: { loginRequired: false } },
        { path: ROUTE_REGISTER, component: Register, name: Register, meta: { loginRequired: false } },
        { path: ROUTE_VERIFY_EMAIL, component: VerifyEmail, name: VerifyEmail, meta: { loginRequired: false } },
        { path: ROUTE_FORGOT_PASSWORD, component: ForgotPassword, name: ForgotPassword, meta: { loginRequired: false } },
        { path: ROUTE_PASSWORD_RESET, component: ResetPassword, name: ResetPassword, meta: { loginRequired: false } },
        { path: ROUTE_GOOGLE_AUTH, component: GoogleAuth, name: GoogleAuth, meta: { loginRequired: false } },
        { path: ROUTE_GOOGLE_ALREADY_REGISTERED, component: AlreadyRegistered, name: AlreadyRegistered, meta: { loginRequired: false } },
        { path: ROUTE_GOOGLE_LOGIN_ACCOUNT_NOT_FOUND, component: GoogleLoginAccountNotFound, name: GoogleLoginAccountNotFound, meta: { loginRequired: false } },
        { path: '/:catchAll(.*)', component: NotFound, meta: { loginRequired: false } },

        { path: ROUTE_ADMIN_HOME, component: AdminDashboard, name: AdminDashboard, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_WEBSITES, component: Website, name: Website, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_ADMINS, component: Admins, name: Admins, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_CUSTOMERS, component: Users, name: Users, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_CUSTOMERS_CREATE, component: CreateUsers, name: CreateUsers, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_SENDING_SERVER, component: SendingServer, name: SendingServer, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_SENDING_SERVER_UPDATE, component: SendingServerUpdate, name: SendingServerUpdate, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_ADMINS_CREATE, component: CreateAdmins, name: CreateAdmins, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_SUBSCRIPTIONS, component: AdminSubscriptions, name: AdminSubscriptions, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_TESTIMONIALS, component: Testimonials, name: Testimonials, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_FAQ, component: Faq, name: Faq, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_CURRENCY, component: Currency, name: Currency, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_PLANS, component: Plans, name: Plans, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_PAYMENT_METHODS, component: PaymentMethods, name: PaymentMethods, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_TEAM, component: Teams, name: Teams, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_TERMS_CONDITIONS, component: TermsAndConditions, name: TermsAndConditions, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_PRIVACY_POLICY, component: PrivacyPolicy, name: PrivacyPolicy, meta: { loginRequired: true, role: ROLE_ADMIN } },
        { path: ROUTE_ADMIN_NAME_VALIDATION, component: NameValidation, name: NameValidation, meta: { loginRequired: true, role: ROLE_ADMIN } },

        { path: ROUTE_USER_HOME, component: Dashboard, name: Dashboard, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS, component: Campaigns, name: Campaigns, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_TAGS, component: Tags, name: Tags, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_TAGS_CREATE, component: CreateTags, name: CreateTags, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_TEMPLATES, component: Templates, name: Templates, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_TEMPLATES_CREATE, component: CreateTemplates, name: CreateTemplates, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_LISTS, component: Lists, name: Lists, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_LISTS_SHOW, component: ListShow, name: ListShow, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_LEAD_FINDER, component: LeadFinder, name: LeadFinder, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_SALES_CRM, component: SalesCrm, name: SalesCrm, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_SALES_CRM_SHOW, component: SalesCrmShow, name: SalesCrmShow, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_SHOW, component: CampaignShow, name: CampaignShow, meta: { loginRequired: true, role: ROLE_USER } },

        { path: ROUTE_USER_CAMPAIGNS_STEP_1, component: Step_1, name: Step_1, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_2, component: Step_2, name: Step_2, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_3, component: Step_3, name: Step_3, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_4, component: Step_4, name: Step_4, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_5, component: Step_5, name: Step_5, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_6, component: Step_6, name: Step_6, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_7, component: Step_7, name: Step_7, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_8, component: Step_8, name: Step_8, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_9, component: Step_9, name: Step_9, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_10, component: Step_10, name: Step_10, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_11, component: Step_11, name: Step_11, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_STEP_12, component: Step_12, name: Step_12, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS, component: AddMoreLeads, name: AddMoreLeads, meta: { loginRequired: true, role: ROLE_USER } },

        { path: ROUTE_USER_BLACKLIST, component: Blacklist, name: Blacklist, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_PROFILE, component: Profile, name: Profile, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CHANGE_PASSWORD, component: ChangePassword, name: ChangePassword, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_CHANGE_PROFILE, component: ChangeProfile, name: ChangeProfile, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_EMAIL_ACCOUNTS, component: EmailAccounts, name: EmailAccounts, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_API_KEYS, component: ApiKeys, name: ApiKeys, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_API_DOMAINS, component: Domains, name: Domains, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_HELP, component: Help, name: Help, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_BILLING_INFORMATION, component: Billing, name: Billing, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_API_SUBSCRIPTIONS, component: Subscriptions, name: Subscriptions, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_INACTIVE, component: Inactive, name: Inactive, meta: { loginRequired: true, role: ROLE_USER } },
        { path: ROUTE_USER_RESTRICTED, component: Restricted, name: Restricted, meta: { loginRequired: true, role: ROLE_USER } },

    ],
});
router.beforeEach((to, from, next) => {
    const isLoggedIn = !!localStorage.getItem('sanctum_token');
    const userRole = localStorage.getItem('role');
    let user= localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : {};
    
    if (to.meta?.loginRequired === false) {
        return next();
    }
    if (!isLoggedIn && to.path !== ROUTE_LOGIN) {
        return next(ROUTE_LOGIN);
    }
    
    if (isLoggedIn && userRole === ROLE_USER && user.status === 0 && to.path !== ROUTE_USER_INACTIVE) {
        return next(ROUTE_USER_INACTIVE);
    }
    if (isLoggedIn && userRole === ROLE_USER && user.status === 2 && to.path !== ROUTE_USER_RESTRICTED) {
        return next(ROUTE_USER_RESTRICTED);
    }
    if (to.meta?.role && to.meta.role !== userRole) {
        return next({ path: ROUTE_UNAUTHORIZED });
    }
    
    next();
});
export default router;
