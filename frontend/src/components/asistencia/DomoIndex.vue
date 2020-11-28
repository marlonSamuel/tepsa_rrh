<template>
  <v-layout align-start v-loading="loading">
      <v-dialog v-model="dialog" max-width="800px" persistent>
          <v-card>
            <v-card-title>
              <span class="headline">Desbloquear empleado</span>
            </v-card-title>

            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 sm12 md12>
                    <v-textarea
                      v-model="form.razon_desbloqueo"
                      label="Justificación"
                      v-validate="'required'"
                      type="text"
                      data-vv-name="razon_desbloqueo"
                      data-vv-as="justificación de desbloqueo"
                      :error-messages="errors.collect('razon_desbloqueo')"
                    >
                    </v-textarea>
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
        <v-toolbar-title>HISTORIAL ASISTENCIA DOMO </v-toolbar-title>
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
                    v-model="form.turno"
                    label="Turno"
                    placeholder="seleccione turno"
                    item-text="asistencia_domo.turno"
                    item-value="asistencia_domo.turno"
                    :items="turnos"
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
              <span v-if="props.item.asistencia_domo !== null">
                  {{props.item.asistencia_domo.turno}}
              </span>
          </td>
          <td class="text-xs-left">
              <span v-if="props.item.asistencia_domo !== null">
                  {{props.item.asistencia_domo.hora_entrada | moment('hh:mm')}}
              </span>
          </td>
          <td class="text-xs-left">
              <span v-if="props.item.asistencia_domo !== null & props.item.asistencia_domo.hora_salida !== null">
                  {{props.item.asistencia_domo.hora_salida | moment('hh:mm')}}
              </span>
              <span v-else class="text--red">
                  sin asistencia
              </span>
          </td>
          <td class="text-xs-left">
              <span v-if="props.item.asistencia_domo !== null">
                  <span v-if="props.item.asistencia_domo.bloqueado" class="red--text">
                      bloqueado
                  </span>
                  <span v-else class="green--text">
                    activo
                  </span>
                  <span v-if="props.item.asistencia_domo.desbloqueado" class="green--text">
                      - desbloqueado
                  </span>
            </span>
            <span v-else class="red--text">
                sin asistencia
            </span>
          </td>
          <td>
            <span v-if="props.item.asistencia_domo !== null">
              <v-tooltip top v-if="props.item.asistencia_domo.bloqueado & !props.item.asistencia_domo.desbloqueado">
                <template v-slot:activator="{ on }">
                  <v-icon
                    v-on="on"
                    color="success"
                    fab
                    dark
                    @click="desbloquear(props.item)"
                  >
                    check</v-icon
                  >
                </template>
                <span>Desbloquear</span>
              </v-tooltip>
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
  name: "DomoIndex",
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
        { text: 'Turno', value: 'turno' },
        { text: 'Hora entrada', value: 'hora_entrada' },
        { text: 'Hora salida', value: 'hora_salida' },
        { text: 'Estado', value: 'estado' },
        { text: 'acción', value: '' }
      ],
      rowsPerPageItems: [10, 20, 30, 40],
      pagination: {
          rowsPerPage: 20
      },

      itemsB: [
        {
          text: 'ASISTENCIA DOMO',
          disabled: false,
          href: '#/asistencia_domo',
        },
        {
          text: 'HISTORIAL ASISTENCIA',
          disabled: true,
          href: '#',
        },
      ],

      form: {
        id: null,
        asignacion_id: null,
        fecha_buque: "",
        turno: null,
        razon_desbloqueo: ''
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
                x.asignacion_domos.forEach((d,j)=>{
                    self.asignaciones.push({
                        asignacion_id: d.asignacion_empleado_id,
                        fecha_buque: moment(d.fecha).format('DD/MM/YYYY')+' - '+x.planificacion.buque.nombre,
                        fecha: d.fecha
                    })

                    if(d.asistencia_domo.length > 0){
                        d.asistencia_domo.forEach((a,k)=>{
                            self.all_items.push({
                                asignacion_id: d.asignacion_empleado_id,
                                fecha_buque: moment(d.fecha).format('DD/MM/YYYY')+' - '+x.planificacion.buque.nombre,
                                fecha: d.fecha,
                                empleado: d.empleado,
                                asistencia_domo: a
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
        self.turnos.push({
          asistencia_domo: {
            turno: 'todos'
          }
        })
        console.log(self.turnos)
        
        if(self.form.turno !== null){
          self.changeTurn()
        }
    },

    changeTurn(){
        let self = this
        self.items = self.form.turno !== 'todos' ? self.all_items.filter(x=>x.asistencia_domo.turno == self.form.turno 
                                          && x.fecha_buque == self.form.fecha_buque) : self.all_items
        console.log(self.items)
    },

    validate(){
      let self = this
      this.$validator.validateAll().then(result => {
        if (result) {
          let data = self.form
          console.log(data)
          self.$confirm("Seguro que desbloquear empleado, esta acción se realizará una sola vez?")
          .then(res => {
            self.loading = true;
            self.$store.state.services.asistenciaDomoService
              .desbloquear(data)
              .then(r => {
                self.loading = false;
                if (self.$store.state.global.captureError(r)) {
                  return;
                }
                self.getAll()
                this.$toastr.success("empleado desbloqueado con éxito", "éxito")
                self.close()
              })
              .catch(r => {});
          })
          .catch(cancel => {});
        }
      })
    },

    desbloquear(data){
      let self = this
      self.dialog = true
      self.form.id = data.asistencia_domo.id
    },

    close(){
      let self =this
      self.dialog = false
      self.razon_desbloqueo = ""
    },

    print(){
      let self = this
      let asignacion = self.asignaciones.find(x=>x.fecha_buque == self.form.fecha_buque)
      let data = self.form
      data.asignacion_id = asignacion.asignacion_id
      data.fecha = asignacion.fecha
      self.loading = true
      self.$store.state.services.asignacionDomoService
      .printAsistencia(data.asignacion_id,data.fecha,data.turno)
        .then(r => {
          self.loading = false
          if(r.response){
            this.$toastr.error(r.response.data.error, 'error')
            return
          }
          const url = window.URL.createObjectURL(new Blob([r.data], { type: 'application/pdf' }));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'asistencia_domo_fecha_'+data.fecha+'_turno_'+data.turno); 
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