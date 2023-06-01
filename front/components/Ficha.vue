<template>
    <div>
       <v-card :class="preview === true? '':'mb-3'" style="position: relative;" :style="moveToSub? 'opacity: .5; background:' + fichaDataLocal.layout.bgcolor : 'background:' + fichaDataLocal.layout.bgcolor" @click="openLink()">
            <v-img
              :src="fichaDataLocal.mode === 'regular' ? fichaDataLocal.image : ''"
              class="white--text align-start"
              gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
              :height="fichaDataLocal.layout.size"
              style="position: relative; z-index: 10;"
              :style="fichaDataLocal.domain === 'www.youtube.com' || fichaDataLocal.domain === 'youtu.be' || fichaDataLocal.domain === 'www.tiktok.com'?'width: 3rem; margin-left: calc(100% - 3rem);' : ''"
              v-if="!(preview === true && fichaDataLocal.domain === 'www.tiktok.com')"
            >
              <v-card-title v-if="fichaDataLocal.mode === 'regular'" v-text="fichaDataLocal.title" style="text-shadow: 0 0 2px black; padding-right: 3rem;"></v-card-title>
              <v-card-subtitle class="d-flex align-center text-caption font-weight-light" :style="fichaDataLocal.layout.icons" v-if="board !== true">
                <!-- <div class="mr-2 white--text" v-if="fichaDataLocal.board !== null"><v-icon class="white--text" style="font-size: small;">mdi-check-circle</v-icon> 23</div> -->
                <div class="mr-2 white--text"><v-icon class="white--text" style="font-size: small;">mdi-eye</v-icon> {{ fichaDataLocal.views }}</div>
                <div class="mr-2 white--text"><v-icon class="white--text" style="font-size: small;">mdi-comment-text</v-icon> {{ fichaDataLocal.comments }}</div>
              </v-card-subtitle>
              <div class="d-flex flex-column justify-space-around" :style="fichaDataLocal.layout.actions" v-if="ghost !== true && moveToSub !== true">
                <div><v-icon class="white--text" style="font-size: xx-large" @click.stop="showOptions = true" :disabled="preview" v-if="ispublic !== true && board !== true && (fichaDataLocal.owner === $store.state.authUser.id || allowEdit === true)">mdi-dots-vertical</v-icon></div>
                <div><v-icon class="white--text" style="font-size: xx-large" @click.stop="setFab()" :disabled="preview" v-if="ispublic !== true && board !== true">{{ fichaDataLocal.isFab ? 'mdi-star' : 'mdi-star-outline' }}</v-icon></div>
                <div><v-icon class="white--text" style="font-size: xx-large" @click.stop="loadComments()" :disabled="preview" v-if="board !== true">mdi-comment</v-icon></div>
              </div>
              <!--<div class="d-flex flex-column justify-space-around" style="position: absolute; right: 25%; top: 25px; height: 500px;" v-if="fichaDataLocal.domain === 'www.tiktok.com'">
                <v-icon class="white--text" style="font-size: 10rem;">mdi-play-circle-outline</v-icon>
              </div>-->
            </v-img>
            <v-card-text class="d-flex" :dark="ghost !== true || moveToSub !== true" v-if="fichaDataLocal.mode === 'regular'" :style="ghost === true || moveToSub == true? 'background: white; color: black;':''">
              <v-avatar v-if="fichaDataLocal.favicon" class="mr-2" size="32">
                <img
                  :src="fichaDataLocal.favicon"
                  alt="Favicon"
                >
              </v-avatar>
              <div>
                {{ fichaDataLocal.description }}
              </div>
            </v-card-text>
            <div :id="'capture_' + _uid" v-if="fichaDataLocal.mode === 'embebed'" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="position: absolute; top: 0px; z-index: 5; width: 100%; background: white;">
                <div v-if="fichaDataLocal.domain === 'www.instagram.com'" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%; overflow: hidden;">
                  <blockquote style="width:100%;"  class="instagram-media" data-instgrm-captioned :data-instgrm-permalink="fichaDataLocal.link" data-instgrm-version="13" >
                    <a :href="fichaDataLocal.link"></a>
                  </blockquote>
                  <!-- <script async src="https://platform.instagram.com/en_US/embeds.js"></script> -->
                  <script async src="//www.instagram.com/embed.js"></script>
                  <script>instgrm.Embeds.process()</script>
                </div>
                <div v-else-if="fichaDataLocal.domain === 'www.facebook.com'" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%; overflow: hidden;">
                  <!-- <iframe allowfullscreen="true" scrolling="no" frameborder="0" style="width: 100%; height: 100%;" :src="$store.state.info.location + 'embed?url=' + fichaDataLocal.link + '&card=' + fichaDataLocal.id" /> -->
                  <iframe
                    ref="embedFrame"
                    allowfullscreen="true"
                    scrolling="no"
                    frameborder="0"
                    style="width: 100%; height: 100%;"
                    :src="'/embed.html?url=' + fichaDataLocal.link"
                  />
                  <!--
                  <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
                  <div
                    class="fb-post"
                    :data-href="fichaDataLocal.link"
                  ></div>
                  -->
                </div>
                <div v-else-if="fichaDataLocal.domain === 'twitter.com'" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%; overflow: hidden;">
                  <blockquote class="twitter-tweet" style="width: 400px;" data-dnt="true">
                    <p lang="en" dir="ltr"></p>
                    <a :href="fichaDataLocal.link"></a>
                  </blockquote>
                  <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                </div>
                <div v-else-if="fichaDataLocal.domain === 'www.linkedin.com' && fichaDataLocal.linkedIn" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%; overflow: hidden; text-align: center; background: white;">
                  <iframe
                    ref="embedFrame"
                    allowfullscreen="true"
                    scrolling="no"
                    frameborder="0"
                    style="width: 100%; height: 100%;"
                    :src="'/linkedin.html?url=' + fichaDataLocal.linkedIn"
                  />
                  <!--
                  <script type="text/javascript" src="https://platform.linkedin.com/badges/js/profile.js" async defer></script>
                  <div
                    class="badge-base LI-profile-badge"
                    data-locale="es_ES"
                    data-size="large"
                    data-theme="light"
                    data-type="HORIZONTAL"
                    :data-vanity="fichaDataLocal.linkedIn"
                    style="width: 336px; display: inline-block;"
                  >
                    <a class="LI-simple-link" :href="fichaDataLocal.link + '?trk=profile-badge'"></a>
                  </div>
                  -->
                </div>
                <div v-else-if="fichaDataLocal.domain === 'www.youtube.com' || fichaDataLocal.domain === 'youtu.be'">
                  <iframe :src="fichaDataLocal.link" allow="autoplay" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%;" />
                </div>
                <div v-else-if="fichaDataLocal.domain === 'www.tiktok.com'" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%; overflow: hidden;">
                  <iframe
                    ref="embedFrame"
                    allowfullscreen="true"
                    scrolling="no"
                    frameborder="0"
                    sandbox="allow-same-origin allow-scripts"
                    style="width: 100%; height: 100%;"
                    :srcdoc="embedInfo"
                  ></iframe>
                </div>
                <iframe v-else :src="fichaDataLocal.link" :style="'height: ' + (fichaDataLocal.layout.size - 30) + 'px'" style="width: 100%;" />
            </div>
        </v-card>
        <v-bottom-sheet v-model="showOptions" light>
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
            <v-list flat style="background: transparent;">
              <v-list-item link @click="setGhost()">
                <v-list-item-content>
                  <v-list-item-title>
                    Mover a pizara
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item link @click="moveSubBoard()" v-if="$route.params.id !== 'main' && fichaDataLocal.board !== null">
                <v-list-item-content>
                  <v-list-item-title>
                    Mover a sub-pizara
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item link @click="copyLink()">
                <v-list-item-content>
                  <v-list-item-title>
                    Copiar link
                  </v-list-item-title>
                  <input style="height:0px;" ref="copyLink" :value="fichaData.link" />
                </v-list-item-content>
              </v-list-item>
              <v-list-item link :to="'/board/' + fichaData.board" v-if="$route.params.id === 'main' && fichaData.board !== null">
                <v-list-item-content>
                  <v-list-item-title>
                    Ver en pizarra {{ fichaData.board_name }}
                  </v-list-item-title>
                  <input style="height:0px;" ref="copyLink" :value="fichaData.link" />
                </v-list-item-content>
              </v-list-item>
              <!--
              <v-list-item link >
                <v-list-item-content>
                  <v-list-item-title>
                    Favorito
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item link >
                <v-list-item-content>
                  <v-list-item-title>
                    Estadisticas
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              -->
              <v-list-item link @click="confirmDelete = true">
                <v-list-item-content>
                  <v-list-item-title>
                    Eliminar ficha
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-sheet>
        </v-bottom-sheet>
        <v-bottom-sheet v-model="showComments" light>
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
          <div class="d-flex align-center justify-center mb-4">
            <v-spacer></v-spacer>
            <b>{{comments.length}} <span v-if="comments.length === 1">comentario</span><span v-else>comentarios</span></b>
            <v-spacer></v-spacer>
            <v-btn
              icon
              @click="showComments = false"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </div>
          <div style="max-height: 17rem; overflow: auto;">
          <v-list three-line dense style="background: transparent;">
            <v-list-item class="pa-0" v-for="comment in comments" :key="comment.id">
              <v-list-item-avatar>
                <img
                  :src="comment.image"
                  :alt="comment.name + ' ' + comment.lastname"
                >
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title>{{ comment.name }} {{ comment.lastname }}</v-list-item-title>
                <v-list-item-subtitle>
                  <div>
                    {{ comment.comment }}
                  </div>
                </v-list-item-subtitle>
                <div class="text-right" style="font-size: x-small;">
                    {{ comment.created }}
                </div>
              </v-list-item-content>
              <!--
              <v-list-item-action class="align-center">
                <v-btn icon>
                  <v-icon>mdi-heart</v-icon>
                </v-btn>
                <v-list-item-action-text>1</v-list-item-action-text>
              </v-list-item-action>
              -->
            </v-list-item>
            <!--
            <v-list-item>
              <v-list-item-avatar>
                <img
                  src="https://cdn.vuetifyjs.com/images/lists/4.jpg"
                  alt="Natalia"
                >
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title>Natalia187_</v-list-item-title>
                <v-list-item-subtitle>
                  <div>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent semper elit in rhoncus pretium. Suspendisse eget purus suscipit, venenatis elit sed, congue nisl.
                  </div>
                </v-list-item-subtitle>
                <div class="text-right" style="font-size: x-small;">
                    12/04 17:08
                </div>
              </v-list-item-content>
              <v-list-item-action class="align-center">
                <v-btn icon>
                  <v-icon>mdi-heart</v-icon>
                </v-btn>
                <v-list-item-action-text>28</v-list-item-action-text>
              </v-list-item-action>
            </v-list-item>
            <v-list-group class="comments">
              <template v-slot:activator>
                <v-list-item-content class="ml-12">
                  <v-list-item-title>12 respuestas</v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-item class="ml-12">
                <v-list-item-avatar size="30">
                  <img
                    src="https://cdn.vuetifyjs.com/images/john.jpg"
                    alt="John"
                  >
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>Jhon</v-list-item-title>
                  <v-list-item-subtitle>
                    <div>
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent semper elit in rhoncus pretium. Suspendisse eget purus suscipit, venenatis elit sed, congue nisl.
                    </div>
                  </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                  <v-btn icon>
                    <v-icon>mdi-heart</v-icon>
                  </v-btn>
                  <v-list-item-action-text>28</v-list-item-action-text>
                </v-list-item-action>
              </v-list-item>
            </v-list-group>
            -->
          </v-list>
          </div>
          </v-sheet>
          <v-sheet class="d-flex align-center px-4 py-2" v-if="ispublic !== true">
            <v-avatar
                size="36px"
                class="mr-2"
              >
                <img
                  alt="Avatar"
                  :src="$store.state.authUser.image"
                >
            </v-avatar>
            <v-textarea
              rows="1"
              auto-grow
              placeholder="Añadir un comentario..."
              v-model="commentText"
              append-outer-icon="mdi-send"
              @click:append-outer="saveComment()"
            ></v-textarea>
          </v-sheet>
          <v-sheet class="d-flex align-center px-4 py-2 text-caption" v-else>
            Debe estar registrado para dejar un comentario
          </v-sheet>
        </v-bottom-sheet>
        <v-dialog
          v-model="confirmDelete"
          max-width="250"
          class="rounded-xl"
        >
          <v-card class="rounded-xl text-center pt-4 grey darken-1" style="border: 1px solid white;">
            <v-card-text>
              ¿Estás seguro que deseas eliminar la ficha "{{ fichaDataLocal.title }}"?
            </v-card-text>
            <v-card-actions class="mt-n4 mb-2">
              <v-btn
                plain
                style="font-size: x-small"
                @click="confirmDelete = false"
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
        <v-snackbar
        v-model="copyLinkNotify"
        color="green"
        class="white--text"
        >
        Link copiado
        <template v-slot:action="{ attrs }">
            <v-btn
            color="white"
            text
            v-bind="attrs"
            @click="copyLinkNotify = false"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
        </template>
        </v-snackbar>
    </div>
