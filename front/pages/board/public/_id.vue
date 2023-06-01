<template>
    <div style="height: 100%; max-height: calc(100vh - 6.75rem)">
      <Flotante
        :title="boardData.name"
        :destination="$store.state.authUser? '/board/' + boardData.id: '/board/public/detail/' + boardData.permalink"
        :ispublic="false"
        :cards="fichas"
        :id="'board-' + boardData.id"
        :link="boardData.permalink"
      />
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'shareview',
  data: () => ({
    boardData: {},
    fichas: [],
    fichaLoading: true
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
    this.$store.dispatch('addOpenBoard', this.boardData.id)
    this.update(null)
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
              owner: this.boardData.owner,
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
    }
  }
}
</script>
