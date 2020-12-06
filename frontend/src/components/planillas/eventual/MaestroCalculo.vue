<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-card>
          <v-card-title>
                MAESTRO DE CALCULOS
          </v-card-title>
          <v-card-text>
              TABLA REPORTE
          </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from 'moment'
export default {
  name: "info_planilla_eventual_maestro_calculo",
  props: {
      source: String
    },
  data() {
    return {
      search: '',
      loading: false,
      id: null,
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
        .info(id,'C')
        .then(r => {
          self.loading = false
          if(self.$store.state.global.captureError(r)){
            return
          }
        })
        .catch(r => {});
    },
  },

  computed: {
  },
};
</script>