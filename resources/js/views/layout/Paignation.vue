<template>
  <div class="row align-items-center py-2">
    <div class="col-md-3">
      <span v-if="total"><b>{{ number_format(total) }}</b> Records Found.</span>
    </div>
    <div class="col-md-9 d-flex justify-content-end ">
      <ul v-if="prev || next" class="pagination  float-right pb-0 mb-0">
        <li v-for="(link, index) in links" :key="index" >
          <a class="btn btn-primary mx-1"
             href
             v-if="isLabeled(link.label) === 'Previous' && prev"
             @click.prevent="navigate(currentPage - 1)">
            <i class="mdi mdi-chevron-double-left"></i>
          </a>
          <a class="btn btn-primary  mx-1"
             href
             v-else-if="isLabeled(link.label) === 'Next' && next"
             @click.prevent="navigate(currentPage + 1)">
            <i class="mdi mdi-chevron-double-right"></i>

          </a>
          <a class="btn btn-primary  mx-1"
             href
             :class="{ active: link.active }"
             v-else-if="isLabeled(link.label) !== 'Previous' && isLabeled(link.label) !== 'Next'"
             @click.prevent="navigate(parseInt(link.label))">
            {{ link.label }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>

import {number_format} from "../../constants.js";

export default {
  name:'Pagination',
  props: {
    data: {
      type: Object,
      required: true
    },
    list: {
      type: Function,
      required: true
    },
  },
  computed: {
    links() {
      return this.data.links || [];
    },
    total() {
      return this.data.total || 0;
    },
    prev() {
      return this.data.prev_page_url;
    },
    next() {
      return this.data.next_page_url;
    },
    currentPage() {
      return Number(this.$route.query.page) || 1;
    }
  },
  methods: {
    number_format,
    isLabeled(label) {
      if (label.includes("Previous")) return 'Previous';
      else if (label.includes("Next")) return 'Next';
      else return label;
    },
    navigate(page) {
      this.$router.replace({ query: { page } });
      this.list(page);
    }
  }
};
</script>
