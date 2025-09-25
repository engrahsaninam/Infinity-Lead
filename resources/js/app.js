import {createApp} from 'vue';
import App from './App.vue';
import router from './route.js';
import axios from 'axios'
import VueAxios from 'vue-axios'
import 'bootstrap'
// import '../sass/app.scss'
//import 'vue-multiselect/dist/vue-multiselect.css'
import Toast from 'vue3-toastify'
import 'vue3-toastify/dist/index.css';
const options = { autoClose: 3000, position: 'bottom-right' }
import { Quasar,  Dialog } from 'quasar'
import Vuex from 'vuex';
import {notify, NOTIFY_TYPE_ERROR, ROUTE_LOGIN} from "./constants.js";
import store from './store.js';
import 'primeicons/primeicons.css'


import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';




import 'froala-editor/css/plugins/colors.min.css';
import 'froala-editor/css/plugins/image.min.css';
import 'froala-editor/css/plugins/code_view.min.css';
import 'froala-editor/js/froala_editor.pkgd.min.js';
import 'froala-editor/css/froala_editor.pkgd.min.css';
import 'froala-editor/css/froala_style.min.css';
import 'font-awesome/css/font-awesome.css';
import 'froala-editor/js/froala_editor.pkgd.min.js'; // This includes most plugins
import 'froala-editor/js/plugins/align.min.js';
import 'froala-editor/js/plugins/image.min.js';
import 'froala-editor/js/plugins/colors.min.js';
import 'froala-editor/js/plugins/lists.min.js';
import 'froala-editor/js/plugins/paragraph_format.min.js';
import 'froala-editor/js/plugins/paragraph_style.min.js';
import 'froala-editor/js/plugins/code_view.min.js';
import 'froala-editor/js/plugins/table.min.js';
import 'froala-editor/js/plugins/quote.min.js';
import 'froala-editor/js/plugins/link.min.js';
import 'froala-editor/js/plugins/save.min.js';
import VueFroala from 'vue-froala-wysiwyg';



const app = createApp(App);
app.config.productionTip = false;
app.use(VueFroala);

app.use(router);
app.use(VueAxios, axios);
app.use(Toast, options)
app.use(Vuex);
import 'vue-multiselect/dist/vue-multiselect.css'


import Vue3FormWizard from 'vue3-form-wizard'
import 'vue3-form-wizard/dist/style.css'
app.use(Vue3FormWizard)

app.component('QuillEditor', QuillEditor)


app.use(Quasar, { plugins: { Dialog } })

var baseUrl = window.location.origin;
axios.defaults.baseURL = baseUrl + '/api/';

axios.interceptors.request.use(function (config) {
    let tokenTwo = window.localStorage.getItem('sanctum_token');
    if (tokenTwo) {
        config.headers['Authorization'] = 'Bearer ' + tokenTwo;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            const currentRoute = router.currentRoute.value;
            if (currentRoute.meta?.loginRequired !== false) {
                router.push(ROUTE_LOGIN);
            }
        }
        return Promise.reject(error);
    }
);

app.config.globalProperties.$flattenErrors = function (error) {
    console.log(error);
    const flattenedErrors = {};
    if (error.response.status === 422) {
        console.log(error.response.data.errors);
        for (const field in error.response.data.errors) {
            if (Array.isArray(error.response.data.errors[field])) {
                notify(error.response.data.errors[field][0], null, NOTIFY_TYPE_ERROR);
                flattenedErrors[field] = error.response.data.errors[field][0];
            }
        }
    } else if (error.response.status === 421) {
        notify(error.response.data.errors, null, NOTIFY_TYPE_ERROR);
        flattenedErrors['message'] = error.response.data.errors;
    }else if (error.response.status === 401) {
        notify('Action is unauthorized', null, NOTIFY_TYPE_ERROR);
    }else if (error.response.status === 405) {
        notify(error.response.data.errors, null, NOTIFY_TYPE_ERROR);
    }else if (error.response.status === 403){
        notify(error.response.data.errors, null, NOTIFY_TYPE_ERROR);
    }
    else if (error.response.status === 500) {
        notify(error.response.data.errors, null, NOTIFY_TYPE_ERROR);
    }

    return flattenedErrors;
}
app.use(store)
app.mount('#app');
