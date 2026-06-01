<template>
  <Layout>
    <template #header>
    </template>
    <template #main>
      <div class="page">
        <h1>Calendrier JDR</h1>
        <p>Infos coulos</p>
        <hr>
        <h2>Sessions Spéciales</h2>
        <div v-if="getSpecialSessions().length == 0">
          <p>Aucune sessions spéciales :(</p>
        </div>
        <div v-else>
          <Session
          v-for="session in getSpecialSessions()"
          :key="session.title"
          :title="session.title"
          :info="session.info"
          :game-master="session.gameMaster"
          :image="session.image"
          :dates="session.dates"
          :tags="session.tags"
          :special="session.special"
          />
        </div>
        <hr>
        <Calendar :sessions="sessions" />
      </div>
    </template>
    <template #footer>
      <hr>
      Foot
    </template>
  </Layout>
</template>

<script setup>
  import Layout from './components/Layout.vue';
  import Calendar from './components/Calendar.vue';
  import Session from './components/Session.vue';

  import sessions from './data/sessions.json';

  const events = {
    [new Date().toDateString()]: "Test",
  }

  const getSpecialSessions = () => sessions.filter(session => session.special === true)
</script>

<style>
  :root{
    --header-light: #f4ecd8;
    --main-color: #e8dcc0;
    --footer-dark: #c4b08a;
  }

  html{
    background: #c4b08a;
  }

  body{
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header{
    background: linear-gradient(to bottom, color-mix(in srgb, var(--main-color) 60%, white), var(--main-color));
    padding: 40px;
  }

  main{
    background: var(--main-color);
    display: flex;
    justify-content: center;
    padding: 40px;
  }

  footer{
    background: linear-gradient( to bottom, var(--main-color), color-mix(in srgb, var(--main-color) 60%, black));
    padding: 40px;
  }

  .page {
    background: white;
    max-width: 1100px;
    margin: 0 auto;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  }

  button.active{
    transition: 0.2s;
    background: black;
    color: white;
  }

  button:hover{
    transform: scale(1.05);
  }
</style>