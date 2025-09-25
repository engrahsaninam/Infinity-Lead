<template>
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <router-link :to="ROUTE_USER_HOME()" href="" class="logo">
                        <span class="logo-sm"><img src="/logo.png" alt="" height="24"></span>
                        <span class="logo-lg"><img src="/logo.png" alt="" class="w-100 pr-5"> </span>
                    </router-link>
                </div>
                <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect d-lg-none" id="vertical-menu-btn">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>
            <div class="d-flex">
              <div class="" v-if="authUser.role === ROLE_USER">
                  <div class="dropdown-css header-item" tabindex="0">
                    <div class="d-flex align-items-center email-header-pill p-0 rounded-0">
                      <i class="mdi mdi-email-outline mdi-24px"></i>
                      <span class="counter" v-if="unread">{{unread}}</span>
                    </div>
                    <div class="dropdown-css-content notifications">
                      <h5 class="p-2 border-bottom  shadow-sm">🔔 Notifications <sup class="counter badge badge-soft-warning float-right" v-if="notifications.length">{{notifications.length}}</sup></h5>
                      <div class="d-flex align-items-center" v-for="notif in notifications" v-if="notifications.length>0">
                        <a href @click.prevent="navigateToLeadPage(notif.id)">
                          <small v-if="!notif.read_at" class="text-primary"><i class="mdi mdi-circle"></i></small>
                          {{notif.message}}
                        </a>
                        <span>{{notif.human_created_at}}</span>
                      </div>
                      <div class="d-flex align-items-center justify-content-center" v-else>
                        <a href class="text-muted ">
                          <i>No Notifications Found!</i>
                        </a>
                      </div>

                    </div>
                  </div>
                </div>
              <div class="">

                  <div class="dropdown-css header-item" tabindex="0">
                    <div class="d-flex align-items-center p-0 rounded-0">
                      <img :src="authUser.profile" alt="Auth Profile" class="rounded-circle header-profile-user">
                      <span class="d-none d-xl-inline-block ms-1 fw-medium">{{authUser.name}}</span>
                      <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </div>
                    <div class="dropdown-css-content ">
                      <router-link v-if="authUser.role === 'user'" :to="ROUTE_USER_PROFILE()"><i class="mdi mdi-cog font-size-16 align-middle me-1"></i> Settings</router-link>
                      <a href @click="this.LOGOUT(this)"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </header>

    <button class="btn btn-light border impersonate-btn" @click="impersonateAdmin" v-if="show_impersonate">Admin View</button>
</template>
<script>
import store from "../../store.js";
import {mapGetters} from "vuex";
import {
  notify,
  ROUTE_USER_PROFILE,
  LOGOUT,
  ROUTE_USER_HOME,
  ROUTE_USER_BLACKLIST,
  ROUTE_USER_SALES_CRM_SHOW,
  ROLE_USER,
  ROUTE_ADMIN_HOME
} from "../../constants.js";

export default {
    name: 'Header',
    data(){
        return {
          notifications:[],
          unread:0,
          show_impersonate:false,
        };
    },
    methods: {
      ROUTE_USER_SALES_CRM_SHOW(id) {
        return ROUTE_USER_SALES_CRM_SHOW.replace(':id',id)
      },
      ROUTE_USER_BLACKLIST() {
        return ROUTE_USER_BLACKLIST
      },
      ROUTE_USER_HOME() {
        return ROUTE_USER_HOME
      },
      ROUTE_USER_PROFILE() {
        return ROUTE_USER_PROFILE
      },
      LOGOUT,
      navigateToLeadPage(id){
        this.axios.post('user/dashboard/notif_read_redirect',{id:id}).then((response)=>{
          this.$router.push({
            path: this.ROUTE_USER_SALES_CRM_SHOW(response.data.data.lead_id),
            query: { reload: Date.now() }
          });
        });
      },
      fetchReplyNotifications(){
        this.axios.post('user/dashboard/notifications').then(({ data }) => {
          this.notifications=data.data.notifications;
          this.unread=data.data.unread;
        }).catch((error) => {
          this.$flattenErrors(error);
        });
      },
      impersonateAdmin() {
        this.axios.post('user/back-to-admin', { token: localStorage.getItem('impersonation_token') }).then((response) => {
          notify(response.data.message);
          localStorage.removeItem('user');
          localStorage.removeItem('impersonation_token');
          localStorage.removeItem('sanctum_token');
          localStorage.removeItem('expiration_date');
          var user = JSON.stringify(response.data.user);
          var impersonation_token = response.data.impersonation_token;
          localStorage.setItem('impersonation_token', impersonation_token);
          localStorage.setItem('user', user);
          var role = response.data.user.role;
          localStorage.setItem('role', role);
          localStorage.setItem('sanctum_token', response.data.token);
          var self = this;
          store.dispatch('setAuthUser');
          setTimeout(function () {
            self.$router.push(ROUTE_ADMIN_HOME);
          }, 1000);
        }).catch((error) => {
          console.log(error);
          this.processing = false;
          this.errors = this.$flattenErrors(error);
        });
      },
    },
    computed:{
      ...mapGetters(['authUser']),
    },
    created() {
      const token = localStorage.getItem('impersonation_token');
      if (token) {
        this.show_impersonate = true;
      }
      if (token == 'undefined'){
        this.show_impersonate = false;
      }
      
    }
}
</script>
