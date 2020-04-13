/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// window.Vue = require('vue');
import Vue from 'vue';
import VueRouter from "vue-router";
import Form from "./utilities/Form";
window.Form = Form;

import router from './routes';
import Index from './Index';
import VueAxios from 'vue-axios';
import axios from 'axios';
import VueAuth from '@websanova/vue-auth';
import auth from './auth';
// import VueToast from 'vue-toast-notification';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VuejsClipper from 'vuejs-clipper'

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.use(VuejsClipper)



// Set Vue globally
window.Vue = Vue

// Vue.use(VueToast);
// Set Vue router
Vue.router = router
Vue.use(VueRouter)

// // Set Vue authentication
Vue.use(VueAxios, axios)
axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api/v1`
Vue.use(VueAuth, auth)
Vue.prototype.$appName = 'My App'

Vue.prototype.$baseUrl = process.env.MIX_APP_URL;


// Load Index
Vue.component('index', Index)
const app = new Vue({
  el: '#app',
  router
})
