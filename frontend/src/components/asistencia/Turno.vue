<template>
  <v-layout align-start v-loading="loading">
      <v-flex wrap>
          <v-toolbar flat color="white">
              <v-toolbar-title>ASISTENCIA TURNOS </v-toolbar-title>
              <v-flex>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon v-on="on" color="info" fab dark @click="$router.push(`asistencia_turno_index`)"> file_copy</v-icon>
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
                    <qrcode-stream :torch="torchActive" @decode="onDecode" @init="onInit" >
                      <button class="buttonCam" @click="torchActive = !torchActive" :disabled="torchNotSupported">
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
                        <br />
                        <v-text-field v-model="codigo" 
                          label="Codigo de carnet"
                          type="text"
                          :append-icon="'search'"
                          @click:append="search">
                      </v-text-field>
                      </v-flex>
                    </v-layout>
                  </v-container>

                  
                  <v-container grid-list-md v-if="asignacion !== null && !active_qr">
                    <v-flex v-if="check_salida">
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
                          <strong>TURNO: </strong># {{turno.numero}}
                          <br />
                          <strong>FECHA TURNO: </strong> {{fecha}} de {{turno.hora_inicio}} a {{turno.hora_fin}}
                          <br />
                        </div>
                      </v-flex>
                      <v-flex sm7 md7 xs12 v-if="asignacion.asistencia_turno !== null">
                        <div>
                          <strong>ROL: </strong> {{asignacion.asistencia_turno.cargo_turno.cargo.nombre | uppercase}}
                          <br />
                          <strong>HORA ENTRADA: </strong> {{asignacion.asistencia_turno.hora_entrada | moment('hh:mm:ss')}}
                          <br />
                          <strong>HORA SALIDA: </strong>
                           <span v-if="check_salida">
                              {{asignacion.asistencia_turno.hora_salida | moment('hh:mm:ss')}}
                            </span>
                           <span v-else class="red--text">Sin asistencia de salida</span>
                          <br />
                        </div>
                        <v-flex sm12 md12 xs12>
                        <v-textarea v-model="form.observaciones" 
                          rows="2"
                          label="Observaciones (especifique)"
                          :counter="255"
                          v-validate="'max:255'"
                          type="text"
                          data-vv-name="observaciones"
                          :readonly="check_salida!== null ? true : false"
                          :error-messages="errors.collect('observaciones')">
                        </v-textarea>
                      </v-flex>
                      </v-flex>
                    </v-layout>
                    
                    
                    <v-layout wrap>
                        <v-flex sm4 md4 xs12 v-if="asignacion.asistencia_turno == null">
                          <v-autocomplete
                              v-model="form.cargo_turno_id"
                              label="Rol"
                              placeholder="seleccione rol"
                              :items="cargos"
                              item-text="cargo.nombre"
                              item-value="id"
                              v-validate="'required'"
                              data-vv-name="rol"
                              :error-messages="errors.collect('rol')">
                          </v-autocomplete>
                        </v-flex>
                        <v-flex sm4 md4 xs12 v-if="asignacion.asistencia_turno == null">
                          <v-autocomplete
                              v-model="form.bodega"
                              label="Bodega"
                              placeholder="seleccione numero de bodega"
                              :items="bodegas"
                              v-validate="'required'"
                              data-vv-name="bodega"
                              :error-messages="errors.collect('bodega')">
                          </v-autocomplete>
                        </v-flex>
                      
                      <v-flex sm2 md3 xs6 v-if="!check_salida">
                        <v-divider></v-divider>
                        <v-btn color="success" @click="createOrEdit"><v-icon>check_circle</v-icon> asistencia</v-btn>
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
  name: "asistencia_turno",
  components: {
  },
  props: {
      source: String
    },
  data() {
    return {
      loading: false,
      alert: true,
      torchActive: false,
      torchNotSupported: false,
      check_salida: false,
      code: '',
      error: null,
      turnos: [],
      turno: null,
      codigo: "",
      turno_id: null ,
      fecha: null,
      asignacion: null,
      bodegas: [],
      cargos: [],
      active_qr: false,
      form: {
          id: null,
          hora_entrada: "",
          hora_salida: "",
          cargo_turno_id: null,
          detalle_asignacion_empleado_id: null,
          bodega: null,
          observaciones: "",
          salida: false
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

    //obtener roles
    getCargos(turno_id){
        let self = this
        self.loading = true
        self.$store.state.services.turnoService
        .getCargos(turno_id)
        .then(r=>{
            self.loading = false
            self.cargos = r.data
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
      .getAsign(self.codigo,self.fecha,self.turno.id)
      .then(r=>{
        self.loading = false
        if(self.$store.state.global.captureError(r)){
          self.clearData()
          return
        }
        self.asignacion = r.data
        self.form.detalle_asignacion_empleado_id = self.asignacion.id
        if(self.asignacion.asistencia_turno !== null){
          self.mapData(self.asignacion.asistencia_turno)
          self.asignacion.asistencia_turno.hora_salida !== null ? self.check_salida = true : self.check_salida = false
        }
        self.setBodegas(r.data.asignacion.planificacion.buque.no_bodegas)
      }).catch(e=>{})
    },

    //funcion para guardar registro
    create(){
      let self = this
      let data = self.form
      //data.hora_entrada = moment().format('YYYY-MM-DD hh:mm:ss')
      self.loading = true
      self.$store.state.services.asistenciaTurnoService
        .create(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          this.$toastr.success('asistencia entrada registrada con éxito', 'éxito')
          self.clearData()
          self.active_qr = true
        })
        .catch(r => {});
    },

     //funcion para actualizar registro
    update(){
      let self = this
      let data = self.form
      data.salida = true
      console.log(data)
      self.loading = true
      self.$store.state.services.asistenciaTurnoService
        .update(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          this.$toastr.success('asistencia salida registrada con éxito', 'éxito')
          self.clearData()
          self.active_qr = false
        })
        .catch(r => {});
    },

    //mapear datos a formulario
    mapData(data){
        let self = this
        self.form.id = data.id
        self.form.bodega = data.bodega
        self.form.cargo_turno_id = data.cargo_turno_id
        self.form.observaciones = data.observaciones
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit(){
      let self = this
      console.log(self.form)
      this.$validator.validateAll().then((result) => {
          if (result) {
              if(self.form.id > 0 && self.form.id !== null){
                self.update()
              }else{
                self.create()
              }
           }
      });
      
    },

    //limpiar data de formulario
    clearData(){
        let self = this
        Object.keys(self.form).forEach(function(key,index) {
          if(typeof self.form[key] === "string") 
            self.form[key] = ''
          else if (typeof self.form[key] === "boolean") 
            self.form[key] = true
          else if (typeof self.form[key] === "number") 
            self.form[key] = null
        });
        self.$validator.reset()
        self.codigo = null
        self.asignacion = null
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
              var extra_e = moment().subtract(1,'d').format('YYYY-MM-DD') + ' '
              var start_time = moment(extra_e + t.hora_inicio)

              if(moment(start_time).format('YYYY-MM-DD') < moment(end_time).format('YYYY-MM-DD')){
                self.fecha = moment().subtract(1,'d').format('YYYY-MM-DD')
              }
            }
              
            
            if(moment(currentTime).isBetween(start_time, end_time)){
                self.turno = t
                self.getCargos(t.id)
            }
                
        })
    },

    //setear bodegas en buque
    setBodegas(no_bodegas){
      let self = this
      self.bodegas = []
      for(var i=1; i<=no_bodegas; i++){
        self.bodegas.push({text: i, value: i})
      }
    },

    //active qr
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
          self.torchNotSupported = !capabilities.torch
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
    icon() {
      if (this.torchActive)
        return '/flash-off.svg'
      else
        return '/flash-on.svg'
    }
      
  },
};
</script>

<style scoped>
.buttonCam {
  position: absolute;
  left: 10px;
  top: 10px;
}
.error {
  color: red;
  font-weight: bold;
}
</style>