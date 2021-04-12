<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>Empleados </v-toolbar-title>
        <v-divider class="mx-2" inset vertical></v-divider><v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Buscar"
          single-line
          hide-details
        ></v-text-field>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="900px" persistent>
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
                  <v-flex d-flex xs12 sm12 md12>
                    <v-layout>
                      <v-container>
                        <v-layout row wrap>
                          <v-flex>
                            <div id="uploader">
                              <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                  <v-icon
                                    v-on="on"
                                    color="primary"
                                    @click="$refs.file.click()"
                                    >insert_photo</v-icon
                                  >Cargar fotografía
                                </template>
                                <span>Seleccionar fotografía de empleado</span>
                              </v-tooltip>
                              <input
                                v-show="false"
                                @change="selectedFile"
                                class="input-file hidden"
                                ref="file"
                                type="file"
                                accept="image/*"
                              />
                            </div>
                          </v-flex>
                          <v-flex xs12 md12 lg12>
                            <v-layout column>
                              <v-avatar
                                :tile="true"
                                size="200"
                                color="blue lighten-4"
                              >
                                <img
                                  :src="image !== null ? image : image_default"
                                />
                              </v-avatar>
                            </v-layout>
                          </v-flex>
                        </v-layout>
                      </v-container>
                    </v-layout>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.primer_nombre"
                      label="Primer Nombre"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="primer_nombre"
                      :error-messages="errors.collect('primer_nombre')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.segundo_nombre"
                      label="Segundo Nombre"
                      
                      type="text"
                      data-vv-name="segundo_nombre"
                      :error-messages="errors.collect('segundo_nombre')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.primer_apellido"
                      label="Primer Apellido"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="primer_apellido"
                      :error-messages="errors.collect('primer_apellido')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.segundo_apellido"
                      label="Segundo Apellido"
                      
                      type="text"
                      data-vv-name="segundo_apellido"
                      :error-messages="errors.collect('segundo_apellido')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.nit"
                      label="NIT"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="nit"
                      :error-messages="errors.collect('nit')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.dpi"
                      label="DPI"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="dpi"
                      :error-messages="errors.collect('dpi')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm12 md4>
                    <v-text-field
                      v-model="form.fecha_ingreso"
                      label="Fecha Ingreso"
                      v-validate="'required'"
                      type="date"
                      data-vv-name="fecha_ingreso"
                      data-vv-as="fecha de ingreso"
                      :error-messages="errors.collect('fecha_ingreso')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm4 md4>
                    <v-select
                      placeholder="Cargo"
                      prepend-icon="person_add"
                      v-model="form.idCargo"
                      v-validate="'required'"
                      :items="cargos"
                      :error-messages="errors.collect('idCargo')"
                      label="Cargo"
                      item-value="idCargo"
                      item-text="nombre"
                      data-vv-name="idCargo"
                      required
                    ></v-select>
                  </v-flex>
                  <v-flex xs12 sm4 md4>
                    <v-text-field
                      prepend-icon="phone"
                      v-model="form.telefono"
                      label="Telefono"
                      
                      type="text"
                      data-vv-name="telefono"
                      :error-messages="errors.collect('telefono')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.direccion"
                      label="Direccion"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="direccion"
                      :error-messages="errors.collect('direccion')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm4 md4>
                    <v-switch
                      v-model="form.tipo_empleado"
                      :label="`Empleado: ${setLabel}`"
                    ></v-switch>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.cuenta"
                      label="No. Cuenta"
                      type="text"
                      data-vv-name="cuenta"
                      :error-messages="errors.collect('cuenta')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      prepend-icon="add"
                      v-model="form.igss"
                      label="No. IGSS"
                      type="text"
                      data-vv-name="igss"
                      :error-messages="errors.collect('igss')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm6 md6 v-if="form.tipo_empleado == 1">
                    <v-select
                      placeholder="Carnet"
                      prepend-icon="card"
                      v-model="form.carnet_id"
                      v-validate="'required'"
                      :items="carnets"
                      :error-messages="errors.collect('carnet_id')"
                      label="Carnet"
                      item-value="id"
                      item-text="codigo"
                      data-vv-name="carnet_id"
                      required
                    ></v-select>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red darken-1" flat @click="close">Volver</v-btn>
              <v-btn color="blue darken-1" flat @click="createOrEdit"
                >Guardar</v-btn
              >
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="prestacion" max-width="1000px" persistent>
          <v-card>
            <v-card-title>
              <span class="headline">Asignacion de Prestaciones</span>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs4>
                    <template>
                      <v-list dense>
                        <v-subheader>Prestaciones Asignadas</v-subheader>
                        <v-list-tile
                          v-for="(item, i) in asignacionPrestacion"
                          :key="i"
                        >
                          <v-list-tile-action>
                            <v-icon
                              color="error"
                              fab
                              dark
                              @click="destroyPrestacion(item)"
                              >delete</v-icon
                            >
                          </v-list-tile-action>
                          <v-list-tile-title
                            v-text="item.prestacion.descripcion"
                          ></v-list-tile-title>
                        </v-list-tile>
                      </v-list>
                    </template>
                  </v-flex>
                  <v-divider></v-divider>
                  <v-flex xs8>
                    <h3 class="grey--text">Seleccione Prestaciones</h3>
                    <v-layout row wrap>
                      <v-flex
                        xs6
                        sm2
                        md2
                        v-for="prestacion in prestaciones"
                        :key="prestacion.id"
                      >
                        <v-checkbox
                          v-model="form2.prestaciones"
                          :label="prestacion.descripcion"
                          color="primary"
                          :value="prestacion.id"
                          hide-details
                          :disabled="validateDisabled(prestacion.id)"
                        ></v-checkbox>
                      </v-flex>
                    </v-layout>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary"
                small
                dark
                class="mb-2"
                @click="savePrestacion"
                ><v-icon>add</v-icon> Asignar Prestaciones</v-btn
              >
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
          <td class="text-xs-left">{{ props.item.primer_nombre }}</td>
          <td class="text-xs-left">{{ props.item.segundo_nombre }}</td>
          <td class="text-xs-left">{{ props.item.primer_apellido }}</td>
          <td class="text-xs-left">{{ props.item.segundo_apellido }}</td>
          <td class="text-xs-left">{{ props.item.cargo.nombre }}</td>
          <td class="text-xs-left">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  :color="color(props.item.estado)"
                  fab
                  dark
                  @click="disabledEmpleado(props.item.idEmpleado)"
                >
                  {{ setEstado(props.item.estado) }}</v-icon
                >
              </template>
              <span>{{ setSpan(props.item.estado) }}</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="primary"
                  fab
                  dark
                  @click="addPrestacion(props.item)"
                >
                  add_circle</v-icon
                >
              </template>
              <span>Agregar Prestaciones</span>
            </v-tooltip>
            <v-tooltip top>
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

            <v-tooltip top>
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
  name: "empleadoIndex",
  props: {
    source: String,
  },
  data() {
    return {
      loading: false,
      search: "",
      items: [],
      dialog: false,
      prestacion: false,
      headers: [
        { text: "Primer Nombre", value: "primer_nombre" },
        { text: "Segundo Nombre", value: "segundo_nombre" },
        { text: "Primer Apellido", value: "primer_apellido" },
        { text: "Segundo Apellido", value: "segundo_apellido" },
        { text: "Cargo", value: "cargo.nombre" },
        { text: "Acciones", value: "", sortable: false },
      ],
      form: {
        idEmpleado: null,
        primer_nombre: "",
        segundo_nombre: "",
        primer_apellido: "",
        segundo_apellido: "",
        nit: "",
        dpi: "",
        direccion: "",
        telefono: "",
        idCargo: 0,
        cuenta: "",
        tipo_empleado: 0,
        foto: "",
        estado: "A",
        carnet_id: 0,
        igss: "",
        fecha_ingreso: null,
      },
      form2: {
        empleado_id: null,
        prestaciones: [],
      },
      prestaciones: [],
      asignacionPrestacion: [],
      carnets: [],
      image: null,
      image_default: this.$store.state.base_url + "img/user_empty.png",
      cargos: [],
    };
  },
  created() {
    let self = this;
    self.getAll();
    self.getCargos();
    self.getPrestacions();
    self.getCarnets();
  },
  methods: {
    getAll() {
      let self = this;
      self.loading = true;
      self.$store.state.services.empleadoService
        .getAll()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.items = r.data;
        })
        .catch((r) => {});
    },
    getCargos() {
      let self = this;
      self.loading = true;
      self.$store.state.services.cargoService
        .getAll()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.cargos = r.data;
        })
        .catch((r) => {});
    },
    getPrestacions() {
      let self = this;
      self.loading = true;
      self.$store.state.services.prestacionService
        .getAll()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.prestaciones = r.data;
        })
        .catch((r) => {});
    },
    getCarnets() {
      let self = this;
      self.loading = true;
      self.$store.state.services.carnetService
        .getAll()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          //self.carnets = r.data.find(x => x.asignado === 1);
          r.data.forEach(function (item) {
            if (item.asignado === 0) {
              self.carnets.push(item);
            }
          });
        })
        .catch((r) => {});
    },
    create() {
      let self = this;
      let data = self.form;
      self.loading = true;
      self.$store.state.services.empleadoService
        .create(data)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success("registro agregado con éxito", "éxito");
          self.getAll();
          self.clearData();
        })
        .catch((r) => {});
    },

    update() {
      let self = this;
      self.loading = true;
      let data = self.form;
      self.$store.state.services.empleadoService
        .update(data)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getAll();
          this.$toastr.success("registro actualizado con éxito", "éxito");
          self.clearData();
        })
        .catch((r) => {});
    },
    createOrEdit() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          if (self.form.idEmpleado > 0 && self.form.idEmpleado !== null) {
            self.update();
          } else {
            self.create();
          }
        }
      });
      let self = this;
    },
    savePrestacion() {
      let self = this;
      let data = self.form2;
      if (data.prestaciones.length > 0) {
        self.loading = true;
        self.$store.state.services.empleadoPrestacionService
          .create(data)
          .then((r) => {
            self.loading = false;
            if (self.$store.state.global.captureError(r)) {
              return;
            }
            this.$toastr.success("Registro guardado con éxito", "éxito");
            self.prestacion = false;
            self.asignacionPrestacion = [];
            self.getAll();
            self.clearData();
          })
          .catch((r) => {});
      } else {
        this.$toastr.error("Seleccione por lo menos una Prestación", "error");
      }
    },
    destroy(data) {
      let self = this;
      self
        .$confirm("Seguro que desea eliminar Empleado?")
        .then((res) => {
          self.loading = true;
          self.$store.state.services.empleadoService
            .destroy(data)
            .then((r) => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll();
              this.$toastr.success("registro eliminado con exito", "exito");
              self.clearData();
            })
            .catch((r) => {});
        })
        .catch((cancel) => {});
    },
    disabledEmpleado(id) {
      let self = this;
      self.loading = true;
      self.$store.state.services.empleadoService
        .disabled(id)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getAll();
          this.$toastr.success("Cambio de Estado exitoso", "exito");
          self.clearData();
          self.close();
        })
        .catch((r) => {});
    },
    destroyPrestacion(data) {
      let self = this;
      self
        .$confirm("Seguro que desea eliminar Prestacion?")
        .then((res) => {
          self.loading = true;
          self.$store.state.services.empleadoPrestacionService
            .destroy(data)
            .then((r) => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll();
              this.$toastr.success("registro eliminado con exito", "exito");
              self.clearData();
              self.close();
            })
            .catch((r) => {});
        })
        .catch((cancel) => {});
    },
    edit(data) {
      let self = this;
      this.dialog = true;
      self.mapData(data);
    },
    addPrestacion(data) {
      let self = this;
      self.prestacion = true;
      self.loading = true;
      self.form2.empleado_id =
        data.idEmpleado == undefined ? data.empleado_id : data.idEmpleado;
      self.$store.state.services.empleadoService
        .get(data.idEmpleado)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          let prestacion = r.data[0].empleado_prestacion;
          self.asignacionPrestacion = prestacion;
        })
        .catch((r) => {});
    },
    validateDisabled(id) {
      let self = this;
      return !!self.asignacionPrestacion.find((x) => x.prestacion_id === id);
    },
    mapData(data) {
      let self = this;

      self.form.idEmpleado = data.idEmpleado;
      self.form.primer_nombre = data.primer_nombre;
      self.form.segundo_nombre = data.segundo_nombre;
      self.form.primer_apellido = data.primer_apellido;
      self.form.segundo_apellido = data.segundo_apellido;
      self.form.nit = data.nit;
      self.form.dpi = data.dpi;
      self.form.direccion = data.direccion;
      self.form.telefono = data.telefono;
      self.form.idCargo = data.idCargo;
      self.form.telefono = data.telefono;
      self.form.cuenta = data.cuenta;
      self.form.tipo_empleado = data.tipo_empleado;
      self.form.igss = data.igss;
      self.form.fecha_ingreso = data.fecha_ingreso;
      data.foto !== null
        ? (self.image = self.$store.state.base_url + data.foto)
        : self.$store.state.base_url + "img/user_empty.png";
        console.log(self.form);
    },
    clearData() {
      let self = this;
      Object.keys(self.form).forEach(function (key, index) {
        if (typeof self.form[key] === "string") self.form[key] = "";
        else if (typeof self.form[key] === "boolean") self.form[key] = true;
        else if (typeof self.form[key] === "number") self.form[key] = null;
      });
      Object.keys(self.form2).forEach(function (key, index) {
        if (typeof self.form2[key] === "string") self.form2[key] = "";
        else if (typeof self.form2[key] === "boolean") self.form2[key] = true;
        else if (typeof self.form2[key] === "number") self.form2[key] = null;
      });
      self.form2.prestaciones = [];
      self.asignacionPrestacion = [];
      self.image = null;
      self.$validator.reset();
    },
    close() {
      let self = this;
      self.dialog = false;
      self.prestacion = false;
      self.clearData();
    },
    openDialogFiles() {
      document.querySelector("#uploader .input-file").click();
    },
    selectedFile() {
      let self = this;
      var input = document.querySelector("#uploader .input-file");
      var files = input.files;
      var oFReader = new FileReader();
      oFReader.readAsDataURL(files[0]);
      oFReader.onload = function (oFREvent) {
        self.image = oFREvent.target.result;
        self.form.foto = self.image;
      };
    },
    setEstado(estado) {
      let self = this;
      return estado == "A" ? "close " : "check";
    },
    setSpan(estado) {
      let self = this;
      return estado == "A" ? "Desactivar" : "Activar";
    },
    color(estado) {
      let self = this;
      return estado == "A" ? "error" : "primary";
    },
  },
  computed: {
    setTitle() {
      let self = this;
      return self.form.idEmpleado !== null
        ? "Actualizar Empleado " +
            self.form.primer_nombre +
            " " +
            self.form.segundo_nombre +
            self.form.primer_apellido +
            " " +
            self.form.segundo_apellido
        : "Nuevo Registro";
    },
    setLabel() {
      let self = this;
      return self.form.tipo_empleado ? "Fijo " : "Eventual";
    },
  },
};
</script>
