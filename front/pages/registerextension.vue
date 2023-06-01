<template>
    <div>Conectando...</div>
</template>
<script>
export default {
  layout: 'blank',
  mounted () {
    this.$store.commit('SET_EXT_ID', this.$route.query.id)
    this.$router.push('/')
  }
}
</script>
