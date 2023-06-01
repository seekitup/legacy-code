<template>
    <div>
        <v-app-bar light>
          <v-toolbar-title style="text-transform: uppercase;">
            Mi perfil
          </v-toolbar-title>
        </v-app-bar>
        <div class="text-center pa-3 black" style="height: calc(100vh - 7rem); overflow: auto;">
            <div class="mr-n9 rounded-circle" style="display: inline-block; padding: 12px; border: 1px solid white;">
              <v-img max-width="150" class="rounded-circle" :src="profileData.image" referrerpolicy="no-referrer"/>
            </div>
            <v-btn
              class="mt-n7"
              fab
              x-small
              color="#D22F82"
            >
                <v-icon dark>
                    mdi-pencil
                </v-icon>
            </v-btn>
            <br/>
            <div class="text-caption">{{profileData.usr}}</div>
            <div class="d-flex justify-space-between my-4" style="width: 80%; margin-left: 10%;">
              <div class="text-center">
                <b>84</b>
                <div class="text-caption">Pizarras</div>
              </div>
              <div class="text-center">
                <b>175</b>
                <div class="text-caption">Fichas</div>
              </div>
              <div class="text-center" @click="displayFollowers = true">
                <b>30</b>
                <div class="text-caption">Contactos</div>
              </div>
            </div>
            <div style="width: 80%; margin-left: 10%;">
                <v-text-field
                    v-model="profileData.fullname"
                    label="Nombre para mostrar"
                ></v-text-field>
                <!--<v-textarea
                    label="Biografía"
                    counter
                ></v-textarea>
                <v-btn block light class="mt-5" to="/activity">Tu actividad</v-btn>-->
                <v-btn block light class="mt-5" @click="logout()">Cerrar sesión</v-btn>
            </div>
        </div>
        <v-bottom-sheet
          v-model="displayFollowers"
          inset
        >
          <v-sheet>
            <div class="text-right">
              <v-btn
                text
                color="error"
                @click="displayFollowers = !displayFollowers"
              >
                Cerrar
              </v-btn>
            </div>
            <div class="mx-3">
              <h5 class="text-h5">Seguidores (3)</h5>
              <v-list>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/john.jpg"
                      alt="John"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/lists/2.jpg"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo 2</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/lists/3.jpg"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo 3</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <h5 class="text-h5">Seguidos (2)</h5>
              <v-list>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/john.jpg"
                      alt="John"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/lists/2.jpg"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo 2</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </div>
          </v-sheet>
        </v-bottom-sheet>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'gestion',
  data: () => ({
    displayFollowers: false,
    displayTitle: 'Seguidores'
  }),
  async asyncData ({ store }) {
    return await axios
      .post(store.state.info.location + 'users/users/getOne',
        JSON.stringify(
          {
            id: store.state.authUser.id
          }
        ),
        {
          validateStatus (status) {
            return status < 500 // Resolve only if the status code is less than 500
          },
          headers: {
            Authorization: 'Bearer ' + store.state.token,
            'Content-Type': 'application/json'
          }
        })
      .then((evtResponse) => {
        console.log(evtResponse.data)
        evtResponse.data.records[0].fullname = evtResponse.data.records[0].name + ' ' + evtResponse.data.records[0].lastname
        return { profileData: evtResponse.data.records[0] }
      })
  },
  mounted () {
    this.$store.commit('setGhost', false)
  },
  methods: {
    logout () {
      this.$store.dispatch('logout')
      this.$router.push('login')
    }
  }
}
</script>
