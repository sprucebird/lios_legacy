/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import swal from 'sweetalert';

const feather = require('feather-icons')
feather.replace();




/*
Vue.js components defined here
*/
window.Vue = require('vue');



import VueRouter from 'vue-router';
import VueCookies from 'vue-cookies'
Vue.use(VueRouter);
Vue.use(VueCookies);


//import DateTimePicker & it's settings
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
Vue.use(datePicker);


import VueApexCharts from 'vue-apexcharts'
Vue.component('apexchart', VueApexCharts)

import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
Vue.component('vueDropzone', vue2Dropzone);

//Import calendar component
import 'tui-calendar/dist/tui-calendar.css'
import {
  Calendar
} from '@toast-ui/vue-calendar'
Vue.component('calendar', Calendar);

import RadarSpinner from 'epic-spinners/src/components/lib/RadarSpinner'
Vue.component('radar-spinner', RadarSpinner);

import Lingallery from 'lingallery';
Vue.component('lingallery', Lingallery);

import VuePureLightBox from 'vue-pure-lightbox';
Vue.use(VuePureLightBox);

import offline from 'v-offline';
Vue.use(offline);

//File upload plugin
import vUploader from 'v-uploader';

// v-uploader plugin global config
const uploaderConfig = {
  language: 'en',
  preview: true,
  // file uploader service url
  uploadFileUrl: 'http://xxx/upload/publicFileUpload',
  // file delete service url
  deleteFileUrl: 'http://xxx/upload/deleteUploadFile',
  // set the way to show upload message(upload fail message)
  showMessage: (vue, message) => {
    swal("Atsiprašome", "Sistemoje įvyko klaida. Norėdami užtikrinti jos pašalinimą, prašome apie ją pranešti techninio aptarnavimo personalui. Dėkojame už Jūsų supratingumą", "error");
  }
};
// install plugin with options
Vue.use(vUploader, uploaderConfig);

Vue.use(require('vue-cookies'));


import axios from 'axios';


import Vue from 'vue'
// import * as Sentry from '@sentry/browser';
// import * as Integrations from '@sentry/integrations';

// Sentry.init({
//   dsn: 'https://003acf5a23374eb38bb892137f753525@sentry.io/1541495',
//   integrations: [new Integrations.Vue({Vue, attachProps: true})],
// });

import VueGoogleCharts from 'vue-google-charts'
Vue.use(VueGoogleCharts)

document.addEventListener("DOMContentLoaded", function(event) {
      // - Code to execute when all DOM content is loaded.
      // - including fonts, images, etc.
      var loader = document.getElementById("loader");
      loader.style.display = 'none';
});


// 1. Define route components.
// These can be imported from other files
// import navigation from './components/navigation.vue';

// import signups from './components/signup-requests.vue';
import home from './components/home.vue';
import transportIndex from './components/transport/index.vue';
import transportCreate from './components/transport/create.vue';
import transportDelete from './components/transport/delete.vue';
import transportEdit from './components/transport/edit.vue';
import transportCheck from './components/transport/check.vue';
import transportCheckCreate from './components/transport/checkCreate.vue';
import transportCheckUpdate from './components/transport/checkUpdate.vue';

import driversIndex from './components/drivers/index.vue';
import driversCreate from './components/drivers/create.vue';
import driversEdit from './components/drivers/edit.vue';

import userAdd from './components/user-add.vue';
import users from './components/users.vue';
import err from './components/sysErrors.vue';


//dev_only vue components
//IMPORTANT NOTE: DISABLE OR COMMENT BEFORE DEPLOYMENT
// import calendarTest from './components/dev_only/calendar.vue';

// 0. If using a module system (e.g. via vue-cli), import Vue and VueRouter
// and then call `Vue.use(VueRouter)`.



// 2. Define some routes
// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// `Vue.extend()`, or just a component options object.
// We'll talk about nested routes later.
const routes = [{
    path: '/',
    component: home
  },
  {
    path: '/transport',
    component: transportIndex
  },
  {
    path: '/transport/create',
    component: transportCreate
  },
  {
    path: '/transport/edit/:id',
    component: transportEdit
  },
  {
    path: '/transport/delete/:id',
    component: transportDelete
  },
  {
    path: '/drivers',
    component: driversIndex
  },
  {
    path: '/drivers/create',
    component: driversCreate
  },
  {
    path: '/drivers/edit/:id',
    component: driversEdit
  },
  {
    path: '/reports/transport/status',
    component: transportCheck
  },
  {
    path: '/reports/transport/status/create',
    name: 'create',
    component: transportCheckCreate
  },
  // { path: '/dev/calendar', component: calendarTest },
  {
    path: '/reports/transport/status/update',
    name: 'update',
    component: transportCheckUpdate
  },
  {
    path: '/settings/users',
    component: users
  },
  {
    path: '/settings/users/create',
    component: userAdd
  },
  {
    path: '/system/errors',
    component: err
  },

]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = new VueRouter({
  mode: 'history',
  linkExactActiveClass: 'is-active',
  routes // short for `routes: routes`
});

// 4. Create and mount the root instance.
// Make sure to inject the router with the router option to make the
// whole app router-aware.

// register modal component
Vue.component('search-modal', {
  data: function() {
    return {
      q: null,
      search_results: [],
      search_status: null,
      authenticatedUser: null,
    }
  },
  watch: {
    q(after, before) {
      this.makeSearch();
    }
  },
  methods: {
    makeSearch: function() {
      if (this.q == '') {
        this.search_results = null;
      } else {
        axios.post('/api/search', {
          searchQ: this.q,
        }).then(response => {
          this.search_results = response.data;
          if (this.search_results.status == "error") {
            this.search_status = false;
          } else {
            this.search_status = true;
          }
        });
      }

    }
  },
  template: '#modal-search',
});

const app = new Vue({
  components: {
    offline
  },
  data: {
    connectionStatus: true,
    contentLoadingDialog: true,
    showSearchModal: false,
    showConfirmMemberModal: false,
    selectedMemberID: 0,
    dark: false,
    MediaUploadSessionID: Math.floor((Math.random() * 99999) + 10000),
  },
  methods: {
    checkInternetConnectivity() {
      if (navigator.onLine) {
        this.connectionStatus = true;
      } else {
        this.connectionStatus = false;
      }
    },
    cook() {
      if ($cookies.isKey("dark")) this.dark = ($cookies.get("dark") == "true" ? true : false);
      else $cookies.set("dark", false);

      if (this.dark) document.body.classList.add("change");
    },
    cdark() {
      $cookies.set("dark", this.dark);
      if (this.dark) document.body.classList.add("change");
      else document.body.classList.remove("change");
    },
    contentLoadingStart() {
      this.contentLoadingDialog = true;
    },
    contentLoadingStop() {
      this.contentLoadingDialog = false;
    },
  },
  mounted() {
    this.checkInternetConnectivity();
    this.cook();
  },
  router
}).$mount('#app');