</template>
<script>
import html2canvas from 'html2canvas'
import axios from 'axios'

export default {
  props: ['fichaData', 'preview', 'ghost', 'board', 'allowEdit', 'delay'],
  data: () => ({
    showOptions: false,
    showComments: false,
    comments: [],
    commentText: null,
    confirmDelete: false,
    moveToSub: false,
    calculateHeight: 0,
    embedInfo: '',
    copyLinkNotify: false
  }),
  computed: {
    fichaDataLocal () {
      if (this.fichaData) {
        const fichaDataLocal = this.fichaData
        fichaDataLocal.mode = 'regular'
        fichaDataLocal.layout = {
          icons: 'text-shadow: 0 0 2px black; padding-right: 3rem;',
          actions: 'position: absolute; right: 10px; top: 0px; height: 195px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
          bgcolor: 'rgba(0, 0, 0, 0.8)',
          size: 195
        }
        if (fichaDataLocal.title && fichaDataLocal.title.length > 55) {
          fichaDataLocal.title = fichaDataLocal.title.substring(0, 55) + '...'
        }
        if (fichaDataLocal.description && fichaDataLocal.description.length > 150) {
          fichaDataLocal.description = fichaDataLocal.description.substring(0, 150) + '...'
        }
        const fichaDomainParts = fichaDataLocal.link.toLowerCase().split('/')
        if (fichaDomainParts[0] === 'http:' || fichaDomainParts[0] === 'https:') {
          fichaDomainParts.splice(0, 2)
        }
        fichaDataLocal.domain = fichaDomainParts[0]
        console.log(fichaDomainParts[0])
        const img = new Image()
        img.src = this.$store.state.info.location + 'repository/captures/' + fichaDataLocal.permalink + '.png'
        if (img.height !== 0) {
          fichaDataLocal.image = this.$store.state.info.location + 'repository/captures/' + fichaDataLocal.permalink + '.png'
        }

        if (fichaDataLocal.domain === 'www.facebook.com') {
          fichaDataLocal.mode = 'embebed'
          /*
          if (fichaDomainParts[1] !== 'plugins') {
            fichaDataLocal.link = 'https://www.facebook.com/plugins/post.php?href=' + encodeURI(fichaDataLocal.link)
          }
          */
          let localHeight = 195
          if (this.calculateHeight !== 0) {
            localHeight = this.calculateHeight
          }

          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 0px; height: ' + localHeight + 'px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: localHeight
          }
          if (!this.preview && !this.ghost) {
            this.calcSize()
          }
        } else if (fichaDataLocal.domain === 'www.instagram.com') {
          fichaDataLocal.mode = 'embebed'
          let localHeight = 195
          if (this.calculateHeight !== 0) {
            localHeight = this.calculateHeight
          }

          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 0px; height: 420px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: localHeight
          }

          if (!this.preview && !this.ghost) {
            this.calcSize()
          }
        } else if (fichaDataLocal.domain === 'twitter.com') {
          fichaDataLocal.mode = 'embebed'
          let localHeight = 195
          if (this.calculateHeight !== 0) {
            localHeight = this.calculateHeight
          }
          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 0px; height: ' + localHeight + 'px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: localHeight
          }
          if (!this.preview && !this.ghost) {
            this.calcSize()
          }
        } else if (fichaDataLocal.domain === 'www.linkedin.com') {
          if (fichaDomainParts[1] === 'in') {
            let localHeight = 195
            if (this.calculateHeight !== 0) {
              localHeight = this.calculateHeight
            }
            fichaDataLocal.mode = 'embebed'
            fichaDataLocal.linkedIn = fichaDomainParts[2]
            fichaDataLocal.layout = {
              icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
              actions: 'position: absolute; right: 10px; top: 10px; height: 220px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
              bgcolor: 'rgba(0, 0, 0, 0.8)',
              size: localHeight
            }
            if (!this.preview && !this.ghost) {
              this.calcSize()
            }
          } else if (fichaDomainParts[1] === 'embed') {
            fichaDataLocal.mode = 'embebed'
            /*
            const fichaLinkParts = fichaDataLocal.link.split('/')
            if (fichaLinkParts[0] === 'http:' || fichaLinkParts[0] === 'https:') {
              fichaLinkParts.splice(0, 2)
            }
            const paramsParts = fichaLinkParts[2].split('-')
            fichaDataLocal.link = 'https://www.linkedin.com/embed/feed/update/urn:li:share:' + paramsParts[paramsParts.length - 2]
            fichaDataLocal.layout = {
              icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
              actions: 'position: absolute; right: 10px; top: 10px; height: 220px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
              bgcolor: 'rgb(9 89 169 / 75%)',
              size: 250
            }
            */
          }
        } else if (fichaDataLocal.domain === 'www.youtube.com') {
          fichaDataLocal.mode = 'embebed'
          if (fichaDomainParts[1] !== 'embed') {
            const queryParamsParts = fichaDataLocal.link.split('/')[3].split('&')
            fichaDataLocal.link = 'https://www.youtube.com/embed/' + queryParamsParts[0].substring(8)
          }
          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 25px; height: 145px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: 220
          }
        } else if (fichaDataLocal.domain === 'youtu.be') {
          fichaDataLocal.mode = 'embebed'
          if (fichaDomainParts[1] !== 'embed') {
            const queryParamsParts = fichaDataLocal.link.split('/')
            fichaDataLocal.link = 'https://www.youtube.com/embed/' + queryParamsParts[3]
          }
          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 25px; height: 145px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: 220
          }
        } else if (fichaDataLocal.domain === 'www.tiktok.com') {
          fichaDataLocal.mode = 'embebed'
          let localHeight = 561
          if (this.calculateHeight !== 0) {
            localHeight = this.calculateHeight
          }
          fichaDataLocal.layout = {
            icons: 'text-shadow: 0 0 2px black; position: absolute; bottom: -10px; left: 0px;',
            actions: 'position: absolute; right: 10px; top: 25px; height: 145px; font-size: small; text-align: right; text-shadow: 0 0 2px black;',
            bgcolor: 'rgba(0, 0, 0, 0.8)',
            size: localHeight
          }
          this.getOembed('tiktok', fichaDataLocal.link.split('?')[0])
        }
        return fichaDataLocal
      } else {
        return null
      }
    },
    ispublic () {
      if (this.$store.state.authUser) {
        return false
      } else {
        return true
      }
    }
  },
  methods: {
    setGhost () {
      this.$store.dispatch('ghostCard', this.fichaData)
      this.$router.push('/')
    },
    moveSubBoard () {
      this.moveToSub = true
      this.$emit('movetosub', this.fichaData)
      this.showOptions = false
    },
    openLink () {
      if (!this.preview && !this.ghost && !this.moveToSub && !this.ispublic) {
        axios
          .post(this.$store.state.info.location + 'boards/views/save',
            JSON.stringify({ user: this.$store.state.authUser.id, card: this.fichaDataLocal.id }),
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
            window.open(this.fichaData.link, '_blank')
            this.$emit('update')
          })
      } else if (this.ispublic) {
        window.open(this.fichaData.link, '_blank')
      } else if (this.moveToSub) {
        this.moveToSub = false
        this.$emit('cancelmovetosub')
      }
    },
    calcSize () {
      setTimeout(function () {
        console.log('Calculate ' + 'capture_' + this._uid)
        if (this.fichaDataLocal.domain === 'twitter.com') {
          if (
            document.getElementById('capture_' + this._uid).getElementsByClassName('twitter-tweet-rendered').length === 0 ||
            document.getElementById('capture_' + this._uid).getElementsByClassName('twitter-tweet-rendered')[0].getElementsByTagName('iframe').length === 0 ||
            document.getElementById('capture_' + this._uid).getElementsByClassName('twitter-tweet')[0].getElementsByTagName('iframe')[0].getBoundingClientRect().height === 0
          ) {
            this.calcSize()
          } else {
            console.log(document.getElementById('capture_' + this._uid).getElementsByClassName('twitter-tweet')[0].getElementsByTagName('iframe')[0].getBoundingClientRect().height)
            this.calculateHeight = document.getElementById('capture_' + this._uid).getElementsByClassName('twitter-tweet')[0].getElementsByTagName('iframe')[0].getBoundingClientRect().height + 30
          }
        }
        if (this.fichaDataLocal.domain === 'www.facebook.com') {
          console.log(this.$refs.embedFrame.contentDocument.body.scrollHeight)
          if (this.$refs.embedFrame.contentDocument.body.scrollHeight === 0) {
            this.calcSize()
          } else {
            this.calculateHeight = this.$refs.embedFrame.contentDocument.body.scrollHeight + 30
          }
        }
        if (this.fichaDataLocal.domain === 'www.linkedin.com') {
          if (this.$refs.embedFrame.contentDocument.body.scrollHeight === 0) {
            this.calcSize()
          } else {
            this.calculateHeight = this.$refs.embedFrame.contentDocument.body.scrollHeight + 30
          }
          /*
          if (
            document.getElementById('capture_' + this._uid).getElementsByTagName('iframe').length === 0 ||
            document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height === 0
          ) {
            this.calcSize()
          } else {
            console.log(document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height)
            this.calculateHeight = document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height + 30
          }
          */
        }
        if (this.fichaDataLocal.domain === 'www.instagram.com') {
          if (
            document.getElementById('capture_' + this._uid).getElementsByTagName('iframe').length === 0 ||
            document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height === 0
          ) {
            this.calcSize()
          } else {
            console.log(document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height)
            this.calculateHeight = document.getElementById('capture_' + this._uid).getElementsByTagName('iframe')[0].getBoundingClientRect().height - 10
          }
        }
        if (this.fichaDataLocal.domain === 'www.tiktok.com') {
          if (this.$refs.embedFrame.contentDocument.body.scrollHeight < 400) {
            this.calcSize()
          } else {
            this.calculateHeight = this.$refs.embedFrame.contentDocument.body.scrollHeight + 30
          }
        }
      }.bind(this), 1000)
    },
    getCapture () {
      setTimeout(function () {
        // document.getElementById('capture_' + this._uid).innerHTML = ''
        const element = 'capture_' + this._uid
        html2canvas(document.querySelector('#' + element)).then((canvas) => {
          document.getElementById(element).innerHTML = ''
          document.getElementById(element).append(canvas)
          console.log('captura')
        })
      }.bind(this), 1000)
    },
    loadComments () {
      if (this.ispublic) {
        axios
          .post(this.$store.state.info.location + 'public/comments',
            JSON.stringify({
              card: this.fichaDataLocal.id,
              order: [['created', 'DESC']],
              joins: [
                {
                  field: ['name', 'lastname', 'image'],
                  table: 'users',
                  on: ['id', 'user']
                }
              ]
            }),
            {
              validateStatus (status) {
                return status < 500 // Resolve only if the status code is less than 500
              },
              headers: {
                'Content-Type': 'application/json'
              }
            })
          .then((evtResponse) => {
            this.comments = evtResponse.data.records
            this.showComments = true
            this.fichaDataLocal.comments = this.comments.length
          })
      } else {
        axios
          .post(this.$store.state.info.location + 'boards/comments/find',
            JSON.stringify({
              card: this.fichaDataLocal.id,
              order: [['created', 'DESC']],
              joins: [
                {
                  field: ['name', 'lastname', 'image'],
                  table: 'users',
                  on: ['id', 'user']
                }
              ]
            }),
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
            this.comments = evtResponse.data.records
            this.showComments = true
            this.fichaDataLocal.comments = this.comments.length
          })
      }
    },
    saveComment () {
      if (this.commentText === null || this.commentText === '') {
        return false
      }
      axios
        .post(this.$store.state.info.location + 'boards/comments/save',
          JSON.stringify({
            card: this.fichaDataLocal.id,
            user: this.$store.state.authUser.id,
            comment: this.commentText
          }),
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
          this.commentText = null
          this.loadComments()
        })
    },
    setFab () {
      axios
        .post(this.$store.state.info.location + 'boards/cards/setFab',
          JSON.stringify({
            card: this.fichaDataLocal.id,
            user: this.$store.state.authUser.id,
            state: !this.fichaDataLocal.isFab
          }),
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
          this.fichaDataLocal.isFab = !this.fichaDataLocal.isFab
        })
    },
    remove () {
      if (this.$route.params.id !== 'main') {
        axios
          .post(this.$store.state.info.location + 'boards/cards/save',
            JSON.stringify(
              {
                id: this.fichaDataLocal.id,
                board: 'NULL'
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
            this.$emit('update')
          })
      } else {
        axios
          .post(this.$store.state.info.location + 'boards/cards/delete',
            JSON.stringify({
              id: this.fichaDataLocal.id
            }),
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
            this.$emit('update')
          })
      }
    },
    async getOembed (server, url) {
      if (server === 'tiktok') {
        if (this.embedInfo === '') {
          await axios
            .get('https://www.tiktok.com/oembed?url=' + url)
            .then((evtResponse) => {
              console.log(evtResponse)
              this.embedInfo = '<body style="margin:-1.5rem -2px 0px; padding: 0px;"><img src="' + evtResponse.data.thumbnail_url + '" style="max-width: 100%" /></body>'
              if (!this.preview && !this.ghost) {
                this.calculateHeight = 571
              }
              if (this.delay && !isNaN(this.delay)) {
                const parentFunction = this
                if (this.board !== true) {
                  setTimeout(() => {
                    console.log(evtResponse)
                    if (parentFunction.board && parentFunction.board === true) {
                      parentFunction.embedInfo = evtResponse.data.html + '<style>.tiktok-embed{ margin: -175% -75% !important; transform: scale(.5);}</style>'
                    } else {
                      parentFunction.embedInfo = evtResponse.data.html + '<style>.tiktok-embed{ margin: -11px !important; }</style>'
                    }
                  }, 2000 * this.delay)
                }
              }
            })
        }
      }
    },
    copyLink () {
      const copyText = this.$refs.copyLink
      copyText.select()
      copyText.setSelectionRange(0, 99999)
      document.execCommand('copy')
      this.copyLinkNotify = true
    }
  }
}
</script>
