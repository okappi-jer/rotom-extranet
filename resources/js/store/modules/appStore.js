import {
  IS_LOADING,
  IS_MENU_OPEN,
  SET_PAGE_TITLE,
  SET_PAGE_CTA,
} from '../../constants';

const state = {
  isLoading: false,
  isMenuOpen: false,
  pageTitle: null,
  pageUrl: null,
};

const getters = {
  isLoading: state => state.isLoading,
};

const actions = {
  [IS_LOADING]({commit}, payload) {
    commit(IS_LOADING, payload);
  },
  [IS_MENU_OPEN]({commit}, payload) {
    commit(IS_MENU_OPEN, payload);
  },
  [SET_PAGE_TITLE]({commit}, payload) {
    commit(SET_PAGE_TITLE, payload);
  },
  [SET_PAGE_CTA]({commit}, payload) {
    commit(SET_PAGE_CTA, payload);
  },
};

const mutations = {
  [IS_LOADING](state, payload) {
    state.isLoading = payload;
  },
  [IS_MENU_OPEN](state, payload) {
    state.isMenuOpen = payload;
  },
  [SET_PAGE_TITLE](state, payload) {
    state.pageTitle = payload;
  },
  [SET_PAGE_CTA](state, payload) {
    state.pageUrl = payload;
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
  namespaced: true,
};
