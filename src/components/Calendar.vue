<template>
  <div>
    <Filter
      :sessions="sessions"
      @update:tags="selectedTags = $event"
    />
    <div>
      <button @click="previousWeek"><</button>
        Semaine du {{ formatDate(startOfWeek) }} au {{ formatDate(endOfWeek) }}
      <button @click="nextWeek">></button>
    </div>
    <div class="days">
      <button
      v-for="day in weekDays"
      :key="day.date"
      :class="{ active: isSelected(day.date) }"
      @click="selectDay(day.date)"
      >
        <div>
          {{ day.label }}
        </div>
        <div>
          {{ formatDay(day.date) }}
        </div>
        <div v-if="getSessionCountForDay(day.date) == 1">
          {{ getSessionCountForDay(day.date) }} session
        </div>
        <div v-else-if="getSessionCountForDay(day.date) == 0">
          aucune sessions
        </div>
        <div v-else>
          {{ getSessionCountForDay(day.date) }} sessions
        </div>
      </button>
    </div>
    <h2>
      <br>{{ selectedDayLabel  }}
    </h2>
    <div v-if="getSessionsForDay(selectedDay).length == 0">
      <p>Aucune sessions prévue :(</p>
    </div>
    <div v-else>
      <Session
      v-for="session in getSessionsForDay(selectedDay)"
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
  </div>
</template>

<script setup>
  import { ref, computed } from "vue"
  import Session from "./Session.vue"
  import Filter from "./Filter.vue"

  const props = defineProps({
    sessions: {
      type: Array,
      default: () => []
    }
  })

  const currentDate = ref(new Date())
  const selectedDay = ref(new Date())
  const selectedTags = ref([])

  const startOfWeek = computed(() => getStartOfWeek(currentDate.value))

  const endOfWeek = computed(() => {
    const end = new Date(startOfWeek.value)
    return end.setDate(end.getDate() + 6)
  })

  const weekDays = computed(() => {
    const days = []
    const labels = ["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche",]

    for(let i = 0; i < 7; i++){
      const date = new Date(startOfWeek.value)
      date.setDate(date.getDate() + i)
      days.push({label: labels[i],date,})
    }
    return days
  })

  const selectedDayLabel = computed(() => new Intl.DateTimeFormat("fr-FR", {weekday: "long",}).format(selectedDay.value).toUpperCase())

  const getStartOfWeek = (date) => {
    const newdate = new Date(date)
    const day = newdate.getDay()
    const diff = day === 0 ? -6 : 1 - day

    newdate.setDate(newdate.getDate() + diff)
    newdate.setHours(0, 0, 0, 0)

    return newdate
  }

  const nextWeek = () => {
    const date = new Date(currentDate.value)

    date.setDate(date.getDate() + 7)
    currentDate.value = date
  }

  const previousWeek = () => {
    const date = new Date(currentDate.value)
    
    date.setDate(date.getDate() - 7)
    currentDate.value = date
  }

  const selectDay = (date) => selectedDay.value = new Date(date)

  const isSelected = (date) => (selectedDay.value.toDateString() === new Date(date).toDateString())

  const formatDate = (date) => {
    return new Intl.DateTimeFormat("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
    }).format(date)
  }
  const formatDay = (date) => {
    return new Intl.DateTimeFormat("fr-FR", {
      day: "numeric",
      month: "short",
    }).format(date)
  }

  const getSessionsForDay = (date) => {
    return filteredSessions.value.filter(session => session.dates.some(newDate => new Date(newDate).toDateString() === new Date(date).toDateString()))
  }

  const getSessionCountForDay = (date) => getSessionsForDay(date).length
  
  const filteredSessions = computed(() => {
    if(selectedTags.value.length === 0){
      return props.sessions
    }
    return props.sessions.filter(session => session.tags?.some(tag => selectedTags.value.includes(tag)))
  })
</script>

<style>
  .days{
    display: flex;
    flex-wrap: nowrap;
    gap: 10px;
    padding: 10px;
    padding-top: 20px;
    padding-bottom: 0;
    justify-content: center;
    scrollbar-width: none;
    touch-action: none;
  }

  .days::-webkit-scrollbar{
    display: none;
  }

  .days button{
    flex: 1 1 clamp(60px, 10vw, 100px);
    aspect-ratio: 1 / 1;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  h2{
    margin-top: 0;
  }
</style>