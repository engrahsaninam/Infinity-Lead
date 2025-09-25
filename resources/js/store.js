import Vuex from 'vuex';
import axios from 'axios';

import createPersistedState from "vuex-persistedstate";

export default new Vuex.Store({
    plugins: [createPersistedState({
        paths: ['authPermissions']
    })],
    state: {
        authUser: {
            name:"",
        },
        authPermissions:[],
    },
    mutations: {
        setAuthUser(state, user) {
            state.authUser = user;
        },

        setAuthPermissions(state, permissions) {
            state.authPermissions = permissions;
        },


    },
    actions: {
        setAuthUser({ commit }) {
            axios.post('auth-user')
                .then((response) => {
                    var user=JSON.stringify(response.data.data);
                    localStorage.setItem('user', user);
                    commit('setAuthUser', response.data.data);
                })
                .catch(() => {
                    commit('setAuthUser', {});
                });
        },
        setAuthPermissions({ commit }) {
            axios.post('auth-permissions')
                .then((response) => {  
                    commit('setAuthPermissions', response.data.data);
                })
                .catch(() => {
                    commit('authPermissions', []);
                });
        },


    },
    getters: {
        authUser : state => state.authUser,
        authPermissions : state => state.authPermissions,

    },
});
