<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>Planilla personal eventual </v-toolbar-title>
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
        <v-dialog v-model="dialog" max-width="800px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" small dark class="mb-2" v-on="on"><v-icon>add</v-icon> Nuevo</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{setTitle}}</span>
            </v-card-title>
  
           
                <v-card-text class="px-0">
                    <v-container grid-list-md>
                        <v-form data-vv-scope="form_a" v-if="form.id == null">
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
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
                    <v-flex xs12 sm6 md6>
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
                        </v-layout>
                        
                        
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
                        
                    </v-form>
                    
                       <v-form data-vv-scope="form" v-if="form.asignacion_empleado_id !== null">
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form.numero"
                                    label="Planilla No."
                                    v-validate="'required'"
                                    type="text"
                                    data-vv-name="numero"
                                    data-vv-as="numero de planilla"
                                    readonly
                                    :error-messages="
                                    errors.collect('form.numero')
                                    "
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form.inicio_descarga"
                                    label="Fecha inicio descarga"
                                    v-validate="'required'"
                                    type="date"
                                    data-vv-name="inicio_descarga"
                                    data-vv-as="fecha inicio descarga"
                                    readonly
                                    :error-messages="
                                    errors.collect('form.inicio_descarga')
                                    "
                                >
                                </v-text-field>
                            </v-flex>
                                <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form.fin_descarga"
                                    label="Fecha fin descarga"
                                    v-validate="'required'"
                                    type="date"
                                    data-vv-name="fin_descarga"
                                    data-vv-as="fecha fin descarga"
                                    readonly
                                    :error-messages="
                                    errors.collect('form.fin_descarga')
                                    "
                                >
                                </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form.fecha"
                                    label="Fecha planilla"
                                    v-validate="'required'"
                                    type="date"
                                    data-vv-name="fecha"
                                    data-vv-as="fecha de planilla"
                                    @input="setNumber"
                                    :error-messages="
                                    errors.collect('form.fecha')
                                    "
                                >
                                </v-text-field>
                            </v-flex>

                            
                        
                        </v-layout>
                    </v-form>

                      
                </v-container>

                    
            </v-card-text>
            
            <v-card-actions>
            <v-spacer></v-spacer>
                <v-btn color="green darken-1" flat @click="validateForm('form')" v-if="form.asignacion_empleado_id !== null">Guardar</v-btn>
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
          <td class="text-xs-left">{{props.item.numero}}</td>
          <td class="text-xs-left">{{props.item.fecha | moment('DD/MM/YYYY')}}</td>
          <td class="text-xs-left">{{props.item.buque}}</td>
          <td class="text-xs-left">{{props.item.inicio_descarga | moment('DD/MM/YYYY')}}</td>
          <td class="text-xs-left">{{props.item.fin_descarga | moment('DD/MM/YYYY')}}</td>
          <td class="text-xs-left">
               <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-icon v-on="on" @click="$router.push('planilla_eventual_info/'+props.item.id)"  color="success" fab dark> info</v-icon>
                </template>
                <span>Información planilla</span>
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

<script>
import moment from 'moment'
export default {
  name: "turno",
  props: {
      source: String
    },
  data() {
    return {
      dialog: false,
      search: '',
      loading: false,
      items: [],
      buques: [],
      headers: [
        { text: '#', value: 'numero', width:"10%"},
        { text: 'Fecha', value: 'fecha' },
        { text: 'Buque', value: 'buque' },
        { text: 'Fecha inicio descarga', value: 'inicio_descarga' },
        { text: 'Fecha fin descarga', value: 'fin_descarga' },
        { text: 'Acciones', value: '', sortable: false }
      ],
      form: {
            id: null,
            asignacion_empleado_id: null,
            buque:"",
            numero:"PQ-01-2020",
            inicio_descarga:"",
            fecha:"",
            fin_descarga:""
      },

      form_a: {
        fecha_atraque: null,
        buque_id: null
      }
    }
  },

  created() {
    let self = this
    self.getAll()
    self.getBuques()
  },

  methods: {
     getAll() {
      let self = this
      self.loading = true
      self.$store.state.services.planillaEventualService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.items = r.data
          self.setNumber()
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
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.buques = r.data;
        })
        .catch(r => {});
    },

    //buscar planificacion
    searchPlanification() {
      let self = this;
      self.loading = true;
      self.$store.state.services.asignacionService
        .search(self.form_a.fecha_atraque, self.form_a.buque_id)
        .then(r => {
          self.loading = false;
          if (self.$store.state.global.captureError(r)) {
            return;
          }
          self.setValues(r.data.asignacion.detalle_asignacion)
          self.form.asignacion_empleado_id = r.data.asignacion.id
          self.form.buque = r.data.buque.nombre
        })
        .catch(r => {});
    },

    //set date values
    setValues(asignaciones){
        let self = this
        asignaciones = asignaciones.sort(function(a,b){
            return new Date(a.fecha) - new Date(b.fecha);
        })
        self.form.inicio_descarga = asignaciones[0].fecha
        self.form.fin_descarga = asignaciones[asignaciones.length-1].fecha
    },

    //set number
    setNumber(){
        let self = this
        let date = self.form.fecha !== "" ? moment(self.form.fecha) : moment() 
        let planillas_year = self.items.filter(x=>moment(x.fecha).year() == date.year())
        let numero = (planillas_year.length + 1) < 10 ? '0'+(planillas_year.length+1) : planillas_year.length+1

        self.form.numero = 'PQ-'+numero+'-'+date.year()
        console.log(self.form)
    },

    //funcion para guardar registro
    create(){
      let self = this
      let data = self.form
      self.loading = true
      self.$store.state.services.planillaEventualService
        .create(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          this.$toastr.success('planilla procesada con éxito', 'éxito')
          self.getAll()
          self.close()
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update(){
      let self = this
      self.loading = true
      let data = self.form
       
      self.$store.state.services.planillaEventualService
        .update(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.getAll()
          this.$toastr.success('registro actualizado con éxito', 'éxito')
          self.close()
        })
        .catch(r => {});
    },

    //funcion para eliminar registro
    destroy(data){
      let self = this
      self.$confirm('Seguro que desea eliminar planilla, ya no será posible recuperarla?').then(res => {
        self.loading = true
            self.$store.state.services.planillaEventualService
            .destroy(data)
            .then(r => {
                self.loading = false
                if(self.$store.state.global.captureError(r)){
                    return
                }
                self.getAll()
                this.$toastr.success('planilla eliminada con éxito', 'exito')
                self.close()
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

    //mapear datos a formulario
    mapData(data){
        let self = this
        self.form.id = data.id
        self.form.fecha = data.fecha
        self.form.numero = data.numero
        self.form.inicio_descarga = data.inicio_descarga
        self.form.fin_descarga = data.fin_descarga
        self.form.buque = data.buque
    },

    //funcion, validar si se guarda o actualiza
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
    
    cancelar(){
      let self = this
    },

    close () {
        let self = this
        self.dialog = false
        self.clearData()
    },
  },

  computed: {
    setTitle(){
      let self = this
      return self.form.id == null ? 'Nueva Planilla' : 'Actualizar planilla buque '+self.form.buque
    }
  },
};
</script>