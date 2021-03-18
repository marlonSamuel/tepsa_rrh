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
                  <v-flex xs4 sm4 md4 v-show="false">
                    <v-text-field
                      v-model="form.anticipo"
                      label="Anticipo"
                      v-validate="'decimal'"
                      type="number"                     
                      data-vv-name="anticipo"
                      :error-messages="errors.collect('anticipo')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.otro_ingreso"
                      label="Prestamo"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="otro_ingreso"
                      :error-messages="errors.collect('otro_ingreso')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.hora_extra_simple"
                      label="Total de Horas Extras Simple"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="hora_extra_simple"
                      :error-messages="errors.collect('hora_extra_simple')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.hora_extra_doble"
                      label="Total de Horas Extras Doble"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="hora_extra_doble"
                      :error-messages="errors.collect('hora_extra_doble')"
                    >
                    </v-text-field>
                  </v-flex>
                  <v-flex xs4 sm4 md4>
                    <v-text-field
                      v-model="form.otro_descuento"
                      label="Otros Descuentos"
                      v-validate="'decimal'"
                      type="number"
                      data-vv-name="otro_descuento"
                      :error-messages="errors.collect('otro_descuento')"
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
                        <td class="text-xs-left">{{props.item.salario | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.bonificacion_incetivo | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.otro_ingreso | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.hora_extra_simple}}</td>
                        <td class="text-xs-left">{{props.item.monto_hora_extra_simple | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.hora_extra_doble}}</td>
                        <td class="text-xs-left">{{props.item.monto_hora_extra_doble | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.total_ingresos | currency('Q ')}}</td> 
                        <td class="text-xs-left">{{props.item.anticipo | currency('Q ')}}</td> 
                        <td class="text-xs-left">{{props.item.igss | currency('Q ')}}</td>   
                        <td class="text-xs-left">{{props.item.ISR | currency('Q ')}}</td>                     
                        <td class="text-xs-left">{{props.item.otro_descuento | currency('Q ')}}</td>
                        <td class="text-xs-left">{{props.item.total_egresos | currency('Q ')}}</td> 
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
  name: "info_planilla_fijo_impresion_planilla",
  props: {
      source: String,
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
        { text: 'Salario Ordinario Mensual', value: 'salario' },
        { text: 'Bonificacion Incentivo', value: 'bonificacion_incetivo' },
        { text: 'Prestamo', value: 'otro_ingreso' },
        { text: 'H. Extra Simple', value: 'hora_extra_simple' },
        { text: 'Total H. Extra Simple', value: 'monto_hora_extra_simple' },
        { text: 'H. Extra Doble', value: 'hora_extra_doble' },
        { text: 'Total H. Extra Doble', value: 'monto_hora_extra_doble' },
        { text: 'Total Ingresos', value: 'total_ingresos' },
        { text: 'Anticipo', value: 'anticipo' },
        { text: 'IGSS', value: 'igss' }, 
        { text: 'ISR', value: 'ISR' },        
        { text: 'Otros descuentos', value: 'otro_descuento' },
        { text: 'Total Egresos', value: 'total_egresos' },
        { text: 'Liquido a recibir', value: 'liquido_a_recibir' },
        { text: 'Acciones', value: '', sortable: false }
      ],

      form: {
        id: null,
        anticipo: null,
        otro_ingreso: null,
        hora_extra_simple: null,
        hora_extra_doble:null,
        otro_descuento:null,
        empleado: ""
      }
    }
  },

  created() {
    let self = this
    self.id = self.$route.params.id
    console.log(self.id);
    self.getAll(self.id);
    self.get(self.id)
  },

  methods: {
     getAll(id) {
      let self = this
      self.loading = true
      self.$store.state.services.pagoEmpleadoFijoService
        .info(id,'P')
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          console.log(r.data.data);
          self.items = r.data.data
        })
        .catch(r => {});
    },

    get(id){
      let self = this
      self.loading = true
      self.$store.state.services.pagoEmpleadoFijoService
        .getPlanilla(id)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
            self.planilla = r.data;
        })
        .catch(r => {});
    },

    edit(data){
      let self = this
      self.dialog = true
      self.form.id = data.id
      self.form.otro_ingreso = data.otro_ingreso
      self.form.anticipo = data.anticipo
      self.form.hora_extra_simple = data.hora_extra_simple
      self.form.hora_extra_doble = data.hora_extra_doble
      self.form.otro_descuento = data.otro_descuento
      self.form.empleado = data.nombre
    },

     update() {
      let self = this;
      self.loading = true;
      let data = self.form;

      self.$store.state.services.pagoEmpleadoFijoService
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
      self.$store.state.services.pagoEmpleadoFijoService
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
            id > 0 ? "planilla_"+self.planilla.quincena+'-'+self.planilla.anio.anio+"boleta_"+data.nombre : "planilla_"+self.planilla.quincena+'-'+self.planilla.anio.anio
          );
          //link.target = '_blank'
          link.click()
        })
        .catch(r => {});
    },

    
      exportExcel(){
        let self = this
        self.loading = true
        self.$store.state.services.pagoEmpleadoFijoService
            .export(self.id)
            .then(response => {
                var file_name = 'Planilla_Fijos-PQ-'+self.planilla.quincena+'-'+self.planilla.anio.anio
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