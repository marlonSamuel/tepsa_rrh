<template>
  <v-layout v-loading="loading" grid-list-md>
      <v-layout wrap>
        ESCRITORIO PROYECTO BASE
        <el-divider></el-divider>
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
  },

  computed: {
    logo(){
      let self = this
      return self.$store.state.global.getLogo()
    }
  },
};
</script>