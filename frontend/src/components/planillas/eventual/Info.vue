<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-toolbar flat color="white">
        <v-toolbar-title v-if="planilla !== null">
            DEPARTAMENTO DE OPERACIONES  PLANILLA PERSONAL EVENTUAL {{planilla.fecha | moment('DD/MM/YYYY')}}
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
                       <strong>PLANILLA NO: </strong><strong class="blue--text">{{planilla.numero}}</strong><br />
                       <strong>FECHA INICIO DESCARGA: </strong><strong class="blue--text">{{planilla.inicio_descarga | moment('DD/MM/YYYY')}}</strong><br />
                       <strong>FECHA FIN DESCARGA: </strong><strong class="blue--text">{{planilla.fin_descarga | moment('DD/MM/YYYY')}}</strong><br />
                  </v-flex>
                  <v-flex xs12 md6 lg6 sm6>
                      <strong>LUGAR: </strong><strong class="blue--text">MUELLE EPQ</strong><br />
                       <strong>BUQUE: </strong><strong class="blue--text">{{planilla.buque | uppercase}}</strong><br />
                  </v-flex>
              </v-layout>
          </v-card-title>
          <v-card-text>
              <v-tabs
                    centered
                    color="primary"
                    dark
                    height="50"
                    icons-and-text
                >
                    <v-tabs-slider color="yellow"></v-tabs-slider>

                        <v-tab href="#impresion">
                            Impresión planilla
                        <v-icon>fas fa-list</v-icon>
                        </v-tab>

                        <v-tab href="#calculos">
                            Maestro de calculos
                        <v-icon>fas fa-list</v-icon>
                        </v-tab>

                        <v-tab-item 
                            value='impresion'
                        >
                            <v-card flat>
                                <v-card-text>
                                    <impresion-planilla></impresion-planilla>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>

                        <v-tab-item
                            value="calculos"
                            >
                            <v-card flat>
                                <v-card-text>
                                    <maestro-calculo></maestro-calculo>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>
                </v-tabs>
          </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from 'moment'
import ImpresionPlanilla from './ImpresionPlanilla'
import MaestroCalculo from './MaestroCalculo'
export default {
  name: "info_planilla_eventual",
  components: {
      ImpresionPlanilla,
      MaestroCalculo
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
      text: ''
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
      self.$store.state.services.planillaEventualService
        .get(id)
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
          self.planilla = r.data
        })
        .catch(r => {});
    },
  },

  computed: {
  },
};
</script>