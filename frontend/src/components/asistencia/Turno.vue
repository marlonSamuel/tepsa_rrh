<template>
  <v-layout align-start v-loading="loading">
    <v-flex wrap>
      <v-toolbar flat color="white">
        <v-toolbar-title>ASISTENCIA TURNOS </v-toolbar-title>
        <v-flex v-if="isAdmin">
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-icon
                v-on="on"
                color="info"
                fab
                dark
                @click="$router.push(`asistencia_turno_index`)"
              >
                file_copy</v-icon
              >
            </template>
            <span>ir a historial de asistencias</span>
          </v-tooltip>
        </v-flex>
      </v-toolbar>
      <v-card>
        <v-flex>
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
          <qrcode-stream :torch="torchActive" @decode="onDecode" @init="onInit">
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
        <v-card-text>
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

          <v-container grid-list-md v-if="asignacion !== null && !active_qr">
            <v-flex v-if="check_salida">
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
                  <strong>TURNO: </strong># {{ asignacion.turno.numero }}
                  <br />
                  <strong>FECHA TURNO: </strong> {{ fecha | moment("DD/MM/YYYY") }} de
                  {{ asignacion.turno.hora_inicio }} a {{ asignacion.turno.hora_fin }}
                  <br />
                </div>
              </v-flex>
              <v-flex sm7 md7 xs12 v-if="asignacion.asistencia_turno !== null">
                <div>
                  <strong>ROL: </strong>
                  {{
                    asignacion.asistencia_turno.cargo_turno.cargo.nombre
                      | uppercase
                  }}
                  <br />
                  <strong>HORA ENTRADA: </strong>
                  {{
                    asignacion.asistencia_turno.hora_entrada
                      | moment("hh:mm:ss")
                  }}
                  <br />
                  <strong>BLOQUEADO: </strong>
                  {{asignacion.asistencia_turno.bloqueado ? 'SI' : 'NO'}}
                  <br />

                  <div v-if="asignacion.asistencia_turno.bloqueado">
                    <strong>DESBLOQUEADO: </strong>
                    {{asignacion.asistencia_turno.desbloqueado ? 'SI' : 'NO'}}
                    <br />
                  </div>
                  

                  <strong>HORA SALIDA: </strong>
                  <span v-if="check_salida">
                    {{
                      asignacion.asistencia_turno.hora_salida
                        | moment("hh:mm:ss")
                    }}
                  </span>
                  <span v-else class="red--text">Sin asistencia de salida</span>
                  <br />
                </div>
                <v-flex sm12 md12 xs12>
                  <v-textarea
                    v-model="form.observaciones"
                    rows="2"
                    label="Observaciones (especifique)"
                    :counter="255"
                    v-validate="'max:255'"
                    type="text"
                    data-vv-name="observaciones"
                    :error-messages="errors.collect('observaciones')"
                  >
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
                  :items="cargosTurn(asignacion.turno.numero)"
                  item-text="cargo.nombre"
                  item-value="id"
                  v-validate="'required'"
                  data-vv-name="rol"
                  :error-messages="errors.collect('rol')"
                >
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
                  :error-messages="errors.collect('bodega')"
                >
                </v-autocomplete>
              </v-flex>

              <v-flex sm2 md3 xs6 v-if="!check_salida & (!form.bloqueado || form.desbloqueado)">
                <v-divider></v-divider>
                <v-btn color="success" @click="createOrEdit"
                  ><v-icon>check_circle</v-icon> asistencia</v-btn
                >
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
  name: "asistencia_turno",
  components: {},
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
      code: "",
      error: null,
      turnos: [],
      turno: null,
      turno2: null,
      codigo: "",
      turno_id: null,
      turno_id_2: null,
      fecha: null,
      asignacion: null,
      bodegas: [],
      cargos: [],
      cargos2: [],
      active_qr: false,
      bloqueado: false,
      form: {
        id: null,
        hora_entrada: "",
        hora_salida: "",
        cargo_turno_id: null,
        detalle_asignacion_empleado_id: null,
        bodega: null,
        observaciones: "",
        salida: false,
        bloqueado: false,
        desbloqueado: false
      }
    };
  },

  created() {
    let self = this;
    self.fecha = moment().format("YYYY-MM-DD");
    self.getTurnos();
  },

  methods: {
    //obtener turnos
    getTurnos() {
      let self = this;
      self.loading = true;
      self.$store.state.services.turnoService
        .getAll()
        .then(r => {
          self.loading = false;
          self.setCurrentTurn(r.data);
        })
        .catch(e => {});
    },

    //obtener roles
    getCargos(turno_id,turno) {
      let self = this
      self.loading = true
      self.$store.state.services.turnoService
        .getCargos(turno_id)
        .then(r => {
          self.loading = false
          if(turno < 4){
            self.cargos = r.data
          }else{
            self.cargos2 = r.data
          }
        })
        .catch(e => {});
    },

    //buscar por carnet
    search() {
      let self = this;
      if (self.codigo == "" || self.codigo == null) {
        self.$toastr.error("el campo codigo no puede estar vació", "error");
        return;
      }
      self.loading = true;
      self.$store.state.services.detalleAsignacionService
        .getAsign(self.codigo, self.fecha, self.turno.id, self.turno2.id)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            self.clearData();
            return;
          }
          self.asignacion = r.data;
          self.form.detalle_asignacion_empleado_id = self.asignacion.id;
          if (self.asignacion.asistencia_turno !== null) {
            self.mapData(self.asignacion.asistencia_turno);
            self.asignacion.asistencia_turno.hora_salida !== null
              ? (self.check_salida = true)
              : (self.check_salida = false);
          }
          self.setBodegas(r.data.asignacion.planificacion.buque.no_bodegas);
        })
        .catch(e => {});
    },

    //funcion para guardar registro
    create() {
      let self = this;
      let data = self.form;
      let current_time = moment()

      const hourDiff = current_time.diff(self.turno._hora_inicio, "hours")

      if(hourDiff >= 1){
        data.bloqueado = true
      }

      self.loading = true
      self.$store.state.services.asistenciaTurnoService
        .create(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            self.clearData();
            self.active_qr = true;
            return;
          }
          this.$toastr.success(
            "asistencia entrada registrada con éxito",
            "éxito"
          );
          self.clearData();
          self.active_qr = true;
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update() {
      let self = this;
      let data = self.form
      data.salida = data.hora_entrada == null ? false :  true
      console.log(data)
      self.loading = true
      self.$store.state.services.asistenciaTurnoService
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
          self.clearData();
          self.active_qr = false;
        })
        .catch(r => {});
    },

    //mapear datos a formulario
    mapData(data) {
      let self = this;
      self.form.id = data.id
      self.form.bodega = data.bodega
      self.form.cargo_turno_id = data.cargo_turno_id
      self.form.observaciones = data.observaciones
      self.form.bloqueado = data.bloqueado
      self.form.desbloqueado = data.desbloqueado
      self.form.hora_entrada = data.hora_entrada
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit() {
      let self = this
      this.$validator.validateAll().then(result => {
        if (result) {
          if (self.form.id > 0 && self.form.id !== null) {
            self.update();
          } else {
            self.create();
          }
        }
      })
    },

    //limpiar data de formulario
    clearData() {
      let self = this;
      Object.keys(self.form).forEach(function(key, index) {
        if (typeof self.form[key] === "string") self.form[key] = "";
        else if (typeof self.form[key] === "boolean") self.form[key] = false;
        else if (typeof self.form[key] === "number") self.form[key] = null;
      });
      self.$validator.reset();
      self.codigo = null;
      self.asignacion = null;
    },

    //obtener turno segun horario
    setCurrentTurn(turns) {
      let self = this;
      var currentTime = moment();
      var extra = moment().format("YYYY-MM-DD") + " ";

      turns.forEach((t, i) => {
        var start_time = moment(extra + t.hora_inicio)
        var end_time = moment(extra + t.hora_fin)
        if (t.hora_fin < t.hora_inicio) {

        /* var extra_e =
            moment()
              .subtract(1, "d")
              .format("YYYY-MM-DD") + " ";
          var start_time = moment(extra_e + t.hora_inicio);*/

          //end_time = end_time.add(1,'d')
          start_time = start_time.subtract(1, "days")

         /* if (
            moment(end_time).format("YYYY-MM-DD") ==
            moment(currentTime).format("YYYY-MM-DD")
          ) {
            self.fecha = moment()
              .subtract(1, "d")
              .format("YYYY-MM-DD");
          }*/
        }

        if (moment(currentTime).isBetween(start_time, end_time)) {
          self.fecha = moment(start_time).format("YYYY-MM-DD")
          if(t.numero < 4){
            self.turno = t
            self.getCargos(t.id,t.numero)
            self.turno._hora_inicio = start_time
            self.turno._hora_fin = end_time
          }else{
            self.getCargos(t.id,t.numero)
            self.turno2 = t
            self.turno2._hora_inicio = start_time
            self.turno2._hora_fin = end_time
          }
        }
      })
    },

    //setear bodegas en buque
    setBodegas(no_bodegas) {
      let self = this;
      self.bodegas = [];
      for (var i = 1; i <= no_bodegas; i++) {
        self.bodegas.push({ text: i, value: i });
      }
    },

    //active qr
    activeQR() {
      let self = this;
      self.active_qr = !self.active_qr;
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

    cargosTurn(numero){
      let self = this
      if(numero < 4){
        return self.cargos
      }else{
        return self.cargos2
      }
    },

    //erorroa sicncronos
    async onInit(promise) {
      let self = this;
      try {
        await promise;
        const { capabilities } = await promise;
        self.torchNotSupported = !capabilities.torch;
        if (self.torchNotSupported) {
          self.$toastr.warning(
            "flash no soportado en este dispositivo",
            "advertencia"
          );
        }
      } catch (error) {
        console.log(error.name);
        self.active_qr = false
        self.alert = true
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
    icon() {
      if (this.torchActive) return "/flash-off.svg";
      else return "/flash-on.svg";
    },
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
