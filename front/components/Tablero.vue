<template>
    <div>
      <v-card :to="'/' + action" class="my-2 rounded-xl" style="position: relative;">
        <v-img
          :src="calcImg"
          :class="this.img ? '' : 'white'"
          class="white--text my-2"
          gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
          height="200px"
          :contain="!this.img"
        >
          <div class="rounded-xl ma-2 py-2 px-4 d-flex" style="background: rgba(0,0,0,.5); border: 1px solid white;">
            <div style="line-height: 1rem;">
              <b>{{ title }}</b><br/>
              <small>{{ cards }} <span v-if="cards === 1">ficha</span><span v-else>fichas</span></small>
            </div>
            <div class="ml-auto">
              <v-icon style="font-size: xx-large;">mdi-account-group</v-icon>
            </div>
          </div>
        </v-img>
      </v-card>
    </div>
</template>
<script>
export default {
  props: ['img', 'title', 'action', 'cards'],
  computed: {
    calcImg () {
      if (this.img) {
        return this.img
      } else {
        return '/logo.svg'
      }
    }
  }
}
</script>
