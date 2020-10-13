import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/index'
import multiguard from 'vue-router-multiguard'

import Default from '@/components/Default'
import ExampleIndex from '@/components/example/Index'
import Login from '@/components/login/Index'
import CambiarContrasenia from '@/components/accesos/CambiarContrasenia'
import Turno from '@/components/configuracion/Turno'
import Carnet from '@/components/configuracion/Carnet'

Vue.use(Router)

//validar authenticacion
const isLoggedIn = (to, from, next) => {
    return store.state.is_login ? next() : next('/login')
}

const isLoggedOut = (to, from, next) => {
    return store.state.is_login ? next('/') : next()
}

const routes = [
    { path: '*', redirect: '/' },
    { path: '/', name: 'Default', component: Default, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/example_index', name: 'ExampleIndex', component: ExampleIndex, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/login', name: 'Login', component: Login, beforeEnter: multiguard([isLoggedOut]) },
    { path: '/change_password', name: 'CambiarContrasenia', component: CambiarContrasenia, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/turno', name: 'Turno', component: Turno, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/carnet', name: 'Carnet', component: Carnet, beforeEnter: multiguard([isLoggedIn]) },
]


const router = new Router({ routes })

export default router