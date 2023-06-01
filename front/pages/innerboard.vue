<template>
    <div
      class="pa-0 ma-0"
      style="
      height: calc(100vh - 56px);
      width:100%;
      overflow-y: auto;
      scroll-behavior: smooth;
      position: absolute;
      top: 0px;
      left: 0px;
      transition: 300ms;
      "
      id="scroll-target">
        <div class="d-flex justify-space-between align-center" style="position: fixed; width: 100%; z-index: 10; top: .5rem; background: rgba(233,30,99,.5);">
          <!--<div></div>-->
          <div>
            <v-menu
            v-model="menu"
            :close-on-content-click="false"
            offset-y
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  text
                  v-bind="attrs"
                  v-on="on"
                  style="color: white;"
                >
                  Pizarra interior <v-icon>mdi-menu-down</v-icon>
                </v-btn>
              </template>
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
                    <v-list-item-subtitle>Propietario de la pizarra</v-list-item-subtitle>
                  </v-list-item-content>

                  <v-list-item-action>
                    <v-btn
                      :class="fav ? 'red--text' : ''"
                      icon
                      @click="fav = !fav"
                    >
                      <v-icon>mdi-heart</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
                <v-list-item @click="menu = false">
                  <v-list-item-icon><v-icon>mdi-share-variant</v-icon></v-list-item-icon>
                  <v-list-item-title>Compartir pizarra</v-list-item-title>
                </v-list-item>
                <v-list-item @click="menu = false">
                  <v-list-item-icon><v-icon>mdi-comment-outline</v-icon></v-list-item-icon>
                  <v-list-item-title>Comentar pizarra</v-list-item-title>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item @click="menu = false">
                  <v-list-item-icon><v-icon>mdi-cog</v-icon></v-list-item-icon>
                  <v-list-item-title>Administrar pizarra</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
          <div class="mr-3" @click="share = true"><v-icon style="color: white;">mdi-cloud-lock-outline</v-icon></div>
        </div>
        <div
        class="d-flex flex-column"
        style="width: 100vw; margin-top: 2rem;"
        v-touch="{
            left: () => swipe('Left'),
            right: () => swipe('Right'),
            up: () => swipe('Up'),
            down: () => swipe('Down')
        }"
        >
        <Ficha v-for="(ficha,i) in fichas" :key="i" :img="ficha.img" :lazyimg="ficha.lazy" :title="ficha.title" />
        </div>
        <v-bottom-sheet
          v-model="share"
          inset
        >
          <v-sheet>
            <div class="text-right">
              <v-btn
                text
                color="error"
                @click="share = !share"
              >
                Cerrar
              </v-btn>
            </div>
            <div class="mx-3">
              <h5 class="text-h5">Compartir con personas</h5>
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
                    <v-list-item-subtitle>Propietario de la pizarra</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      src="https://cdn.vuetifyjs.com/images/lists/1.jpg"
                      alt="John"
                    >
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>Usuario Demo 2</v-list-item-title>
                    <v-list-item-subtitle>Editor de la pizarra</v-list-item-subtitle>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon>
                      <v-icon color="grey lighten-1">mdi-lock</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
              <!--
              <v-autocomplete
              v-model="friends"
              :items="people"
              filled
              chips
              color="blue-grey lighten-2"
              label="Usuarios"
              item-text="name"
              item-value="name"
              multiple
            >
              <template v-slot:selection="data">
                <v-chip
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  close
                  @click="data.select"
                  @click:close="remove(data.item)"
                >
                  <v-avatar left>
                    <v-img :src="data.item.avatar"></v-img>
                  </v-avatar>
                  {{ data.item.name }}
                </v-chip>
              </template>
              <template v-slot:item="data">
                <template v-if="typeof data.item !== 'object'">
                  <v-list-item-content v-text="data.item"></v-list-item-content>
                </template>
                <template v-else>
                  <v-list-item-avatar>
                    <img :src="data.item.avatar">
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title v-html="data.item.name"></v-list-item-title>
                    <v-list-item-subtitle v-html="data.item.group"></v-list-item-subtitle>
                  </v-list-item-content>
                </template>
              </template>
            </v-autocomplete>
            <v-btn block @click="friends = []">Agregar</v-btn>-->
            </div>
          </v-sheet>
        </v-bottom-sheet>
    </div>
</template>
<script>
export default {
  layout: 'inside',
  data: () => ({
    fichas: [],
    fav: true,
    menu: false,
    share: false,
    people: [
      { header: 'Group 1' },
      { name: 'Sandra Adams', group: 'Group 1', avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg' },
      { name: 'Ali Connors', group: 'Group 1', avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg' },
      { name: 'Trevor Hansen', group: 'Group 1', avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg' },
      { name: 'Tucker Smith', group: 'Group 1', avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg' },
      { divider: true },
      { header: 'Group 2' },
      { name: 'Britta Holt', group: 'Group 2', avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg' },
      { name: 'Jane Smith ', group: 'Group 2', avatar: 'https://cdn.vuetifyjs.com/images/lists/5.jpg' },
      { name: 'John Smith', group: 'Group 2', avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg' },
      { name: 'Sandra Williams', group: 'Group 2', avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg' }
    ],
    friends: []
  }),
  mounted () {
    for (let f = 1; f < 6; f++) {
      const img = (Math.round(Math.random() * 10) * 6 + 10) + f
      this.fichas.push({
        img: 'https://picsum.photos/500/300?image=' + img,
        lazy: 'https://picsum.photos/10/6?image=' + img,
        title: 'Ficha de prueba ' + f
      })
    }
  },
  methods: {
    swipe (direction) {
      console.log(direction)
      if (direction === 'Left' || direction === 'Right') {
        if (direction === 'Right') {
          document.getElementById('scroll-target').style.left = '25%'
        } else {
          document.getElementById('scroll-target').style.left = '-25%'
        }
        this.$router.go()
      }
    },
    remove (item) {
      const index = this.friends.indexOf(item.name)
      if (index >= 0) {
        this.friends.splice(index, 1)
      }
    }
  }
}
</script>
