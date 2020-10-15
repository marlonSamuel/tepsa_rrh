<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>Prestaciones </v-toolbar-title>
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
                  <v-flex xs12 sm12 md12>
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
                    <v-switch
                      v-model="form.fijo"
                      label="Prestacion Fija"
                    ></v-switch>
                  </v-flex>
                  <v-flex xs6 sm6 md6>
                    <v-text-field
                      v-model="form.calculo"
                      :label="setLabel"
                      v-validate="'required'"
                      type="number"
                      data-vv-name="calculo"
                      :error-messages="errors.collect('calculo')"
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
          <td class="text-xs-left">{{ props.item.descripcion }}</td>
          <td class="text-xs-left">
            <v-chip small :color="props.item.fijo ? 'primary' : 'green'">{{
              props.item.fijo ? "Si" : "No"
            }}</v-chip>
          </td>
          <td class="text-xs-left">{{ props.item.calculo }}</td>
          <td class="text-xs-left">
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
        id: null,
        descripcion: "",
        fijo: true,
        calculo: 0
      },
      items: [],
      headers: [
        { text: "Descripcion", value: "descripcion" },
        { text: "Fijo", value: "fijo" },
        {
          text: "Monto o Porcentaje",
          value: "calculo"
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
      self.$store.state.services.prestacionService
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
      self.$store.state.services.prestacionService
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

      self.$store.state.services.prestacionService
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
          self.$store.state.services.prestacionService
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
      self.form.id = data.id;
      self.form.descripcion = data.descripcion;
      self.form.fijo = data.fijo;
      self.form.calculo = data.calculo;
    },
    createOrEdit() {
      this.$validator.validateAll().then(result => {
        if (result) {
          if (self.form.id > 0 && self.form.id !== null) {
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
    }
  },
  computed: {
    setTitle() {
      let self = this;
      return self.form.id !== null
        ? "Actualizar Prestación " + self.form.descripcion
        : "Nuevo Registro";
    },
    setLabel() {
      let self = this;
      return self.form.fijo ? "Monto " : "Porcentaje";
    }
  }
};
</script>
