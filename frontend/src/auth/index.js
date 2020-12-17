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

    permisos_asistencia_muelle: [
        'AsistenciaTurno','CambiarContrasenia'
    ],

    permisos_asistencia_domo: [
        'AsistenciaDomo','CambiarContrasenia'
    ],

    permisos_asistencia_alimentos: [
        'AsistenciaAlmuerzo','CambiarContrasenia'
    ],


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
                if(r.data.rol.nombre.toLowerCase() == "asistencia muelle"){
                    store.dispatch('setPermisos', self.permisos_asistencia_muelle)
                }else if(r.data.rol.nombre.toLowerCase() == "asistencia domo"){
                    store.dispatch('setPermisos', self.permisos_asistencia_domo)
                }else{
                    store.dispatch('setPermisos', self.permisos_asistencia_alimentos)
                }
            }).catch(e => {

            })
    }
}