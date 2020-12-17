import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/index'
import multiguard from 'vue-router-multiguard'

import Default from '@/components/Default'
import ExampleIndex from '@/components/example/Index'
import Login from '@/components/login/Index'
import CambiarContrasenia from '@/components/accesos/CambiarContrasenia'
import Usuario from '@/components/accesos/Usuario'
import Turno from '@/components/configuracion/Turno'
import Carnet from '@/components/configuracion/Carnet'
import Prestacion from '@/components/configuracion/Prestacion'
import EmpleadoIndex from '@/components/configuracion/empleado/Index'
import Cargo from '@/components/configuracion/Cargo'

import Asignacion from '@/components/administracion/Asignacion'
import AsistenciaTurno from '@/components/asistencia/Turno'
import AsistenciaTurnoIndex from '@/components/asistencia/TurnoIndex'
import AsistenciaAlmuerzo from '@/components/asistencia/Almuerzo'
import AsistenciaAlmuerzoIndex from '@/components/asistencia/AlmuerzoIndex'
import AsignacionDomo from '@/components/administracion/AsignacionDomo'
import AsistenciaDomo from '@/components/asistencia/Domo'
import AsistenciaDomoIndex from '@/components/asistencia/DomoIndex'

import PlanillaEventual from '@/components/planillas/eventual/Index'
import PlanillaEventualInfo from '@/components/planillas/eventual/Info'

Vue.use(Router)

//validar authenticacion
const isLoggedIn = (to, from, next) => {
    return store.state.is_login ? next() : next('/login')
}

const isLoggedOut = (to, from, next) => {
    return store.state.is_login ? next('/') : next()
}

//proteger rutas de los sistema, verificar si tiene acceso
const permissionValidations = (to, from, next) => {
    if(store.state.rol.toLowerCase() == 'administrador'){
        return next()
    }
    var permisos = store.state.permisos //obtener permisos del usuario
    name = to.name
    var permiso = _.includes(permisos, name) //verificar si permiso existe
    return permiso ? next() : next('/')
}

const routes = [
    { path: '*', redirect: '/' },
    { path: '/', name: 'Default', component: Default, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/login', name: 'Login', component: Login, beforeEnter: multiguard([isLoggedOut]) },
    { path: '/usuario', name: 'Usuario', component: Usuario, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/change_password', name: 'CambiarContrasenia', component: CambiarContrasenia, beforeEnter: multiguard([isLoggedIn]) },
    { path: '/turno', name: 'Turno', component: Turno, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/carnet', name: 'Carnet', component: Carnet, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/prestacion', name: 'Prestacion', component: Prestacion, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/empleado_index', name: 'EmpleadoIndex', component: EmpleadoIndex, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asignacion', name: 'Asignacion', component: Asignacion, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asignacion_domo/:id', name: 'AsignacionDomo', component: AsignacionDomo, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/cargo', name: 'Cargo', component: Cargo, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_turno', name: 'AsistenciaTurno', component: AsistenciaTurno, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_turno_index', name: 'AsistenciaTurnoIndex', component: AsistenciaTurnoIndex, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_almuerzo', name: 'AsistenciaAlmuerzo', component: AsistenciaAlmuerzo, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_almuerzo_index', name: 'AsistenciaAlmuerzoIndex', component: AsistenciaAlmuerzoIndex, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_domo', name: 'AsistenciaDomo', component: AsistenciaDomo, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/asistencia_domo_index', name: 'AsistenciaDomoIndex', component: AsistenciaDomoIndex, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/planilla_eventual', name: 'PlanillaEventual', component: PlanillaEventual, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
    { path: '/planilla_eventual_info/:id', name: 'PlanillaEventualInfo', component: PlanillaEventualInfo, beforeEnter: multiguard([isLoggedIn,permissionValidations]) },
]


const router = new Router({ routes })

export default router