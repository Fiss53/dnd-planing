<template>
  <Layout>
    <template #header>
    </template>
    <template #main>
      <iframe
          :src="`${diceBaseUrl}ascii-d20-rapier.html`"
          class="dice-iframe"
      />

      <div class="content-wrapper">
        <div class="title-box">
          <h1>Calendrier Lune Rousse</h1>
        </div>
        <div class="page">

          <div class="top-bar">
            <div v-if="!isLoggedIn">
              <input
                  v-model="passwordInput"
                  type="password"
                  placeholder="Mot de passe"
                  class="login-font"
              />
              <button @click="login" class="login-font">Login</button>
            </div>
            <div v-else>
              <button  @click="logout" class="login-font">Logout</button>
            </div>
          </div>
          <hr v-if="isLoggedIn">
          <div v-if="isLoggedIn" class="create-form">
            <SessionForm
                v-model="newSession"
                :available-tags="availableTags"
                @save="createSession"
            />
          </div>
          <h2>Sessions Spéciales</h2>
          <div v-if="getSpecialSessions().length == 0">
            <p>Aucune sessions spéciales :(</p>
          </div>
          <div v-else>
            <div v-for="session in getSpecialSessions()" :key="session.id">
              <div class="next-date">
                {{ formatShortDate(getFirstSpecialDate(session)) }}
              </div>
              <Session
                  :id="session.id"
                  :title="session.title"
                  :info="session.info"
                  :game-master="session.gameMaster"
                  :image="session.image"
                  :dates="session.dates"
                  :day="session.day"
                  :frequency="session.frequency"
                  :location="session.location"
                  :time="session.time"
                  :players="session.players"
                  :max-players="session.maxPlayers"
                  :tags="session.tags"
                  :special="session.special"
                  :can-edit="isLoggedIn"
                  :available-tags="availableTags"
                  @update="updateSession"
              />
            </div>
          </div>
          <hr>
          <Calendar :sessions="sessionsComputed" :can-edit="isLoggedIn" @update="updateSession"/>
        </div>
      </div>

    </template>
    <template #footer>
      <hr>
      Foot
    </template>
  </Layout>
</template>

<script setup>
import { ref, computed } from "vue"
import Layout from './components/Layout.vue';
import Calendar from './components/Calendar.vue';
import Session from './components/Session.vue';
import SessionForm from "./components/SessionForm.vue";

import initialSessions from './data/sessions.json';

import { onMounted } from "vue"
import "./stylemain.css"

// BASE_URL vaut "/" en dev et "/planning/" une fois compile (voir vite.config.js),
// pour que l'iframe pointe toujours au bon endroit une fois deploye a cote de WordPress.
const diceBaseUrl = import.meta.env.BASE_URL

onMounted(() => {
  loadSessions()
})

const emit = defineEmits(['update'])

//const sessions = ref([...initialSessions])
const sessions = ref([])

const sessionsComputed = computed(() => sessions.value)

const isLoggedIn = ref(false)
const passwordInput = ref("")

const dateInput = ref("")
const tagInput = ref("")

const logout = async () => {
  await fetch(`${import.meta.env.VITE_API_URL}/logout`, {
    method: "POST",
    credentials: "include"
  })
  isLoggedIn.value = false
}

const canEdit = computed(() =>
  isLoggedIn.value
)

const newSession = ref({
  title: "",
  info: "",
  gameMaster: "",
  image: "",
  dates: [],
  location: "",
  time: "",
  players: 0,
  maxPlayers: 0,
  tags: [],
  special: false
})

const emptySession = () => ({
  title: "",
  info: "",
  gameMaster: "",
  image: "",
  dates: [],
  location: "",
  time: "",
  players: 0,
  maxPlayers: 0,
  tags: [],
  special: false
})

const addDate = () => {
  if (!dateInput.value) return

  newSession.value.dates.push(dateInput.value)
  dateInput.value = ""
}

const removeDate = (index) => {
  newSession.value.dates.splice(index, 1)
}

const addTag = () => {
  if (!tagInput.value) return

  newSession.value.tags.push(tagInput.value)
  tagInput.value = ""
}

const removeTag = (index) => {
  newSession.value.tags.splice(index, 1)
}

const events = {
  [new Date().toDateString()]: "Test",
}

const getSpecialSessions = () => sessions.value.filter(session => session.special)

const availableTags = computed(() => {
  return [...new Set(
    sessions.value.flatMap(session => session.tags ?? [])
  )].sort()
})

const getFirstSpecialDate = (session) => {
  if (!session.dates?.length) return null
  return session.dates
    .map(d => new Date(d))
    .sort((a, b) => a - b)[0]
}

const formatShortDate = (date) => {
  if (!date) return ""

  return new Intl.DateTimeFormat("fr-FR", {
    day: "numeric",
    month: "long",
    weekday: "long"
  }).format(date)
}

const updateSession = async (updatedSession) => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/sessions/${updatedSession.id}`, {
    method: "PUT",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(updatedSession)
  })

  const savedSession = await res.json()

  const session = sessions.value.find(s => s.id === savedSession.id)
  if (!session) return

  Object.assign(session, savedSession)
}

const createSession = async () => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/sessions`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(newSession.value)
  })

  const savedSession = await res.json()

  sessions.value.push(savedSession)
  newSession.value = emptySession()
}

