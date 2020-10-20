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
                                    :readonly="form.id !== null ? true: false"
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
                                    :readonly="form.id !== null ? true: false"
                                    :error-messages="errors.collect('form_a.buque_id')">
                                    >
                                </v-autocomplete>
                            </v-flex>
                        </v-card-text>
                        </v-form>
                       
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn :disabled="form.id !== null ? true: false" color="green darken-1" flat @click="validateForm('form_a')"><v-icon>search</v-icon> buscar</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
                <v-flex xs8 v-if="planificacion !== null">
                    <v-card>
                        <v-card-title>
                            <div>
                              <span class="headline">Detalle asignación</span><br />
                              <strong v-if="planificacion !== null">Fecha de zarpe: {{planificacion.fecha_zarpe | moment('DD/MM/YYYY')}}</strong>
                            </div>
                        </v-card-title>
                        <v-card-text class="px-0"> 

                            <v-form data-vv-scope="form">
                             <v-card-text class="px-0"> 
                                 <v-container grid-list-md>
                                    <v-layout wrap>
                                       <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form.turno_id"
                                                label="Turno"
                                                placeholder="seleccione turno"
                                                :items="turnos"
                                                item-text="turno"
                                                item-value="id"
                                                v-validate="'required'"
                                                data-vv-name="turno"
                                                :error-messages="errors.collect('form.turno')"
                                                @change="detailChange">
                                                >
                                            </v-autocomplete>
                                        </v-flex>
                                        <v-flex xs12 sm4 md4>
                                            <v-text-field v-model="form.fecha" 
                                                label="Fecha"
                                                v-validate="'required'"
                                                type="date"
                                                data-vv-name="fecha"
                                                :error-messages="errors.collect('form.fecha')"
                                                @input="detailChange">
                                            </v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form.empleado_id"
                                                label="Empleado"
                                                placeholder="seleccione empleado"
                                                :items="empleados"
                                                item-text="empleado"
                                                item-value="idEmpleado"
                                                v-validate="'required'"
                                                data-vv-name="empleado"
                                                :error-messages="errors.collect('form.empleado')"
                                                >
                                                >
                                            </v-autocomplete>
                                        </v-flex>

                                        <v-flex xs12 sm4 md4>
                                            <v-autocomplete
                                                v-model="form.carnet_id"
                                                label="Carnet"
                                                placeholder="seleccione carnet"
                                                :items="carnets"
                                                item-text="codigo"
                                                item-value="id"
                                                v-validate="'required'"
                                                data-vv-name="carnet"
                                                :error-messages="errors.collect('form.carnet')">
                                                >
                                            </v-autocomplete>
                                        </v-flex>

                                        <v-flex xs6 sm2 md2>
                                            <v-btn :disabled="planificacion!==null ? false:true" block dark color="green darken-1" @click="validateForm('form')"><v-icon>add</v-icon> agregar</v-btn>
                                        </v-flex>

                                    </v-layout>
                                 </v-container>

                        </v-card-text>
                        </v-form>

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
                                      #{{props.item.turno.numero}}-
                                      {{'2020-04-05 '+ props.item.turno.hora_inicio | moment('h:mm a')}}-
                                      {{'2020-04-05 '+ props.item.turno.hora_fin | moment('h:mm a')}}
                                    </td>
                                    <td class="text-xs-left">{{props.item.fecha | moment('DD/MM/YYYY')}}</td>
                                    <td class="text-xs-left">
                                      {{props.item.empleado.primer_nombre}} {{props.item.empleado.segundo_nombre}}
                                      {{props.item.empleado.primer_apellido}} {{props.item.empleado.segundo_apellido}}
                                    </td>
                                    <td class="text-xs-left">{{props.item.carnet.codigo}}</td>
                                    <v-tooltip top>
                                      <template v-slot:activator="{ on }">
                                          <v-icon v-on="on" color="error" fab dark @click="removeDetail(props.item)"> remove_circle</v-icon>
                                      </template>
                                      <span>Remover</span>
                                  </v-tooltip>
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
      detalle_asignacion: [],
      planificacion: null,
      headers: [
        { text: 'Buque', value: 'buque' },
        { text: 'Fecha de atraque', value: 'fecha_atraque' },
        { text: 'Fecha de sarpe', value: 'fecha_sarpe' },
        { text: 'Acciones', value: '', sortable: false }
      ],
      headers_details: [
        {text: 'turno', value: 'turno'},
        {text: 'fecha', value: 'fecha'},
        {text: 'Empleado', value: 'empleado'},
        {text: 'Carnet', value: 'carnet'},
        { text: 'Accion', value: '', sortable: false }
          
      ],
      form:{
          id: null,
          planificacion_id: null,
          detalle_id: null,
          fecha: null,
          empleado_id: null,
          turno_id: null,
          carnet_id: null
      },
      form_a: {
        fecha_atraque: null,
        buque_id: null
      }
    };
  },

  created() {
    let self = this
    self.getAll()
    self.getBuques()
    self.getCarnets()
    self.getTurnos()
    self.getEmpleados()
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
    getEmpleados() {
      let self = this
      self.loading = true
      self.$store.state.services.empleadoService
        .getAll()
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          r.data.map(obj=> ({ ...obj.empleado = obj.primer_nombre+' '+obj.segundo_nombre+' '+obj.primer_apellido+' '+obj.segundo_apellido}))
          self.empleados = r.data
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
          self.carnets = r.data
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
            self.form.planificacion_id = null
            self.detalle_asignacion = []
            return
          }
          self.planificacion = r.data
          self.form.planificacion_id = self.planificacion.idPlano_Estiba
          console.log(self.form)
        })
        .catch(r => {});
    },

    //obtener detalles
    getDetailData(id,turno_id,fecha){
      let self = this
      self.loading = true
      self.$store.state.services.asignacionService
        .getDetail(id,turno_id,fecha)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.detalle_asignacion = r.data
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
          self.form.id = r.data.id
          self.getAll()
          self.getDetailData(r.data.id, data.turno_id, data.fecha)
        })
        .catch(r => {});
    },

    //funcion para actualizar registro
    update(){
      let self = this
      let data = self.form

      if(self.detalle_asignacion.some(x=>x.carnet_id == data.carnet_id)){
        self.$toastr.error("numero de carnet ya fue asignado","error");
        return
      }

       if(self.detalle_asignacion.some(x=>x.empleado_id == data.empleado_id)){
        self.$toastr.error("empleado ya fue asignado","error");
        return
      }
      
      return
      self.loading = true
      self.$store.state.services.asignacionService
        .update(data)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.getDetailData(data.id, data.turno_id, data.fecha)
          //this.$toastr.success('registro actualizado con éxito', 'éxito')
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

    //remover detail
    removeDetail(data){
      let self = this
      self.$confirm('Seguro que desea remover asignacion?').then(res => {
        self.loading = true
            self.$store.state.services.detalleAsignacionService
            .destroy(data)
            .then(r => {
                self.loading = false
                if(self.$store.state.global.captureError(r)){
                    return
                }
                this.$toastr.success('empleado removido con éxito', 'éxito')
                self.getDetailData(data.asignacion_empleado_id, data.turno_id, data.fecha)  
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
        self.detalle_asignacion = []
        self.clearData()
        self.mapData(data)   
    },

    //mapear datos a formulario
    mapData(data){
        let self = this
        self.form.id = data.id
        self.form.planificacion_id = data.planificacion_id
        self.planificacion = data.planificacion
        self.form_a.buque_id = data.planificacion.idBuque
        self.form_a.fecha_atraque = data.planificacion.fecha_atraque
    },

    //validar formulario
    validateForm(scope){
      let self = this
      this.$validator.validateAll(scope).then((result) => {
          if (result) {
             scope == 'form_a' ? self.searchPlanification() : self.createOrEdit()
            }
      });
    },

    //funcion, validar si se guarda o actualiza
    createOrEdit(){
        let self = this
        console.log(self.form)
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

    detailChange(){
      let  self = this
      let data = self.form
      if(data.id !== null && data.turno_id !== null && data.fecha !== null){
        self.getDetailData(data.id, data.turno_id, data.fecha)
      }
    }
  },

  computed: {
    setTitle(){
      let self = this
      return self.form.id !== null ? 'actualizar asignación' : 'Nuevo Registro'
    }
  },
};
</script>