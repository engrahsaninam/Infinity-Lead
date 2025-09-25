<template>
  <div id="layout-wrapper">
    <Header />
    <Sidebar />
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 mt-5">
              <h4 class="">Hello, {{ authUser.name }} 👋</h4>
              <p>Welcome back to your account dashboard.</p>
            </div>
          </div>
          <div class="row mb-5">
            <div class="col-md-4" v-for="(card, index) in cards" :key="index">
              <div class="card process h-100" :class="'border-' + card.color">
                <div class="card-body p-5">
                  <h4 class="">
                    <router-link :to="card.route" :class="'text-' + card.color">
                      {{ card.title }}
                      <i class="pi pi-arrow-right font-size-13 float-right"></i>
                    </router-link>
                  </h4>
                  <p class=" pb-0 mb-0">{{ card.description }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3" v-for="(item, key) in usageBars" :key="key">
              <h5>{{ item.label }}</h5>
              <p class="mb-1">
                 {{ item.used }} /
                 {{ item.maxText  }}
                <span v-if="!item.infinite" class="float-right">{{ item.percent }}%</span>
              </p>
              <div class="progress">
                <div class="progress-bar" role="progressbar"
                  :style="{ width: item.infinite ? '0%' : item.percent + '%' }"
                  :aria-valuenow="item.infinite ? 0 : item.percent" aria-valuemin="0" aria-valuemax="100">
                  {{ item.infinite ? '∞' : item.percent + '%' }}
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

import Header from "../layout/Header.vue";
import Sidebar from "../layout/Sidebar.vue";
import {
  ROUTE_USER_CAMPAIGNS_CREATE,
  ROUTE_USER_CAMPAIGNS_STEP_1, ROUTE_USER_CAMPAIGNS_STEP_10,
  ROUTE_USER_EMAIL_ACCOUNTS,
  ROUTE_USER_LEAD_FINDER
} from "../../constants.js";
import { mapGetters } from "vuex";

export default {
  name: 'Dashboard',
  components: { Sidebar, Header },
  data() {
    return {
      cards: [
        {
          color: 'primary',
          title: "Create Campaigns",
          description: "Start your first cold email campaign.",
          route: this.ROUTE_USER_CAMPAIGNS_STEP_1()
        },
        {
          color: 'success',
          title: "Connect Your Email",
          description: "Link your email accounts to start sending campaigns.",
          route: ROUTE_USER_EMAIL_ACCOUNTS
        },
        {
          color: 'primary',
          title: "Find Leads",
          description: "Gather verified email leads from LinkedIn Sales Navigator.",
          route: ROUTE_USER_LEAD_FINDER
        }
      ],
      quota:{

      },
    }
  },
  methods: {
    ROUTE_USER_CAMPAIGNS_STEP_1() {
      return ROUTE_USER_CAMPAIGNS_STEP_1.replace(':id?', '')
    },
    ROUTE_USER_CAMPAIGNS_CREATE() {
      return ROUTE_USER_CAMPAIGNS_CREATE
    },
    _quota() {
      this.processing = true;
      this.axios.post('user/dashboard/quota').then((data) => {
        this.quota=data.data.data;
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((ms) => {
        this.processing = false;
      });
    },

  },
  created() {
    this._quota();
  },
  computed: {
    ...mapGetters(['authUser']),
    usageBars() {
      const q = this.quota || {};
      const format = (used, max, label) => {
        used = Number(used ?? 0);
        max = Number(max);

        const infinite = max === -1;
        const percent = infinite
          ? 0
          : max === 0
            ? 0
            : Math.min(100, ((used / max) * 100).toFixed(2));

        return {
          label,
          used,
          maxText: infinite ? '∞' : max,
          percent,
          infinite,
        };
      };

      return {
        credits: format(q.credits_used ?? 0, q.credits , 'Sending Credits'),
        lists: format(q.lists_used ?? 0, q.list_max , 'Lists'),
        campaigns: format(q.campaign_used ?? 0, q.campaign_max, 'Campaigns'),
        subscribers: format(q.subscriber_used ?? 0, q.subscriber_max, 'Subscribers'),
      };
    },
  }

}
</script>
<style>
.card.border-success {
  background-color: #f3fff9;
}

.card.border-primary {
  background-color: #f3f7ff;
}

.card.border-warning {
  background-color: #fffdf3;
}

.card.border-dark {
  background-color: #e1e1e1;
}

.card.process {
  border-width: 2px;
  border-radius: 20px;
}
</style>