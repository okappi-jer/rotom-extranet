/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//window.Vue = require('vue');
window.Vue = require('vue').default;

import VueRouter from 'vue-router';
import wysiwyg from "vue-wysiwyg";
import axios from 'axios';
import IdleVue from "idle-vue";
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

import store from './store';
import router from './router';
import filters from './filters';

import { REFRESH_USER, LOGOUT_USER,
} from './constants';

// axios.defaults.baseURL = process.env.MIX_API_URL;

Vue.router = router;
Vue.use(VueRouter);
Vue.use(wysiwyg, {
    maxHeight: "500px",
    paragraphSeparator: 'p',
    forcePlainTextOnPaste: true,
    hideModules: {
        "image": true,
    }
});

Vue.use(VueMoment, {
    moment,
})

Vue.use(IdleVue, {
    store,
    idleTime: 600000, // 10 minutes - 1sec = 1000
    startAtIdle: false,
});

//Vue.use(require('vue-moment'));

// Auto register components
const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

axios.interceptors.request.use(function (config) {
    const key = JSON.parse(localStorage.getItem('key'));
    if (key) {
        config.headers['Authorization'] = `Bearer ${key.token}`
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

const app = new Vue({
    router,
    store,
    el: '#app',
    created() {
        if (localStorage.getItem('key')) {
            this.$store.dispatch(REFRESH_USER);
        }
    },
    computed: {
        isIdle() {
            return this.$store.state.idleVue.isIdle;
        },
    },
    watch: {
        isIdle(newVal) {
            if (localStorage.getItem("key")) {
                if (newVal) {
                    this.$store.dispatch(LOGOUT_USER);
                }
            }
        },
    },
});
