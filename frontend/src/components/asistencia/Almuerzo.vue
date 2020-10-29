<template>
  <v-layout align-start v-loading="loading">
      <v-flex wrap>
          <v-toolbar flat color="white">
              <v-toolbar-title>ASISTENCIA ALIMENTOS </v-toolbar-title>
              <v-flex>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon v-on="on" color="info" fab dark @click="$router.push(`asistencia_almuerzo_index`)"> file_copy</v-icon>
                    </template>
                    <span>ir a historial de asistencias</span>
                </v-tooltip>
              </v-flex>
          </v-toolbar>
          <v-card>
             <v-flex>
                <v-btn :color="!active_qr?'success':'error'" @click="activeQR" small dark class="mb-2">
                  <v-icon>videocam</v-icon> {{!active_qr?'activar':'detener'}}
                </v-btn>
              </v-flex>
              <v-flex v-if="active_qr">
                <br />
                <br />
                <br />
                  <qrcode-stream size="40" :torch="torchActive" @decode="onDecode" @init="onInit" >
                    <button @click="torchActive = !torchActive" :disabled="torchNotSupported">
                      <v-icon color="white" v-if="!torchActive">flash_on</v-icon>
                      <v-icon color="white" v-else>flash_off</v-icon>
                    </button>
                  </qrcode-stream>
              </v-flex>
               <v-flex v-if="error !== null">
                      <v-alert
                        v-model="alert"
                        dismissible
                        type="error"
                        >
                        {{error}}
                    </v-alert>
                  </v-flex>
              <v-card-text>
                  
                  <v-container>
                    <v-layout>
                      <v-flex sm3 md3 xs6>
                        <v-text-field v-model="codigo" 
                          label="Codigo de carnet"
                          type="text"
                          :append-icon="'search'"
                          @click:append="search">
                      </v-text-field>
                      </v-flex>
                    </v-layout>
                  </v-container>

                  
                  <v-container grid-list-md v-if="asignacion !== null">
                    <v-flex v-if="false">
                      <v-alert
                        v-model="alert"
                        dismissible
                        type="info"
                        >
                        ASISTENCIA TOMADA
                    </v-alert>
                  </v-flex>
                    <v-layout wrap>
                      <v-flex sm5 md5 xs12>
                        <div>
                          <strong>EMPLEADO: </strong> {{asignacion.empleado.primer_nombre | uppercase}}
                                                    {{asignacion.empleado.segundo_nombre | uppercase}}
                                                    {{asignacion.empleado.primer_apellido | uppercase}}
                                                    {{asignacion.empleado.segundo_apellido | uppercase}}
                          <br />
                          <strong>BUQUE: </strong>{{asignacion.asignacion.planificacion.buque.nombre | uppercase}}
                          <br />
                          <strong>FECHA ATRAQUE: </strong> {{asignacion.asignacion.planificacion.fecha_atraque | moment('DD/MM/YYYY')}}
                          <br />
                          <strong>TURNO: </strong># {{asignacion.turno.numero}}
                          <br />
                          <strong>FECHA TURNO: </strong> {{fecha}} de {{turno.hora_inicio}} a {{turno.hora_fin}}
                          <br />
                        </div>
                      </v-flex>
                    </v-layout>
                  </v-container>
                  
              </v-card-text>
          </v-card>
      </v-flex>
  </v-layout>
</template>

