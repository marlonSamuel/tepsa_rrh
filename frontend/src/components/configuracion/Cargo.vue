<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>Cargos </v-toolbar-title>
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
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      v-model="form.nombre"
                      label="Nombre"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="nombre"
                      :error-messages="errors.collect('nombre')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-text-field
                      v-model="form.descripcion"
                      label="Descripcion"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="descripcion"
                      :error-messages="errors.collect('descripcion')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      v-model="form.salario"
                      label="Salario"
                      v-validate="'required'"
                      type="number"
                      data-vv-name="salario"
                      :error-messages="errors.collect('salario')"
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
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="items"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:items="props">
          <td class="text-xs-left">{{ props.item.nombre }}</td>
          <td class="text-xs-left">{{ props.item.descripcion }}</td>
          <td class="text-xs-left">{{ props.item.salario }}</td>
          <td class="text-xs-left">
            <v-chip
              small
              :color="props.item.estado == 'A' ? 'primary' : 'error'"
              >{{ props.item.estado == "A" ? "Activo" : "Inactivo" }}</v-chip
            >
          </td>
          <td class="text-xs-left">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  :color="color(props.item.estado)"
                  fab
                  dark
                  @click="disabledCargo(props.item)"
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
export default {
  name: "prestacions",
  props: {
    source: String
  },
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      form: {
        idCargo: null,
        nombre: "",
        descripcion: "",
        salario: 0,
        estado: "A"
      },
      items: [],
      headers: [
        { text: "Nombre", value: "nombre" },
        { text: "Descripcion", value: "descripcion" },
        { text: "Salario", value: "salario" },
        {
          text: "Estado",
          value: "estado"
        },
        { text: "Acciones", value: "", sortable: false }
      ]
    };
  },
  created() {
    let self = this;
    self.getAll();
  },

  methods: {
    getAll() {
      let self = this;
      self.loading = true;
      self.$store.state.services.cargoService
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
    create() {
      let self = this;
      let data = self.form;
      self.loading = true;
      console.log(data);
      self.$store.state.services.cargoService
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

    update() {
      let self = this;
      self.loading = true;
      let data = self.form;
      self.$store.state.services.cargoService
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
    destroy(data) {
      let self = this;
      self
        .$confirm("Seguro que desea eliminar Prestación?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.cargoService
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
    disabledCargo(data) {
      let self = this;
      self.loading = true;
      self.$store.state.services.cargoService
        .disabled(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getAll();
          this.$toastr.success("Cambio de Estado exitoso", "exito");
          self.clearData();
          self.close();
        })
        .catch(r => {});
    },
    clearData() {
      let self = this;
      Object.keys(self.form).forEach(function(key, index) {
        if (typeof self.form[key] === "string") self.form[key] = "";
        else if (typeof self.form[key] === "boolean") self.form[key] = true;
        else if (typeof self.form[key] === "number") self.form[key] = null;
      });
      self.$validator.reset();
    },
    edit(data) {
      let self = this;
      this.dialog = true;
      self.mapData(data);
    },
    mapData(data) {
      let self = this;
      self.form.idCargo = data.idCargo;
      self.form.descripcion = data.descripcion;
      self.form.nombre = data.nombre;
      self.form.salario = data.salario;
      self.form.estado = data.estado;
    },
    createOrEdit() {
      this.$validator.validateAll().then(result => {
        if (result) {
          if (self.form.idCargo > 0 && self.form.idCargo !== null) {
            self.update();
          } else {
            self.create();
          }
        }
      });
      let self = this;
    },
    close() {
      let self = this;
      self.dialog = false;
      self.clearData();
    },
    cancelar() {
      let self = this;
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
    }
  },
  computed: {
    setTitle() {
      let self = this;
      return self.form.idCargo !== null
        ? "Actualizar Cargo " + self.form.nombre
        : "Nuevo Registro";
    }
  }
};
</script>
