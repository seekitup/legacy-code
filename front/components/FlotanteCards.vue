<template>
   <div style="width:100vw; height: 100%; padding: 1.5rem 2.5rem;">
       <v-card elevation="10" class="rounded-xl flotante d-flex flex-column" style="width: 100%; height: 100%;">
           <v-card-title v-if="ispublic === undefined || ispublic === false" class="justify-center" style="text-transform: uppercase;">
               <span>{{title}}</span>
           </v-card-title>
           <v-card-title v-if="ispublic === true" class="align-center" style="text-transform: uppercase;">
              <v-avatar color="#D22F82" size="1.75rem" class="mr-2">
                <img src="logo_abeja.svg" style="filter: invert(1); height: 1rem; width: 1rem;" />
              </v-avatar>
              <span>{{title}}</span>
              <v-spacer></v-spacer>
              <v-icon style="font-size:2rem; color: black;" @click="showShare = true">mdi-share</v-icon>
           </v-card-title>
           <v-card-text class="pa-0 flex-grow-1" @click="$router.push(destination)">
             <div class="" style="height: 100%; margin-top: -10%; position: relative; overflow: hidden;" v-if="cards.length > 0">
                 <Ficha
                   v-for="(card, index) in cards"
                   :key="card.id"
                   :fichaData="card"
                   :preview="true"
                   style="transform: scale(.75); position: absolute; left: 0px; width: 100%;"
                   :style="'top: ' + index * 2 + 'rem'"
                   :delay="index + 1"
                 />
             </div>
             <div class="d-flex justify-center align-center" style="height: 100%; font-size: x-large; line-height: 2.25rem;" v-if="cards.length === 0">
               <div class="text-center px-4">Comenzá tu aventura junto a Seekitup<br/>¡Adelante!</div>
             </div>
           </v-card-text>
        </v-card>
        <Share v-model="showShare"/>
    </div>
</template>
<script>
export default {
  props: ['title', 'destination', 'images', 'ispublic', 'cards'],
  data: () => ({
    showShare: false
  })
}
</script>