<script>
import moment from 'moment'
export default {
  name: "asistencia_almuerzo",
  components: {
  },
  props: {
      source: String
    },
  data() {
    return {
      loading: false,
      alert: true,
      active_qr: false,
      torchActive: false,
      torchNotSupported: false,
      code: '',
      error: null,
      turno: null,
      codigo: "",
      turno_id: null ,
      fecha: null,
      asignacion: null,
      form: {
          id: null,
          detalle_asignacion_empleado_id: null,
          created_at: ""
      }
    };
  },

  created() {
    let self = this
    self.fecha = moment().format('YYYY-MM-DD')
    self.getTurnos()
  },

  methods: {
      //obtener turnos
    getTurnos(){
        let self = this
        self.loading = true
        self.$store.state.services.turnoService
        .getAll()
        .then(r=>{
            self.loading = false
            self.setCurrentTurn(r.data)
        }).catch(e=>{})
    },

    //buscar por carnet
    search(){
      let self = this
      if(self.codigo == "" || self.codigo == null){
        self.$toastr.error('el campo codigo no puede estar vació','error');
        return
      }
      self.loading = true
      self.$store.state.services.detalleAsignacionService
      .getAsign(self.codigo,'2020-10-14',1)
      .then(r=>{
        self.loading = false
        self.active_qr = true
        if(self.$store.state.global.captureError(r)){
          self.clearData()
          return
        }
        self.asignacion = r.data
        self.form.detalle_asignacion_empleado_id = self.asignacion.id
        self.create()
      }).catch(e=>{})
    },

    //obtener turno segun horario
    setCurrentTurn(turns){
        let self = this
        var currentTime = moment()
        var extra = moment().format('YYYY-MM-DD') + ' '

        turns.forEach((t,i)=>{
            var start_time = moment(extra + t.hora_inicio)
            var end_time = moment(extra + t.hora_fin)
            if(t.hora_fin < t.hora_inicio){
              var extra_e = moment().add(1,'d').format('YYYY-MM-DD') + ' '
              var end_time = moment(extra_e + t.hora_fin)

              if(moment(start_time).format('YYYY-MM-DD') == moment(end_time).format('YYYY-MM-DD'))
                self.fecha = moment().subtract(1,'d').format('YYYY-MM-DD')
            }
              
            if(moment(currentTime).isBetween(start_time, end_time)){
                self.turno = t
            }
                
        })
    },

    //funcion para guardar registro
    create(){
      let self = this
      let data = self.form
      //data.hora_entrada = moment().format('YYYY-MM-DD hh:mm:ss')
      self.loading = true
      self.$store.state.services.asistenciaAlmuerzoService
        .create(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          this.$toastr.success('asistencia alimento registrada con éxito', 'éxito')
          self.clearData()
        })
        .catch(r => {});
    },


    //limpiar data de formulario
    clearData(){
        let self = this
        self.codigo = null
        self.asignacion = null
    },

    activeQR(){
      let self = this
      self.active_qr = !self.active_qr
    },

    //decode
    onDecode (result) {
        console.log(result)
        let self = this
        navigator.vibrate([500])
        self.codigo = result
        self.active_qr = false
        self.search()
    },

    //erorroa sicncronos
    async onInit (promise) {
      let self = this
      try {
          await promise
          const { capabilities } = await promise
          this.torchNotSupported = !capabilities.torch

          if(self.torchNotSupported){
            self.$toastr.warning("flash no soportado en este dispositivo","advertencia");
          }
      } catch (error) {
          console.log(error.name)
          if (error.name === 'NotAllowedError') {
          this.error = "ERROR: necesitas permiso para utilizar la camara"
          } else if (error.name === 'NotFoundError') {
          this.error = "ERROR: camara inexistente en este dispositivo"
          } else if (error.name === 'NotSupportedError') {
          this.error = "ERROR: necesitas cerficiado de seguridad (HTTPS, localhost)"
          } else if (error.name === 'NotReadableError') {
          this.error = "ERROR: la camara esta en uso?"
          } else if (error.name === 'OverconstrainedError') {
          this.error = "ERROR: las camaras no son compatibles"
          } else if (error.name === 'StreamApiNotSupportedError') {
          this.error = "ERROR: Stream API no es soportada por el navegador, de preferencia utilize google chrome"
          } else if (error.name == 'InsecureContextError') {
              this.error = "ERROR: se necesita certificado de seguridad (HTTS,loclhos)"
          }
      }
    }
  },

  computed: {
      
  },
};
</script>

<style scoped>
button {
  position: absolute;
  left: 10px;
  top: 10px;
}
.error {
  color: red;
  font-weight: bold;
}
</style>