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
        <v-toolbar-title v-if="planilla !== null">
            DEPARTAMENTO DE OPERACIONES  PLANILLA PERSONAL FIJO
        </v-toolbar-title>
        <v-divider
          class="mx-2"
          inset
          vertical
        ></v-divider><v-spacer></v-spacer>
      </v-toolbar>
      <v-card>
          <v-card-title>
              <v-layout v-if="planilla !== null" wrap>
                  <v-flex xs12 md6 lg6 sm6>
                       <strong>QUINCENA NO: </strong><strong class="blue--text">{{planilla.quincena}}</strong><br />
                       <strong>FECHA INICIO: </strong><strong class="blue--text">{{planilla.fecha_inicio | moment('DD/MM/YYYY')}}</strong><br />
                       <strong>FECHA FIN: </strong><strong class="blue--text">{{planilla.fecha_fin | moment('DD/MM/YYYY')}}</strong><br />
                  </v-flex>
              </v-layout>
          </v-card-title>
          <v-card-text>
            <impresion-planilla ></impresion-planilla>
          </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from 'moment'
import ImpresionPlanilla from './ImpresionPlanilla'
export default {
  name: "info_planilla_fijo",
  components: {
      ImpresionPlanilla
  },
  props: {
      source: String
    },
  data() {
    return {
      search: '',
      loading: false,
      id: null,
      planilla: null,
      text: '',
      itemsB: [
        {
          text: 'PLANILLAS',
          disabled: false,
          href: '#/planilla_fijo',
        },
        {
          text: 'INFORMACION PLANILLA',
          disabled: true,
          href: '#',
        },
      ],
    }
  },

  created() {
    let self = this
    self.id = self.$route.params.id
    self.get(self.id)
  },

  methods: {
     get(id) {
      let self = this
      self.loading = true
      self.$store.state.services.pagoEmpleadoFijoService
        .getPlanilla(id)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.planilla = r.data
          console.log(self.planilla);
        })
        .catch(r => {});
    },
  },

  computed: {
  },
};
</script>