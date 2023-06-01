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
        <v-app-bar light>
          <v-toolbar-title class="text-center" style="text-transform: uppercase; width: 100%;">
           Todas mis pizarras
          </v-toolbar-title>
        </v-app-bar>
        <div
        class="d-flex flex-column px-6"
        style="width: 100vw;"
        v-touch="{
            left: () => swipe('Left'),
            right: () => swipe('Right'),
            up: () => swipe('Up'),
            down: () => swipe('Down')
        }"
        >
        <v-text-field
            filled
            rounded
            dense
            append-icon="mdi-magnify"
            :hide-details="true"
            class="mt-4 mb-2 grey"
            style="border: 1px solid white;"
        ></v-text-field>
        <Tablero
        v-for="board in boards"
        :key="board.id"
        :img="board.image"
        :title="board.name"
        :action="'board/' + board.id"
        :cards="board.cards"
        />
        </div>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'inside',
  data: () => ({}),
  async asyncData ({ store }) {
    return await axios
      .post(store.state.info.location + 'boards/boards/find',
        JSON.stringify(
          {
            owner: store.state.authUser.id,
            parentboard: 'NULL',
            subqueries: [
              {
                field: 'board',
                table: 'cards',
                alias: 'cards'
              }
            ]
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
        return { boards: evtResponse.data.records }
      })
  },
  methods: {
    swipe (direction) {
      console.log(direction)
      if (direction === 'Left' || direction === 'Right') {
        if (direction === 'Right') {
          document.getElementById('scroll-target').style.left = '25%'
          if (this.$store.state.openBoards.length === 0) {
            this.$router.push('/board/main')
          } else {
            this.$router.push('/board/' + this.$store.state.openBoards[0])
          }
        } else {
          document.getElementById('scroll-target').style.left = '-25%'
          this.$router.push('/board/main')
        }
      }
    }
  }
}
</script>
