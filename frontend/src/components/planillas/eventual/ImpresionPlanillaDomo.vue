<template>
  <v-layout align-start v-loading="loading">
    <v-flex>
      <v-card>
        <v-card-title>
          IMPRESION PLANILLA

          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-icon color="red" v-on="on" large fab dark @click="print(false)"
                >fas fa-file-pdf</v-icon
              >
            </template>
            <span>Imprimir boletas</span>
          </v-tooltip>

          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-icon
                color="success"
                v-on="on"
                large
                fab
                dark
                @click="exportExcel"
                >fas fa-file-excel</v-icon
              >
            </template>
            <span>Exportar excel</span>
          </v-tooltip>
        </v-card-title>
        <v-card-text>
          <v-flex sm4 md4 lg4>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-flex>
          <br />
          <v-data-table
            :headers="headers"
            :items="items"
            :search="search"
            class="elevation-1"
          >
            <template v-slot:items="props">
              <td class="text-xs-left">{{ props.item.codigo }}</td>
              <td class="text-xs-left">{{ props.item.nombre }}</td>
              <td class="text-xs-left">{{ props.item.dpi }}</td>
              <td class="text-xs-left">{{ props.item.cargo }}</td>
              <td class="text-xs-left">{{ props.item.cuenta }}</td>
              <td class="text-xs-left">
                {{ props.item.turnos_trabajados}}
              </td>
              <td class="text-xs-left">
                {{ props.item.total_liquido | currency("Q ") }}
              </td>
              <td class="text-xs-left">
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-icon
                      v-on="on"
                      color="success"
                      small
                      fab
                      dark
                      @click="print(props.item)"
                    >
                      print</v-icon
                    >
                  </template>
                  <span>Imprimir boleta</span>
                </v-tooltip>
              </td>
            </template>
            <template v-slot:no-data>
              <v-btn color="primary" @click="getAll">Reset</v-btn>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>
import moment from "moment";
import fileSaver from "file-saver";
export default {
  name: "info_planilla_domo_impresion_planilla",
  props: {
    source: String,
  },
  data(){
    return{
      search: '',
      loading: false,
      dialog: false,
      id: null,
      items: [],
      planilla: {},
      headers: [
        { text: '#', value: 'codigo'},
        { text: 'Nombre empleado', value: 'nombre' },
        { text: 'Dpi', value: 'dpi' },
        { text: 'Puesto', value: 'puesto' },
        { text: 'cuenta', value: 'cuenta' },
        { text: 'Turnos trabajados', value: 'turnos' },
        { text: 'Total devengado', value: 'total_deventado' },
        { text: 'Acciones', value: '', sortable: false }
      ],
    }
  },
  created() {
    let self = this
    self.id = self.$route.params.id
    self.getAll(self.id)
    self.get(self.id)
  },

  methods: {
     getAll(id) {
      let self = this
      self.loading = true
      self.$store.state.services.planillaDomoService
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

    get(id){
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
    clearData() {
      let self = this;
      Object.keys(self.form).forEach(function(key, index) {
        if (typeof self.form[key] === "string") self.form[key] = "";
        else if (typeof self.form[key] === "boolean") self.form[key] = true;
        else if (typeof self.form[key] === "number") self.form[key] = null;
      });
      self.$validator.reset();
    },

    print(data) {
      let self = this
      let id = !data ? 0 : data.id 
      console.log(self.planilla)
      self.loading = true
      self.$store.state.services.planillaDomoService
        .print(self.id,id)
        .then(r => {
          self.loading = false
          if (r.response) {
            this.$toastr.error(r.response.data.error, "error");
            return;
          }
          const url = window.URL.createObjectURL(
            new Blob([r.data], { type: "application/pdf" })
          );
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute(
            "download",
            id > 0 ? "planilla_"+self.planilla.numero+"boleta_"+data.nombre : "planilla_"+self.planilla.numero
          );
          //link.target = '_blank'
          link.click()
        })
        .catch(r => {});
    },

    
      exportExcel(){
        let self = this
        self.loading = true
        self.$store.state.services.planillaDomoService
            .export(self.id)
            .then(response => {
                var file_name = 'planilla_'+self.planilla.numero
                self.loading = false
                if(response.response){
                    this.$toastr.error(r.response.data.error, 'error')
                    return
                }
                var blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                fileSaver.saveAs(blob, file_name);
                a.click();
            })
            .catch(r => {});
        }
  },

  computed: {
  },
};
</script>