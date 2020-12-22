<template>
  <v-layout grid-list-md>
      <v-layout wrap>
        <v-flex>
          <v-card>
              <v-card-text>
                  <v-layout wrap v-loading="loading">
                      <v-flex xs12 lg7 md7 sm7>
                          <planilla-eventual></planilla-eventual>
                      </v-flex>
                      <v-flex xs12 lg4 md4 sm4>
                          <planilla-days></planilla-days>
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
import PlanillaEventual from './charts/PlanillaEventual.vue';
import PlanillaDays from './charts/PlanillaDays.vue';
export default {
  name: "counts",
  components: {
    PlanillaEventual,
    PlanillaDays
  },
  props: {
      
    },
  data() {
    return {
      loading: false,
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
        .groupByPlanilla()
        .then(r=>{
            self.loading = false
            self.$nextTick(()=>{
              events.$emit('eventual_chart',r.data.planillas),
              events.$emit('eventual_days_chart',r.data.diffs)
            })
        }).catch(e=>{

        })
    }
  },
};
</script>