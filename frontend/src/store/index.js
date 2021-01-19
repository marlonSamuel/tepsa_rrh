import Vue from 'vue'
import Vuex from 'Vuex'
import services from './services'
import global from '../components/sharedjs/global'
import moment from 'moment'
import auth from '../auth'
import Axios from 'axios'

Vue.use(Vuex)

const state = {
    services,
    global,
    usuario: {},
    rol: '',
    permisos: [],
    token: null,
    is_login: false,
    token_expired: null,
    client_id: 2,
    //base_url: 'http://www.tepsa-rrh.com/',
    base_url: 'https://167.172.158.187/tepsa-rrh/',
    client_secret: 'mCPVq5Hh0K1bRtHea1G2nj2pBuTJRBFxcBSHBKVn'
}

const mutations = {
    setUser(state, usuario) {
        state.usuario = usuario
        state.rol = usuario.rol.nombre
    },

    setPermisos(state, permisos) {
        state.permisos = permisos
    },

    setToken(state, token) {
        state.token = token
        state.is_login = true
    },

    logout(state) {
        state.is_login = false
        state.token = null
        state.rol = ''
    },

    setState(state) {
        state.is_login = false
        state.token = null
    },

    setTokenExpired(state, time) {
        state.token_expired = time
    }
}

const actions = {
    guardarToken({ commit }, data_user) {
        Axios.defaults.headers.common.Authorization = `Bearer ${data_user.access_token}`
        commit("setToken", data_user.access_token)
        $cookies.set('token_data', data_user)
    },

    logout({ commit }) {
        $cookies.remove('token_data')
        commit("logout")
    },

    autoLogin({ commit }) {
        let token = $cookies.get('token_data')
        if (token) {
            commit('setToken', token)
            auth.getUser()
        } else {
            commit('setState')
        }
    },

    setUser({ commit }, user) {
        commit('setUser', user)
    },

    setPermisos({ commit }, permisos) {
        commit('setPermisos', permisos)
    }
}

export default new Vuex.Store({
    state,
    mutations,
    actions
})