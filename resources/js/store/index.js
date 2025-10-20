import { createStore } from 'vuex';

export default createStore({
  state: {
    user: null,
    token: localStorage.getItem('token'),
    isLoading: false
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user;
    },
    SET_TOKEN(state, token) {
      state.token = token;
      if (token) {
        localStorage.setItem('token', token);
      } else {
        localStorage.removeItem('token');
      }
    },
    SET_LOADING(state, isLoading) {
      state.isLoading = isLoading;
    }
  },
  actions: {
    login({ commit }, { user, token }) {
      commit('SET_USER', user);
      commit('SET_TOKEN', token);
      localStorage.setItem('user', JSON.stringify(user));
    },
    logout({ commit }) {
      commit('SET_USER', null);
      commit('SET_TOKEN', null);
      localStorage.removeItem('user');
    },
    setLoading({ commit }, isLoading) {
      commit('SET_LOADING', isLoading);
    }
  },
  getters: {
    isAuthenticated: state => !!state.token,
    user: state => state.user || JSON.parse(localStorage.getItem('user') || 'null'),
    isLoading: state => state.isLoading
  }
});