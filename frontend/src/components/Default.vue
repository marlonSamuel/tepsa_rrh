<template>
  <v-layout v-loading="rol == ''" grid-list-md>
      <v-layout wrap>
        <v-flex>
          <v-card v-loading="rol == ''">
              <v-card-text v-if="isAdmin">
                  ADMINISTRADOR {{user | uppercase}}
              </v-card-text>
              <v-card-text v-else>
                BIENVENIDO {{user | uppercase}} USTED HA INGRESADO CON ROL DE {{rol | uppercase}}

                <v-flex>
                   <v-btn
                      color="green"
                      class="white--text"
                      @click="navigateTo"
                    >
                      IR A ASISTENCIAS
                      <v-icon right dark>file_copy</v-icon>
                    </v-btn>
                </v-flex>
              </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
  </v-layout>
</template>

<script>
export default {
  name: "default",
  components: {
  },
  props: {
      source: String
    },
  data() {
    return {
      loading: false,
    };
  },

  created() {
    let self = this
  },

  methods: {
    navigateTo(){
      let self = this
      if(self.$store.state.rol.toLowerCase() == 'asistencia muelle'){
        self.$router.push('/asistencia_turno')
      }else if(self.$store.state.rol.toLowerCase() == 'asistencia domo'){
        self.$router.push('/asistencia_domo')
      }else{
        self.$router.push('/asistencia_almuerzo')
      }
    }
  },

  computed: {
    logo(){
      let self = this
      return self.$store.state.global.getLogo()
    },
    isAdmin(){
      let self = this
      return self.$store.state.rol.toLowerCase() == 'administrador' ? true : false
    },

    user(){
      let self = this
      return self.$store.state.usuario.nombre
    },

    rol(){
      let self = this
      return self.$store.state.rol
    }
  },
};
</script>