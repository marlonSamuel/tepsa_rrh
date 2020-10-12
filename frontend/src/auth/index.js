import Axios from 'axios'
import router from '../router'
import store from '../store'

export default {

    data_refresh_token: {
        grant_type: 'refresh_token',
        refresh_token: '',
        client_id: '',
        cliente_secret: ''
    },


    getRefreshToken() {
        var token_data = $cookies.get('token_data')
        this.data_refresh_token.refresh_token = token_data.refresh_token
        this.data_refresh_token.client_id = store.state.client_id,
            this.data_refresh_token.client_secret = store.state.client_secret

        return this.data_refresh_token
    },

    saveToken(token) {
        store.dispatch('guardarToken', token)
    },

    noAcceso() {
        store.dispatch('logout')
        router.push('/login')
    },

    getUser() {
        let self = this
        store.state.services.loginService.me()
            .then(r => {
                store.dispatch('setUser', r.data)
            }).catch(e => {

            })
    }
}