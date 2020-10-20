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
        <v-tooltip top v-if="items.length > 0">
            <template v-slot:activator="{ on }">
              <v-btn small v-on="on"><v-icon>print</v-icon></v-btn>
            </template>
            <span>Imprimir</span>
        </v-tooltip>
        <v-toolbar-title>HISTORIAL ASISTENCIA </v-toolbar-title>
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
      </v-toolbar>

      <v-toolbar flat color="white">
         
        <v-container grid-list-md>
          <v-layout wrap>
              <v-flex xs12 sm3 md3>
                <v-text-field v-model="form.fecha" 
                    label="Fecha"
                    type="date"
                    @input="changeReq">
                </v-text-field>
            </v-flex>
            <v-flex xs12 sm3 md3>
                <v-autocomplete
                    v-model="form.turno_id"
                    label="Turno"
                    placeholder="seleccione turno"
                    :items="turnos"
                    item-text="numero"
                    item-value="id"
                    @change="changeReq">
                </v-autocomplete>
            </v-flex>
            <v-flex xs12 sm3 md3>
                <v-autocomplete
                    v-model="form.buque_id"
                    label="Buque"
                    placeholder="seleccione buque"
                    :items="buques"
                    item-text="nombre"
                    item-value="idBuque"
                    @input="changeBuq">
                </v-autocomplete>
            </v-flex>
            <v-flex xs12 sm3 md3>
                <v-autocomplete
                    v-model="form.bodega"
                    label="Bodega"
                    placeholder="seleccione bodega"
                    :items="bodegas"
                    @input="changeBod">
                </v-autocomplete>
            </v-flex>
          </v-layout>
        </v-container>
      </v-toolbar>

      
      <v-data-table
        :headers="headers"
        :items="items"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:items="props">
          <td class="text-xs-left">{{props.item.empleado.primer_nombre}}
                                    {{props.item.empleado.segundo_nombre}}
                                    {{props.item.empleado.primer_apellido}}
                                    {{props.item.empleado.segundo_apellido}}
          </td>
          <td class="text-xs-left">{{props.item.asistencia_turno.cargo_turno.cargo.nombre}}</td>
          <td class="text-xs-left">{{props.item.asistencia_turno.bodega}}</td>
          <td class="text-xs-left">{{props.item.asistencia_turno.hora_entrada | moment('hh:mm')}}</td>
          <td class="text-xs-left">
              <span v-if="props.item.asistencia_turno.hora_salida !==null">
                  {{props.item.asistencia_turno.hora_salida | moment('hh:mm')}}
              </span>
              <span v-else class="text--red">
                  No marcó salida
              </span>
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
  name: "TurnoIndex",
  components: {
    
  },
  props: {
      source: String
    },
  data() {
    return {
      dialog: false,
      search: '',
      loading: false,
      all_items: [],
      items: [],
      turnos: [],
      buques: [],
      bodegas: [],
      headers: [
        { text: 'empleado', value: 'empleado' },
        { text: 'rol', value: 'rol' },
        { text: 'No. bodega', value: 'bodega' },
        { text: 'Hora entrada', value: 'hora_entrada' },
        { text: 'Hora salida', value: 'hora_salida' },
      ],

      itemsB: [
        {
          text: 'ASISTENCIA',
          disabled: false,
          href: '#/asistencia_turno',
        },
        {
          text: 'HISTORIAL ASISTENCIA',
          disabled: true,
          href: '#',
        },
      ],

      form: {
        fecha: null,
        turno_id: null,
        buque_id: null,
        bodega: null
      },
    };
  },

  created() {
    let self = this
    self.getTurns()
  },

  methods: {
      //obtener asistencias
    getAll(fecha,turno) {
        let self = this
        self.loading = true

        self.$store.state.services.detalleAsignacionService
        .getTurnDate(fecha,turno)
        .then(r=>{
            self.loading = false
            if(self.$store.state.global.captureError(r)){
                return
            }
            
            self.bodegas = []
            self.buques = []
            self.all_items = r.data
            r.data.forEach((e,i)=>{
                self.buques.push(e.asignacion.planificacion.buque)
            })
            //self.items = r.data
        }).catch(e=>{})
    },

    //obtener turnos
    getTurns(){
        let self = this
        self.loading = true
        self.$store.state.services.turnoService
        .getAll()
        .then(r=>{
            self.loading = false
            if(self.$store.state.global.captureError(r)){
            return
            }
            self.turnos = r.data
        }).catch(e=>{})
    },

    //capturar onChange para el request
    changeReq(){
      let  self = this
      let data = self.form
      if(data.turno_id !== null && data.fecha !== null){
        self.getAll(data.fecha,data.turno_id)
      }
    },

    //change buque
    changeBuq(){
        let self = this
        self.bodegas = []
        let buque = self.buques.find(x=>x.idBuque == self.form.buque_id)
        for(var i=1; i<=buque.no_bodegas; i++){
            self.bodegas.push({text:i,value:i})
        }
    },

    //change bodegas
    changeBod(){
        let self = this
        self.items = self.all_items.filter(x=>x.asistencia_turno.bodega == self.form.bodega)
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