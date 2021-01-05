<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>Planilla Empleados Fijos </v-toolbar-title>
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
              <span class="headline"></span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm6 md6>
                    <v-select
                      placeholder="Mes"
                      v-model="mes_id"
                      v-validate="'required'"
                      :items="meses"
                      :error-messages="errors.collect('mes')"
                      label="Mes"
                      item-value="id"
                      item-text="mes"
                      data-vv-name="mes"
                      required
                      @change="getQuincenas"
                    ></v-select>
                  </v-flex>
                  <v-flex xs12 sm6 md6>
                    <v-select
                      placeholder="Quincena"
                      prepend-icon="person_add"
                      v-model="quincena_id"
                      v-validate="'required'"
                      :items="quincenas"
                      :error-messages="errors.collect('quincena')"
                      label="Quincena"
                      item-value="id"
                      item-text="quincena"
                      data-vv-name="quincena"
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
      </v-toolbar>
      <v-data-table
        :headers="headers"
        :items="items"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:items="props">
            <td class="text-xs-left">{{ props.item.anio.anio }}</td>
            <td class="text-xs-left">{{ props.item.mes.mes }}</td>
          <td class="text-xs-left">{{ props.item.quincena }}</td>
          <td class="text-xs-left">{{ props.item.fecha_inicio }}</td>
          <td class="text-xs-left">{{ props.item.fecha_fin }}</td>
          <td class="text-xs-left">
            <v-chip
              small
              :color="props.item.cerrada == true ? 'primary' : 'error'"
              >{{ props.item.cerrada == false ? "Sin Paga" : "Pagada" }}</v-chip
            >
          </td>
          <td class="text-xs-left">
           <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-icon v-on="on" @click="$router.push('planilla_fijo_info/'+props.item.id)"  color="success" fab dark> info</v-icon>
                </template>
                <span>Información planilla</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                  v-on="on"
                  color="error"
                  fab
                  dark
                  
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
  name: "pagoFijoINdex",
  props: {
    source: String,
  },
  data() {
    return {
      search: "",
      dialog: false,
      loading: false,
      quincena_id: null,
      mes_id: null,
      meses: [],
      quincenas: [],
      form: {
        id: null,
        quincena_id: null,
      },
      quincena_pagada: [],
      items: [],
      headers: [
          { text: "Año", value: "anio.anio" },
          { text: "Mes", value: "mes.mes" },
        { text: "Quincena", value: "quincena" },
        { text: "Fecha Inicio", value: "fecha_inicio" },
        { text: "Fecha Fin", value: "fecha_fin" },
        {
          text: "Cerrada",
          value: "cerrada"
        },
        { text: "Acciones", value: "", sortable: false }
      ]
    };
  },
  created() {
    let self = this;
    self.getAll();
    self.getMeses();
  },
  methods: {
    getAll() {
      let self = this;
      self.loading = true;
      self.$store.state.services.pagoEmpleadoFijoService
        .getAll()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.items = [];
          r.data.forEach(function(item) {
            if (item.pago_empleado_fijo.length > 0) {
              self.items.push(item);
            }
          });
          
          console.log(self.items);
        })
        .catch((r) => {});
    },
    getMeses() {
      let self = this;
      self.loading = true;
      self.$store.state.services.pagoEmpleadoFijoService
        .getMes()
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.meses = r.data;
          console.log(self.meses);
        })
        .catch((r) => {});
    },
    getQuincenas() {
      let self = this;
      self.loading = true;
      self.$store.state.services.pagoEmpleadoFijoService
        .getQuincena(self.mes_id)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.quincenas = r.data;
          self.quincenas = [];
          r.data.forEach(function(item) {
            if (!item.cerrada) {
              self.quincenas.push(item);
            }
          });
          
        })
        .catch((r) => {});
    },
    //funcion para guardar registro
    create() {
      let self = this;
      self.form.quincena_id = self.quincena_id;
      let data = self.form;
      self.loading = true;
      self.$store.state.services.pagoEmpleadoFijoService
        .create(data)
        .then((r) => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          this.$toastr.success("Quincena procesada con éxito", "éxito");
          self.getAll();
          self.close();
        })
        .catch((r) => {});
    },

    createOrEdit() {
      let self = this;
      console.log(self.form);
      if (self.form.id > 0 && self.form.id !== null) {
        self.update();
      } else {
        self.create();
      }
    },
    close() {
      let self = this;
      self.dialog = false;
    },
    setEstado(estado) {
      let self = this;
      return estado == false ? "close " : "check";
    },
    setSpan(estado) {
      let self = this;
      return estado == false ? "Cerrar" : "Reabrir";
    },
    color(estado) {
      let self = this;
      return estado == false ? "error" : "primary";
    }
  },
};
</script>