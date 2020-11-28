<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-card>
          <v-card-title>
                IMPRESION PLANILLA
          </v-card-title>
          <v-card-text>
              <v-flex style="overflow: auto">
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
                        <td class="text-xs-left">{{props.item.turnos}}</td>
                        <td class="text-xs-left">{{props.item.septimo}}</td>
                        <td class="text-xs-left">{{props.item.total_deventado}}</td>
                        <td class="text-xs-left">{{props.item.igss}}</td>
                        <td class="text-xs-left">{{props.item.bonificacion_incentivo}}</td>
                        <td class="text-xs-left">{{props.item.bono_14}}</td>
                        <td class="text-xs-left">{{props.item.aguinaldo}}</td>
                        <td class="text-xs-left">{{props.item.liquido_a_recibir}}</td>
                        <td class="text-xs-left">
                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" color="success" fab dark> info</v-icon>
                                </template>
                                <span>Información planilla</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-icon v-on="on"  color="warning" fab dark> edit</v-icon>
                                </template>
                                <span>Editar</span>
                            </v-tooltip>
                        </td>
                    </template>
                    <template v-slot:no-data>
                    <v-btn color="primary" @click="getAll">Reset</v-btn>
                    </template>
                </v-data-table>
              </v-flex>
          </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from 'moment'
export default {
  name: "info_planilla_eventual_impresion_planilla",
  props: {
      source: String
    },
  data() {
    return {
      search: '',
      loading: false,
      id: null,
      items: [],
      headers: [
        { text: 'Codigo', value: 'codigo', width: "50%", align: 'left' },
        { text: 'Nombre empleado', value: 'nombre' },
        { text: 'Afiliación igss', value: 'afiliacion_igss' },
        { text: 'Dpi', value: 'dpi' },
        { text: 'Puesto', value: 'puesto' },
        { text: 'cuenta', value: 'cuenta' },
        { text: 'Turnos trabajados', value: 'turnos' },
        { text: 'Septimo',value: 'septimo' },
        { text: 'Total devengado', value: 'total_deventado' },
        { text: 'Descuento de IGSS', value: 'igss' },
        { text: 'Bonificación de ley', value: 'bonificacion_incentivo' },
        { text: 'Bono 14', value: 'bono_14' },
        { text: 'Aguinaldo', value: 'aguinaldo' },
        { text: 'Liquido a recibir', value: 'liquido_a_recibir' },
        { text: 'Acciones', value: '', sortable: false }
      ],
    }
  },

  created() {
    let self = this
    self.getAll(self.$route.params.id)
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
  },

  computed: {
  },
};
</script>