<template>
   <div style="width:100vw; height: 100%; padding: 1.5rem 2.5rem;">
       <v-card elevation="10" class="rounded-xl flotante d-flex flex-column" style="width: 100%; height: 100%;">
           <v-card-title v-if="ispublic === undefined || ispublic === false" class="justify-center" style="text-transform: uppercase;">
               <span @click="$router.push(destination)">{{title}}</span>
           </v-card-title>
           <v-card-title v-if="ispublic === true" class="align-center" style="text-transform: uppercase;">
              <v-avatar @click="$router.push(destination)" color="#D22F82" size="1.75rem" class="mr-2">
                <img src="logo_abeja_blanco.svg" style="height: 1rem; width: 1rem;" />
              </v-avatar>
              <span
                style="
                  overflow: hidden;
                  max-width: calc(100% - 5rem - 20px);
                  text-overflow: ellipsis;
                  white-space: nowrap;"
                @click="$router.push(destination)"
              >
                {{title}}
              </span>
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
           <v-card-text class="pa-0 flex-grow-1" style="overflow: auto; max-height: calc(100vh - 13.75rem)" @click="$router.push(destination)">
            <div class="d-flex justify-center flex-wrap align-stretch" style="height: 100%" v-if="cards.length !== 0">
              <div class="flex-grow-1 align-self-stretch" style="width: 50%; max-height: 50%; min-height: 50%; overflow: hidden;" v-for="(card, index) in cardslocal" :key="card.id">
                <v-img
                  :src="card.image"
                  class="grey lighten-2"
                  style="min-height: calc(40vh - 3rem); max-height: 100%;"
                  v-if="card.embed === false"
                >
                  <div class="d-flex align-center elevation-1" style="background: #FFFFFF80; color: black;">
                    <v-avatar v-if="card.favicon" class="mx-1" size="16">
                      <img
                        :src="card.favicon"
                        alt="Favicon"
                      >
                    </v-avatar>
                    <div style="display: inline-block;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: calc(100% - 2rem);">{{ card.title }}</div>
                  </div>
                </v-img>
                <Ficha
                  v-else
                  :fichaData = card
                  :preview="true"
                  :board="true"
                  :delay="index + 1"
                />
              </div>
            </div>
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
        cardslocal[index].embed = false
        const img = new Image()
        img.src = this.$store.state.info.location + 'repository/captures/' + cardslocal[index].permalink + '.png'
        if (img.height !== 0) {
          cardslocal[index].image = this.$store.state.info.location + 'repository/captures/' + cardslocal[index].permalink + '.png'
        }
        const fichaDomainParts = element.link.toLowerCase().split('/')
        if (fichaDomainParts[0] === 'http:' || fichaDomainParts[0] === 'https:') {
          fichaDomainParts.splice(0, 2)
        }
        cardslocal[index].domain = fichaDomainParts[0]
        if (cardslocal[index].domain === 'www.facebook.com') {
          cardslocal[index].favicon = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Facebook_icon_2013.svg/768px-Facebook_icon_2013.svg.png'
          cardslocal[index].image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Facebook_icon_2013.svg/768px-Facebook_icon_2013.svg.png'
          cardslocal[index].embed = true
        }
        if (cardslocal[index].domain === 'www.instagram.com') {
          cardslocal[index].favicon = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/1024px-Instagram_icon.png'
          cardslocal[index].image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/1024px-Instagram_icon.png'
          cardslocal[index].embed = true
        }
        if (cardslocal[index].domain === 'twitter.com') {
          cardslocal[index].favicon = 'https://image.flaticon.com/icons/png/512/124/124021.png'
          cardslocal[index].image = 'https://image.flaticon.com/icons/png/512/124/124021.png'
          cardslocal[index].title = 'Twitter'
          cardslocal[index].embed = true
        }
        if (cardslocal[index].domain === 'www.linkedin.com') {
          cardslocal[index].favicon = 'https://image.flaticon.com/icons/png/512/174/174857.png'
          cardslocal[index].image = 'https://image.flaticon.com/icons/png/512/174/174857.png'
          cardslocal[index].embed = true
        }
        if (cardslocal[index].domain === 'www.youtube.com' || cardslocal[index].domain === 'youtu.be') {
          cardslocal[index].favicon = 'https://www.youtube.com/s/desktop/110d331e/img/favicon_144x144.png'
          cardslocal[index].image = 'https://www.youtube.com/s/desktop/110d331e/img/favicon_144x144.png'
          cardslocal[index].title = 'YouTube'
          cardslocal[index].embed = true
        }
        if (cardslocal[index].domain === 'www.tiktok.com') {
          cardslocal[index].title = 'Tik tok'
          cardslocal[index].embed = true
        }
      })
      return cardslocal
    }
  }
}
</script>
