<!-- author - xdomra00 -->

<script setup>
import { RouterLink, RouterView } from 'vue-router'
import HomeView from './views/HomeView.vue';
import RecycleGameView from './views/xdomra00Views/RecycleGameView.vue';
import RiddleGameView from './views/xivano08Views/RiddleGameView.vue';
// import HelloWorld from './components/HelloWorld.vue'



</script>

<script>
export default {
  mounted() {
    if (this.$route.name === null) {
      this.$router.push({ name: 'home' });
    }
  }
}
</script>

<template>

      <!-- <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
      </nav> -->


  <RouterView />
  <!-- <HomeView/> -->
  <!-- <RecycleGameView/> -->
  <!-- <RiddleGameView/> -->
</template>


<style>
h1,h2,h3,h4,h5
{
  text-align: center;
}

body
{
  background-color: #FCEEDF;
}

ul
{
  list-style: none;
  margin: 0;
  padding: 0;
}

ul li
{
  margin: 0;
}

</style>
