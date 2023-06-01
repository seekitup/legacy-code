<template>
   <div style="width: 100%; height: 100%;">
   <v-card
        elevation="10"
        class="rounded-xl"
        style="width: 100%; height: 100%; color: white; background: #C7C9C933; border: .5px solid white"
    >
      <div v-if="newMenu === 1" class="d-flex flex-column" style="height: 100%; font-size: xx-large; text-transform: uppercase; line-height: 2.15rem; ">
        <div class="d-flex align-center justify-center flex-grow-1 text-center" style="border-bottom:.5px solid rgba(255,255,255,.75);" @click="newMenu = 2">
            <div>Nueva<br/>pizarra<br/><v-icon class="white--text" style="font-size: xxx-large; margin: 1rem;">mdi-plus-box-multiple</v-icon></div>
        </div>
        <div class="d-flex align-center justify-center flex-grow-1 text-center" @click="newMenu = 3">
            <div>Nueva<br/>ficha<br/><v-icon class="white--text" style="font-size: xxx-large; margin: 1rem;">mdi-plus-box-multiple</v-icon></div>
        </div>
      </div>
      <div v-if="newMenu === 2" class="d-flex flex-column pa-4" style="height: 100%;">
        <div class="d-flex align-center justify-center" style="font-family: 'Chivo', sans-serif; width: 100%;">
            <img src="logo_abeja.svg" class="ml-auto" style="height: 2rem; filter: invert(1); margin-right: .5rem;" /> Seekitup
            <v-icon class="white--text ml-auto" style="font-size: xx-large;" @click="resetData(); newMenu = 1">mdi-close</v-icon>
        </div>
        <div class="flex-grow-1 d-flex align-center justify-center flex-column">
            <div class="text-right" style="width: 100%;">
            <v-text-field label="Nombre de la pizarra" v-model="appendBoardName"></v-text-field>
            <v-text-field label="Foto (URL Imagen)" v-model="appendBoardImage" persistent-hint hint="Copiar dirección de imagen"></v-text-field>
            <v-btn class="mt-4" rounded light @click="createBoard()"><v-icon class="mr-1">mdi-check</v-icon>Listo</v-btn>
            </div>
        </div>
      </div>
      <div v-if="newMenu === 3" class="d-flex flex-column pa-4" style="height: 100%;">
        <div class="d-flex align-center justify-center" style="font-family: 'Chivo', sans-serif; width: 100%;">
            <img src="logo_abeja.svg" class="ml-auto" style="height: 2rem; filter: invert(1); margin-right: .5rem;" /> Seekitup
            <v-icon class="white--text ml-auto" v-if="close !== true" style="font-size: xx-large;" @click="resetData(); newMenu = 1">mdi-close</v-icon>
            <v-icon class="white--text ml-auto" v-else style="font-size: xx-large;" @click="$emit('close')">mdi-close</v-icon>
        </div>
        <div class="text-center mt-4">
            <v-expand-transition>
                <div v-show="camera === 'auto'" class="rounded-lg" style="background: black; width: 150px; height: 150px; display: inline-block;">
                    <qrcode-stream
                      class="rounded-lg"
                      v-show="camera === 'auto'"
                      :camera="camera"
                      :key="_uid"
                      :track="onDecode"
                      @init="logErrors"
                    ></qrcode-stream>
                </div>
            </v-expand-transition>
            <v-btn v-if="camera === 'off'" style=" margin-top: 10px; height: 48px; background: white; border-radius: 2rem;" @click="switchCamera()">
            <!-- <div style="padding: .5rem; border: 2px solid black; border-radius: 2rem; color: black;">-->
                <v-icon style="font-size: xx-large; color: black;">mdi-qrcode</v-icon>
                <span class="ml-2" style="font-size: xx-small; color: black;">Escanear QR</span>
            <!-- </div> -->
            </v-btn>
        </div>
        <div class="text-center mt-2">
            Escriba, pegue o escanee la URL del sitio a agregar
            <v-text-field label="URL" v-model="urlAppend" :loading="loadingMeta"></v-text-field>
        </div>
        <div class="d-flex mt-4" style="width: 100%;">
            <v-btn rounded light :disabled="loadingMeta || urlAppend === ''" @click="getMetadata()"><v-icon class="mr-1">mdi-eye</v-icon>Probar</v-btn>
            <v-btn class="ml-auto" rounded light :disabled="loadingMeta || urlAppend === ''" @click="saveCard()"><v-icon class="mr-1">mdi-check</v-icon>Listo</v-btn>
        </div>
      </div>
      <div v-if="newMenu === 4" class="d-flex flex-column align-center justify-center" style="height: 100%; font-size: x-large; line-height: 2.15rem; ">
        <div>Un momento por favor</div>
        <v-progress-circular
            indeterminate
        ></v-progress-circular>
        </div>
        <div v-if="newMenu === 5" class="d-flex flex-column pa-4" style="height: 100%;">
        <div class="d-flex align-center justify-center" style="font-family: 'Chivo', sans-serif; width: 100%;">
            <img src="logo_abeja.svg" class="ml-auto" style="height: 2rem; filter: invert(1); margin-right: .5rem;" /> Seekitup
            <v-icon class="white--text ml-auto" style="font-size: xx-large;" @click="resetData(); newMenu = 1">mdi-close</v-icon>
        </div>
        <div style="height: 40%; overflow: hidden;">
            <Ficha :fichaData="democard" :preview="true" v-if="copyAppend" style="transform: scale(.75); margin-top: -15%;" />
        </div>
        <div class="text-center mt-2">
            ¿Desea guardar la URL copiada?
            <v-text-field label="URL" v-model="urlAppend" :loading="loadingMeta"></v-text-field>
        </div>
        <div class="d-flex mt-4" style="width: 100%;">
            <v-btn class="ml-auto" rounded light :disabled="loadingMeta || urlAppend === ''" @click="saveCard()"><v-icon class="mr-1">mdi-check</v-icon>Listo</v-btn>
        </div>
      </div>
    </v-card>
    <v-dialog
      v-model="dialog"
      max-width="360"
    >
      <Ficha :fichaData="democard" :preview="true" v-if="dialog" />
    </v-dialog>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  props: ['close', 'subboard'],
  data: () => ({
    newMenu: 1,
    loadingMeta: false,
    urlAppend: null,
    democard: {},
    dialog: false,
    copyAppend: false,
    appendBoardName: null,
    appendBoardImage: null,
    camera: 'off'
  }),
  mounted () {
    if (this.close === true) {
      this.newMenu = 3
    }
  },
  methods: {
    async getMetadata () {
      if (this.urlAppend !== null && this.urlAppend) {
        this.purgeURL()
        this.loadingMeta = true
        this.democard = {}
        await axios
          .post(this.$store.state.info.location + 'boards/metadata/getURLInfo',
            JSON.stringify({ url: this.urlAppend }),
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
            this.loadingMeta = false
            this.democard = evtResponse.data.data
            if (Array.isArray(this.democard)) {
              this.democard = {}
            }
            this.democard.link = this.urlAppend
            this.dialog = true
          })
      }
    },
    async saveCard () {
      this.newMenu = 4
      this.purgeURL()
      const params = {
        user: this.$store.state.authUser.id,
        url: this.urlAppend
      }
      if (this.$route.params && this.$route.params.id !== 'main' && this.$route.params.id !== null) {
        params.board = this.$route.params.id
        if (this.subboard !== undefined && this.subboard !== null) {
          params.board = this.subboard
        }
      }
      await axios
        .post(this.$store.state.info.location + 'boards/cards/save',
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
          if (this.close === true) {
            this.newMenu = 3
          } else {
            this.newMenu = 1
          }
          this.loadingMeta = false
          this.urlAppend = null
          this.democard = {}
          this.copyAppend = false
          this.appendBoardName = null
          this.appendBoardImage = null
          this.camera = 'off'

          this.$emit('save')
        })
    },
    purgeURL () {
      this.urlAppend = this.urlAppend.trim()
      if (this.urlAppend.toLowerCase().search('iframe') !== -1) {
        const start = this.urlAppend.toLowerCase().search('src=') + 5
        const end = this.urlAppend.toLowerCase().indexOf('"', start)
        this.urlAppend = this.urlAppend.substring(start, end)
      }
      return true
    },
    async getCopyMetadata () {
      if (this.urlAppend !== null && this.urlAppend) {
        this.purgeURL()
        this.loadingMeta = true
        this.democard = {}
        await axios
          .post(this.$store.state.info.location + 'boards/metadata/getURLInfo',
            JSON.stringify({ url: this.urlAppend }),
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
            this.loadingMeta = false
            this.democard = evtResponse.data.data
            if (Array.isArray(this.democard)) {
              this.democard = {}
            }
            this.democard.link = this.urlAppend
            this.copyAppend = true
          })
      }
    },
    async createBoard () {
      if (this.appendBoardName === '' || this.appendBoardName === null) {
        return false
      }
      this.newMenu = 4
      await axios
        .post(this.$store.state.info.location + 'boards/boards/save',
          JSON.stringify(
            {
              owner: this.$store.state.authUser.id,
              name: this.appendBoardName,
              image: this.appendBoardImage
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
          this.$router.push('/board/' + evtResponse.data.data)
        })
    },
    onDecode (detectedCodes, ctx) {
      for (const detectedCode of detectedCodes) {
        const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

        ctx.strokeStyle = 'red'

        ctx.beginPath()
        ctx.moveTo(firstPoint.x, firstPoint.y)
        for (const { x, y } of otherPoints) {
          ctx.lineTo(x, y)
        }
        ctx.lineTo(firstPoint.x, firstPoint.y)
        ctx.closePath()
        ctx.stroke()
        this.urlAppend = detectedCode.rawValue
      }
    },
    logErrors (promise) {
      promise.catch(console.error)
    },
    switchCamera () {
      if (this.camera === 'off') {
        this.camera = 'auto'
      } else {
        this.camera = 'off'
      }
    },
    resetData () {
      this.urlAppend = null
      this.appendBoardName = null
      this.appendBoardImage = null
      this.camera = 'off'
    }
  }
}
</script>
