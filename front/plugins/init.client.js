import axios from 'axios'
import Vue from 'vue'
import VueQrcodeReader from 'vue-qrcode-reader'

Vue.use(VueQrcodeReader)

export default async function ({ store, redirect, route }) {
  function validURL (str) {
    const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i') // fragment locator
    return !!pattern.test(str)
  }
  window.lastURL = ''
  window.addEventListener('focus', function () {
    console.log('Volvió')
    navigator.clipboard.readText().then((clipText) => {
      if (validURL(clipText) && clipText !== window.lastURL) {
        /*
        if (document.getElementById('linkalert')) {
          document.getElementById('linkalert').remove()
        }
        document.getElementById('app').insertAdjacentHTML('beforeend',
          '<div id="linkalert" class="v-snack v-snack--absolute v-snack--active v-snack--top v-snack--has-background">' +
          '<div class="v-snack__wrapper v-sheet theme--dark elevation-24 green">' +
          '<div role="status" aria-live="polite" class="v-snack__content">' +
          'Desea agregar el link copiado <br/>' + clipText +
          '</div > <div class="v-snack__action ">' + '<span onclick="document.getElementById(\'linkalert\').remove()"> GUARDAR </span>' + '</div></div ></div > '
        )
        */
        window.lastURL = clipText
        // window.$nuxt.$emit('append')
        // return redirect('/', { append: true })
      }
      console.log('Papelera:', clipText)
    })
  })

  let info = {}
  await axios.get('/conf/main.json').then((response) => {
    info = response.data
  })

  console.log(info)
  store.commit('SET_INFO', info.api)

  if (localStorage.openBoards && localStorage.openBoards !== '') {
    store.commit('SET_OPEN_BOARDS', JSON.parse(localStorage.openBoards))
  }

  if (localStorage.extensionID && localStorage.extensionID !== '') {
    store.commit('SET_EXT_ID', localStorage.extensionID)
  }

  if (localStorage.token && localStorage.token !== '') {
    store.commit('SET_TOKEN', localStorage.token)

    try {
      let data = {}
      await store.dispatch('validate_token').then((result) => {
        data = result
      })
      if (data.result === 1) {
        // store.commit('SET_TOKEN', localStorage.token)
        await store.dispatch('getUserInfo', data.data.id)
        console.log('Ruta', route)
        if (localStorage.lastpath) {
          return redirect(localStorage.lastpath, { login: true })
        } else if (route.path !== '/login') {
          return redirect(route.path, { login: true })
        } else {
          return redirect('/', { login: true })
        }
      }
    } catch (error) {
      console.log(error)
      localStorage.token = ''
      return redirect('/login')
    }
  } else {
    return redirect('/login')
  }
}
