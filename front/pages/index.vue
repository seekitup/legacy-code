<template>
  <div class="px-0 ma-0" style="height: 100%; width:100%; overflow-x: auto;" id="scroll-target">
    <div
      class="d-flex"
      style="width: max-content; height: 100%;"
      v-touch="{
        left: () => swipe('Left'),
        right: () => swipe('Right')
      }"
    >
    <div style="width:100vw; height: 100%; max-height: calc(100vh - 6.75rem); padding: 1.5rem 2.5rem;">
       <Addpanel ref="addpanel" @save="$router.push('/board/main')" />
    </div>
    <Flotante title="Mi seekitup" destination="/board/main" :cards="mainBoard" />
    <div style="width:100vw; height: 100%; padding: 1.5rem 2.5rem;">
       <v-card elevation="10" class="d-flex flex-column rounded-xl flotante" style="width: 100%; height: 100%;">
           <v-card-title class="justify-center text-center" style="text-transform: uppercase; line-height: 1.5rem; padding: .5rem 1rem;">
               <span>Todas<br>mis pizarras</span>
           </v-card-title>
           <v-card-text class="pa-0 flex-grow-1">
               <div
                 style="overflow: auto; min-height:100%; max-height:calc(100vh - 13.75rem);"
                >
                  <div class="d-flex flex-column flex-wrap">
                    <AllboardsCard
                      v-for="(allBoardsBoard, index) in allBoards"
                      :key="allBoardsBoard.id"
                      :boardData="allBoardsBoard"
                      :last="index === allBoards.length - 1"
                    />
                  </div>
                  <!--
                  <div
                    class="flex-grow-1 d-flex align-end"
                    style="
                      background-image: url(https://picsum.photos/500/300?image=45);
                      background-position: center center;
                      background-size: cover;
                      border-bottom: 2px solid white;
                    "
                  >
                    <div class="allboardsTitle">Pizarra 1</div>
                  </div>
                  <div
                    class="flex-grow-1 d-flex align-end"
                    style="
                      background-image: url(https://picsum.photos/500/300?image=55);
                      background-position: center center;
                      background-size: cover;
                      border-bottom: 2px solid white;
                    "
                  >
                    <div class="allboardsTitle">Pizarra 2</div>
                  </div>
                  <div
                    class="flex-grow-1 d-flex align-end rounded-b-xl"
                    style="
                      background-image: url(https://picsum.photos/500/300?image=35);
                      background-position: center center;
                      background-size: cover;
                    "
                  >
                    <div class="allboardsTitle">Pizarra 3</div>
                  </div>-->
               </div>
           </v-card-text>
       </v-card>
    </div>
    <Flotante
      v-for="openBoardsBoard in $store.state.openBoards"
      :key="openBoardsBoard"
      :title="openBoards[openBoardsBoard].name"
      :destination="'/board/' + openBoardsBoard"
      :ispublic="true"
      :cards="openBoards[openBoardsBoard].cards"
      @remove="removeOpen(openBoardsBoard)"
      :id="'board-' + openBoardsBoard"
      :link="openBoards[openBoardsBoard].permalink"
    />
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex'
import axios from 'axios'

export default {
  layout: 'empty',
  data: () => ({}),
  computed: {
    ...mapState({
      ghost: state => state.ghost
    })
  },
  async asyncData ({ store, redirect }) {
    return await axios
      .post(store.state.info.location + 'boards/boards/getforhome',
        JSON.stringify({ user: store.state.authUser.id, openboards: store.state.openBoards }),
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
        if (evtResponse.data.result === -1) {
          redirect('/login')
        }
        return evtResponse.data.data
      })
  },
  mounted () {
    document.getElementById('scroll-target').scrollTo((1 * window.innerWidth), 0)
    this.$root.$on('new', () => {
      this.swipeToNew()
    })
    if (this.$route.params.new) {
      this.swipeToNew()
    }
    this.$root.$on('main', () => {
      document.getElementById('scroll-target').scrollTo((1 * window.innerWidth), 0)
    })
    this.$root.$on('append', () => {
      if (this.$refs.addpanel) {
        this.$refs.addpanel.copyAppend = false
        this.$refs.addpanel.urlAppend = window.lastURL
        this.$refs.addpanel.newMenu = 5
        this.$refs.addpanel.getCopyMetadata()
        this.swipeToNew()
      }
    })
    if (this.$route.query.append) {
      this.$refs.addpanel.copyAppend = false
      this.$refs.addpanel.urlAppend = window.lastURL
      this.$refs.addpanel.newMenu = 5
      this.$refs.addpanel.getCopyMetadata()
      this.swipeToNew()
    }
    if (this.$route.query.goto) {
      if (this.$route.query.goto === 'allBoards') {
        document.getElementById('scroll-target').scrollTo((2 * window.innerWidth), 0)
      } else {
        const gotoIndex = parseInt(this.$store.state.openBoards.indexOf(this.$route.query.goto))
        console.log(gotoIndex)
        document.getElementById('scroll-target').scrollTo(((3 + gotoIndex) * window.innerWidth), 0)
      }
    }
    document.getElementById('scroll-target').style.scrollBehavior = 'smooth'
  },
  methods: {
    swipe (direction) {
      console.log(direction)
      const margin = 0
      const panel = Math.round((document.getElementById('scroll-target').scrollLeft + margin) / window.innerWidth)
      document.getElementById('scroll-target').scrollTo((panel * window.innerWidth) - margin, 0)
    },
    swipeToNew () {
      document.getElementById('scroll-target').scrollTo(0, 0)
    },
    removeOpen (boardId) {
      console.log('Remover pizarra ' + boardId)
      document.getElementById('board-' + boardId).firstElementChild.style.transform = 'translateY(-400px)'
      setTimeout(function () {
        this.$store.dispatch('removeOpenBoard', boardId)
        // this.openBoards = Object.assign({}, this.$store.state.openBoards)
      }.bind(this), 500)
    }
  }
}
</script>
