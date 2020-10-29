<template v-loading="loading">
  <div>
    <v-navigation-drawer
      v-model="drawer"
      :clipped="$vuetify.breakpoint.lgAndUp"
      fixed
      app
      dark
    >
      <v-list dense>
        <template>
          <v-list-tile :to="'/'">
            <v-list-tile-action>
              <v-icon>home</v-icon>
            </v-list-tile-action>
            <v-list-tile-title to="/">
              Inicio
            </v-list-tile-title>
          </v-list-tile>
        </template>
        <template v-for="item in items">
          <v-list-group v-if="item.children" :prepend-icon="item.icon">
            <v-list-tile slot="activator">
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <template v-for="child in item.children">
              <v-list-tile :to="child.path">
                <v-list-tile-action>
                  <v-icon>{{ child.icon }}</v-icon>
                </v-list-tile-action>
                <v-list-tile-title>
                  {{ child.text }}
                </v-list-tile-title>
              </v-list-tile>
            </template>
          </v-list-group>

          <v-list-tile v-else @click="">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
              <v-list-tile-title>{{ item.text }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>
      </v-list>
    </v-navigation-drawer>
    <v-toolbar
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      dark
      app
      fixed
      dense
      color="blue darken-4"
    >
      <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
        <v-toolbar-side-icon
          @click.stop="drawer = !drawer"
        ></v-toolbar-side-icon>
        <span class="hidden-sm-and-down" @click="$router.push(`/`)">
          <v-avatar :tile="false" size="45" dark>
            <img :src="logo" alt="avatar" />
          </v-avatar>
          RRH
        </span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <h2 class="hidden-sm-and-down">RECURSOS HUMANOS TEPSA</h2>
      <v-spacer></v-spacer>

      {{ userName }}

      <v-menu
        offset-y
        origin="center center"
        :nudge-bottom="10"
        transition="scale-transition"
      >
        <v-btn icon large flat slot="activator">
          <v-avatar size="30px">
            <v-btn icon>
              <v-icon>account_circle</v-icon>
            </v-btn>
          </v-avatar>
        </v-btn>
        <v-list class="pa-0">
          <v-list-tile
            v-for="(item, index) in options"
            :to="!item.href ? { name: item.name } : null"
            :href="item.href"
            @click="item.click"
            ripple="ripple"
            :disabled="item.disabled"
            :target="item.target"
            rel="noopener"
            :key="index"
          >
            <v-list-tile-action v-if="item.icon">
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-menu>
    </v-toolbar>
  </div>
</template>
<script>
export default {
  name: "navigation_menu",
  props: {
    source: String
  },
  data() {
    return {
      dialog: false,
      drawer: null,
      loading: false,
      logo: this.$store.state.global.getLogo(),
      options: [
        {
          icon: "account_circle",
          href: "",
          title: "Cambiar contraseĂ±a",
          click: this.change
        },
        {
          icon: "logout",
          href: "",
          title: "salir",
          click: this.logout
        }
      ],

      items: [
        {
          icon: "settings",
          text: "Configuracion",
          name: "Configuracion",
          model: true,
          path: "",
          children: [
            { name: "turno", icon: "add", text: "Turnos", path: "/turno" },
            { name: "carnet", icon: "add", text: "Carnets", path: "/carnet" },
            {
              name: "prestacion",
              icon: "add",
              text: "Prestaciones",
              path: "/prestacion"
            },
            {
              name: "empleado",
              icon: "add",
              text: "Empleados",
              path: "/empleado_index"
            }
          ]
        },
        {
          icon: "file_copy",
          text: "Administracion",
          name: "Administracion",
          model: true,
          path: "",
          children: [
            { name: "asignacion",icon: "add",text: "Asignaciones",path: "/asignacion"}
          ]
        },
        {
          icon: "file_copy",
          text: "Asistencias",
          name: "Asistencias",
          model: true,
          path: "",
          children: [
            { name: "asistencia_turno",icon: "add",text: "Asistencia turno",path: "/asistencia_turno"},
            { name: "asistencia_almuerzo",icon: "add",text: "Asistencia almuerzo",path: "/asistencia_almuerzo"}
          ]
        }
      ]
    };
  },

  created() {
    let self = this;
  },

  methods: {
    logout() {
      let self = this;
      self.loading = true;
      self.$store.state.services.loginService
        .logout()
        .then(r => {
          self.$store.dispatch("logout");
          self.$router.push("/login");
          self.loading = false;
        })
        .catch(e => {});
    },

    change() {
      let self = this;
      self.$router.push("/change_password");
    }
  },

  computed: {
    userName() {
      let self = this;
      return self.$store.state.usuario.name;
    },
    ciclo() {
      return this.$store.state.ciclo;
    }
  }
};
</script>
