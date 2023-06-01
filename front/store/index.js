import axios from 'axios'

export const state = () => ({
  ghost: false,
  ghostData: {},
  authUser: null,
  token: null,
  info: {},
  openBoards: [],
  scrollPos: {},
  extensionID: null
})
export const mutations = {
  setGhost (state, value) {
    state.ghost = value
  },
  SET_USER (state, user) {
    state.authUser = user
    if (state.extensionID !== null && window.chrome && window.chrome.runtime) {
      window.chrome.runtime.sendMessage(state.extensionID, { user })
    }
  },
  SET_TOKEN (state, token) {
    state.token = token
  },
  SET_INFO (state, info) {
    state.info = info
  },
  SET_OPEN_BOARDS (state, boards) {
    state.openBoards = boards
    localStorage.openBoards = JSON.stringify(state.openBoards)
  },
  SET_SCROLLPOS (state, payload) {
    state.scrollPos[payload.board] = payload.position
  },
  SET_EXT_ID (state, payload) {
    state.extensionID = payload
    localStorage.extensionID = payload
  }
}

export const actions = {
  async login (context, { username, password }) {
    try {
      const { data } = await axios.post(
        context.state.info.location + 'gettoken',
        JSON.stringify({
          user: username,
          password
        })
      )
      context.commit('SET_TOKEN', data.data.jwt)
      localStorage.token = data.data.jwt
      await context.dispatch('getUserInfo', data.data.id)
    } catch (error) {
      if (error.response && error.response.status === 401) {
        throw new Error('Credenciales ingresadas incorrectas')
      }
      throw error
    }
  },

  logout ({ commit }) {
    commit('SET_USER', null)
    localStorage.token = ''
  },

  async getUserInfo (context, usrid) {
    try {
      const { data } = await axios.post(
        context.state.info.location + 'users/users/getuserinfo',
        JSON.stringify({
          id: usrid
        }),
        {
          headers: {
            Authorization: 'Bearer ' + context.state.token,
            'Content-Type': 'application/json'
          }
        }
      )
      context.commit('SET_USER', data.data)
    } catch (error) {
      if (error.response && error.response.status === 401) {
        throw new Error('Bad credentials')
      }
      throw error
    }
  },

  reset_token ({ commit }) {
    commit('SET_USER', null)
    localStorage.token = ''
  },

  async validate_token (context) {
    try {
      const { data } = await axios.post(
        context.state.info.location + 'validatetoken',
        null,
        {
          headers: {
            Authorization: 'Bearer ' + context.state.token,
            'Content-Type': 'application/json'
          }
        }
      )
      context.commit('SET_TOKEN', data.data.newtoken)
      localStorage.token = data.data.newtoken
      return data
    } catch (error) {
      if (error.response && error.response.status === 401) {
        throw new Error('Invalid token')
      }
      throw error
    }
  },

  async sso (context, { provider, loginresult }) {
    let providerInfo = {}
    if (provider === 'google') {
      providerInfo = {
        server: 'firebase',
        provider: 'google'
      }
    }
    try {
      const { data } = await axios.post(
        context.state.info.location + 'sso',
        JSON.stringify({
          entity: providerInfo,
          extid: loginresult.user.uid,
          email: loginresult.user.email,
          token: loginresult.credential.accessToken,
          image: loginresult.user.photoURL
        })
      )
      context.commit('SET_TOKEN', data.data.jwt)
      localStorage.token = data.data.jwt
      await context.dispatch('getUserInfo', data.data.id)
    } catch (error) {
      if (error.response && error.response.status === 401) {
        throw new Error(error.response.data.message)
      }
      throw error
    }
  },

  addOpenBoard ({ state, commit }, payload) {
    const openBoards = state.openBoards.slice(0)
    if (!openBoards.includes(payload)) {
      openBoards.push(payload)
      commit('SET_OPEN_BOARDS', openBoards)
    }
  },

  removeOpenBoard ({ state, commit }, payload) {
    const openBoards = state.openBoards.slice(0)
    const position = openBoards.indexOf(payload)
    if (position !== -1) {
      openBoards.splice(position, 1)
      commit('SET_OPEN_BOARDS', openBoards)
    }
  },

  ghostCard ({ state, commit }, payload) {
    state.ghostData = payload
    commit('setGhost', true)
  }
}
