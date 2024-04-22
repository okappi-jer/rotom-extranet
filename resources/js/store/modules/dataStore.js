import axios from 'axios';
import {
  IS_LOADING,
  GET_CONTACTS,
  GET_DELIVERIES,
  GET_EMPLOYEES,
  GET_VISITORS,
} from '../../constants';

const api_url = process.env.MIX_API_URL;

const state = {
  deliveries: null,
  contacts: null,
  employees: null,
  visitors: null,
};

const getters = {

};

const actions = {
  [GET_CONTACTS]({commit, dispatch, rootGetters}) {
    dispatch('appStore/' + IS_LOADING, true, { root: true });
    const user_id = rootGetters.getUser ? rootGetters.getUser.id : null;

    axios.get(`${api_url}/contacts/${user_id}`)
      .then(res => {
        commit(GET_CONTACTS, res.data.data);
      })
      .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
      .catch(err => {
        console.log(err);
      });
  },
  [GET_EMPLOYEES]({commit, dispatch, rootGetters}) {
    dispatch('appStore/' + IS_LOADING, true, { root: true });
    const user_id = rootGetters.getUser ? rootGetters.getUser.id : null;

    axios.get(`${api_url}/employees`)
      .then(res => {
        commit(GET_EMPLOYEES, res.data.data);
      })
      .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
      .catch(err => {
        console.log(err);
      });
  },
  [GET_VISITORS]({commit, dispatch, rootGetters}) {
    dispatch('appStore/' + IS_LOADING, true, { root: true });
    const user_id = rootGetters.getUser ? rootGetters.getUser.id : null;

    axios.get(`${api_url}/visitors`)
      .then(res => {
        commit(GET_VISITORS, res.data.data);
      })
      .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
      .catch(err => {
        console.log(err);
      });
  },
  [GET_DELIVERIES]({commit, dispatch, rootGetters}) {
    dispatch('appStore/' + IS_LOADING, true, { root: true });
    const user_id = rootGetters.getUser ? rootGetters.getUser.id : null;

    axios.get(`${api_url}/delivery/all`)
      .then(res => {
        commit(GET_DELIVERIES, res.data.data);
      })
      .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
      .catch(err => {
        console.log(err);
      });
  },
};

const mutations = {
  [GET_CONTACTS](state, payload) {
    state.contacts = payload;
  },
  [GET_EMPLOYEES](state, payload) {
    state.employees = payload;
  },
  [GET_VISITORS](state, payload) {
    state.visitors = payload;
  },
  [GET_DELIVERIES](state, payload) {
    state.deliveries = payload;
  },
};

export default {
  state,
  getters,
  actions,
  mutations
};
