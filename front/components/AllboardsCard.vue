<template>
    <div
        :class="last ? 'rounded-b-xl': 'mb-2'"
        style="
        border-bottom: 2px solid white;
        height: calc(45vh - 3rem);
        position: relative;
        display: grid;
        max-width: 100%;
        "
        :style="calcLayout"
        @click="$router.push('/board/' + boardData.id)"
    >
      <div
        v-for="card in cardslocal"
        :key="card.id"
        style="max-height: 100%; overflow: hidden;"
      >
        <v-img
          class="grey lighten-2"
          :src="card.image"
          v-if="card.embed === false"
        />
        <Ficha
            v-else
            :fichaData = card
            :preview="true"
            :board="true"
        />
      </div>
      <div v-if="cardslocal.length === 0" class="d-flex justify-center align-center" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;">
          Pizarra vacía
      </div>
      <div style="position: absolute; bottom: .25rem; left: .25rem; z-index: 20;">
        <div class="allboardsTitle mr-1 d-inline-block" v-if="boardData.owner !== $store.state.authUser.id">Siguiendo</div>
        <div class="allboardsTitle d-inline-block">{{ boardData.name }}</div>
      </div>
    </div>
</template>
<script>
export default {
  props: ['boardData', 'last'],
  computed: {
    cardslocal () {
      const cardslocal = this.boardData.cards.slice(0)
      cardslocal.forEach((element, index) => {
        cardslocal[index].embed = false
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
    },
    calcLayout () {
      if (this.boardData.cards.length === 2) {
        return 'grid-template-columns: 50% 50%; grid-template-rows: 100%;'
      } else if (this.boardData.cards.length === 3 || this.boardData.cards.length === 4) {
        return 'grid-template-columns: 50% 50%; grid-template-rows: 50% 50%;'
      } else if (this.boardData.cards.length >= 5) {
        return 'grid-template-columns: 33% 33% 33%; grid-template-rows: 50% 50%;'
      }
      return 'grid-template-columns: 100%; grid-template-rows: 100%;'
    }
  }
}
</script>
