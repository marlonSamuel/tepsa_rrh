<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>ASIGNACIONES ENTREADA A PORTUARIA </v-toolbar-title>
        <v-divider class="mx-2" inset vertical></v-divider><v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Buscar"
          single-line
          hide-details
        ></v-text-field>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog_turn" max-width="1000px">
          <v-card>
            <v-card-title v-if="planificacion !== null">
              <div>
                <span class="headline"
                  >TURNOS CONTROL DE INGRESO/EGRESO DE VISITA AL BUQUE</span
                ><br />

                <strong>BUQUE: </strong
                >{{ planificacion.buque.nombre | uppercase }}<br />
                <strong v-if="planificacion !== null"
                  >FECHA ZARPE: {{ planificacion.fecha_zarpe }}</strong
                >
              </div>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout row wrap>
                  <v-flex
                    sm3
                    md3
                    v-for="(value, key, index) in turnos_print"
                    :key="index"
                  >
                    <v-card>
                      <v-card-title>
                        {{ value.fecha | moment("DD/MM/YYYY") }}
                      </v-card-title>
                      <v-card-text>
                        <v-btn
                          v-for="t in value.turnos"
                          :key="t.id"
                          color="info"
                          small
                          class="mb-2"
                          @click="print(t)"
                          :disabled="blockTurn(t)"
                        >
                          <v-icon>print</v-icon> turno {{ t.numero }}
                        </v-btn>
                      </v-card-text>
                    </v-card>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialog" full-width persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" small dark class="mb-2" v-on="on"
              ><v-icon>add</v-icon> Nuevo</v-btn
            >
          </template>

          <v-card>
            <v-card-title>
              <span class="headline">{{ setTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs4>
                    <v-card>
                      <v-form data-vv-scope="form_a">
                        <v-card-text class="px-0">
                          <v-card-title>
                            <span class="headline">Buscar</span>
                          </v-card-title>

                          <v-flex xs12 sm12 md12>
                            <v-text-field
                              v-model="form_a.fecha_atraque"
                              label="Fecha atraque"
                              v-validate="'required'"
                              type="date"
                              data-vv-name="fecha_atraque"
                              data-vv-as="fecha de atraque"
                              :readonly="form.id !== null ? true : false"
                              :error-messages="
                                errors.collect('form_a.fecha_atraque')
                              "
                            >
                            </v-text-field>
                          </v-flex>
                          <v-flex xs12 sm12 md12>
                            <v-autocomplete
                              v-model="form_a.buque_id"
                              label="Buque"
                              placeholder="seleccione buque"
                              :items="buques"
                              item-text="nombre"
                              item-value="idBuque"
                              v-validate="'required'"
                              data-vv-name="buque_id"
                              data-vv-as="buque"
                              :readonly="form.id !== null ? true : false"
                              :error-messages="
                                errors.collect('form_a.buque_id')
                              "
                            >
                              >
                            </v-autocomplete>
                          </v-flex>
                        </v-card-text>
                      </v-form>

                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          :disabled="form.id !== null ? true : false"
                          color="green darken-1"
                          flat
                          @click="validateForm('form_a')"
                          ><v-icon>search</v-icon> buscar</v-btn
                        >
                      </v-card-actions>
                    </v-card>
                  </v-flex>
                  <v-flex xs8 v-if="planificacion !== null">
                    <v-card>
                      <v-card-title>
                        <div>
                          <span class="headline">Detalle asignación</span><br />
                          <strong v-if="planificacion !== null"
                            >Fecha de zarpe:</strong
                          >
                          {{ planificacion.buque.nombre }}<br />
                          <strong v-if="planificacion !== null"
                            >Fecha de zarpe:</strong
                          >
                          {{ planificacion.fecha_zarpe | moment("DD/MM/YYYY") }}
                        </div>
                      </v-card-title>
                      <v-card-text class="px-0">
                        <v-form data-vv-scope="form">
                          <v-card-text class="px-0">
                            <v-container grid-list-md>
                              <v-layout wrap>
                                <v-flex xs12 sm4 md4>
                                  <v-autocomplete
                                    v-model="form.turno_id"
                                    label="Turno"
                                    placeholder="seleccione turno"
                                    :items="turnos"
                                    item-text="turno"
                                    item-value="id"
                                    v-validate="'required'"
                                    data-vv-name="turno"
                                    :error-messages="
                                      errors.collect('form.turno')
                                    "
                                    @change="detailChange"
                                  >
                                    >
                                  </v-autocomplete>
                                </v-flex>
                                <v-flex xs12 sm4 md4>
                                  <v-text-field
                                    v-model="form.fecha"
                                    label="Fecha"
                                    v-validate="'required'"
                                    type="date"
                                    data-vv-name="fecha"
                                    :error-messages="
                                      errors.collect('form.fecha')
                                    "
                                    @input="detailChange"
                                  >
                                  </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm4 md4>
                                  <v-autocomplete
                                    v-model="form.empleado_id"
                                    label="Empleado"
                                    placeholder="seleccione empleado"
                                    :items="empleados"
                                    @input="changeEmpleado"
                                    item-text="empleado"
                                    item-value="idEmpleado"
                                    v-validate="'required'"
                                    data-vv-name="empleado"
                                    :error-messages="
                                      errors.collect('form.empleado')
                                    "
                                  >
                                    >
                                  </v-autocomplete>
                                </v-flex>

                                <v-flex xs12 sm4 md4 v-if="empleado_carnet == null">
                                  <v-autocomplete
                                    v-model="form.carnet_id"
                                    label="Carnet"
                                    placeholder="seleccione carnet"
                                    :items="carnets"
                                    item-text="codigo"
                                    item-value="id"
                                    v-validate="'required'"
                                    data-vv-name="carnet"
                                    :error-messages="
                                      errors.collect('form.carnet')
                                    "
                                  >
                                    >
                                  </v-autocomplete>
                                </v-flex>

                                <v-flex xs12 sm4 md4 v-else>
                                  <v-text-field
                                    v-model="empleado_carnet.carnet.codigo"
                                    label="Carnet"
                                    placeholder="seleccione carnet"
                                    readonly
                                  >
                                    
                                  </v-text-field>
                                </v-flex>

                                <v-flex xs6 sm2 md2>
                                  <v-btn
                                    :disabled="
                                      planificacion !== null ? false : true
                                    "
                                    block
                                    dark
                                    color="green darken-1"
                                    @click="validateForm('form')"
                                    ><v-icon>add</v-icon> agregar</v-btn
                                  >
                                </v-flex>
                              </v-layout>
                            </v-container>
                          </v-card-text>
                        </v-form>
                        <v-flex v-if="detalle_asignacion.length > 0">
                          <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-icon
                                v-on="on"
                                color="info"
                                fab
                                dark
                                @click="printAll"
                              >
                                print</v-icon
                              >
                            </template>
                            <span>Imprimir asignacion</span>
                          </v-tooltip>
                        </v-flex>
                        <v-data-table
                          :headers="headers_details"
                          :items="detalle_asignacion"
                          :search="search"
                          :expand="false"
                          class="elevation-1"
                          disable-initial-sort
                          hide-actions
                        >
                          <template v-slot:items="props">
                            <td class="text-xs-left">
                              #{{ props.item.turno.numero }}-
                              {{
                                ("2020-04-05 " + props.item.turno.hora_inicio)
                                  | moment("h:mm a")
                              }}-
                              {{
                                ("2020-04-05 " + props.item.turno.hora_fin)
                                  | moment("h:mm a")
                              }}
                            </td>
                            <td class="text-xs-left">
                              {{ props.item.fecha | moment("DD/MM/YYYY") }}
                            </td>
                            <td class="text-xs-left">
                              {{ props.item.empleado.primer_nombre }}
                              {{ props.item.empleado.segundo_nombre }}
                              {{ props.item.empleado.primer_apellido }}
                              {{ props.item.empleado.segundo_apellido }}
                            </td>
                            <td class="text-xs-left">
                              {{ props.item.carnet.codigo }}
                            </td>
                            <v-tooltip top>
                              <template v-slot:activator="{ on }">
                                <v-icon
                                  v-on="on"
                                  color="info"
                                  fab
                                  dark
                                  @click="singlePrint(props.item)"
                                >
                                  print</v-icon
                                >
                              </template>
                              <span>Imprimir asignacion empleado</span>
                            </v-tooltip>
                            <v-tooltip top>
                              <template v-slot:activator="{ on }">
                                <v-icon
                                  v-on="on"
                                  color="error"
                                  fab
                                  dark
                                  @click="removeDetail(props.item)"
                                >
                                  remove_circle</v-icon
                                >
                              </template>
                              <span>Remover</span>
                            </v-tooltip>
                          </template>
                        </v-data-table>
                      </v-card-text>
                    </v-card>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red darken-1" flat @click="close">Volver</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="items"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:items="props">
          <td class="text-xs-left">
            {{ props.item.planificacion.buque.nombre | uppercase }}
          </td>
          <td class="text-xs-left">
            {{ props.item.planificacion.fecha_atraque | moment("DD/MM/YYYY") }}
          </td>
          <td class="text-xs-left">
            {{ props.item.planificacion.fecha_zarpe | moment("DD/MM/YYYY") }}
          </td>

          <td class="text-xs-left">
            <v-tooltip top v-if="!props.item.terminada">
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="info"
                  fab
                  dark
                  @click="get(props.item)"
                >
                  print</v-icon
                >
              </template>
              <span>imprimir</span>
            </v-tooltip>
            <v-tooltip top v-if="!props.item.terminada">
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="green"
                  fab
                  dark
                  @click="$router.push('asignacion_domo/'+props.item.id)"
                >
                  file_copy</v-icon
                >
              </template>
              <span>asignacion domo</span>
            </v-tooltip>
            <v-tooltip top v-if="!props.item.terminada">
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="warning"
                  fab
                  dark
                  @click="edit(props.item)"
                >
                  edit</v-icon
                >
              </template>
              <span>Editar</span>
            </v-tooltip>
            <v-tooltip top v-if="!props.item.terminada">
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="success"
                  fab
                  dark
                  @click="releaseCards(props.item)"
                >
                  lock_open</v-icon
                >
              </template>
              <span>Liberar carnets</span>
            </v-tooltip>
            <v-tooltip top  v-if="!props.item.terminada">
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="error"
                  fab
                  dark
                  @click="destroy(props.item)"
                >
                  delete</v-icon
                >
              </template>
              <span>Eliminar</span>
            </v-tooltip>
            <div v-if="props.item.terminada">
              <v-chip color="success" text-color="white">terminada</v-chip>
            </div>
          </td>
        </template>
        <template v-slot:no-data>
          <v-btn color="primary" @click="getAll">Reset</v-btn>
        </template>
      </v-data-table>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from "moment";
export default {
  name: "asignacion",
  props: {
    source: String
  },
  data() {
    return {
      dialog: false,
      dialog_turn: false,
      search: "",
      loading: false,
      items: [],
      buques: [],
      turnos: [],
      carnets: [],
      empleados: [],
      detalle_asignacion: [],
      turnos_print: [],
      planificacion: null,
      empleado_carnet: null,
      headers: [
        { text: "Buque", value: "buque" },
        { text: "Fecha de atraque", value: "fecha_atraque" },
        { text: "Fecha de sarpe", value: "fecha_sarpe" },
        { text: "Acciones", value: "", sortable: false }
      ],
      headers_details: [
        { text: "turno", value: "turno" },
        { text: "fecha", value: "fecha" },
        { text: "Empleado", value: "empleado" },
        { text: "Carnet", value: "carnet" },
        { text: "Accion", value: "", sortable: false }
      ],
      form: {
        id: null,
        planificacion_id: null,
        detalle_id: null,
        fecha: null,
        empleado_id: null,
        turno_id: null,
        carnet_id: null
      },
      form_a: {
        fecha_atraque: null,
        buque_id: null
      }
    };
  },

  created() {
    let self = this;
    self.getAll();
    self.getBuques();
    self.getCarnets();
    self.getTurnos();
    self.getEmpleados();
  },

  methods: {
    getAll() {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .getAll()
        .then(r => {
          self.loading = false
          console.log("llego");
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.items = r.data
        })
        .catch(r => {});
    },

    //obtener buques
    getBuques() {
      let self = this;
      self.loading = true;
      self.$store.state.services.buqueService
        .getAll()
        .then(r => {
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.buques = r.data
        })
        .catch(r => {});
    },

    //obtener buques
    getEmpleados() {
      let self = this;
      self.loading = true;
      self.$store.state.services.empleadoService
        .getAll()
        .then(r => {
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          r.data.map(obj => ({
            ...(obj.empleado =
              obj.primer_nombre +
              " " +
              obj.segundo_nombre +
              " " +
              obj.primer_apellido +
              " " +
              obj.segundo_apellido)
          }));
          self.empleados = r.data;
        })
        .catch(r => {});
    },

    //obtener buques
    getTurnos() {
      let self = this;
      self.loading = true;
      self.$store.state.services.turnoService
        .getAll()
        .then(r => {
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          r.data.map(obj => ({
            ...(obj.turno =
              "turno #" +
              obj.numero +
              ": " +
              moment("2020-10-13 " + obj.hora_inicio).format("h:mm a") +
              " - " +
              moment("2020-10-13 " + obj.hora_fin).format("h:mm a"))
          }));
          self.turnos = r.data;
        })
        .catch(r => {});
    },

    //obtener buques
    getCarnets() {
      let self = this;
      self.$store.state.services.carnetService
        .getAll()
        .then(r => {
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          r.data = r.data.filter(x=>!x.asignado)
          self.carnets = r.data
        })
        .catch(r => {});
    },

    //buscar planificacion
    searchPlanification() {
      let self = this;
      self.loading = true;
      self.$store.state.services.planificacionService
        .search(self.form_a.fecha_atraque, self.form_a.buque_id)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            self.planificacion = null;
            self.form.planificacion_id = null;
            self.detalle_asignacion = [];
            return;
          }
          self.planificacion = r.data;
          self.form.planificacion_id = self.planificacion.idPlano_Estiba;
          console.log(self.form);
        })
        .catch(r => {});
    },

    //obtener detalles
    getDetailData(id, turno_id, fecha) {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .getDetail(id, turno_id, fecha)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.detalle_asignacion = r.data
          self.getCarnets()
        })
        .catch(r => {});
    },

        //obtener detalles
    getByEmpleado(id, empleado_id) {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .getByEmpleado(id, empleado_id)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.empleado_carnet = r.data.data
          if(self.empleado_carnet !== null){
            self.form.carnet_id = self.empleado_carnet.carnet.id
          }
        })
        .catch(r => {});
    },

    //funcion para guardar registro
    create() {
      let self = this;
      let data = self.form;
      self.loading = true;
      self.$store.state.services.asignacionService
        .create(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success("registro agregado con éxito", "éxito");
          self.form.id = r.data.id;
          self.getAll()
          self.getDetailData(r.data.id, data.turno_id, data.fecha)
          self.getByEmpleado(r.data.id, data.empleado_id)
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update() {
      let self = this;
      let data = self.form;
      console.log(data)
      if (self.detalle_asignacion.some(x => x.carnet_id == data.carnet_id && x.empleado_id != data.empleado_id)) {
        self.$toastr.error("numero de carnet ya fue asignado", "error");
        return;
      }

      if (
        self.detalle_asignacion.some(x => x.empleado_id == data.empleado_id)
      ) {
        self.$toastr.error("empleado ya fue asignado", "error");
        return;
      }

      self.loading = true;
      self.$store.state.services.asignacionService
        .update(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getDetailData(data.id, data.turno_id, data.fecha);
          self.getByEmpleado(r.data.id, data.empleado_id)
          //this.$toastr.success('registro actualizado con éxito', 'éxito')
        })
        .catch(r => {});
    },

    //funcion para eliminar registro
    destroy(data) {
      let self = this;
      self
        .$confirm("Seguro que desea eliminar asignación?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.asignacionService
            .destroy(data)
            .then(r => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll();
              this.$toastr.success("registro eliminado con éxito", "éxito");
              self.clearData();
            })
            .catch(r => {});
        })
        .catch(cancel => {});
    },

    //liberar carnets
    releaseCards(data){
      let self = this;
      self
        .$confirm("Seguro que desea liberar carnets, la asignación se marcará como terminada, ya no se podrán ingresar nuevas fechas para esta asignacion?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.asignacionService
            .releaseCards(data)
            .then(r => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll()
              this.$toastr.success("carnets han sido liberados con éxito", "éxito")
            })
            .catch(r => {});
        })
        .catch(cancel => {});
    },

    //remover detail
    removeDetail(data) {
      let self = this;
      self
        .$confirm("Seguro que desea remover asignacion?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.detalleAsignacionService
            .destroy(data)
            .then(r => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              this.$toastr.success("empleado removido con éxito", "éxito");
              self.getDetailData(
                data.asignacion_empleado_id,
                data.turno_id,
                data.fecha
              );
            })
            .catch(r => {});
        })
        .catch(cancel => {});
    },

    //limpiar data de formulario
    clearData() {
      let self = this;
      Object.keys(self.form).forEach(function(key, index) {
        if (typeof self.form[key] === "string") self.form[key] = "";
        else if (typeof self.form[key] === "boolean") self.form[key] = true;
        else if (typeof self.form[key] === "number") self.form[key] = null;
      });
      self.$validator.reset();
    },
    //editar registro
    edit(data) {
      let self = this;
      this.dialog = true;
      self.detalle_asignacion = [];
      self.clearData();
      self.mapData(data);
    },

    //mapear datos a formulario
    mapData(data) {
      let self = this;
      self.form.id = data.id;
      self.form.planificacion_id = data.planificacion_id;
      self.planificacion = data.planificacion;
      self.form_a.buque_id = data.planificacion.idBuque;
      self.form_a.fecha_atraque = data.planificacion.fecha_atraque;
    },

    //validar formulario
    validateForm(scope) {
      let self = this;
      this.$validator.validateAll(scope).then(result => {
        if (result) {
          scope == "form_a" ? self.searchPlanification() : self.createOrEdit();
        }
      });
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit() {
      let self = this;
      console.log(self.form);
      if (self.form.id > 0 && self.form.id !== null) {
        self.update();
      } else {
        self.create();
      }
    },

    cancelar() {
      let self = this;
    },

    close() {
      let self = this
      self.dialog = false
      self.clearData()
      self.planificacion = null
    },

    detailChange() {
      let self = this;
      let data = self.form;
      if (data.id !== null && data.turno_id !== null && data.fecha !== null && data.fecha !== "" && data.fecha !== undefined) {
        self.getDetailData(data.id, data.turno_id, data.fecha);
      }
    },

    changeEmpleado(id){
      let self = this
      let data = self.form
      self.getByEmpleado(data.id,id)
    },

    singlePrint(data) {
      let self = this;
      self.print({
        empleado_id: data.empleado_id,
        fecha: data.fecha,
        turno_id: data.turno_id
      });
    },

    printAll() {
      let self = this;
      self.print({
        fecha: self.form.fecha,
        turno_id: self.form.turno_id
      });
    },

    //obtener turnos
    get(data) {
      let self = this;
      self.mapData(data);
      self.loading = true;
      self.dialog_turn = true;
      self.$store.state.services.asignacionService
        .get(data.id)
        .then(r => {
          self.loading = false

          if (self.$store.state.global.captureError(r)) {
            return;
          }


          self.turnos_print = _(r.data)
            .groupBy("fecha")
            .map(function(items, fecha) {
              return {
                fecha: fecha,
                turnos: _(items)
                  .groupBy("turno_id")
                  .map(function(items2, turno_id) {
                    return {
                      fecha: fecha,
                      turno_id: parseInt(turno_id),
                      numero: items2[0].turno.numero
                    };
                  })
                  .value()
              };
            })
            .value();
        })
        .catch(r => {});
    },

    //imprimir hora entrada
    print(data) {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .print(self.form.id, data.turno_id, data.fecha, data.empleado_id)
        .then(r => {
          self.loading = false;
          if (r.response) {
            this.$toastr.error(r.response.data.error, "error");
            return;
          }
          const url = window.URL.createObjectURL(
            new Blob([r.data], { type: "application/pdf" })
          );
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute(
            "download",
            "asignacion_fecha_" + data.fecha + "_turno_" + data.turno_id
          );
          //link.target = '_blank'
          link.click();
        })
        .catch(r => {});
    },

    blockTurn(data){
      let self = this
      let t = self.turnos.find(x=>x.id == data.turno_id)

      var currentTime = moment()
      var extra = moment(data.fecha).format("YYYY-MM-DD") + " "
      var start_time = moment(extra + t.hora_inicio)
      var end_time = moment(extra + t.hora_fin)

      if(end_time < start_time){
        end_time = moment(end_time).add('d',1)
      }

      if(currentTime > end_time){
        return true
      }

      return false
    }
  },

  computed: {
    setTitle() {
      let self = this;
      return self.form.id !== null ? "actualizar asignación" : "Nuevo Registro";
    }
  }
};
</script>