const loadSessions = async () => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/sessions`, {
    credentials: "include"
  })
  const data = await res.json()

  sessions.value = data.map(session => ({
    ...session,
    dates: typeof session.dates === "string" ? JSON.parse(session.dates || "[]") : session.dates,
    tags: typeof session.tags === "string" ? JSON.parse(session.tags || "[]") : session.tags,
    special: !!session.special
  }))
}

const login = async () => {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/login`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      password: passwordInput.value
    })
  })

  if (!res.ok) {
    alert("Mot de passe incorrect")
    passwordInput.value = ""
    return
  }

  isLoggedIn.value = true
  passwordInput.value = ""
}
</script>

<style>

html {
  background: var(--color-bg);
}
* {
  font-family: "PixelMono", monospace;
}
body {
  font-size: max(16px, 1vw);
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

header {
  padding: 40px;
}

main {
  background: var(--color-bg);
  display: flex;
  justify-content: center;
  padding: 40px;

}

.content-wrapper {
  width: 100%;
  min-width: 0;
}

footer {
  padding: 40px;
}

.page {
  background: var(--color-primary);
  max-width: 1100px;
  margin: 0 auto;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 10px 50px rgba(0, 0, 0, 0.7);
  position: relative;
  z-index: 1;

}

button.active {
  transition: 0.2s;
  background: black;
  color: var(--color-accent);

}

button:hover {
  transform: scale(1.05);
}

.top-bar {

  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.top-bar h1 {
  margin: 0;
}

.next-date {
  margin-bottom: 8px;
  font-weight: 600;
  opacity: 0.8;
  font-size: 7mm;
}




.dice-iframe {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  z-index: 0;
  pointer-events: none;
  border: none;
}



.title-box {
  background: var(--color-primary);
  max-width: 1100px;
  margin: 0 auto 20px;
  border-radius: 20px;
  padding: 25px 40px;
  box-shadow: 0 10px 50px rgba(0, 0, 0, 0.7);
  position: relative;
  z-index: 1;
  text-align: center;
  overflow: hidden;
}

.title-box::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 90%;
  height: 140px;
  max-width: 800px;
  transform: translate(-50%, -50%);
  background: radial-gradient(ellipse, rgba(255, 224, 168, 0.55) 0%, rgba(255, 224, 168, 0) 70%);
  pointer-events: none;
  z-index: 0;
}

.title-box h1 {
  margin: 0;
  font-size: clamp(1.5rem, 4vw, 2.75rem);
  position: relative;
  z-index: 1;
}

@media (max-width: 640px) {
  main {
    padding: 16px;
  }

  .page {
    padding: 20px;
    border-radius: 14px;
  }

  .title-box {
    padding: 16px 20px;
    border-radius: 14px;
  }

  .title-box::before {
    height: 90px;
  }

  .top-bar {
    flex-wrap: wrap;
    gap: 10px;
  }
}
</style>