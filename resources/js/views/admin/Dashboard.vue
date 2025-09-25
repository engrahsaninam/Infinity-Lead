<template>
    <div id="layout-wrapper">
        <AdminSidebar/>
        <Header/>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                      <div class="col-lg-12">
                        <h4 class=" my-4">Welcome {{authUser.name}} 👋</h4>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {mapGetters} from "vuex";
import AdminSidebar from "../layout/admin/AdminSidebar.vue";
import Header from "../layout/Header.vue";
export default {
    name: 'AdminDashboard',
  components: { Header,AdminSidebar},
    data() {
        return {
          
        }
    },
    methods: {
    },
    created() {
    },
    computed:{
      ...mapGetters(['authUser']),
    },
}
</script>
