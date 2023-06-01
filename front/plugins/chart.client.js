import Vue from 'vue'
import { Line, Doughnut, Bar } from 'vue-chartjs'

Vue.component('my-line', {
  extends: Line,
  props: ['options', 'data'],
  mounted () {
    this.renderChart(this.data, this.options)
  }
})

Vue.component('my-doughnut', {
  extends: Doughnut,
  props: ['options', 'data'],
  mounted () {
    this.renderChart(this.data, this.options)
  }
})

Vue.component('my-bar', {
  extends: Bar,
  props: ['options', 'data'],
  mounted () {
    this.renderChart(this.data, this.options)
  }
})
