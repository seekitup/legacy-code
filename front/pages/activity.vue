<template>
    <div
      class="pa-3 ma-0"
      style="
      height: calc(100vh - 56px);
      width:100%;
      overflow-y: auto;
      scroll-behavior: smooth;
      position: absolute;
      top: 0px;
      left: 0px;
      transition: 300ms;
      "
      id="scroll-target">
        <h5 class="text-h5">Tu actividad</h5>
        <div class="text-subtitle-1">Cantidad de pizarras generadas</div>
        <v-divider class="mb-3"></v-divider>
        <div class="d-flex justify-space-around">
            <v-chip
                color="green"
                text-color="white"
            >
            <v-avatar
                left
                class="green darken-4"
            >
                1
            </v-avatar>
            Privada
            </v-chip>
            <v-chip
                color="indigo"
                text-color="white"
            >
            <v-avatar
                left
                class="indigo darken-4"
            >
                5
            </v-avatar>
            Compartidas
            </v-chip>
        </div>
        <div class="text-subtitle-1">Cantidad de fichas generadas</div>
        <v-divider class="mb-3"></v-divider>
          <div class="d-flex justify-space-around">
            <v-chip
                color="deep-purple"
                text-color="white"
            >
            <v-avatar
                left
                class="deep-purple darken-4"
            >
                1
            </v-avatar>
              Link
            </v-chip>
            <v-chip
                color="red"
                text-color="white"
            >
            <v-avatar
                left
                class="red darken-4"
            >
                5
            </v-avatar>
              Video
            </v-chip>
            <v-chip
                color="orange"
                text-color="white"
            >
            <v-avatar
                left
                class="orange darken-4"
            >
                3
            </v-avatar>
              E-Commerce
            </v-chip>
          </div>
        <div class="text-subtitle-1">Cantidad de vistas por fichas</div>
        <v-divider class="mb-3"></v-divider>
        <v-list>
            <v-list-item>
                <v-list-item-avatar>
                    <v-img :src="'https://picsum.photos/500/300?image=' + (Math.round(Math.random() * 10) * 6 + 10)"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title>Ficha de muestra</v-list-item-title>
                    <v-list-item-subtitle>http://link-de-prueba.com/algo</v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                    <span v-text="(Math.round(Math.random() * 10) * 6 + 10)"></span> <v-icon>mdi-eye-outline</v-icon>
                </v-list-item-action>
            </v-list-item>
            <v-divider inset></v-divider>
            <v-list-item>
                <v-list-item-avatar>
                    <v-img :src="'https://picsum.photos/500/300?image=' + (Math.round(Math.random() * 10) * 6 + 12)"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title>Ficha de muestra</v-list-item-title>
                    <v-list-item-subtitle>http://link-de-prueba.com/algo</v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                    <span v-text="(Math.round(Math.random() * 10) * 6 + 12)"></span> <v-icon>mdi-eye-outline</v-icon>
                </v-list-item-action>
            </v-list-item>
            <v-divider inset></v-divider>
            <v-list-item>
                <v-list-item-avatar>
                    <v-img :src="'https://picsum.photos/500/300?image=' + (Math.round(Math.random() * 10) * 6 + 13)"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title>Ficha de muestra</v-list-item-title>
                    <v-list-item-subtitle>http://link-de-prueba.com/algo</v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                    <span v-text="(Math.round(Math.random() * 10) * 6 + 14)"></span> <v-icon>mdi-eye-outline</v-icon>
                </v-list-item-action>
            </v-list-item>
        </v-list>
        <div class="text-subtitle-1">Etiquetas más populares tuyas</div>
        <v-divider class="mb-3"></v-divider>
        <div class="d-flex justify-space-around">
            <v-chip>
                Prueba
            </v-chip>
            <v-chip>
                Otra prueba
            </v-chip>
            <v-chip>
                Algo más
            </v-chip>
        </div>
        <div class="text-subtitle-1">Etiquetas más populares global</div>
        <v-divider class="mb-3"></v-divider>
        <div class="d-flex justify-space-around">
            <v-chip>
                Global
            </v-chip>
            <v-chip>
                Otra prueba
            </v-chip>
            <v-chip>
                Algo más
            </v-chip>
        </div>
        <div class="text-subtitle-1">Estadisticas por sitio</div>
        <v-divider class="mb-3"></v-divider>
            <my-doughnut v-if="displaychart" :data="datacollection" :options="{responsive: true}" />
        <v-simple-table dense>
            <template v-slot:default>
            <thead>
                <tr>
                <th class="text-left">
                    Sitio
                </th>
                <th class="text-left">
                    Fichas
                </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sitio de prueba</td>
                    <td><span v-text="(Math.round(Math.random() * 10) * 6 + 10)"></span></td>
                </tr>
                <tr>
                    <td>Otro sitio de prueba</td>
                    <td><span v-text="(Math.round(Math.random() * 10) * 6 + 13)"></span></td>
                </tr>
                <tr>
                    <td>Otra muestra más</td>
                    <td><span v-text="(Math.round(Math.random() * 10) * 6 + 16)"></span></td>
                </tr>
            </tbody>
            </template>
        </v-simple-table>
      </div>
</template>
<script>
export default {
  layout: 'inside',
  data: () => ({
    datacollection: null,
    displaychart: false
  }),
  mounted () {
    this.fillData()
  },
  methods: {
    fillData () {
      this.datacollection = {
        labels: ['Sitio de prueba', 'Otro sitio', 'Otra muestra'],
        datasets: [
          {
            data: [16, 61, 40],
            backgroundColor: ['#f87979', '#007bff', '#4caf50']
          }
        ]
      }
      this.displaychart = true
    }
  }
}
</script>
