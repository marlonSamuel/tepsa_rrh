<template>
  <v-layout grid-list-md>
      <v-layout wrap>
        <v-flex>
          <v-card>
              <v-card-text>
                  <v-layout wrap v-loading="loading">
                      
                  <v-flex xs6 md4 sm4 lg4>
                        <v-card
                            class="mx-auto"
                            color="grey lighten-4"
                            max-width="600"
                        >
                            <v-card-title>
                            <v-icon large color="blue darken-2" class="mr-5" size="64">person_pin</v-icon>
                            <v-layout
                                column
                                align-start
                            >
                                <div class="caption grey--text text-uppercase">
                                    EMPLEADOS REGISTRADOS
                                </div>
                                <div>
                                <span
                                    class="display font-weight-black"
                                    v-text="total"
                                ></span>
                                <strong>EMPLEADOS</strong>
                                </div>
                            </v-layout>

                            <v-spacer></v-spacer>

                            <v-btn icon class="align-self-start" size="28" @click="$router.push(`/empleado_index`)">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            </v-card-title>
                        </v-card>
                    </v-flex>
                    <v-flex xs6 md4 sm4 lg4 style="padding-left: 5px;">
                        <v-card
                            class="mx-auto"
                            color="grey lighten-4"
                            max-width="600"
                        >
                            <v-card-title>
                            <v-icon large color="blue darken-2" class="mr-5" size="64">person_pin</v-icon>
                            <v-layout
                                column
                                align-start
                            >
                                <div class="caption grey--text text-uppercase">
                                EMPLEADOS FIJOS
                                </div>
                                <div>
                                <span
                                    class="display font-weight-black"
                                    v-text="fijos"
                                ></span>
                                <strong>EMPLEADOS</strong>
                                </div>
                            </v-layout>

                            <v-spacer></v-spacer>

                            <v-btn @click="$router.push(`/empleado_index`)" icon class="align-self-start" size="28">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            </v-card-title>
                        </v-card>
                        </v-flex>

                    <v-flex xs6 md4 sm4 lg4 style="padding-left: 5px;">
                        <v-card
                            class="mx-auto"
                            color="grey lighten-4"
                            max-width="600"
                        >
                            <v-card-title>
                            <v-icon large color="blue darken-2" class="mr-5" size="45">person_pin</v-icon>
                            <v-layout
                                column
                                align-start
                            >
                                <div class="caption grey--text text-uppercase">
                                EMPLEADOS EVENTUALES
                                </div>
                                <div>
                                <span
                                    class="display font-weight-black"
                                    v-text="eventuales"
                                ></span>
                                <strong>EMPLEADOS</strong>
                                </div>
                            </v-layout>

                            <v-spacer></v-spacer>

                            <v-btn @click="$router.push(`/empleado_index`)" icon class="align-self-start" size="28">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            </v-card-title>
                        </v-card>
                        </v-flex>
                        <el-divider></el-divider>
                        <v-flex xs6 md4 sm4 lg4 style="padding-left: 5px;">
                        <v-card
                            class="mx-auto"
                            color="grey lighten-4"
                            max-width="600"
                        >
                            <v-card-title>
                            <v-icon large color="yellow darken-2" class="mr-5" size="45">person_pin</v-icon>
                            <v-layout
                                column
                                align-start
                            >
                                <div class="caption grey--text text-uppercase">
                                PLANILLAS EVENTUALES {{year}}
                                </div>
                                <div>
                                <span
                                    class="display font-weight-black"
                                    v-text="planilla_eventuales"
                                ></span>
                                <strong>PLANILLAS</strong>
                                </div>
                            </v-layout>

                            <v-spacer></v-spacer>

                            <v-btn @click="$router.push(`/planilla_eventual`)" icon class="align-self-start" size="28">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            </v-card-title>
                        </v-card>
                        </v-flex>

                        <v-flex xs6 md4 sm4 lg4 style="padding-left: 5px;">
                        <v-card
                            class="mx-auto"
                            color="grey lighten-4"
                            max-width="600"
                        >
                            <v-card-title>
                            <v-icon large color="yellow darken-2" class="mr-5" size="45">money</v-icon>
                            <v-layout
                                column
                                align-start
                            >
                                <div class="caption grey--text text-uppercase">
                                TOTAL LIQUIDADO {{year}}
                                </div>
                                <div>
                                <span
                                    class="display font-weight-black"
                                >
                                    {{total_planilla.toFixed(2) | currency('Q ')}}</span>
                                <strong>MUELLE</strong>
                                </div>
                            </v-layout>

                            <v-spacer></v-spacer>

                            <v-btn @click="$router.push(`/planilla_eventual`)" icon class="align-self-start" size="28">
                                <v-icon>arrow_forward</v-icon>
                            </v-btn>
                            </v-card-title>
                        </v-card>
                        </v-flex>
                  </v-layout>
              </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
  </v-layout>
</template>

<script>
import moment from 'moment'
export default {
  name: "counts",
  components: {
  },
  props: {
      
    },
  data() {
    return {
      loading: false,
      total: 0,
      fijos: 0,
      eventuales: 0,
      planilla_eventuales: 0,
      total_planilla: 0,
      year: moment().year()
    };
  },

  created() {
    let self = this
    self.getAll()
  },

  methods: {
    getAll(){
        let self = this
        self.loading = true
        self.$store.state.services.dashboardService
        .getAll()
        .then(r=>{
            self.loading = false
            self.total = r.data.total
            self.fijos = r.data.fijos
            self.eventuales = r.data.eventuales
            self.planilla_eventuales = r.data.planilla_eventuales
            self.total_planilla = r.data.total_planilla
        }).catch(e=>{

        })
    }
  },
};
</script>