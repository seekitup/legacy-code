<template>
  <v-app>
    <v-main>
      <v-container style="height: 100%; padding: 0px;">
        <nuxt />
      </v-container>
    </v-main>
    <v-bottom-navigation
    grow
    light
    v-touch="{
        up: () => $router.push('/')
      }"
  >
    <v-btn to="/">
      <span>Inicio</span>

      <img src="logo_abeja_blanco.svg" style="width:2rem; margin-top: -.35rem;" />
    </v-btn>

    <v-btn :to="{ name: 'index', params: { new : true } }">
      <span>Agregar</span>

      <v-icon style="margin-bottom: .15rem; margin-top: -.15rem; color: black;">mdi-plus-thick</v-icon>
    </v-btn>

    <!--<v-btn>
      <span>Buscar</span>

      <v-icon>mdi-magnify</v-icon>
    </v-btn>-->

    <v-btn to="/profile">
      <span>Mi perfil</span>

      <v-icon style="margin-bottom: .15rem; margin-top: -.15rem; color: black;">mdi-account</v-icon>
    </v-btn>

  </v-bottom-navigation>
  <div v-if="ghost" style="position: fixed; bottom: 1rem; transform: scaleX(.85) scaleY(.75); opacity: .9; width: 100%;" @click="cancelghost()">
    <Ficha
      :ghost="true"
      :fichaData="fichaData"
    />
    <div class="d-flex justify-center align-center" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; z-index: 50; background: rgba(0,0,0,.5);">
      <v-icon style="font-size: 10rem">mdi-close-circle-outline</v-icon>
    </div>
    <!--
    <v-card>
        <v-img
          src="https://picsum.photos/500/300?image=30"
          lazy-src="https://picsum.photos/10/6?image=30"
          class="white--text align-end"
          gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
          height="200px"
        >
          <v-card-title>Ficha fantasma</v-card-title>
        </v-img>
        <v-card-text>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent semper elit in rhoncus pretium. Suspendisse eget purus suscipit, venenatis elit sed, congue nisl.
        </v-card-text>
    </v-card>
    -->
  </div>
  <div class="d-none d-md-flex justify-center align-center text-center" style="position: absolute; top: 0px; left: 0px; width: 100vw; height: 100vh; background: black; z-index: 1000; color: white;">
    La versión de escritorio aún no está disponible<br/>
    Acceda desde un celular para disfrutar la experiencia Seekitup
  </div>
  </v-app>
</template>
<script>
import { mapState } from 'vuex'

export default {
  middleware: 'auth',
  data: () => ({}),
  computed: {
    ...mapState({
      ghost: state => state.ghost,
      fichaData: state => state.ghostData
    })
  },
  methods: {
    cancelghost () {
      this.$store.commit('setGhost', false)
    }
  }
}
</script>
