<template>
    <div
      class="pa-0 ma-0"
      style="
      height: calc(100vh - calc(100vh - 100%));
      width:100%;
      overflow-y: auto;
      scroll-behavior: smooth;
      position: absolute;
      top: 0px;
      left: 0px;
      transition: 300ms;
      "
      @scroll="updateCalculateAppBar()"
      id="scroll-target">

        <v-app-bar
          class="rounded-t-xl mx-6"
          :class="calculateAppBar"
          ref="appBar"
          light
          fixed
          style="z-index:25;"
        >
          <v-toolbar-title style="text-transform: uppercase;" @click="$router.push('/board/public/' + $route.params.id)">
            <span v-if="$route.params.id !== 'main' && fichaactiva === null">{{ boardData.name }}</span>
            <span v-else-if="$route.params.id !== 'main'">{{ childBoardData.name }}</span>
            <span v-else>Mi Seekitup</span>
          </v-toolbar-title>

          <v-spacer></v-spacer>
          <div class="d-flex" style="min-width: 7.15rem;" v-if="$route.params.id !== 'main'">
            <div v-if="fichaactiva === null">
              <v-btn icon style="width: 37px;" @click="displayHelp = !displayHelp">
                <v-icon style="color: black;">mdi-magnify</v-icon>
              </v-btn>

              <v-badge
                color="#D22F82"
                dot
                overlap
                bordered
                offset-y="20"
              >
                <v-btn icon style="width: 37px;" @click="displayOptions = true">
                  <v-icon style="color: black;">mdi-cog</v-icon>
                </v-btn>
              </v-badge>

            </div>
            <v-btn icon style="width: 37px;" @click="showShare = true">
              <v-icon style="color: black;">mdi-share</v-icon>
            </v-btn>
          </div>
         </v-app-bar>
         <v-expand-transition>
          <div class="pa-2 grey darken-4 mx-6" style="margin-top: 4.5rem;" v-if="displayHelp">
           <v-text-field
            filled
            rounded
            dense
            append-icon="mdi-magnify"
            append-outer-icon="mdi-star"
            :hide-details="true"
           ></v-text-field>
          </div>
        </v-expand-transition>
        <v-expand-transition v-if="$route.params.id !== 'main'">
          <div class="px-2 py-3 grey darken-4 mx-6 d-flex align-center" style="margin-top: 4.5rem;" v-if="!displayHelp">
            <div class="d-flex flex-column text-center" style="font-size: x-small;" @click="selBoard(null)">
              <v-avatar size="50" class="rounded-lg" :style="fichaactiva === null ? 'border: 3px solid white;': '' ">
                <v-img v-if="boardData.image" :src="boardData.image" />
                <v-img v-else src="/logo_abeja.svg" class="rounded-lg" contain style="background-color: white;" />
              </v-avatar>
              <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 50px;">
                {{ boardData.name }}
              </div>
            </div>
            <v-slide-group show-arrows="always" class="flex-grow-1" style="max-width:calc(100% - 45px);">
              <v-slide-item v-for="childBoard in childBoards" :key="childBoard.id">
                <div class="d-flex flex-column text-center mx-2" style="font-size: x-small;" @click="selBoard(childBoard.id)">
                  <v-avatar size="50" class="rounded-lg" :style="fichaactiva === childBoard.id ? 'border: 3px solid white;': '' ">
                    <v-img v-if="childBoard.image" :src="childBoard.image" />
                    <v-img v-else src="/fondo.svg" class="rounded-lg" style="background-color: white;" />
                  </v-avatar>
                  <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 50px;">
                    {{ childBoard.name }}
                  </div>
                </div>
              </v-slide-item>
            </v-slide-group>
          </div>
         </v-expand-transition>
        <div
        class="d-flex flex-column mx-6"
        style="width: calc(100vw - 48px); min-height: calc(100% - 56px - 90px);"
        v-if="fichaLoading"
        >
        <Ficha
          v-for="(ficha, index) in fichas"
          :key="ficha.id"
          :fichaData="ficha"
          :delay="index + 1"
          @update="update(fichaactiva)"
        />
        </div>
        <v-bottom-sheet v-model="displayOptions" light>
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
            <b>Detalles de la pizarra</b>
            <v-list three-line flat dense style="background: transparent;" class="mb-0">
              <v-list-item>
                <v-list-item-avatar>
                  <img
                    :src="boardData.user_image"
                    :alt="boardData.user_name + ' ' + boardData.user_lastname"
                  >
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title><b>{{boardData.user_name + ' ' + boardData.user_lastname}}</b><br/>Propietario de la pizarra</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-btn icon>
                    <img src="logo_abeja.svg" style="width:1rem;" />
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-sheet>
        </v-bottom-sheet>
        <Share v-model="showShare" :link="'b' + boardData.permalink"/>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'shareview',
  data: () => ({
    displayHelp: false,
    displayOptions: false,
    fichaactiva: null,
    showShare: false,
    childBoards: [],
    fichas: [],
    boardData: {},
    displayAdd: false,
    subBoardName: null,
    subBoardImage: null,
    movetoSub: null,
    fichaLoading: true,
    displayEdit: false,
    editBoardName: null,
    editBoardImage: null,
    displayDelete: false,
    calculateAppBar: 'mt-4'
  }),
  async asyncData ({ store, params }) {
    const board = params.id
    return await axios
      .post(store.state.info.location + 'public/boards',
        JSON.stringify(
          {
            'main`.`permalink': board,
            joins: [
              {
                field: ['name', 'lastname', 'image'],
                table: 'users',
                alias: ['user_name', 'user_lastname', 'user_image'],
                on: ['id', 'owner']
              }
            ]
          }
        ),
        {
          validateStatus (status) {
            return status < 500 // Resolve only if the status code is less than 500
          },
          headers: {
            'Content-Type': 'application/json'
          }
        })
      .then((evtResponse) => {
        console.log(evtResponse.data)
        return { boardData: evtResponse.data.records[0] }
      })
  },
  head () {
    return {
      title: this.boardData.name,
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: this.boardData.name + ' - Una pizarra de ' + this.boardData.user_name + ' ' + this.boardData.user_lastname
        },
        {
          hid: 'twitter:title',
          name: 'twitter:title',
          content: this.boardData.name
        },
        {
          hid: 'twitter:description',
          name: 'twitter:description',
          content: this.boardData.name + ' - Una pizarra de ' + this.boardData.user_name + ' ' + this.boardData.user_lastname
        },
        {
          hid: 'og:title',
          property: 'og:title',
          content: this.boardData.name
        },
        {
          hid: 'og:description',
          property: 'og:description',
          content: this.boardData.name + ' - Una pizarra de ' + this.boardData.user_name + ' ' + this.boardData.user_lastname
        }
      ]
    }
  },
  mounted () {
    if (this.$store.state.ghost) {
      this.$store.commit('setGhost', false)
    }

    if (this.$route.params.id !== 'main' && this.$route.params.id !== null) {
      this.updateChildBoards()
    }

    this.update(null)
  },
  computed: {
    childBoardData () {
      if (this.fichaactiva === null) {
        return {
          nombre: null
        }
      } else {
        let result = null
        this.childBoards.forEach((element) => {
          if (element.id === this.fichaactiva) {
            result = element
          }
        })
        return result
      }
    }
  },
  methods: {
    async update (subboard) {
      let board = 'NULL'
      if (this.$route.params.id !== 'main') {
        if (subboard !== null) {
          board = subboard
        } else {
          board = this.boardData.id
        }
      }
      this.fichaLoading = false
      await axios
        .post(this.$store.state.info.location + 'public/cards',
          JSON.stringify(
            {
              board,
              order: [['created', 'DESC']]
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.fichas = evtResponse.data.records
          this.fichaLoading = true
        })
    },
    selBoard (subboardID) {
      this.fichaactiva = subboardID
      this.update(subboardID)
    },
    async updateChildBoards () {
      await axios
        .post(this.$store.state.info.location + 'public/boards',
          JSON.stringify({ parentboard: this.boardData.id }),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.childBoards = evtResponse.data.records
        })
    },
    updateCalculateAppBar () {
      if (document.getElementById('scroll-target').scrollTop > 0) {
        this.calculateAppBar = ''
      } else {
        this.calculateAppBar = 'mt-4'
      }
    }
  }
}
</script>
