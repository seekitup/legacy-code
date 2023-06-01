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
          <v-toolbar-title style="text-transform: uppercase;" @click="navigateUp()">
            <span v-if="$route.params.id !== 'main' && fichaactiva === null"><v-icon v-if="boardData.owner !== $store.state.authUser.id" class="mr-1">mdi-account-arrow-right</v-icon> {{ boardData.name }}</span>
            <span v-else-if="$route.params.id !== 'main'"><v-icon v-if="boardData.owner !== $store.state.authUser.id" class="mr-1">mdi-account-arrow-right</v-icon> {{ childBoardData.name }}</span>
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
            <div v-else-if="boardData.owner === $store.state.authUser.id">
              <v-btn icon style="width: 37px;" @click="displayEditDialog()">
                <v-icon style="color: black;">mdi-pencil</v-icon>
              </v-btn>
              <v-btn icon style="width: 37px;" @click="displayDelete = true">
                <v-icon style="color: black;">mdi-delete</v-icon>
              </v-btn>
            </div>
            <v-btn icon style="width: 37px;" @click="showShare = true">
              <v-icon style="color: black;">mdi-share</v-icon>
            </v-btn>
          </div>
          <div class="d-flex justify-end" style="min-width: 7.15rem;" v-else>
            <div v-if="fichaactiva === null">
              <v-btn icon style="width: 37px;" @click="displayHelp = !displayHelp">
                <v-icon style="color: black;">mdi-magnify</v-icon>
              </v-btn>
            </div>
          </div>
         </v-app-bar>
         <v-expand-transition>
          <div class="pa-2 grey darken-4 mx-6" v-if="displayHelp" style="margin-top: 4.5rem;">
           <v-text-field
            filled
            rounded
            dense
            v-model="search"
            append-icon="mdi-magnify"
            :append-outer-icon="filterStar?'mdi-star':'mdi-star-outline'"
            @click:append-outer="filterStar = !filterStar"
            :hide-details="true"
           ></v-text-field>
          </div>
        </v-expand-transition>
        <v-expand-transition v-if="$route.params.id !== 'main'">
          <div class="px-2 py-3 grey darken-4 d-flex align-center mx-6" style="margin-top: 4.5rem;" v-if="!displayHelp">
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
              <v-slide-item v-if="boardData.owner === $store.state.authUser.id || (boardMembers[$store.state.authUser.id] && boardMembers[$store.state.authUser.id].rol === '1')">
                <div class="d-flex flex-column align-center text-center mx-2" style="height: calc(50px + 1rem)" @click="displayAdd = true">
                  <v-icon style="font-size: 50px;">mdi-plus-box-outline</v-icon>
                  <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 50px; font-size: x-small;">
                    Agregar
                  </div>
                </div>
              </v-slide-item>
            </v-slide-group>
          </div>
         </v-expand-transition>
        <div
        class="d-flex flex-column mx-6"
        style="width: calc(100vw - 48px); min-height: calc(100% - 56px - 90px);"
        :style="$route.params.id === 'main' && !displayHelp?'margin-top: 4.5rem;':''"
        v-if="fichaLoading"
        v-touch="{
            left: () => swipe('Left'),
            right: () => swipe('Right'),
            up: () => swipe('Up'),
            down: () => swipe('Down')
        }"
        >
        <Ficha
          v-for="(ficha, index) in fichasFiltradas"
          :key="ficha.id"
          :fichaData="ficha"
          :allowEdit="boardMembers[$store.state.authUser.id] && boardMembers[$store.state.authUser.id].rol === '1'"
          :delay="index + 1"
          @update="update(fichaactiva)"
          @movetosub="moveToTop(); movetoSub = $event"
          @cancelmovetosub="movetoSub = null"
        />
        </div>
        <v-bottom-sheet v-model="displayOptions" light>
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
            <b>Administrar pizarra</b>
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
            <v-list flat dense style="background: transparent;" class="mt-0" v-if="boardData.owner !== $store.state.authUser.id && (boardMembers[$store.state.authUser.id] && boardMembers[$store.state.authUser.id].rol === '1')">
              <v-list-item link @click="displayEditDialog()">
                <v-list-item-content>
                  <v-list-item-title>Editar pizarra</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                    <v-btn icon>
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
            <v-list flat dense style="background: transparent;" class="mt-0" v-if="boardData.owner === $store.state.authUser.id">
              <v-list-item link @click="displayEditDialog()">
                <v-list-item-content>
                  <v-list-item-title>Editar pizarra</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                    <v-btn icon>
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                </v-list-item-action>
              </v-list-item>
              <v-list-item link :to="'/board/members/' + this.boardData.id">
                <v-list-item-content>
                  <v-list-item-title>Colaboradores</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-badge
                    color="#D22F82"
                    dot
                    overlap
                    bordered
                  >
                    <v-btn icon>
                      <v-icon>mdi-account-group</v-icon>
                    </v-btn>
                  </v-badge>
                </v-list-item-action>
              </v-list-item>
              <!--
              <v-list-item link>
                <v-list-item-content>
                  <v-list-item-title>Estadísticas</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-btn icon>
                    <v-icon>mdi-trending-up</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
              -->
              <v-list-item link @click="displayDelete = true">
                <v-list-item-content>
                  <v-list-item-title>Eliminar pizarra</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-btn icon>
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
            <v-list flat dense style="background: transparent;" class="mt-0" v-else>
              <v-list-item link @click="stopFollow()">
                <v-list-item-content>
                  <v-list-item-title>Dejar de seguir</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-btn icon>
                    <v-icon>mdi-account-cancel</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-sheet>
        </v-bottom-sheet>
        <v-dialog
          v-model="displayAdd"
          max-width="250"
          class="rounded-xl"
        >
          <v-card class="rounded-xl text-center py-4 grey darken-1" style="border: 1px solid white;">
            <v-card-text>
              <div class="text-h6">Agregar sub pizarra</div>
              <div class="text-right mt-4" style="width: 100%;">
                <v-text-field label="Nombre de la pizarra" v-model="subBoardName"></v-text-field>
                <v-text-field label="Foto (URL Imagen)" v-model="subBoardImage" persistent-hint hint="Copiar dirección de imagen"></v-text-field>
              </div>
            </v-card-text>
            <v-card-actions class="mt-n1 mb-2">
              <v-btn
                plain
                style="font-size: x-small"
                @click="displayAdd = false"
              >
                Cancelar
              </v-btn>

              <v-btn
                plain
                style="font-size: x-small"
                class="ml-auto"
                @click="addSubBoard"
              >
                Listo
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog
          v-model="displayEdit"
          max-width="250"
          class="rounded-xl"
        >
          <v-card class="rounded-xl text-center py-4 grey darken-1" style="border: 1px solid white;">
            <v-card-text>
              <div class="text-h6">Editar pizarra</div>
              <div class="text-right mt-4" style="width: 100%;">
                <v-text-field label="Nombre de la pizarra" v-model="editBoardName"></v-text-field>
                <v-text-field label="Foto (URL Imagen)" v-model="editBoardImage" persistent-hint hint="Copiar dirección de imagen"></v-text-field>
              </div>
            </v-card-text>
            <v-card-actions class="mt-n1 mb-2">
              <v-btn
                plain
                style="font-size: x-small"
                @click="displayEdit = false"
              >
                Cancelar
              </v-btn>

              <v-btn
                plain
                style="font-size: x-small"
                class="ml-auto"
                @click="editBoard"
              >
                Listo
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog
          v-model="displayDelete"
          max-width="250"
          class="rounded-xl"
        >
          <v-card class="rounded-xl text-center pt-4 grey darken-1" style="border: 1px solid white;">
            <v-card-text>
              <span v-if="fichaactiva === null">
                ¿Estás seguro que deseas eliminar la pizarra "{{ boardData.name }}"?
              </span>
              <span v-else>
                ¿Estás seguro que deseas eliminar la subpizarra "{{ childBoardData.name }}"?
              </span>
              <br/>
              <div style="font-size: x-small">Las fichas que contenga se perderan</div>
            </v-card-text>
            <v-card-actions class="mt-n4 mb-2">
              <v-btn
                plain
                style="font-size: x-small"
                @click="displayDelete = false"
              >
                Cancelar
              </v-btn>

              <v-btn
                plain
                style="font-size: x-small"
                class="ml-auto"
                @click="remove"
              >
                Sí, eliminar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <Share v-model="showShare" :link="'b' + boardData.permalink"/>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'inside',
  data: () => ({
    displayHelp: false,
    displayOptions: false,
    fichaactiva: null,
    showShare: false,
    childBoards: [],
    fichas: [],
    fichasFiltradas: [],
    boardData: {},
    boardMembers: {},
    displayAdd: false,
    subBoardName: null,
    subBoardImage: null,
    movetoSub: null,
    fichaLoading: true,
    displayEdit: false,
    editBoardName: null,
    editBoardImage: null,
    displayDelete: false,
    calculateAppBar: 'mt-4',
    search: null,
    filterStar: false
  }),
  async asyncData ({ store, params, redirect }) {
    if (params.id !== 'main' && !isNaN(parseInt(params.id))) {
      const board = params.id
      return await axios
        .post(store.state.info.location + 'boards/boards/find',
          JSON.stringify(
            {
              'main`.`id': board,
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
              Authorization: 'Bearer ' + store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          if (evtResponse.data.result === -1) {
            redirect('/login')
          }
          return { boardData: evtResponse.data.records[0] }
        })
    } else {
      return {}
    }
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
      this.moveCard()
      this.$store.commit('setGhost', false)
    }

    if (this.$route.params.id !== 'main' && this.$route.params.id !== null) {
      if (!isNaN(parseInt(this.$route.params.id))) {
        this.updateChildBoards()
        this.$store.dispatch('addOpenBoard', this.$route.params.id)
        if (this.boardData.owner !== this.$store.state.authUser.id) {
          axios
            .post(this.$store.state.info.location + 'boards/boardmembers/save',
              JSON.stringify({ board: this.boardData.id, user: this.$store.state.authUser.id }),
              {
                validateStatus (status) {
                  return status < 500 // Resolve only if the status code is less than 500
                },
                headers: {
                  Authorization: 'Bearer ' + this.$store.state.token,
                  'Content-Type': 'application/json'
                }
              })
            .then(() => {
              this.getMembers()
            })
        }
      } else {
        this.$router.push('/board/public/' + this.$route.params.id)
      }
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
  watch: {
    search () {
      this.filterResults()
    },
    filterStar () {
      this.filterResults()
    }
  },
  methods: {
    filterResults () {
      if (this.search === null || this.search === '') {
        if (this.filterStar) {
          const tempfichasFiltradas = []
          this.fichas.forEach((element) => {
            if (element.isFab) {
              tempfichasFiltradas.push(element)
            }
          })
          this.fichasFiltradas = tempfichasFiltradas
        } else {
          this.fichasFiltradas = this.fichas.slice(0)
        }
      } else {
        const tempfichasFiltradas = []
        const searchPattern = new RegExp(this.search, 'i')
        this.fichas.forEach((element) => {
          console.log('Comparar ' + element.title, searchPattern)
          if (
            (this.filterStar && element.isFab) ||
            (!this.filterStar)
          ) {
            if (searchPattern.test(element.title) || searchPattern.test(element.description)) {
              tempfichasFiltradas.push(element)
            }
          }
        })
        this.fichasFiltradas = tempfichasFiltradas
        console.log('Buscando ' + this.search, searchPattern)
      }
    },
    swipe (direction) {
      console.log(direction)
      if (direction === 'Left' || direction === 'Right') {
        let openboardsPosition = -1
        if (this.$route.params.id !== 'main') {
          this.$store.state.openBoards.forEach((element, index) => {
            if (element === this.$route.params.id) {
              openboardsPosition = index
            }
          })
        }
        if (direction === 'Right') {
          document.getElementById('scroll-target').style.left = '25%'
          if (openboardsPosition === -1) {
            if (this.$store.state.openBoards.length === 0) {
              this.$router.push('/allboards')
            } else {
              this.$router.push('/board/' + this.$store.state.openBoards[0])
            }
          } else if (openboardsPosition === this.$store.state.openBoards.length - 1) {
            this.$router.push('/board/main')
          } else {
            this.$router.push('/board/' + this.$store.state.openBoards[openboardsPosition + 1])
          }
        } else {
          document.getElementById('scroll-target').style.left = '-25%'
          if (openboardsPosition === -1) {
            if (this.$store.state.openBoards.length === 0) {
              this.$router.push('/allboards')
            } else {
              this.$router.push('/board/' + this.$store.state.openBoards[this.$store.state.openBoards.length - 1])
            }
          } else if (openboardsPosition === 0) {
            this.$router.push('/board/main')
          } else {
            this.$router.push('/board/' + this.$store.state.openBoards[openboardsPosition - 1])
          }
        }
      }
    },
    async update (subboard) {
      let board = 'NULL'
      let params = {
        'main`.`owner': this.$store.state.authUser.id,
        order: [['created', 'DESC']],
        joins: [{
          field: ['name'],
          table: 'boards',
          alias: ['board_name'],
          on: ['id', 'board']
        }]
      }
      if (this.$route.params.id !== 'main') {
        if (subboard !== null) {
          board = subboard
        } else {
          board = this.$route.params.id
        }
        params = {
          board,
          order: [['created', 'DESC']]
        }
      }
      this.fichaLoading = false
      await axios
        .post(this.$store.state.info.location + 'boards/cards/find',
          JSON.stringify(params),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.fichas = evtResponse.data.records
          this.fichasFiltradas = this.fichas.slice(0)
          this.search = null
          this.fichaLoading = true
          if (board === 'NULL') {
            board = 'main'
          }
          if (this.$store.state.scrollPos[board]) {
            console.log('scroll to ' + this.$store.state.scrollPos[board])
            setTimeout(() => {
              document.getElementById('scroll-target').scrollTo(0, this.$store.state.scrollPos[board])
            }, 1000)
          }
        })
    },
    selBoard (subboardID) {
      if (this.movetoSub == null) {
        this.fichaactiva = subboardID
        this.update(subboardID)
      } else {
        let board = null
        if (subboardID !== null) {
          board = subboardID
        } else {
          board = this.$route.params.id
        }
        axios
          .post(this.$store.state.info.location + 'boards/cards/save',
            JSON.stringify(
              {
                id: this.movetoSub.id,
                board
              }
            ),
            {
              validateStatus (status) {
                return status < 500 // Resolve only if the status code is less than 500
              },
              headers: {
                Authorization: 'Bearer ' + this.$store.state.token,
                'Content-Type': 'application/json'
              }
            })
          .then((evtResponse) => {
            this.movetoSub = null
            this.fichaactiva = subboardID
            this.update(subboardID)
          })
      }
    },
    async updateChildBoards () {
      await axios
        .post(this.$store.state.info.location + 'boards/boards/find',
          JSON.stringify({ parentboard: this.$route.params.id }),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.childBoards = evtResponse.data.records
        })
    },
    async addSubBoard () {
      if (this.subBoardName === '' || this.subBoardName === null) {
        return false
      }
      this.newMenu = 4
      await axios
        .post(this.$store.state.info.location + 'boards/boards/save',
          JSON.stringify(
            {
              owner: this.$store.state.authUser.id,
              name: this.subBoardName,
              image: this.subBoardImage,
              parentboard: this.$route.params.id
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.displayAdd = false
          this.updateChildBoards()
          this.selBoard(evtResponse.data.data)
        })
    },
    async moveCard () {
      let board = 'NULL'
      if (this.$route.params.id !== 'main') {
        if (this.fichaactiva !== null) {
          board = this.fichaactiva
        } else {
          board = this.$route.params.id
        }
      }
      await axios
        .post(this.$store.state.info.location + 'boards/cards/save',
          JSON.stringify(
            {
              id: this.$store.state.ghostData.id,
              board
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.update(this.fichaactiva)
        })
    },
    displayEditDialog () {
      if (this.fichaactiva !== null) {
        this.childBoards.forEach((element) => {
          if (element.id === this.fichaactiva) {
            this.editBoardName = element.name
            this.editBoardImage = element.image
          }
        })
      } else {
        this.editBoardName = this.boardData.name
        this.editBoardImage = this.boardData.image
      }
      this.displayEdit = true
    },
    async editBoard () {
      let board = null
      if (this.fichaactiva !== null) {
        board = this.fichaactiva
      } else {
        board = this.$route.params.id
      }
      await axios
        .post(this.$store.state.info.location + 'boards/boards/save',
          JSON.stringify(
            {
              id: board,
              name: this.editBoardName,
              image: this.editBoardImage
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.displayEdit = false
          this.displayOptions = false
          this.updateChildBoards()
          if (this.fichaactiva !== null) {
            this.updateChildBoards()
          } else {
            this.boardData.name = this.editBoardName
          }
          // this.selBoard(board)
        })
    },
    async remove () {
      let board = null
      if (this.fichaactiva !== null) {
        board = this.fichaactiva
      } else {
        board = this.$route.params.id
      }
      await axios
        .post(this.$store.state.info.location + 'boards/boards/delete',
          JSON.stringify(
            {
              id: board
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          console.log(evtResponse.data)
          this.displayOptions = false
          this.displayDelete = false
          if (this.fichaactiva === null) {
            this.$store.dispatch('removeOpenBoard', board)
            this.$router.push('/?goto=allBoards')
          } else {
            this.fichaactiva = null
            this.updateChildBoards()
            this.selBoard(null)
          }
        })
    },
    moveToTop () {
      document.getElementById('scroll-target').scrollTo(0, 0)
    },
    stopFollow () {
      axios
        .post(this.$store.state.info.location + 'boards/boards/stopfollow',
          JSON.stringify({ board: this.boardData.id, user: this.$store.state.authUser.id }),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then(() => {
          this.$store.dispatch('removeOpenBoard', this.boardData.id)
          this.$router.push('/')
        })
    },
    updateCalculateAppBar () {
      if (document.getElementById('scroll-target').scrollTop > 0) {
        this.calculateAppBar = ''
      } else {
        this.calculateAppBar = 'mt-4'
      }
      let board = null
      if (this.fichaactiva !== null) {
        board = this.fichaactiva
      } else {
        board = this.$route.params.id
      }
      this.$store.commit('SET_SCROLLPOS', { board, position: document.getElementById('scroll-target').scrollTop })
    },
    async getMembers () {
      const board = this.$route.params.id
      await axios
        .post(this.$store.state.info.location + 'boards/boardmembers/find',
          JSON.stringify(
            {
              board
            }
          ),
          {
            validateStatus (status) {
              return status < 500 // Resolve only if the status code is less than 500
            },
            headers: {
              Authorization: 'Bearer ' + this.$store.state.token,
              'Content-Type': 'application/json'
            }
          })
        .then((evtResponse) => {
          evtResponse.data.records.forEach((element) => {
            this.boardMembers[parseInt(element.user)] = { rol: element.rol }
          })
        })
    },
    navigateUp () {
      if (this.$route.params.id === 'main') {
        this.$router.push('/')
      } else {
        this.$router.push('/?goto=' + this.$route.params.id)
      }
    }
  }
}
</script>
