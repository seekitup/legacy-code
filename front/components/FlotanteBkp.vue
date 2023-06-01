<template>
   <div style="width:100vw; height: 100%; padding: 1.5rem 2.5rem;">
       <v-card elevation="10" class="rounded-xl flotante d-flex flex-column" style="width: 100%; height: 100%;">
           <v-card-title v-if="ispublic === undefined || ispublic === false" class="justify-center" style="text-transform: uppercase;">
               <span>{{title}}</span>
           </v-card-title>
           <v-card-title v-if="ispublic === true" class="align-center" style="text-transform: uppercase;">
              <v-avatar color="#D22F82" size="1.75rem" class="mr-2">
                <img src="logo_abeja.svg" style="filter: invert(1); height: 1rem; width: 1rem;" />
              </v-avatar>
              <span>{{title}}</span>
              <v-spacer></v-spacer>
              <v-menu
                bottom
                left
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    icon
                    v-bind="attrs"
                    v-on="on"
                  >
                    <v-icon class="mr-n2" style="font-size:2rem; color: black;">mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>

                <v-list>
                  <v-list-item>
                    <v-list-item-title @click="$emit('remove')">Cerrar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
              <v-icon class="mr-n2" style="font-size:2rem; color: black;" @click="showShare = true">mdi-share</v-icon>
           </v-card-title>
           <v-card-text class="pa-0 flex-grow-1" @click="$router.push(destination)">
            <div class="rounded-b-xl" style="display: grid; grid-template-columns: auto auto; height: 100%;" v-if="cardslocal.length === 1">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2 rounded-b-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
            </div>
            <div class="rounded-b-xl" style="display: grid; grid-template-rows: 50% 50%; height: calc(100vh - 13.75rem);" v-if="cardslocal.length === 2">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2"
                  style="grid-column: 1 / 2;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[1].image"
                  class="grey lighten-2 rounded-b-xl"
                  style="grid-column: 1 / 2;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[1].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[1].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[1].title }}</div>
                </div>
              </v-img>
            </div>
            <div class="rounded-b-xl" style="display: grid; grid-template-columns: auto auto; height: 100%;" v-if="cardslocal.length === 3">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2 rounded-bl-xl"
                  style="grid-row: 1 / span 2;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[1].image"
                  class="grey lighten-2"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[1].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[1].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[1].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[2].image"
                  class="grey lighten-2 rounded-br-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[2].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[2].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[2].title }}</div>
                </div>
              </v-img>
            </div>
            <div class="rounded-b-xl" style="display: grid; grid-template-columns: 50% 50%; height: 100%;" v-if="cardslocal.length === 4">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[1].image"
                  class="grey lighten-2"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[1].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[1].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[1].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[2].image"
                  class="grey lighten-2 rounded-bl-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[2].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[2].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[2].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[3].image"
                  class="grey lighten-2 rounded-br-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[3].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[3].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[3].title }}</div>
                </div>
              </v-img>
            </div>
            <div class="rounded-b-xl" style="display: grid; grid-template-columns: auto auto; grid-template-rows: 33% 33%; height: 100%;" v-if="cardslocal.length === 5">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2"
                  style="grid-row: 1 / span 2;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[1].image"
                  class="grey lighten-2"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[1].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[1].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[1].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[2].image"
                  class="grey lighten-2"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[2].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[2].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[2].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[3].image"
                  class="grey lighten-2 rounded-bl-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[3].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[3].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[3].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[4].image"
                  class="grey lighten-2 rounded-br-xl"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[4].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[4].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[4].title }}</div>
                </div>
              </v-img>
            </div>
            <div class="rounded-b-xl" style="display: grid; grid-template-columns: 50% 50%; grid-template-rows: 33% 33%; height: 100%;" v-if="cardslocal.length === 6">
              <v-img
                  :src="cardslocal[0].image"
                  class="grey lighten-2"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[0].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[0].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[0].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[1].image"
                  class="grey lighten-2"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[1].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[1].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[1].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[2].image"
                  class="grey lighten-2"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[2].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[2].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[2].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[3].image"
                  class="grey lighten-2"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[3].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[3].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[3].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[4].image"
                  class="grey lighten-2 rounded-bl-xl"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[4].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[4].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[4].title }}</div>
                </div>
              </v-img>
              <v-img
                  :src="cardslocal[5].image"
                  class="grey lighten-2 rounded-br-xl"
                  style="max-height: 100%;"
              >
                <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                  <v-avatar v-if="cardslocal[5].favicon" class="mx-1" size="16">
                    <img
                      :src="cardslocal[5].favicon"
                      alt="Favicon"
                    >
                  </v-avatar>
                  <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ cardslocal[5].title }}</div>
                </div>
              </v-img>
            </div>
                <!--
              <div class="rounded-b-xl" style="display: grid; grid-template-columns: auto auto; height: 100%;" v-if="cards.length > 0">
                <v-img
                    :src="`https://picsum.photos/500/300?image=${0 * 5 + 10}`"
                    :lazy-src="`https://picsum.photos/10/6?image=${0 * 5 + 10}`"
                    class="grey lighten-2"
                >
                  <div style="background: #FFFFFF80; color: black;" class="elevation-1">
                    <v-icon style="font-size: medium; color: black;">mdi-web</v-icon>
                    www.ejemplo.com
                  </div>
                </v-img>
                <v-img
                    :src="`https://picsum.photos/500/300?image=${1 * 5 + 10}`"
                    :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`"
                    class="grey lighten-2"
                    style="grid-row: 1 / span 2;"
                >
                  <div style="background: #FFFFFF80; color: black;" class="elevation-1">
                    <v-icon style="font-size: medium; color: black;">mdi-web</v-icon>
                    www.ejemplo.com
                  </div>
                </v-img>
                <v-img
                    :src="`https://picsum.photos/500/300?image=${2 * 5 + 10}`"
                    :lazy-src="`https://picsum.photos/10/6?image=${2 * 5 + 10}`"
                    class="grey lighten-2"
                >
                  <div style="background: #FFFFFF80; color: black;" class="elevation-1">
                    <v-icon style="font-size: medium; color: black;">mdi-web</v-icon>
                    www.ejemplo.com
                  </div>
                </v-img>
                <v-img
                    :src="`https://picsum.photos/500/300?image=${3 * 5 + 10}`"
                    :lazy-src="`https://picsum.photos/10/6?image=${3 * 5 + 10}`"
                    class="grey lighten-2 rounded-bl-xl"
                >
                  <div style="background: #FFFFFF80; color: black;" class="elevation-1">
                    <v-icon style="font-size: medium; color: black;">mdi-web</v-icon>
                    www.ejemplo.com
                  </div>
                </v-img>
                <v-img
                    :src="`https://picsum.photos/500/300?image=${4 * 5 + 10}`"
                    :lazy-src="`https://picsum.photos/10/6?image=${4 * 5 + 10}`"
                    class="grey lighten-2 rounded-br-xl"
                >
                  <div style="background: #FFFFFF80; color: black;" class="elevation-1">
                    <v-icon style="font-size: medium; color: black;">mdi-web</v-icon>
                    www.ejemplo.com
                  </div>
                </v-img>
            </div> -->
            <div class="d-flex justify-center align-center" style="height: 100%; font-size: x-large; line-height: 2.25rem;" v-if="cards.length === 0">
              <div class="text-center px-4">Comenzá tu aventura junto a Seekitup<br/>¡Adelante!</div>
            </div>
           </v-card-text>
       </v-card>
       <Share v-model="showShare" :link="'b' + this.link"/>
    </div>
