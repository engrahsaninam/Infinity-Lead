<template>
  <div id="layout-wrapper">
    <Header/>
    <admin-sidebar/>
    <div class="main-content">
      <div class="page-content" v-if="shimmer">
        <Shimmer column="12" height="1000" :onLoading="shimmer"/>
      </div>
      <div class="page-content" v-else>
        <div class="container-fluid">
        <div class="row">
          <div class="col-md-12 mb-2">
            <button class="btn btn-danger float-right" @click="importReplacement">
              <i class="mdi mdi-import"></i> Refresh Default Replacements?
            </button>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5 class="pt-1">Remove Symbols</h5></div>
              <div class="card-body">
                <form @submit.prevent="storeRemoveSymbol">
                  <div class="input-group mb-3">
                    <input v-model="removeForm.symbols" type="text" class="form-control" placeholder="Symbol to remove">
                    <div class="input-group-append">
                      <button class="btn btn-success" type="submit" :disabled="processing"><i class="pi pi-plus"></i></button>
                    </div>
                  </div>
                </form>
                <table class="table table-hover table-sm">
                  <thead>
                    <tr>
                      <th width="80%">Symbol</th>
                      <th width="20%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in removeSymbols" :key="item.id">
                      <td>{{ item.symbols }}</td>
                      <td class="text-right">
                        <span class="badge badge-soft-danger" @click="deleteItem(item.id)"><i class="pi pi-trash"></i>  </span>
                      </td>
                    </tr>
                    <tr v-if="removeSymbols.length === 0">
                      <td colspan="2" class="text-center text-muted">No symbols found</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Replace Symbols -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5 class="pt-1">Replace Symbols</h5></div>
              <div class="card-body">
                <form @submit.prevent="storeReplaceSymbol">
                  <div class="input-group mb-3">
                    <input v-model="replaceForm.symbols" type="text" class="form-control" placeholder="Symbol to replace">
                    <input v-model="replaceForm.replace" type="text" class="form-control" placeholder="Replacement">
                    <div class="input-group-append">
                      <button class="btn btn-success" type="submit" :disabled="processing"><i class="pi pi-plus"></i></button>
                    </div>
                  </div>
                </form>
                <table class="table table-hover table-sm">
                  <thead>
                    <tr>
                      <th width="40%">Symbol</th>
                      <th width="40%">Replace With</th>
                      <th width="20%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in replaceSymbols" :key="item.id">
                      <td >{{ item.symbols }}</td>
                      <td>{{ item.replace }}</td>
                      <td class="text-right">
                        <span class="badge badge-soft-danger" @click="deleteItem(item.id)"><i class="pi pi-trash"></i></span>
                      </td>
                    </tr>
                    <tr v-if="replaceSymbols.length === 0">
                      <td colspan="3" class="text-center text-muted">No replacements found</td>
                    </tr>
                  </tbody>
                </table>
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
import { FORM_DATA, notify } from '../../../constants.js';
import Header from "../../layout/Header.vue"
import AdminSidebar from "../../layout/admin/AdminSidebar.vue"
export default {
  name: 'NameValidation',
  components:{
    Header,
    AdminSidebar,
  },
  data() {
    return {
      processing: false,
      removeSymbols: [],
      replaceSymbols: [],
      removeForm: { symbols: '' },
      replaceForm: { symbols: '', replace: '' },
    };
  },
  methods: {
    async fetchSymbols() {
      const { data } = await this.axios.get('/admin/name-validation/fetch');
      this.removeSymbols = data.filter(i => i.remove === 1);
      this.replaceSymbols = data.filter(i => i.remove === 0);
    },

    storeRemoveSymbol() {
      this.processing=true;
      const payload = { ...this.removeForm, remove: 0 };
      this.axios.post('/admin/name-validation/store-remove', payload).then((response) => {
        this.fetchSymbols();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((response) =>{
        this.processing=false;
      });
    },
    storeReplaceSymbol() {
      this.processing=true;
      const payload = { ...this.replaceForm, remove: 0 };
      this.axios.post('/admin/name-validation/store-replace', payload).then((response) => {
        this.fetchSymbols();
      }).catch((error) => {
        this.$flattenErrors(error);
      }).finally((response) =>{
        this.processing=false;
      });
    },
    
    async deleteItem(id) {
      await this.axios.post('/admin/name-validation/delete', { id });
      this.fetchSymbols();
    },
    importReplacement(){
      this.axios.post('admin/name-validation/import-replace').then((response) => {
        this.fetchSymbols();
      });
    },
  },
  created() {
    this.fetchSymbols();
  }
};
</script>
