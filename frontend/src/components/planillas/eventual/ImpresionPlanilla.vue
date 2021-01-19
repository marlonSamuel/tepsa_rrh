<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
     <v-dialog v-model="dialog" max-width="800px" persistent>
          <v-card>
            <v-card-title>
              <span class="headline">Ingresar descuentos {{form.empleado}}</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.prestamos"
                      label="Descuento prestamos"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="prestamos"
                      :error-messages="errors.collect('prestamos')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.alimentacion"
                      label="Descuento alimentación"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="alimentacion"
                      :error-messages="errors.collect('alimentacion')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.otros_descuentos"
                      label="Otros descuentos"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="otros_descuentos"
                      :error-messages="errors.collect('otros_descuentos')"
                    >
                    </v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red darken-1" flat @click="close">Volver</v-btn>
              <v-btn color="blue darken-1" flat @click="validate"
                >Guardar</v-btn
              >
            </v-card-actions>
          </v-card>
        </v-dialog>
      <v-card>
          <v-card-title>
                IMPRESION PLANILLA
                
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-icon color="red" v-on="on" large fab dark @click="print(false)">fas fa-file-pdf</v-icon>
                    </template>
                    <span>Imprimir boletas</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                         <v-icon color="success" v-on="on" large fab dark @click="exportExcel">fas fa-file-excel</v-icon>
                    </template>
                    <span>Exportar excel</span>
                </v-tooltip>         
          </v-card-title>
          <v-card-text>
              <v-flex sm4 md4 lg4>
                 <v-text-field
                  v-model="search"
                  append-icon="search"
                  label="Buscar"
                  single-line
                  hide-details
                ></v-text-field>
              </v-flex>
             <br />
                <v-data-table
                    :headers="headers"
                    :items="items"
                    :search="search"
                    class="elevation-1"
                >
                    <template v-slot:items="props">
                        <td class="text-xs-left">{{props.item.codigo}}</td>
                        <td class="text-xs-left">{{props.item.nombre}}</td>
                        <td class="text-xs-left">{{props.item.afiliacion_igss}}</td>
                        <td class="text-xs-left">{{props.item.dpi}}</td>
                        <td class="text-xs-left">{{props.item.puesto}}</td>
                        <td class="text-xs-left">{{props.item.cuenta}}</td>
                        <td class="text-xs-left">{{props.item.turnos_trabajados}}</td>
                        <td class="text-xs-left">{{props.item.septimo | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.total_devengado | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.igss | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.bonificacion_incetivo | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.bono_14 | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.aguinaldo | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.prestamos | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.alimentos | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.otros_descuentos | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.liquido_a_recibir | currency('Q ')}}</td>
                        <td class="text-xs-left">
                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" color="success" small fab dark @click="print(props.item)"> print</v-icon>
                                </template>
                                <span>Imprimir boleta</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-icon v-on="on"  color="warning" small fab dark @click="edit(props.item)"> edit</v-icon>
                                </template>
                                <span>Editar</span>
                            </v-tooltip>
                        </td>
                    </template>
                    <template v-slot:no-data>
                    <v-btn color="primary" @click="getAll">Reset</v-btn>
                    </template>
                </v-data-table>
          </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>


<script>
import moment from 'moment'
import fileSaver from 'file-saver'
export default {
  name: "info_planilla_eventual_impresion_planilla",
  props: {
      source: String
    },
  data() {
    return {
      search: '',
      loading: false,
      dialog: false,
      id: null,
      items: [],
      planilla: {},
      headers: [
        { text: '#', value: 'codigo'},
        { text: 'Nombre empleado', value: 'nombre' },
        { text: 'Afiliación igss', value: 'afiliacion_igss' },
        { text: 'Dpi', value: 'dpi' },
        { text: 'Puesto', value: 'puesto' },
        { text: 'cuenta', value: 'cuenta' },
        { text: 'Turnos trabajados', value: 'turnos' },
        { text: 'Septimo',value: 'septimo' },
        { text: 'Total devengado', value: 'total_deventado' },
        { text: 'Descuento de IGSS', value: 'igss' },
        { text: 'Bonificación de ley', value: 'bonificacion_incetivo' },
        { text: 'Bono 14', value: 'bono_14' },
        { text: 'Aguinaldo', value: 'aguinaldo' },
        { text: 'Prestamos', value: 'prestamos' },
        { text: 'Alimentacion', value: 'alimentos' },
        { text: 'Otros descuentos', value: 'Otros_descuentos' },
        { text: 'Liquido a recibir', value: 'liquido_a_recibir' },
        { text: 'Acciones', value: '', sortable: false }
      ],

      form: {
        id: null,
        prestamos: null,
        alimentacion: null,
        otros_descuentos: null,
        empleado: ""
      }
    }
  },

  created() {
    let self = this
    self.id = self.$route.params.id
    self.getAll(self.id)
    self.get(self.id)
  },

  methods: {
     getAll(id) {
      let self = this
      self.loading = true
      self.$store.state.services.planillaEventualService
        .info(id,'P')
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.items = r.data.data
        })
        .catch(r => {});
    },

    get(id){
      let self = this
      self.loading = true
      self.$store.state.services.planillaEventualService
        .get(id)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.planilla = r.data
        })
        .catch(r => {});
    },

    edit(data){
      let self = this
      self.dialog = true
      self.form.id = data.id
      self.form.alimentacion = data.alimentos
      self.form.prestamos = data.prestamos
      self.form.otros_descuentos = data.otros_descuentos
      self.form.empleado = data.nombre
    },

     update() {
      let self = this;
      self.loading = true;
      let data = self.form;

      self.$store.state.services.pagoEmpleadoEventualService
        .update(data)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.getAll(self.id);
          this.$toastr.success("registro actualizado con éxito", "éxito");
          self.close();
        })
        .catch(r => {});
    },

    validate() {
      let self = this
      this.$validator.validateAll().then(result => {
        if (result) {
            self.update()
        }
      })
    },

     close() {
      let self = this;
      self.dialog = false;
      self.clearData();
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

    print(data) {
      let self = this
      let id = !data ? 0 : data.id 
      console.log(self.planilla)
      self.loading = true
      self.$store.state.services.pagoEmpleadoEventualService
        .print(self.id,id)
        .then(r => {
          self.loading = false
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
            id > 0 ? "planilla_"+self.planilla.numero+"boleta_"+data.nombre : "planilla_"+self.planilla.numero
          );
          //link.target = '_blank'
          link.click()
        })
        .catch(r => {});
    },

    
      exportExcel(){
        let self = this
        self.loading = true
        self.$store.state.services.planillaEventualService
            .export(self.id)
            .then(response => {
                var file_name = 'planilla_'+self.planilla.numero
                self.loading = false
                if(response.response){
                    this.$toastr.error(r.response.data.error, 'error')
                    return
                }
                var blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                fileSaver.saveAs(blob, file_name);
                a.click();
            })
            .catch(r => {});
        }
  },

  computed: {
  },
};
</script>