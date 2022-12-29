<template>
  <div>
    <input type="text" v-model="text">
    <button @click="searchRequest">Search</button>
  </div>
</template>

<script>
export default {
  name: "SearchBar",
  emits: ['someEvent'],
  data() {
    return {
      text: "",
    }
  },
  methods: {
    searchRequest() {
      this.$emit('searchRequest', {query: this.text});
    }
  }
}
</script>

<style scoped>
  body {
  }
</style>