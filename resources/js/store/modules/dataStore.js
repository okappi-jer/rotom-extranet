import axios from 'axios';
import {
  IS_LOADING,
  GET_CONTACTS,
} from '../../constants';

const api_url = process.env.MIX_API_URL;

const state = {
  contacts: null,
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
};

const mutations = {
  [GET_CONTACTS](state, payload) {
    state.contacts = payload;
  },
};

export default {
  state,
  getters,
  actions,
  mutations
};
