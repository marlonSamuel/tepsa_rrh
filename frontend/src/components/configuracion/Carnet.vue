<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>CARNETS </v-toolbar-title>
        <v-divider
          class="mx-2"
          inset
          vertical
        ></v-divider><v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            append-icon="search"
            label="Buscar"
            single-line
            hide-details
          ></v-text-field>
        <v-spacer></v-spacer>

        <v-dialog v-model="showCarnet" max-width="500px">
            
            <v-card>
                
                <v-card-actions>
                    <v-btn flat color="info" @click="print"><v-icon>print</v-icon></v-btn>
                </v-card-actions>

                <v-card-text>
                   <div class="card-row" id="print" ref="print">
                          <div class="columnCard">
                              <article class="card">
                                <img :src="this.$store.state.global.getLogo()"/>
                                <qrcode-vue :size="70" v-if="form.codigo !== null" :value="form.codigo"></qrcode-vue>
                                <br />
                                <h5>codigo: {{form.codigo}}</h5>
                            </article> 
                          </div>
                          <div class="column">
                              <article class="card">
                                <img :src="this.$store.state.global.getLogo()"/>
                                <qrcode-vue :size="70" v-if="form.codigo !== null" :value="form.codigo"></qrcode-vue>
                                <br />
                                <h5>codigo: {{form.codigo}}</h5>
                            </article> 
                          </div>
                       
                    </div>
                </v-card-text>
                
            </v-card>
            
        </v-dialog>

        <v-dialog v-model="dialog" max-width="800px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" small dark class="mb-2" v-on="on"><v-icon>add</v-icon> Nuevo</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{setTitle}}</span>
            </v-card-title>
  
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs6 sm6 md6>
                    <v-text-field v-model="form.codigo" 
                        label="Codigo"
                        v-validate="'required'"
                        type="text"
                        data-vv-name="codigo"
                        :error-messages="errors.collect('codigo')">
                    </v-text-field>
                  </v-flex>
                  <v-flex xs6 sm3 md3 v-if="form.codigo !== null">
                      <qrcode-vue :value="form.codigo"></qrcode-vue>
                  </v-flex>
                  
                </v-layout>
              </v-container>
            </v-card-text>
  
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="red darken-1" flat @click="close">Volver</v-btn>
              <v-btn color="blue darken-1" flat @click="createOrEdit">Guardar</v-btn>
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
          <td class="text-xs-left">{{props.item.codigo}}</td>
          <td class="text-xs-left">
              <v-chip small :color="props.item.asignado?'primary':'green'">{{props.item.asignado ? 'Asignado':'Disponible'}}</v-chip>
              
            </td>
          <td class="text-xs-left">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-icon v-on="on"  color="info" fab dark @click="showInfo(props.item)"> remove_red_eye</v-icon>
                </template>
                <span>Mostrar carnet</span>
            </v-tooltip>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-icon v-on="on"  color="warning" fab dark @click="edit(props.item)"> edit</v-icon>
                </template>
                <span>Editar</span>
            </v-tooltip>
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-icon v-on="on" color="error" fab dark @click="destroy(props.item)"> delete</v-icon>
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

<style>

    .columnCard {
        float: right;
        width: 60%;
        padding: 0 10px;
    }

    .card-row {
      display: flex;
      justify-content: space-between;
    }

    .content {
        text-align: center;
    }

    .card {
      margin: auto;
      width: 150px;
      height: 200px;
      padding: 10px;
      border: 1px solid gray;
      border-left: 6px solid #130877;
      text-align: center;
      border-radius: 10px;
      box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.15);
    }

    .card > img:first-child {
      border-radius: 7px 7px 0 0;
      width: 150px;
      text-align: right;
      padding-right: 40px;
    }
</style>

<script>
import QrcodeVue from 'qrcode.vue'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'
import moment from 'moment'
var pdfMake = require('pdfmake/build/pdfmake.js')

export default {
  name: "carnet",
  components: {
      QrcodeVue
  },
  props: {
      source: String
    },
  data() {
    return {
      dialog: false,
      showCarnet: false,
      search: '',
      loading: false,
      items: [],
      headers: [
        { text: 'Codigo', value: 'codigo' },
        { text: 'Estado', value: 'asignado' },
        { text: 'Acciones', value: '', sortable: false }
      ],
      form: {
        id: null,
        codigo: null
      },
    };
  },

  created() {
    let self = this
    self.getAll()
  },

  methods: {
     getAll() {
      let self = this
      self.loading = true
      self.$store.state.services.carnetService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.items = r.data
        })
        .catch(r => {});
    },

    //funcion para guardar registro
    create(){
      let self = this
      let data = self.form
      self.loading = true
      self.$store.state.services.carnetService
        .create(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          this.$toastr.success('registro agregado con éxito', 'éxito')
          self.getAll()
          self.clearData()
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update(){
      let self = this
      self.loading = true
      let data = self.form
       
      self.$store.state.services.carnetService
        .update(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.getAll()
          this.$toastr.success('registro actualizado con éxito', 'éxito')
          self.clearData()
        })
        .catch(r => {});
    },

    //funcion para eliminar registro
    destroy(data){
      let self = this
      self.$confirm('Seguro que desea eliminar turno?').then(res => {
        self.loading = true
            self.$store.state.services.carnetService
            .destroy(data)
            .then(r => {
                self.loading = false
                if(self.$store.state.global.captureError(r)){
                    return
                }
                self.getAll()
                this.$toastr.success('registro eliminado con exito', 'exito')
                self.clearData()
            })
            .catch(r => {});
      }).catch(cancel =>{
      })
    },
    //limpiar data de formulario
    clearData(){
        let self = this
        Object.keys(self.form).forEach(function(key,index) {
          if(typeof self.form[key] === "string") 
            self.form[key] = ''
          else if (typeof self.form[key] === "boolean") 
            self.form[key] = true
          else if (typeof self.form[key] === "number") 
            self.form[key] = null
        });
        self.$validator.reset()
    },
    //editar registro
    edit(data){
        let self = this
        this.dialog = true
        self.mapData(data)   
    },

    showInfo(data){
        let self = this
        self.mapData(data)
        self.showCarnet = true
    },

    //mapear datos a formulario
    mapData(data){
        let self = this
        self.form.id = data.id
        self.form.codigo = data.codigo
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit(){
      this.$validator.validateAll().then((result) => {
          if (result) {
              if(self.form.id > 0 && self.form.id !== null){
                self.update()
              }else{
                self.create()
              }
           }
      });
      let self = this
    },
    
    cancelar(){
      let self = this
    },

    close () {
        let self = this
        self.dialog = false
        self.showCarnet = false
        self.clearData()
    },

    print () {
      let self = this
      self.loading = true

      // capturamos el div con html2canvas para despues descargarlo con pdfmake
      html2canvas(self.$refs.print,{scale:5}).then(canvas => {
          self.loading = true
          var data = canvas.toDataURL("image/png");
                var docDefinition = {
                    content: [{
                        image: data,
                        width: 500,
                    }],
                    pageSize: 'LETTER',
                };
                pdfMake.createPdf(docDefinition).download('carnet_'+self.form.codigo);
           self.loading = false
      }).catch((error) => {
          self.loading =false
      });
    }
  },

  computed: {
    setTitle(){
      let self = this
      return self.form.id !== null ? 'actualizar codigo '+self.form.codigo : 'Nuevo Registro'
    }
  },
};
</script>