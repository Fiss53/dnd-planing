<template>
  <div :class="special ? 'special-card' : 'session-card'">
    <div class="session-top">
      <div class="content">
        <div style="display: flex; justify-content: space-between;">
          <span class="session-id">#{{ id }}</span>
          <button v-if="canEdit && !editing" @click="openEdit">Modifier</button>
        </div>
        <div v-if="editing">
          <SessionForm
            v-model="editedSession"
            :available-tags="availableTags"
            @save="save"
          />
        </div>
        <div v-else>
        <h1 class="session-card-title">{{ title }}</h1>
        <hr>
        <p class="session-card-game-master">{{ gameMaster }}</p>
        <p class="session-card-info">{{ info }}</p>
        <hr>
          <div class="tags">
            <span class="tag">{{ location }}</span>
            <span class="tag">{{ time }}</span>
            <span :class="players >= maxPlayers ? 'tag full' : players > 0 ? 'tag mid' : 'tag empty'">{{ players }}/{{ maxPlayers }}</span>
          </div>
        <hr>
        </div>
      </div>
      <img v-if="image" :src="image" :alt="title" />
      <div v-else class="placeholder">Image</div>
    </div>
    <div class="tags">
      <span v-for="tag in tags" :key="tag" class="tag">
        {{ tag }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SessionForm from './SessionForm.vue'
import "../stylemain.css"

const props = defineProps({
  id: String,
  title: String,
  info: String,
  gameMaster: String,
  image: String,
  dates: { type: Array, default: () => [] },
  day: String,
  frequency: Number,
  location: String,
  time: String,
  players: Number,
  maxPlayers: Number,
  tags: { type: Array, default: () => [] },
  special: Boolean,
  canEdit: Boolean,
  availableTags: { type: Array, default: () => [] }
})

const emit = defineEmits(['update'])

const editing = ref(false)

const editedSession = ref({
  title: props.title,
  info: props.info,
  gameMaster: props.gameMaster,
  image: props.image,
  dates: [...(props.dates ?? [])],
  day: props.day,
  frequency: props.frequency,
  location: props.location,
  time: props.time,
  players: props.players,
  maxPlayers: props.maxPlayers,
  tags: [...(props.tags ?? [])],
  special: props.special
})

const openEdit = () => {
  editedSession.value = {
    id: props.id,
    title: props.title,
    info: props.info,
    gameMaster: props.gameMaster,
    image: props.image,
    dates: [...(props.dates ?? [])],
    day: props.day,
    frequency: props.frequency,
    location: props.location,
    time: props.time,
    players: props.players,
    maxPlayers: props.maxPlayers,
    tags: [...(props.tags ?? [])],
    special: props.special
  }

  editing.value = true
}

const save = (session) => {
  emit('update', {
    id: props.id,
    ...session
  })
  editing.value = false
}

</script>

<style>
.content {
  flex: 1;
  text-align: left;
  padding-right: 30px;
  font-family: "PixelMono", Monospace;

}

.session-card,
.special-card {
  display: flex;
  flex-direction: column;
  width: clamp(320px, 85vw, 900px);
  border-radius: 24px;
  padding: 12px;
  margin: 30px auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(123, 123, 123, 0.5);
  box-sizing: border-box;
}

.session-card {
  background: #fcbf49;
}

.special-card {
  margin-top: 0;
  background: #f77f00;
}

.session-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
}

.session-card-title {
  margin: 2px 0;
}

.session-card-info {
  margin: 10px 0;
}

.session-card-game-master {
  margin: 10px 0;
}

img {
  width: clamp(120px, 20vw, 250px);
  max-width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 16px;
  flex-shrink: 0;
}

.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tag {
  background: #444;
  color: white;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.8rem;
}

.tag.empty {
  background: #68ce6d;
}

.tag.mid {
  background: #ee963e;
}

.tag.full {
  background: #e63d3d;
}

.session-id{
  opacity: 50%;
  margin: 0;
}
</style>