import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import authStore from './modules/authStore'
import appStore from './modules/appStore';
import dataStore from './modules/dataStore';

export default new Vuex.Store({
  modules: {
    appStore,
    authStore,
    dataStore,
  }
});
