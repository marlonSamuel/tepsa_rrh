import Axios from 'axios'
import VueCookies from 'vue-cookies'
import store from './index'
import auth from '../auth'
import moment from 'moment'
import { isNullOrUndefined } from 'util';

import exampleService from '../services/ExampleService'
import LoginService from '../services/LoginService'
import UsuarioService from '../services/UsuarioService'
import TurnoService from '../services/TurnoService'
import CarnetService from '../services/CarnetService'
import PrestacionService from '../services/PrestacionService'
import EmpleadoService from '../services/EmpleadoService'
import CargoService from '../services/CargoService'
import AsignacionService from '../services/AsignacionService'
import BuqueService from '../services/BuqueService'
import PlanificacionService from '../services/PlanificacionService'
import DetalleAsignacionService from '../services/DetalleAsignacionService'
import EmpleadoPrestacionService from '../services/EmpleadoPrestacionService'
import AsistenciaTurnoService from '../services/AsistenciaTurnoService'
import AsistenciaAlmuezoService from '../services/AsistenciaAlmuerzoService'


let baseUrl = 'http://www.tepsa-rrh.com/' //base url desarrollo

//let baseUrl = 'https://167.172.158.187/tepsa-rrh/' //url production

let token_data = $cookies.get('token_data')

// Axios Configuration
Axios.defaults.headers.common.Accept = 'application/json'
if (token_data !== null) {
    Axios.defaults.headers.common.Authorization = `Bearer ${token_data.access_token}`
}

const instance = Axios.create();
Axios.interceptors.response.use(response => {
    return response
}, error => {
    if (error.response.status === 401) {
        var token_data = $cookies.get('token_data')
        if (isNullOrUndefined(token_data)) { return error }
        var original_request = error.config
        return refreshToken().then(res => {
            auth.saveToken(res.data)
            original_request.headers['Authorization'] = 'Bearer ' + res.data.access_token
            return Axios.request(original_request)
        })
    }

    return error
});

function refreshToken() {
    var data = auth.getRefreshToken()
    return new Promise(function (resolve, reject) {
        instance.post(baseUrl + 'oauth/token', data)
            .then(r => {
                resolve(r)
            }).catch(e => {
                reject(r)
            })
    })
}

instance.interceptors.response.use(response => {
    return response
}, error => {
    if (error.response.status === 401) {
        auth.noAcceso()
    }
    return error
});


export default {
    exampleService: new exampleService(Axios),
    loginService: new LoginService(Axios, baseUrl),
    usuarioService: new UsuarioService(Axios, baseUrl),
    turnoService: new TurnoService(Axios, baseUrl),
    carnetService: new CarnetService(Axios, baseUrl),
    prestacionService: new PrestacionService(Axios, baseUrl),
    empleadoService: new EmpleadoService(Axios, baseUrl),
    cargoService: new CargoService(Axios, baseUrl),
    asignacionService: new AsignacionService(Axios, baseUrl),
    buqueService: new BuqueService(Axios, baseUrl),
    planificacionService: new PlanificacionService(Axios, baseUrl),
    detalleAsignacionService: new DetalleAsignacionService(Axios, baseUrl),
    empleadoPrestacionService: new EmpleadoPrestacionService(Axios, baseUrl),
    asistenciaTurnoService: new AsistenciaTurnoService(Axios, baseUrl),
    asistenciaAlmuerzoService: new AsistenciaAlmuezoService(Axios, baseUrl),
}