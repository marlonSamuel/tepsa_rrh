<template>
  <v-layout align-start v-loading="loading">
      <v-flex wrap>
          <v-toolbar flat color="white">
              <v-toolbar-title>ASISTENCIA TURNOS </v-toolbar-title>
          </v-toolbar>
          <v-card>
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
                  <!--<qrcode-stream size="40" @decode="onDecode" @init="onInit" />-->
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
                    </div>
                    
                    <v-layout wrap>
                      <v-flex sm4 md4 xs12>
                        <v-autocomplete
                            v-model="form.cargo_id"
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
                      <v-flex sm4 md4 xs12>
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
                      <v-flex sm2 md3 xs6>
                        <v-divider></v-divider>
                        <v-btn color="success"><v-icon>check_circle</v-icon> asistencia</v-btn>
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
      form: {
          id: null,
          hora_entrada: "",
          hora_salida: "",
          cargo_turno_id: null,
          detalle_asignacion_empleado_id: null,
          bodega: null
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
      .getAsign(self.codigo,'2020-10-14',1)
      .then(r=>{
        self.loading = false
        if(self.$store.state.global.captureError(r)){
            return
        }
        self.asignacion = r.data
        self.setBodegas(r.data.asignacion.planificacion.buque.no_bodegas)
      }).catch(e=>{})
    },

    //obtener turno segun horario
    setCurrentTurn(turns){
        let self = this
        var currentTime = moment();
        var extra = moment().format('YYYY-MM-DD') + ' ';

        turns.forEach((t,i)=>{
            var start_time = moment(extra + t.hora_inicio);
            var end_time = moment(extra + t.hora_fin);
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
    //decode
    onDecode (result) {
        console.log(result)
        let self = this
        self.codigo = result
    },

    //erorroa sicncronos
    async onInit (promise) {
    try {
        await promise
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