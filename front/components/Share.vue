<template>
  <div>
    <v-bottom-sheet v-model="showShare" light>
      <v-sheet
        class="rounded-t-lg pt-3 px-4"
        style="background-color: #e5e5e5;"
      >
       <div class="d-flex align-center mb-4">
          <b>Compartir en</b>
          <v-spacer></v-spacer>
          <v-btn
            icon
            @click="hideSheet()"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
       </div>
       <div class="d-flex align-center justify-space-between shareBlock" style="font-size: x-small;">
           <div class="text-center">
            <a :href="'https://wa.me/?text=' + encodeURIComponent(calcLink)" target="_blank" data-action="share/whatsapp/share">
              <v-avatar color="green">
                  <v-icon style="color: white;">mdi-whatsapp</v-icon>
              </v-avatar><br/>
              WhatsApp
            </a>
           </div>
           <!--
           <div class="text-center">
            <v-avatar color="pink">
                <v-icon style="color: white;">mdi-instagram</v-icon>
            </v-avatar><br/>
            Instagram
           </div>
           -->
           <div class="text-center">
            <a :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(calcLink)" target="_blank">
              <v-avatar color="#0959a9">
                  <v-icon style="color: white;">mdi-linkedin</v-icon>
              </v-avatar><br/>
              LinkedIn
            </a>
           </div>
           <div class="text-center">
            <a :href="'http://www.facebook.com/sharer.php?u=' + encodeURIComponent(calcLink)" target="_blank">
              <v-avatar color="#1568d4">
                  <v-icon style="color: white;">mdi-facebook</v-icon>
              </v-avatar><br/>
              Facebook
            </a>
           </div>
           <div class="text-center">
            <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(calcLink)" target="_blank">
              <v-avatar color="#198acf">
                  <v-icon style="color: white;">mdi-twitter</v-icon>
              </v-avatar><br/>
              Twitter
            </a>
           </div>
           <div class="text-center">
            <a :href="'https://t.me/share/url?url=' + encodeURIComponent(calcLink)" target="_blank">
              <v-avatar color="blue">
                  <v-icon style="color: white;">mdi-telegram</v-icon>
              </v-avatar><br/>
              Telegram
            </a>
           </div>
           <!-- <div class="text-center">
            <v-avatar color="black">
                <v-icon style="color: white;">mdi-dots-horizontal</v-icon>
            </v-avatar><br/>
            Otros
           </div> -->
       </div>
        <div class="py-3">
          <b>Link para compartir</b>
          <v-text-field
            outlined
            readonly
            :hide-details="true"
            class="rounded-lg mt-2 white"
            :value="calcLink"
            id="linkToShare"
            @click="copy()"
          ></v-text-field>
        </div>
      </v-sheet>
    </v-bottom-sheet>
    <v-snackbar
    v-model="copyAlert"
    color="green"
    class="white--text"
    >
    Link copiado
    <template v-slot:action="{ attrs }">
        <v-btn
        color="white"
        text
        v-bind="attrs"
        @click="copyAlert = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
    </template>
    </v-snackbar>
  </div>
</template>
<script>
export default {
  props: ['value', 'link'],
  data: () => ({
    showShare: false,
    copyAlert: false
  }),
  watch: {
    value () {
      this.showShare = this.value
    }
  },
  computed: {
    calcLink () {
      return this.$store.state.info.location + 'share/' + this.link
    }
  },
  methods: {
    hideSheet () {
      this.$emit('input', false)
    },
    copy () {
      const copyText = document.getElementById('linkToShare')
      copyText.select()
      copyText.setSelectionRange(0, 99999)
      document.execCommand('copy')
      this.copyAlert = true
    }
  }
}
</script>
