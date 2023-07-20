import router from '../../router';
import axios from 'axios';

import {
  LOGIN_USER,
  LOGOUT_USER,
  SET_AUTH_ERRORS,
  REFRESH_USER,
} from '../../constants';

const api_url = process.env.MIX_API_URL;

const state = {
  user: null,
  authErrors: null,
};

const getters = {
  getUser: state => state.user,
  isAuthenticated: state => state.user !== null,
};

const actions = {
  [LOGIN_USER]({commit}, payload) {
    let formData = new FormData();
    formData.append('email', payload.loginData.email);
    formData.append('password', payload.loginData.password);

    axios.post(`${api_url}/auth/login`, formData).then(res => {
      const item = {
        token: res.data.access_token,
        expires: new Date().getTime(),
      }
      localStorage.setItem('key', JSON.stringify(item));
      commit(LOGIN_USER, res.data.user);
    }).then(() => {
      router.push('/dashboard');
    }).catch(err => {
      console.log(err);
      commit(SET_AUTH_ERRORS, 'Deze inloggegevens zijn niet geldig.')
    });
  },
  [REFRESH_USER]({commit}) {
    const key = JSON.parse(localStorage.getItem('key'));
    if (!key) {
      router.push('/login');
      return;
    }

    const now = new Date().getTime();
    const alive = now - 3600000; // 1 hour in milliseconds
    
    if(key && alive > key.expires) {
      commit(LOGOUT_USER);
    } else {
      axios.post(`${api_url}/auth/refresh`, {}, {
        headers: {
          Authorization: `Bearer ${key.token}`
        }
      }).then(res => {
        const item = {
          token: res.data.access_token,
          expires: new Date().getTime(),
        }
        localStorage.setItem('key', JSON.stringify(item))
        commit(LOGIN_USER, res.data.user);
      }).catch(err => {
        console.log(err);
      });
    }
  },
  [LOGOUT_USER]({commit}) {
    const key = JSON.parse(localStorage.getItem('key'));

    axios.post(`${api_url}/auth/logout`, {}, {
      headers: {
        Authorization: `Bearer ${key.token}`
      }
    })
    .then(() => {
      commit(LOGOUT_USER);
    })
    .catch(err => {
      commit(SET_AUTH_ERRORS, err.response.data.errors);
    });
  },
};

const mutations = {
  [SET_AUTH_ERRORS](state, payload) {
    state.authErrors = payload;
  },
  [LOGIN_USER](state, payload) {
    state.authErrors = '';
    state.user = payload;
  },
  [LOGOUT_USER](state) {
    localStorage.removeItem('key');
    state.user = null;
    router.push('/login');
  },
};

export default {
  state,
  getters,
  actions,
  mutations
};
