<template>
  <v-app>
    <v-main>
      <v-container style="height: 100%; padding: 0px;">
        <nuxt />
      </v-container>
    </v-main>
    <v-bottom-navigation
      grow
      height="106"
      dark
      background-color="rgba(0,0,0,.5)"
    >
    <v-btn to="/">
      <span v-if="$store.state.authUser">Inicio</span>
      <span v-else>Registrate</span>

      <img src="logo_abeja.svg" style="width:2rem; margin:.25rem .25rem 1rem; filter: invert(1)" />
    </v-btn>

    <!--
    <v-btn @click="gotoNew()">
      <span>Agregar</span>

      <v-icon style="font-size: 2.5rem; margin-bottom: .5rem; color: white;">mdi-plus-thick</v-icon>
    </v-btn> -->

    <!--<v-btn>
      <span>Buscar</span>

      <v-icon>mdi-magnify</v-icon>
    </v-btn>-->

    <v-btn to="/profile" v-if="$store.state.authUser">
      <span>Mi perfil</span>

      <v-icon style="font-size: 2.5rem; margin-bottom: .5rem; color: white;">mdi-account</v-icon>
    </v-btn>

  </v-bottom-navigation>
  <div class="d-none d-md-flex justify-center align-center text-center" style="position: absolute; top: 0px; left: 0px; width: 100vw; height: 100vh; background: black; z-index: 1000; color: white;">
    La versión de escritorio aún no está disponible<br/>
    Acceda desde un celular para disfrutar la experiencia Seekitup
  </div>
  </v-app>
</template>
<script>
import { mapState } from 'vuex'

export default {
  data: () => ({}),
  computed: {
    ...mapState({
      ghost: state => state.ghost,
      fichaData: state => state.ghostData
    })
  },
  methods: {
    gotoNew () {
      this.$root.$emit('follow')
    },
    cancelghost () {
      this.$store.commit('setGhost', false)
    }
  }
}
</script>
