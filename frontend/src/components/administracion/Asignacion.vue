<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title>ASIGNACIONES ENTREADA A PORTUARIA </v-toolbar-title>
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
        <v-dialog v-model="dialog" full-width persistent>
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

                <v-flex xs4>
                    <v-card>
                        <v-form data-vv-scope="form_a">
                             <v-card-text class="px-0"> 
                            <v-card-title>
                                <span class="headline">Buscar</span>
                            </v-card-title>
                            
                            <v-flex xs12 sm12 md12>
                                <v-text-field v-model="form_a.fecha_atraque" 
                                    label="Fecha atraque"
                                    v-validate="'required'"
                                    type="date"
                                    data-vv-name="fecha_atraque"
                                    data-vv-as="fecha de atraque"
                                    :error-messages="errors.collect('form_a.fecha_atraque')">
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
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
                                    :error-messages="errors.collect('form_a.buque_id')">
                                    >
                                </v-autocomplete>
                            </v-flex>
                        </v-card-text>
                        </v-form>
                       
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="green darken-1" flat @click="validateForm('form_a')"><v-icon>search</v-icon> buscar</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
                <v-flex xs8>
                    <v-card>
                        <v-card-title>
                            <div>
                              <span class="headline">Detalle asignación</span><br />
                              <strong v-if="planificacion !== null">Fecha de zarpe: {{planificacion.fecha_zarpe | moment('DD/MM/YYYY')}}</strong>
                            </div>
                        </v-card-title>
                        <v-card-text class="px-0"> 

                            <v-form data-vv-scope="form_d">
                             <v-card-text class="px-0"> 
                                 <v-container grid-list-md>
                                    <v-layout wrap>
                                        <v-flex xs12 sm4 md4>
                                            <v-text-field v-model="form_d.fecha" 
                                                label="Fecha"
                                                v-validate="'required'"
                                                type="date"
                                                data-vv-name="fecha"
                                                :error-messages="errors.collect('form_d.fecha_atraque')">
                                            </v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form_d.empleado_id"
                                                label="Empleado"
                                                placeholder="seleccione empleado"
                                                :items="empleados"
                                                item-text="nombre"
                                                item-value="idEmpleado"
                                                v-validate="'required'"
                                                data-vv-name="empleado"
                                                :error-messages="errors.collect('form_d.empleado')">
                                                >
                                            </v-autocomplete>
                                        </v-flex>

                                        <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form_d.carnet_id"
                                                label="Carnet"
                                                placeholder="seleccione carnet"
                                                :items="carnets"
                                                item-text="codigo"
                                                item-value="id"
                                                v-validate="'required'"
                                                data-vv-name="carnet"
                                                :error-messages="errors.collect('form_d.carnet')">
                                                >
                                            </v-autocomplete>
                                        </v-flex>

                                        <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form_d.turno_id"
                                                label="Turno"
                                                placeholder="seleccione turno"
                                                :items="turnos"
                                                item-text="turno"
                                                item-value="id"
                                                v-validate="'required'"
                                                data-vv-name="turno"
                                                :error-messages="errors.collect('form_d.turno')">
                                                >
                                            </v-autocomplete>
                                        </v-flex>
                                        <v-flex xs6 sm2 md2>
                                            <v-btn block dark color="green darken-1" @click="validateForm('form_d')"><v-icon>add</v-icon> agregar</v-btn>
                                        </v-flex>

                                    </v-layout>
                                 </v-container>

                        </v-card-text>
                        </v-form>

                            <v-data-table
                                    :headers="headers_details"
                                    :items="form.detalle"
                                    :search="search"
                                    :expand="false"
                                    class="elevation-1"
                                    disable-initial-sort
                            >
                                <template v-slot:items="props">
                                    <td class="text-xs-left">{{props.item.empleado}}</td>
                                    <td class="text-xs-left">{{props.item.carnet}}</td>
                                </template>
                            </v-data-table>
                        </v-card-text>

                    </v-card>
                </v-flex>
                  
                </v-layout>
              </v-container>
            </v-card-text>
  
            <v-card-actions>
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
          <td class="text-xs-left">{{props.item.planificacion.buque.nombre}}</td>
          <td class="text-xs-left">{{props.item.planificacion.fecha_atraque | moment('DD/MM/YYYY')}}</td>
          <td class="text-xs-left">{{props.item.planificacion.fecha_zarpe | moment('DD/MM/YYYY')}}</td>
          
          <td class="text-xs-left">
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
  name: "asignacion",
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
      turnos: [],
      carnets: [],
      empleados: [],
      planificacion: null,
      headers: [
        { text: 'Buque', value: 'buque' },
        { text: 'Fecha de atraque', value: 'fecha_atraque' },
        { text: 'Fecha de sarpe', value: 'fecha_sarpe' },
        { text: 'Acciones', value: '', sortable: false }
      ],
      headers_details: [
          {text: 'Empleado', value: 'empleado'},
          {text: 'Carnet', value: 'carnet'},
          {text: 'fecha', value: 'fecha'}
      ],
      form:{
          id: null,
          planificacion_id: null,
          detalle_asignacion: []
      },
      form_a: {
        fecha_atraque: null,
        buque_id: null
      },
      form_d: {
          fecha: null,
          empleado_id: null,
          turno_id: null,
          carnet_id: null
      }
    };
  },

  created() {
    let self = this
    self.getAll()
    self.getBuques()
    self.getCarnets()
    self.getTurnos()
  },

  methods: {
     getAll() {
      let self = this
      self.loading = true
      self.$store.state.services.asignacionService
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

    //obtener buques
    getBuques() {
      let self = this
      self.loading = true
      self.$store.state.services.buqueService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.buques = r.data
        })
        .catch(r => {});
    },

    //obtener buques
    getTurnos() {
      let self = this
      self.loading = true
      self.$store.state.services.turnoService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          r.data.map(obj=> ({ ...obj.turno ='turno #'+ obj.numero+': '+ moment('2020-10-13 '+obj.hora_inicio).format('h:mm a')
                                                        +' - '+moment('2020-10-13 '+obj.hora_fin).format('h:mm a')}))
          self.turnos = r.data
        })
        .catch(r => {});
    },

    //obtener buques
    getCarnets() {
      let self = this
      self.loading = true
      self.$store.state.services.carnetService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.carnes = r.data
        })
        .catch(r => {});
    },

    //buscar planificacion
    searchPlanification() {
      let self = this
      self.loading = true
      self.$store.state.services.planificacionService
        .search(self.form_a.fecha_atraque,self.form_a.buque_id)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            self.planificacion = null
            return
          }

          self.planificacion = r.data
        })
        .catch(r => {});
    },

    //funcion para guardar registro
    create(){
      let self = this
      let data = self.form
      self.loading = true
      self.$store.state.services.asignacionService
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
       
      self.$store.state.services.asignacionService
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
      self.$confirm('Seguro que desea eliminar asignación?').then(res => {
        self.loading = true
            self.$store.state.services.asignacionService
            .destroy(data)
            .then(r => {
                self.loading = false
                if(self.$store.state.global.captureError(r)){
                    return
                }
                self.getAll()
                this.$toastr.success('registro eliminado con éxito', 'éxito')
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

    //mapear datos a formulario
    mapData(data){
        let self = this
        self.form.id = data.id
        self.form.planificacion_id = data.planificacion_id
    },

    //validar formulario
    validateForm(scope){
      let self = this
      this.$validator.validateAll(scope).then((result) => {
          if (result) {
             scope == 'form_a' ? self.searchPlanification() : scope == 'form' ? self.createOrEdit() : self.addDetail()
            }
      });
    },

    //agregar detalle
    addDetail(){
        let self = this
        let data = self.form_d
        let carnet = self.carnets.find(x=>x.id == data.carnet_id)
        let turno = self.turnos.find(x=>x.id == data.turno_id)
        let empleado = self.empleados.find(x=>x.idEmpleado == data.empleado_id)
        self.form.detalle_asignacion.push([{
            'empleado_id':data.empleado_id,
            'carnet_id':data.carnet_id,
            'turno_id':data.turno_id,
            'fecha':data.fecha,
            'carnet':carnet.carnet,
            'turno':turno.turno,
            'empleado':empleado.nombre
        }])
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit(){
        let self = this
        if(self.form.id > 0 && self.form.id !== null){
            self.update()
        }else{
            self.create()
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
      return self.form.id !== null ? 'actualizar asignación' : 'Nuevo Registro'
    }
  },
};
</script>