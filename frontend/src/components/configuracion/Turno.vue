<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>TURNOS </v-toolbar-title>
        <v-divider class="mx-2" inset vertical></v-divider><v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Buscar"
          single-line
          hide-details
        ></v-text-field>
        
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="800px" persistent>

        <!--  <template v-slot:activator="{ on }">
            <v-btn color="primary" small dark class="mb-2" v-on="on"
              ><v-icon>add</v-icon> Nuevo</v-btn
            >
          </template>-->
          <v-card>
            <v-card-title>
              <span class="headline">{{ setTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs6 sm2 md2>
                    <v-text-field
                      v-model="form.numero"
                      label="Numero turno"
                      v-validate="'required|numeric'"
                      type="number"
                      data-vv-name="numero"
                      data-vv-as="numero"
                      :error-messages="errors.collect('numero')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm5 md5>
                    <v-text-field
                      v-model="form.hora_inicio"
                      label="Hora inicio"
                      v-validate="'required'"
                      type="time"
                      data-vv-name="hora_inicio"
                      data-vv-as="hora inicio"
                      :error-messages="errors.collect('hora_inicio')"
                    >
                    </v-text-field>
                  </v-flex>

                  <v-flex xs6 sm5 md5>
                    <v-text-field
                      v-model="form.hora_fin"
                      label="Hora fin"
                      v-validate="'required'"
                      type="time"
                      data-vv-name="hora_fin"
                      data-vv-as="hora fin"
                      :error-messages="errors.collect('hora_fin')"
                    >
                    </v-text-field>
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
        <v-dialog v-model="cargoTurno" max-width="800px" persistent>
          <v-card>
            <v-card-title>
              <span class="headline">Configuración de Salario por Turnos</span>
            </v-card-title>
            <v-card-text>
              <v-list dense>
                <v-list-tile v-for="(item, i) in cargos" :key="i">
                  <v-list-tile-title v-text="item.nombre"></v-list-tile-title>
                  <v-list-tile-action>
                    <v-text-field
                      prepend-icon="money"
                      v-model="item.salario"
                      color="primary"
                      type="number"
                      data-vv-name="salario"
                      data-vv-as="salario"
                      :error-messages="errors.collect('salario')"
                    ></v-text-field>
                  </v-list-tile-action>
                </v-list-tile>
              </v-list>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red darken-1" flat @click="close">Volver</v-btn>
              <v-btn color="primary darken-1" flat @click="createCargoTurno"
                >Guardar</v-btn
              >
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
          <td class="text-xs-left">{{ props.item.numero }}</td>
          <td class="text-xs-left">
            {{ ("2020-04-05 " + props.item.hora_inicio) | moment("h:mm a") }}
          </td>
          <td class="text-xs-left">
            {{ ("2020-04-05 " + props.item.hora_fin) | moment("h:mm a") }}
          </td>
          <td class="text-xs-left">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="success"
                  fab
                  dark
                  @click="configSalary(props.item.id)"
                >
                  money</v-icon
                >
              </template>
              <span>Configurar Salarios</span>
            </v-tooltip>
          <!--  <v-tooltip top>
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
            </v-tooltip>-->
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
export default {
  name: "turno",
  props: {
    source: String
  },
  data() {
    return {
      dialog: false,
      cargoTurno: false,
      search: "",
      loading: false,
      items: [],
      headers: [
        { text: "#", value: "numero" },
        { text: "Hora inicio", value: "hora_inicio" },
        { text: "Hora fin", value: "hora_fin" },
        { text: "Acciones", value: "", sortable: false }
      ],
      form: {
        id: null,
        numero: null,
        hora_inicio: null,
        hora_fin: null
      },
      form2: {
        turno_id: null,
        cargos: []
      },
      cargos: [],
      salarioCargoTurno: [],
      idTurno: null
    };
  },

  created() {
    let self = this;
    self.getAll();
    //self.getCargos();
  },

  methods: {
    
    getAll() {
      let self = this;
      self.loading = true;
      self.$store.state.services.turnoService
        .getAll()
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.items = r.data;
        })
        .catch(r => {});
    },
    getCargos() {
      let self = this;
      self.loading = true;
      self.$store.state.services.cargoService
        .getAll()
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.cargos = r.data;
          self.cargos.forEach(function(element) {
            self.validExist(element);
          });
        })
        .catch(r => {});
    },
    validExist(item) {
      let self = this;

      let a = self.salarioCargoTurno.find(x => x.cargo_id == item.idCargo);
      if (a === undefined) {
        item.salario = null;
      } else {
        item.salario = a.salario;
      }
      return item;
    },
    //funcion para guardar registro
    create() {
      let self = this;
      let data = self.form;
      self.loading = true;
      self.$store.state.services.turnoService
        .create(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success("registro agregado con éxito", "éxito");
          self.getAll();
          self.clearData();
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update() {
      let self = this;
      self.loading = true;
      let data = self.form;

      self.$store.state.services.turnoService
        .update(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getAll();
          this.$toastr.success("registro actualizado con éxito", "éxito");
          self.clearData();
        })
        .catch(r => {});
    },

    //funcion para eliminar registro
    destroy(data) {
      let self = this;
      self
        .$confirm("Seguro que desea eliminar turno?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.turnoService
            .destroy(data)
            .then(r => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll();
              this.$toastr.success("registro eliminado con exito", "exito");
              self.clearData();
            })
            .catch(r => {});
        })
        .catch(cancel => {});
    },
    createCargoTurno() {
      let self = this;
      self.loading = true;
      self.prepareDataCargoTurno(self.cargos);
      let data = self.form2;
      if (data.cargos.length > 0) {
        self.$store.state.services.turnoService
          .createCargoTurno(self.idTurno, data)
          .then(r => {
            self.loading = false;
            if (self.$store.state.global.captureError(r)) {
              return;
            }
            this.$toastr.success("Registros guardados con exito", "exito");
          })
          .catch(r => {});
      } else {
        self.loading = false;
        this.$toastr.error("Debe configurar por lo menos un salario", "error");
      }
    },
    prepareDataCargoTurno(data) {
      let self = this;
      self.form2.turno_id = self.idTurno;
      data.forEach(function(element) {
        let a = self.salarioCargoTurno.find(x => x.cargo_id == element.idCargo);
        if (a === undefined && element.salario != null) {
          self.form2.cargos.push({
            id: null,
            cargo_id: element.idCargo,
            salario: element.salario
          });
        } else {
          if (element.salario != null) {
            self.form2.cargos.push({
              id: a.id,
              cargo_id: a.cargo_id,
              salario: element.salario
            });
          }
        }
      });
    },
    configSalary(id) {
      let self = this;
      self.cargoTurno = true;
      self.idTurno = id;
      self.loading = true;
      self.$store.state.services.turnoService
        .getCargos(id)
        .then(r => {
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.salarioCargoTurno = r.data;
          self.getCargos();
          self.loading = false;
        })
        .catch(r => {});
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
      self.mapData(data);
    },

    //mapear datos a formulario
    mapData(data) {
      let self = this;
      self.form.id = data.id;
      self.form.hora_inicio = data.hora_inicio;
      self.form.hora_fin = data.hora_fin;
      self.form.numero = data.numero;
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit() {
      let self = this;
      this.$validator.validateAll().then(result => {
        if (result) {
          if (self.form.id > 0 && self.form.id !== null) {
            self.update();
          } else {
            self.create();
          }
        }
      });
    },

    cancelar() {
      let self = this;
    },

    close() {
      let self = this;
      self.dialog = false;
      self.cargoTurno = false;
      self.clearData();
    }
  },

  computed: {
    setTitle() {
      let self = this;
      return self.form.id !== null ? "Actualizar" : "Nuevo Registro";
    }
  }
};
</script>
