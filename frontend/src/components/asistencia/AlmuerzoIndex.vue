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
        <v-toolbar-title>HISTORIAL ASISTENCIA ALMUERZOS </v-toolbar-title>
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
            <v-flex xs12 sm6 md6>
                <v-autocomplete
                    v-model="form.fecha_buque"
                    label="Fecha / Buque"
                    placeholder="seleccione fecha y buque"
                    :items="asignaciones"
                    item-text="fecha_buque"
                    @input="change">
                </v-autocomplete>
            </v-flex>
            <v-flex xs12 sm3 md3>
                <v-autocomplete
                    v-model="form.turno_id"
                    label="Turno"
                    placeholder="seleccione turno"
                    :items="turnos"
                    item-text="turno.numero"
                    item-value="turno_id"
                    @input="changeTurn">
                </v-autocomplete>
            </v-flex>

             <v-flex xs12 sm3 md3>
              <v-tooltip top v-if="items.length > 0">
              <template v-slot:activator="{ on }">
                <v-btn small v-on="on" @click="print"><v-icon>print</v-icon></v-btn>
              </template>
              <span>Imprimir</span>
          </v-tooltip>
            </v-flex>
          </v-layout>
        </v-container>
      </v-toolbar>


      
      <v-data-table
        :headers="headers"
        :items="items"
        :search="search"
        :rows-per-page-items="rowsPerPageItems"
        :pagination.sync="pagination"
        class="elevation-1"
      >
        <template v-slot:items="props">
          <td class="text-xs-left">{{props.item.empleado.primer_nombre}}
                                    {{props.item.empleado.segundo_nombre}}
                                    {{props.item.empleado.primer_apellido}}
                                    {{props.item.empleado.segundo_apellido}}
          </td>
          <td class="text-xs-left">
              <span v-if="props.item.asistencia_almuerzo !== null">
                  {{props.item.asistencia_almuerzo.created_at | moment('hh:mm')}}
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
import moment from 'moment'
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
      asignaciones: [],
      all_items: [],
      items: [],
      turnos: [],
      headers: [
        { text: 'empleado', value: 'empleado' },
        { text: 'Hora', value: 'hora' }
      ],
      rowsPerPageItems: [10, 20, 30, 40],
      pagination: {
          rowsPerPage: 20
      },

      itemsB: [
        {
          text: 'ASISTENCIA ALMUERZO',
          disabled: false,
          href: '#/asistencia_almuerzo',
        },
        {
          text: 'HISTORIAL ASISTENCIA',
          disabled: true,
          href: '#',
        },
      ],

      form: {
        asignacion_id: null,
        fecha_buque: "",
        turno_id: null,
      },
    };
  },

  created() {
    let self = this
    self.getAll()
  },

  methods: {
      //obtener asistencias
    getAll() {
        let self = this
        self.loading = true

        self.$store.state.services.asignacionService
        .getAll()
        .then(r=>{
            self.loading = false
            if(self.$store.state.global.captureError(r)){
                return
            }
            let data = []
            self.asignaciones = []
            r.data.forEach((x,i)=>{
                x.detalle_asignacion.forEach((d,j)=>{
                    self.asignaciones.push({
                        asignacion_id: d.asignacion_empleado_id,
                        fecha_buque: moment(d.fecha).format('DD/MM/YYYY')+' - '+x.planificacion.buque.nombre,
                        fecha: d.fecha
                    })

                    if(d.asistencia_almuerzo.length > 0){
                        d.asistencia_almuerzo.forEach((a,k)=>{
                            self.all_items.push({
                                asignacion_id: d.asignacion_empleado_id,
                                fecha_buque: moment(d.fecha).format('DD/MM/YYYY')+' - '+x.planificacion.buque.nombre,
                                fecha: d.fecha,
                                turno: d.turno,
                                turno_id: d.turno_id,
                                empleado: d.empleado,
                                asistencia_almuerzo: a
                            })
                        })
                         
                    }
                   
                })
            })
            console.log(self.all_items)
        }).catch(e=>{})
    },

    change(){
        let self = this
        self.turnos = self.all_items.filter(x=>x.fecha_buque == self.form.fecha_buque)
        
        if(self.form.turno_id !== null){
          self.changeTurn()
        }
    },

    changeTurn(){
        let self = this
        self.items = self.all_items.filter(x=>x.turno_id == self.form.turno_id && x.fecha_buque == self.form.fecha_buque)
    },

    print(){
      let self = this
      let asignacion = self.asignaciones.find(x=>x.fecha_buque == self.form.fecha_buque)
      let data = self.form
      data.asignacion_id = asignacion.asignacion_id
      data.fecha = asignacion.fecha
      self.loading = true
      self.$store.state.services.detalleAsignacionService
      .print(data.asignacion_id,data.turno_id,data.fecha,true)
        .then(r => {
          self.loading = false
          if(r.response){
            this.$toastr.error(r.response.data.error, 'error')
            return
          }
          const url = window.URL.createObjectURL(new Blob([r.data], { type: 'application/pdf' }));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'asistencia_almuerzo_fecha_'+data.fecha+'_turno_'+data.turno_id); 
          //link.target = '_blank'
          link.click();
        })
        .catch(r => {});
    }

  },

  computed: {
  },
};
</script>