<template>
  <v-layout align-start v-loading="loading">
    <v-flex wrap>
      <v-toolbar flat color="white">
        <v-toolbar-title>ASISTENCIA DOMO </v-toolbar-title>
        <v-flex v-if="isAdmin">
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-icon
                v-on="on"
                color="info"
                fab
                dark
                @click="$router.push(`asistencia_domo_index`)"
              >
                file_copy</v-icon
              >
            </template>
            <span>ir a historial de asistencias</span>
          </v-tooltip>
        </v-flex>
      </v-toolbar>
      <v-card>

          <v-card flat>
                <v-bottom-nav
                :active.sync="tipo_asistencia"
                :value="true"
                color="transparent"
                >
                <v-btn
                    color="teal"
                    flat
                    value="entrada"
                    @input="setCurrentTurn"
                >
                    <span>Asistencia de entrada</span>
                    <v-icon color="info">watch_later</v-icon>
                </v-btn>

                <v-btn
                    color="teal"
                    flat
                    value="salida"
                    @input="setCurrentTurn"
                >
                    <span>Asistencia de salida</span>
                    <v-icon color="warning">watch_later</v-icon>
                </v-btn>
                </v-bottom-nav>
                <v-flex v-if="tipo_asistencia !== ''">
                    <v-alert v-model="alert" :type="tipo_asistencia=='entrada'?'info':'warning'">
                        ATENCIÓN: Se está tomando asistencia de {{ tipo_asistencia | uppercase }}, <br />
                        Fecha de asignación: {{fecha | moment('dddd[] DD [de] MMMM [del año] YYYY')}}<br />
                        Fecha y Hora actual: {{ this.momentInstance.format('dddd[] DD [de] MMMM [del año] YYYY HH:mm:ss') }}<br />
                        Turno: {{turno}}
                    </v-alert>
                    </v-flex>
            </v-card>

        <v-flex v-if="tipo_asistencia !== ''">
          <v-btn
            :color="!active_qr ? 'success' : 'error'"
            @click="activeQR"
            small
            dark
            class="mb-2"
          >
            <v-icon>videocam</v-icon> {{ !active_qr ? "activar" : "detener" }}
          </v-btn>
        </v-flex>
        <v-flex v-if="active_qr">
          <br />
          <br />
          <br />
          <qrcode-stream
            size="40"
            :torch="torchActive"
            @decode="onDecode"
            @init="onInit"
          >
            <button
              class="buttonCam"
              @click="torchActive = !torchActive"
              :disabled="torchNotSupported"
            >
              <v-icon color="white" v-if="!torchActive">flash_on</v-icon>
              <v-icon color="white" v-else>flash_off</v-icon>
            </button>
          </qrcode-stream>
        </v-flex>
        <v-flex v-if="error !== null">
          <v-alert v-model="alert" dismissible type="error">
            {{ error }}
          </v-alert>
        </v-flex>
        <v-card-text v-if="tipo_asistencia !== ''">
          <v-container>
            <v-layout>
              <v-flex sm3 md3 xs6>
                <br />
                <v-text-field
                  v-model="codigo"
                  label="Codigo de carnet"
                  type="text"
                  :append-icon="'search'"
                  @click:append="search"
                >
                </v-text-field>
              </v-flex>
            </v-layout>
          </v-container>

          <v-container grid-list-md v-if="asignacion !== null">
            <v-flex v-if="false">
              <v-alert v-model="alert" dismissible type="info">
                ASISTENCIA TOMADA
              </v-alert>
            </v-flex>
            <v-flex v-if="form.bloqueado & !form.desbloqueado">
              <v-alert v-model="alert" dismissible color="red" type="error">
                EMPLEADO BLOQUEADO POR RETRASO, SI DESEA DESBLOQUEARLO COMUNIQUESE CON EL ADMINISTRADOR
              </v-alert>
            </v-flex>
            <v-layout wrap>
              <v-flex sm5 md5 xs12>
                <div>
                  <strong>EMPLEADO: </strong>
                  {{ asignacion.empleado.primer_nombre | uppercase }}
                  {{ asignacion.empleado.segundo_nombre | uppercase }}
                  {{ asignacion.empleado.primer_apellido | uppercase }}
                  {{ asignacion.empleado.segundo_apellido | uppercase }}
                  <br />
                  <strong>BUQUE: </strong
                  >{{
                    asignacion.asignacion.planificacion.buque.nombre | uppercase
                  }}
                  <br />
                  <strong>FECHA ATRAQUE: </strong>
                  {{
                    asignacion.asignacion.planificacion.fecha_atraque
                      | moment("DD/MM/YYYY")
                  }}
                  <br />
                  <strong>FECHA: </strong> {{ fecha | moment('DD/MM/YYYY')}}
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
import moment from "moment";
export default {
  name: "asistencia_domo",
  components: {},
  props: {
    source: String
  },
  data() {
    return {
      momentInstance: moment(),
      loading: false,
      tipo_asistencia: '',
      alert: true,
      active_qr: false,
      torchActive: false,
      torchNotSupported: false,
      code: "",
      error: null,
      turno: '',
      codigo: "",
      fecha: null,
      asignacion: null,
      asistencias: [
          {value: 'E',text:"Asistencia entrada"},
          {value: 'S', text: "Asistencia salida"}
      ],
      form: {
        id: null,
        asignacion_domo_id: null,
        turno: null,
        salida: false,
        tipo_asistencia: null,
        start: null,
        bloqueado: false,
        desbloqueado: false,
      }
    };
  },

  created() {
    let self = this;
    self.fecha = moment().format("YYYY-MM-DD")
  },

  methods: {

    //buscar por carnet
    search() {
      let self = this;
      if (self.codigo == "" || self.codigo == null) {
        self.$toastr.error("el campo codigo no puede estar vació", "error");
        return;
      }
      self.loading = true;
      self.$store.state.services.asignacionDomoService
        .getAsign(self.codigo, self.fecha, self.turno.id)
        .then(r => {
          self.loading = false;
          //self.active_qr = true;
          if (self.$store.state.global.captureError(r)) {
            self.clearData();
            return;
          }
          self.asignacion = r.data;
          self.form.asignacion_domo_id = self.asignacion.id
          self.validateStatus()
        })
        .catch(e => {});
    },

    //validar si es entrada o salida según turno
    validateStatus(){
      let self = this
      let asistencia = self.asignacion.asistencia_domo.filter(x=>x.turno == self.form.turno)
      if(asistencia.length > 0){
          self.form.id = asistencia[0].id
          self.form.bloqueado = asistencia[0].bloqueado
          self.form.desbloqueado = asistencia[0].desbloqueado

          if(self.form.bloqueado & !self.form.desbloqueado){
            return
          }else if(self.form.bloqueado & self.form.desbloqueado){
            if(asistencia[0].hora_entrada == null){
              self.update()
              return
            }
          }
          
          if(asistencia[0].hora_entrada !== null){
            if(self.tipo_asistencia == "entrada"){
              self.$toastr.error('entrada ya fue registrada para el turno '+self.form.turno,'error')
              self.asignacion = null
              return
            }

            if(asistencia[0].hora_salida != null){
              if(self.tipo_asistencia == "salida"){
                self.$toastr.warning('entrada y salida ya fueron registradas para el turno '+self.form.turno,'información')
                self.asignacion = null
                return
              }
            }else{
              self.form.salida = true
              self.update()
            }
          }
      }else{
        if(self.tipo_asistencia == 'salida'){
          self.$toastr.error("no se puede registrar asistencia de salida porque no se encontró asistencia de entrada","error")
          return
        }
        self.create()
      }
    },

     //funcion para guardar registro
    create() {
      let self = this
      let data = self.form
      //data.hora_entrada = moment().format('YYYY-MM-DD hh:mm:ss')
      let current_time = moment()

      const hourDiff = current_time.diff(self.form.start, "hours")
      if(hourDiff >= 1){
        data.bloqueado = true
      }

      self.loading = true
      self.$store.state.services.asistenciaDomoService
        .create(data)
        .then(r => {
          self.loading = false
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success(
            "asistencia alimento registrada con éxito",
            "éxito"
          );
          self.clearData()
          self.active_qr = true
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update() {
      let self = this;
      let data = self.form
      console.log(data)
      self.loading = true
      self.$store.state.services.asistenciaDomoService
        .update(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success(
            data.salida ? "salida registrada con éxito" : "entrada registrada con éxito",
            "éxito"
          );
          self.clearData()
          self.active_qr = true
        })
        .catch(r => {});
    },

    //obtener turno segun horario
    setCurrentTurn() {
      let self = this
      
      var currentTime = moment()
      //var extra = moment().format("YYYY-MM-DD") + " "
      let start = moment().format('YYYY-MM-DD') + " 00:00:00"
      let end = moment().format('YYYY-MM-DD') + " 08:00:00"


      let start_1 = moment().format('YYYY-MM-DD') + " 07:00:00"
      let end_1 = moment().format('YYYY-MM-DD') + " 19:00:00"

      let start_2 = moment().format('YYYY-MM-DD') + " 19:00:00"
      let end_2 = moment().add(1,'d').format('YYYY-MM-DD') + " 07:00:00"

      if(self.tipo_asistencia == 'salida' & moment(currentTime).isBetween(start, end)){
          self.fecha = moment().subtract(1,'d').format('YYYY-MM-DD')
      }else{
          self.fecha = moment().format('YYYY-MM-DD')
      }

      if(self.tipo_asistencia == 'entrada' & currentTime < moment(end_1)){
          self.turno = '07:00 - 19:00'
          self.form.turno = 1
          self.form.start = start_1
      }else if(self.tipo_asistencia == 'salida' & moment(currentTime).isBetween(moment(start_1), moment(end_1).add(1,'h'))){
          self.turno = '07:00 - 19:00'
          self.form.turno = 1
          self.form.start = start_1
      }else{
          self.turno = '19:00 - 07:00'
          self.form.turno = 2
          self.form.start = start_2
      }
      console.log(self.form.turno)
    },

    //limpiar data de formulario
    clearData() {
      let self = this
      self.codigo = null
      self.asignacion = null
    },

    activeQR() {
      let self = this;
      self.active_qr = !self.active_qr
    },

    //decode
    onDecode(result) {
      console.log(result);
      let self = this;
      navigator.vibrate([500]);
      self.codigo = result;
      self.active_qr = false;
      self.search();
    },

    //erorroa sicncronos
    async onInit(promise) {
      let self = this;
      try {
        await promise;
        const { capabilities } = await promise;
        this.torchNotSupported = !capabilities.torch;

        if (self.torchNotSupported) {
          self.$toastr.warning(
            "flash no soportado en este dispositivo",
            "advertencia"
          );
        }
      } catch (error) {
        console.log(error.name);
        if (error.name === "NotAllowedError") {
          this.error = "ERROR: necesitas permiso para utilizar la camara";
        } else if (error.name === "NotFoundError") {
          this.error = "ERROR: camara inexistente en este dispositivo";
        } else if (error.name === "NotSupportedError") {
          this.error =
            "ERROR: necesitas cerficiado de seguridad (HTTPS, localhost)";
        } else if (error.name === "NotReadableError") {
          this.error = "ERROR: la camara esta en uso?";
        } else if (error.name === "OverconstrainedError") {
          this.error = "ERROR: las camaras no son compatibles";
        } else if (error.name === "StreamApiNotSupportedError") {
          this.error =
            "ERROR: Stream API no es soportada por el navegador, de preferencia utilize google chrome";
        } else if (error.name == "InsecureContextError") {
          this.error =
            "ERROR: se necesita certificado de seguridad (HTTS,loclhos)";
        }
      }
    }
  },

  computed: {
    isAdmin(){
      let self = this
      return self.$store.state.rol == 'administrador' ? true : false
    }
  }
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