</template>
<script>
export default {
  props: ['title', 'destination', 'images', 'ispublic', 'cards', 'link'],
  data: () => ({
    showShare: false
  }),
  computed: {
    cardslocal () {
      const cardslocal = this.cards.slice(0)
      cardslocal.forEach((element, index) => {
        const fichaDomainParts = element.link.toLowerCase().split('/')
        if (fichaDomainParts[0] === 'http:' || fichaDomainParts[0] === 'https:') {
          fichaDomainParts.splice(0, 2)
        }
        cardslocal[index].domain = fichaDomainParts[0]
        if (cardslocal[index].domain === 'www.facebook.com') {
          cardslocal[index].favicon = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Facebook_icon_2013.svg/768px-Facebook_icon_2013.svg.png'
          cardslocal[index].image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Facebook_icon_2013.svg/768px-Facebook_icon_2013.svg.png'
        }
        if (cardslocal[index].domain === 'www.instagram.com') {
          cardslocal[index].favicon = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/1024px-Instagram_icon.png'
          cardslocal[index].image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/1024px-Instagram_icon.png'
        }
        if (cardslocal[index].domain === 'twitter.com') {
          cardslocal[index].favicon = 'https://image.flaticon.com/icons/png/512/124/124021.png'
          cardslocal[index].image = 'https://image.flaticon.com/icons/png/512/124/124021.png'
          cardslocal[index].title = 'Twitter'
        }
        if (cardslocal[index].domain === 'www.linkedin.com') {
          cardslocal[index].favicon = 'https://image.flaticon.com/icons/png/512/174/174857.png'
          cardslocal[index].image = 'https://image.flaticon.com/icons/png/512/174/174857.png'
        }
        if (cardslocal[index].domain === 'www.youtube.com') {
          cardslocal[index].favicon = 'https://www.youtube.com/s/desktop/110d331e/img/favicon_144x144.png'
          cardslocal[index].image = 'https://www.youtube.com/s/desktop/110d331e/img/favicon_144x144.png'
          cardslocal[index].title = 'YouTube'
        }
      })
      return cardslocal
    }
  }
}
</script>
