<template>
  <div>
    <h2>Filtres :</h2>
    <Filter :sessions="sessions" @update:tags="selectedTags = $event" />
    <hr>

    <h2> Sessions Hebdomadaires</h2>
    <div class="days">
      <button v-for="day in weekDays" :key="day.date" :class="{ active: isSelected(day.date) }"
        @click="selectDay(day.date)">
        <div>
          {{ day.label }}
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
    <hr>
    <h2>
      {{ selectedDayLabel }}
    </h2>
    <div v-if="getSessionsForDay(selectedDay).length == 0">
      <p>Aucune sessions prévue :(</p>
    </div>
    <div v-else>
      <Session v-for="session in getSessionsForDay(selectedDay)" 
      :key="session.id" 
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
      :can-edit="props.canEdit"
      :available-tags="availableTags"
      @update="emit('update', $event)"/>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"
import Session from "./Session.vue"
import Filter from "./Filter.vue"
import "./../stylemain.css"

const props = defineProps({
  sessions: Array,
  canEdit: Boolean,
  availableTags: {
    type: Array,
    default: () => []
  }
})

const dayMap = {
  "Lundi": 1,
  "Mardi": 2,
  "Mercredi": 3,
  "Jeudi": 4,
  "Vendredi": 5,
  "Samedi": 6,
  "Dimanche": 0
}

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
  const labels = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche",]

  for (let i = 0; i < 7; i++) {
    const date = new Date(startOfWeek.value)
    date.setDate(date.getDate() + i)
    days.push({ label: labels[i], date, })
  }
  return days
})

const selectedDayLabel = computed(() => new Intl.DateTimeFormat("fr-FR", { weekday: "long", }).format(selectedDay.value).toUpperCase())

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
  return filteredSessions.value.filter(session => {
    if (session.special){
      return session.dates.some(newDate => new Date(newDate).toDateString() === new Date(date).toDateString())
    }
    return isRecurringSessionOnDate(session, date)
  })
}

const getSessionCountForDay = (date) => getSessionsForDay(date).length

const filteredSessions = computed(() => {
  if (selectedTags.value.length === 0) {
    return props.sessions
  }
  return props.sessions.filter(session => session.tags?.some(tag => selectedTags.value.includes(tag)))
})

const emit = defineEmits(['update'])

const isRecurringSessionOnDate = (session, date) => {
  if (!session.day || !session.frequency) {
    return false
  }

  const targetDate = new Date(date)

  if (targetDate.getDay() !== dayMap[session.day]) {
    return false
  }

  const firstDayOfMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 1)
  const weekNumber = Math.floor((targetDate.getDate() - 1) / 7)

  return weekNumber % session.frequency === 0
}

const availableTags = computed(() => {
  return [...new Set(
    props.sessions.flatMap(s => s.tags ?? [])
  )].sort()
})

</script>

<style>

.days {
  display: flex;
  flex-wrap: nowrap;
  gap: 10px;
  padding: 10px;
  padding-top: 20px;
  padding-bottom: 0;
  justify-content: center;
  overflow-x: auto;
  scroll-snap-type: x proximity;
  scrollbar-width: none;
  touch-action: pan-x;
  font-size: max(16px, 1vw);
}

.days::-webkit-scrollbar {
  display: none;
}

.days button {
  font-size: max(16px, 1vw);
  flex: 1 1 clamp(60px, 10vw, 100px);
  aspect-ratio: 1 / 1;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

@media (max-width: 640px) {
  .days {
    justify-content: flex-start;
  }

  .days button {
    flex: 0 0 78px;
    scroll-snap-align: start;
  }
}

h2 {
  margin-top: 0;

}

.weeks {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  padding: 10px;

}

.weeks span {
  text-align: center;
}

.weeks button {
  width: 35px;
  height: 35px;
  border-radius: 8px;
}
</style>