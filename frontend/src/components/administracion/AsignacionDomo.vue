<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
        <v-layout row wrap justify-end>
            <div>
            <v-breadcrumbs :items="itemsB">
                <template v-slot:divider>
                <v-icon>forward</v-icon>
                </template>
            </v-breadcrumbs>
            </div>
        </v-layout>
      <v-toolbar flat color="white">
        <v-toolbar-title>
            <span v-if="asignacion !== null">
                  ASIGNACIÓN EMPLEADOS A DOMO
              </span>    
        </v-toolbar-title>
        <v-divider class="mx-2" inset vertical></v-divider><v-spacer></v-spacer>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-card>
            <v-card-title>
              <span v-if="asignacion !== null">
                  BUQUE: {{asignacion.planificacion.buque.nombre || upper}} <br />
                  FECHA ATRAQUE: {{asignacion.planificacion.fecha_atraque | moment('DD/MM/YYYY')}}
              </span>
            </v-card-title>

            <v-card-text>
              <v-form data-vv-scope="form">
                    <v-card-text class="px-0">
                    <v-container grid-list-md>
                        <v-layout wrap>
                        <v-flex xs12 sm4 md4>
                            <v-text-field
                            v-model="form.fecha"
                            label="Fecha"
                            v-validate="'required'"
                            type="date"
                            data-vv-name="fecha"
                            :error-messages="errors.collect('form.fecha')"
                            @input="detailChange"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 sm8 md8>
                            <v-autocomplete
                            v-model="form.empleado_id"
                            label="Empleado"
                            placeholder="seleccione empleado"
                            :items="empleados"
                            item-text="empleado"
                            item-value="idEmpleado"
                            @input="changeEmpleado"
                            v-validate="'required'"
                            data-vv-name="empleado"
                            :error-messages="errors.collect('form.empleado')">
                            >
                            </v-autocomplete>
                        </v-flex>

                        
                        <v-flex xs12 sm4 md4>
                            <v-autocomplete
                            v-model="form.cargo_id"
                            label="Cargo"
                            placeholder="seleccione cargo"
                            :items="cargos"
                            item-text="nombre"
                            item-value="idCargo"
                            v-validate="'required'"
                            data-vv-name="cargo"
                            :error-messages="errors.collect('form.cargo')">
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
                            :error-messages=" errors.collect('form.carnet')">
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
                            block
                            small
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
                        {{form.fecha}}<v-icon
                        v-on="on"
                        color="info"
                        fab
                        dark @click="print">
                        print</v-icon>
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
                    <td class="text-xs-left">
                        {{ props.item.cargo.nombre }}
                    </td>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                        <v-icon
                            v-on="on"
                            color="error"
                            fab
                            dark
                            @click="destroy(props.item)"
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
</template>

<script>
import jsPDF from "jspdf";
import moment from "moment";
var pdfMake = require("pdfmake/build/pdfmake.js");

export default {
  name: "asignacion_domo",
  components: {
  },
  props: {
    source: String
  },
  data() {
    return {
      dialog: false,
      showCarnet: false,
      search: "",
      loading: false,
      items: [],
      cargos: [],
      carnets: [],
      empleados: [],
      id: null,
      asignacion: null,
      detalle_asignacion: [],
      empleado_carnet: null,
      headers: [
        { text: "Fecha", value: "fecha" },
        { text: "Acciones", value: "", sortable: false }
      ],

      headers_details: [
        { text: "fecha", value: "fecha" },
        { text: "Empleado", value: "empleado" },
        { text: "Carnet", value: "carnet" },
        { text: "Cargo", value: "cargo" },
        { text: "Accion", value: "", sortable: false }
      ],

      itemsB: [
        {
          text: 'ASIGNACIONES',
          disabled: false,
          href: '#/asignacion',
        },
        {
          text: 'ASISGNACION DOMO',
          disabled: true,
          href: '#',
        },
      ],
      form: {
        id: null,
        asignacion_empleado_id: null,
        cargo_id: null,
        carnet_id: null,
        empleado_id: null,
        fecha: null
      },
    };
  },

  created() {
    let self = this
    self.id = self.$route.params.id
    self.form.asignacion_empleado_id = self.id
    self.getAll(self.id)
    self.getCargos()
    self.getCarnets()
    self.getEmpleados()
  },

  methods: {
    getAll(id) {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .getDomos(id)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.asignacion = r.data
          self.items = r.data.asignacion_domos
          if(self.form.fecha !== null){
              self.detailChange()
          }
        })
        .catch(r => {});
    },

    //obtener cargos
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
          self.cargos = r.data
        })
        .catch(r => {});
    },

     //obtener cargos
    getCarnets() {
      let self = this;
      self.loading = true;
      self.$store.state.services.carnetService
        .getAll()
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          r.data = r.data.filter(x=>!x.asignado)
          self.carnets = r.data
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
          self.loading = false;
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
          self.empleados = r.data
        })
        .catch(r => {});
    },

    //funcion para guardar registro
    create() {
      let self = this
      let data = self.form

     /* if (self.detalle_asignacion.some(x => x.carnet_id == data.carnet_id)) {
        self.$toastr.error("numero de carnet ya fue asignado", "error")
        return
      }*/

      if (self.detalle_asignacion.some(x => x.empleado_id == data.empleado_id)) {
        self.$toastr.error("empleado ya fue asignado", "error")
        return
      }

      self.loading = true
      self.$store.state.services.asignacionDomoService
        .create(data)
        .then(r => {
          self.loading = false
          if (self.$store.state.global.captureError(r)) {
            return
          }
          this.$toastr.success("empleado asignado con éxito", "éxito");
          self.getAll(self.id)
          //self.clearData()
        })
        .catch(r => {});
    },

    //funcion para eliminar registro
    destroy(data) {
      let self = this;
      self.$confirm("Seguro que desea remover empleado?")
        .then(res => {
          self.loading = true;
          self.$store.state.services.asignacionDomoService
            .destroy(data)
            .then(r => {
              self.loading = false;
              if (self.$store.state.global.captureError(r)) {
                return;
              }
              self.getAll(self.id);
              this.$toastr.success("empleado removido con exito", "exito");
              //self.clearData();
            })
            .catch(r => {});
        })
        .catch(cancel => {});
    },

            //obtener detalles
    getByEmpleado(id, empleado_id) {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionDomoService
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

    //validar formulario
    validateForm(scope) {
      let self = this;
      this.$validator.validateAll(scope).then(result => {
        if (result) {
          self.create()
        }
      });
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
      self.form.codigo = data.codigo;
    },


    cancelar() {
      let self = this
    },

    close() {
      let self = this
      self.dialog = false
      self.clearData();
    },

    detailChange() {
      let self = this
      self.detalle_asignacion = self.items.filter(x=>x.fecha == self.form.fecha)
    },

    changeEmpleado(id){
      let self = this
      let empleado_exists = self.items.find(x=>x.empleado_id == id)
      if(empleado_exists == undefined){
        self.form.carnet_id = null
        self.empleado_carnet = null
      }else{
        self.empleado_carnet = empleado_exists
        self.form.carnet_id = empleado_exists.carnet.id
      } 
      
    },

     //imprimir hora entrada
    print() {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionDomoService
        .print(self.id, self.form.fecha)
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
            "asignacion_domo_" + self.form.fecha
          );
          //link.target = '_blank'
          link.click();
        })
        .catch(r => {});
    },
  },

  computed: {
    setTitle() {
      let self = this;
      return self.form.id !== null
        ? "actualizar codigo " + self.form.codigo
        : "Nuevo Registro";
    }
  }
};
</script>
