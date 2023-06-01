<template>
    <div>
        <v-app-bar light>
          <v-toolbar-title style="text-transform: uppercase;">
            <v-btn icon :to="'/board/' + $route.params.id">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            Colaboradores
          </v-toolbar-title>
        </v-app-bar>
        <div class="pa-3 black" style="height: calc(100vh - 7rem); overflow: auto;">
          <v-list
            three-line
            flat
            dense
            style="background: transparent;"
            class="mb-0"
          >
            <v-list-item>
              <v-list-item-avatar class="white">
                <img
                  :src="boardData.user_image"
                  :alt="boardData.user_name + ' ' + boardData.user_lastname"
                >
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title><b>{{boardData.user_name + ' ' + boardData.user_lastname}}</b><br/>Propietario de la pizarra</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <template v-for="(member, index) in members">
              <v-divider
                :inset="true"
                :key="'d-' + member.user"
              />
              <v-list-item :key="'l-' + member.user">
                <v-list-item-avatar class="white">
                    <img
                    :src="member.user_image"
                    :alt="member.user_name + ' ' + member.user_lastname"
                    >
                </v-list-item-avatar>
                <v-list-item-content>
                    <v-list-item-title><b>{{member.user_name + ' ' + member.user_lastname}}</b><br/>{{ calcRol(member.rol) }}</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                    <v-btn icon @click="displayChangeRol(index)">
                      <v-icon>mdi-account-minus</v-icon>
                    </v-btn>
                </v-list-item-action>
              </v-list-item>
            </template>
            <v-list-item @click="addMemberName = null; findUser= null; addMemberSheet = true">
                <v-list-item-avatar class="white">
                    <v-icon style="color: black">mdi-plus</v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                    <v-list-item-title>Agregar colaborador</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="align-center">
                    <v-btn icon >
                      <v-icon>mdi-account-plus</v-icon>
                    </v-btn>
                </v-list-item-action>
              </v-list-item>
          </v-list>
        </div>
        <v-dialog
          v-model="displayRoles"
          max-width="250"
          class="rounded-xl"
        >
          <v-card class="rounded-xl text-center pt-4 grey darken-1" style="border: 1px solid white;">
            <v-card-text>
              ¿Estás seguro que querés quitar este colaborador?
            </v-card-text>
            <v-card-actions class="mt-n4 mb-2">
              <v-btn
                plain
                style="font-size: x-small"
                @click="displayRoles = false"
              >
                Cancelar
              </v-btn>

              <v-btn
                plain
                style="font-size: x-small"
                class="ml-auto"
                @click="updateUserRol(0)"
              >
                Sí, eliminar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <!--
        <v-bottom-sheet
          v-model="displayRoles"
          inset
          light
          class="mb-5"
        >
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
            <div class="text-right">
              <v-btn
                text
                color="success"
                @click="displayRoles = !displayRoles"
              >
                Guardar
              </v-btn>
            </div>
            <div class="mx-3">
              <h5 class="text-h5">Cambiar rol</h5>
              <v-radio-group v-model="rolSel" @change="updateUserRol($event)">
                 <v-radio
                   label="Segudior"
                   :value="0"
                 />
                 <v-radio
                   label="Colaborador"
                   :value="1"
                 />
              </v-radio-group>
            </div>
          </v-sheet>
        </v-bottom-sheet>
        -->
        <v-bottom-sheet
          v-model="addMemberSheet"
          inset
          light
          class="mb-5"
        >
          <v-sheet
            class="rounded-t-lg pt-3 px-4"
            style="background-color: #e5e5e5;"
          >
            <!--
            <div class="text-right">
              <v-btn
                text
                color="error"
                @click="addMemberSheet = !addMemberSheet"
              >
                Cancelar
              </v-btn>
            </div>
            -->
            <div class="mx-3">
              <v-text-field
                label="Mail del usuario"
                clearable
                append-outer-icon="mdi-magnify"
                @click:append-outer="searchUser"
                v-model="addMemberName"
                persistent-hint
                hint="Escriba la dirección del usuario y presione la lupa"
              ></v-text-field>
            </div>
            <div v-if="findUser !== null && findUser.id === null" class="mx-3 my-2 red--text">
              {{ findUser.message }}
            </div>
            <div v-if="addMemberName !== null && findUser !== null && findUser.id !== null && findUser.usr " class="mx-3 my-2 text-right">
              <v-btn text color="green darken-2" @click="addUser()">Asingnar</v-btn>
            </div>
          </v-sheet>
        </v-bottom-sheet>
    </div>
</template>
<script>
import axios from 'axios'

export default {
  layout: 'gestion',
  data: () => ({
    displayRoles: false,
    addMemberSheet: false,
    addMemberName: null,
    userSel: null,
    rolSel: null,
    members: [],
    findUser: null
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
  mounted () {
    this.updateMembers()
  },
  methods: {
    async updateMembers () {
      const board = this.$route.params.id
      await axios
        .post(this.$store.state.info.location + 'boards/boardmembers/find',
          JSON.stringify(
            {
              order: [['rol', 'DESC']],
              board,
              rol: 1,
              joins: [
                {
                  field: ['name', 'lastname', 'image'],
                  table: 'users',
                  alias: ['user_name', 'user_lastname', 'user_image'],
                  on: ['id', 'user']
                }
              ]
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
          this.members = evtResponse.data.records
        })
    },
    calcRol (rol) {
      let result = ''
      switch (rol) {
        case '0':
          result = 'Seguidor'
          break
        case '1':
          result = 'Colaborador'
          break
      }
      return result
    },
    displayChangeRol (userIndex) {
      this.userSel = this.members[userIndex]
      this.rolSel = parseInt(this.userSel.rol)
      this.displayRoles = true
    },
    async updateUserRol (data) {
      console.log(data)
      if (data !== parseInt(this.userSel.rol)) {
        const board = this.$route.params.id
        await axios
          .post(this.$store.state.info.location + 'boards/boards/setMemberLevel',
            JSON.stringify(
              {
                board,
                user: this.userSel.user,
                rol: data
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
          .then(() => {
            this.updateMembers()
            this.displayRoles = false
          })
      }
    },
    async searchUser () {
      await axios
        .post(this.$store.state.info.location + 'users/users/find',
          JSON.stringify(
            {
              usr: this.addMemberName
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
          if (evtResponse.data.records.length === 0) {
            this.findUser = {
              id: null,
              message: 'No se encuentra un usuario con ese correo'
            }
          } else if (evtResponse.data.records.length > 1) {
            this.findUser = {
              id: null,
              message: 'Se encontró más de un usuario con ese correo'
            }
          } else if (evtResponse.data.records[0].id === this.boardData.owner) {
            this.findUser = {
              id: null,
              message: 'El propietario de la pizarra ya tiene privilegios administrativos'
            }
          } else {
            let inMembers = false
            this.members.forEach((element) => {
              if (element.user === evtResponse.data.records[0].id) {
                inMembers = true
              }
            })
            if (inMembers) {
              this.findUser = {
                id: null,
                message: 'El usuario ya es miembro de la pizarra'
              }
            } else {
              this.findUser = evtResponse.data.records[0]
            }
          }
        })
    },
    addUser () {
      const board = this.$route.params.id
      axios
        .post(this.$store.state.info.location + 'boards/boards/addMember',
          JSON.stringify(
            {
              board,
              user: this.findUser.id,
              rol: 1
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
        .then(() => {
          this.updateMembers()
          this.addMemberSheet = false
        })
    }
  }
}
</script>
