<template>
  <div class="session-form">
    <h2>{{ isEdit ? "Modifier la session" : "Créer une session" }}</h2>
    <p v-if="error" class="error-message">
      {{ error }}
    </p>
    <div class="checkbox-field">
      <input id="special" type="checkbox" v-model="model.special" />
      <label for="special">Session spéciale</label>
    </div>
    <div class="grid">
      <div class="field">
        <label>Titre *</label>
        <input v-model="model.title" placeholder="Titre" />
      </div>
      <div class="field">
        <label>Maître du jeu *</label>
        <input v-model="model.gameMaster" placeholder="Maître du jeu" />
      </div>
      <div class="field">
        <label>Image</label>
        <input v-model="model.image" placeholder="URL image" />
      </div>
      <div class="field field-full">
        <label>Description *</label>
        <textarea v-model="model.info" placeholder="Description de la session"></textarea>
      </div>
      <div class="field">
        <label>Lieu *</label>
        <select v-model="model.location">
          <option disabled value="">Choisir un lieu</option>
          <option>Bat C</option>
          <option>Bat K</option>
          <option>Distanciel</option>
        </select>
      </div>
      <div class="field">
        <label>Heure *</label>
        <input v-model="model.time" type="time" />
      </div>
      <div class="field">
        <label>Joueurs inscrits *</label>
        <input v-model.number="model.players" type="number" min="0" :max="model.maxPlayers" />
      </div>
      <div class="field">
        <label>Nombre max de joueurs *</label>
        <input v-model.number="model.maxPlayers" type="number" min="1" placeholder="1" />
      </div>
    </div>
    <div class="block">
      <div v-if="model.special">
        <h3>Dates de la session</h3>
        <div class="inline">
          <input v-model="dateInput" type="date" />
          <button type="button" @click="addDate">Ajouter</button>
        </div>
        <div class="chips">
          <span v-for="(d, i) in model.dates" :key="i" class="chip">
            {{ d }}
            <button type="button" @click="removeDate(i)">×</button>
          </span>
        </div>
      </div>
      <div v-else>
        <h4>Récurrence</h4>
        <label>Jour de la session</label>
        <select v-model="model.day">
          <option value="">Choisir un jour</option>
          <option>Lundi</option>
          <option>Mardi</option>
          <option>Mercredi</option>
          <option>Jeudi</option>
          <option>Vendredi</option>
          <option>Samedi</option>
          <option>Dimanche</option>
        </select>
        <label>Fréquence</label>
        <select v-model="model.frequency">
          <option :value="1">Chaque semaine</option>
          <option :value="2">Toutes les 2 semaines</option>
          <option :value="3">Toutes les 3 semaines</option>
          <option :value="4">Toutes les 4 semaines</option>
        </select>
      </div>
    </div>
    <div class="block">
      <h3>Tags</h3>
      <div class="available-tags">
        <button
          v-for="tag in availableTags"
          :key="tag"
          type="button"
          class="tag-btn"
          :class="{ selected: model.tags.includes(tag) }"
          @click="toggleTag(tag)"
        >
          {{ tag }}
        </button>
      </div>
      <div class="inline">
        <input v-model="tagInput" placeholder="Tag" />
        <button type="button" @click="addTag">Ajouter</button>
      </div>
      <div class="chips">
        <span v-for="(t, i) in model.tags" :key="i" class="chip">
          {{ t }}
          <button type="button" @click="removeTag(i)">×</button>
        </span>
      </div>
    </div>
    <button class="save-btn" type="button" @click="handleSave">
      {{ isEdit ? "Sauvegarder" : "Créer" }}
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  availableTags: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(["update:modelValue", "save"])

const model = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val)
})

const isEdit = computed(() => !!props.modelValue?.id)

const dateInput = ref("")
const tagInput = ref("")
const error = ref("")

const isEmpty = (value) => {
  return value === null || value === undefined || String(value).trim() === ""
}

const validateForm = () => {
  error.value = ""

  if (isEmpty(model.value.title)) {
    error.value = "Le titre est obligatoire."
    return false
  }

  if (isEmpty(model.value.gameMaster)) {
    error.value = "Le maître du jeu est obligatoire."
    return false
  }

  if (isEmpty(model.value.info)) {
    error.value = "La description est obligatoire."
    return false
  }

  if (isEmpty(model.value.location)) {
    error.value = "Le lieu est obligatoire."
    return false
  }

  if (isEmpty(model.value.time)) {
    error.value = "L'heure est obligatoire."
    return false
  }

  if (model.value.players === null || model.value.players === undefined || model.value.players < 0) {
    error.value = "Le nombre de joueurs inscrits est obligatoire."
    return false
  }

  if (!model.value.maxPlayers || model.value.maxPlayers < 1) {
    error.value = "Le nombre max de joueurs doit être au minimum de 1."
    return false
  }

  if (model.value.players > model.value.maxPlayers) {
    error.value = "Le nombre de joueurs inscrits ne peut pas dépasser le nombre max de joueurs."
    return false
  }

  if (model.value.special) {
    if (!model.value.dates || model.value.dates.length === 0) {
      error.value = "Une session spéciale doit avoir au moins une date."
      return false
    }
  } else {
    if (isEmpty(model.value.day)) {
      error.value = "Le jour de la session est obligatoire."
      return false
    }

    if (!model.value.frequency) {
      error.value = "La fréquence est obligatoire."
      return false
    }
  }

  if (!model.value.tags || model.value.tags.length === 0) {
    error.value = "Ajoute au moins un tag."
    return false
  }

  return true
}

const handleSave = () => {
  if (!validateForm()) return

  emit("save", model.value)
}

const addDate = () => {
  if (!dateInput.value) return

  model.value = {
    ...model.value,
    dates: [...model.value.dates, dateInput.value]
  }

  dateInput.value = ""
}

const removeDate = (i) => {
  model.value = {
    ...model.value,
    dates: model.value.dates.filter((_, idx) => idx !== i)
  }
}

const addTag = () => {
  if (!tagInput.value.trim()) return

  if (model.value.tags.includes(tagInput.value.trim())) {
    tagInput.value = ""
    return
  }

  model.value = {
    ...model.value,
    tags: [...model.value.tags, tagInput.value.trim()]
  }

  tagInput.value = ""
}

const removeTag = (i) => {
  model.value = {
    ...model.value,
    tags: model.value.tags.filter((_, idx) => idx !== i)
  }
}

const toggleTag = (tag) => {
  if (model.value.tags.includes(tag)) {
    model.value = {
      ...model.value,
      tags: model.value.tags.filter(t => t !== tag)
    }
  } else {
    model.value = {
      ...model.value,
      tags: [...model.value.tags, tag]
    }
  }
}
</script>

<style scoped>
.session-form {
  background: #f18701;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.error-message {
  background: #ffe5e5;
  color: #b00020;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 15px;
  font-weight: 600;
}

.grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.grid textarea {
  grid-column: span 2;
  min-height: 80px;
}

input, textarea, select {
  padding: 8px;
  border-radius: 10px;
  background: #f7b801;
  border: 1px solid #ddd;
}

.block {
  margin-top: 15px;
}

.inline {
  display: flex;
  gap: 8px;
  margin-bottom: 10px;
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.chip {
  background: #eee;
  padding: 5px 10px;
  border-radius: 999px;
  display: flex;
  gap: 6px;
  align-items: center;
}

.save-btn {
  margin-top: 15px;
  background: #f77f00;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-family: "PixelMono", normal;

}

.field label {
  font-weight: 600;
  font-size: 0.9rem;
}

.field-full {
  grid-column: span 2;
}

.checkbox-field {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.checkbox-field input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-field label {
  font-weight: 600;
  cursor: pointer;
}

.available-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 15px;
}

.tag-btn {
  border: 1px solid #ddd;
  background: white;
  border-radius: 999px;
  padding: 6px 12px;
  cursor: pointer;
  transition: 0.2s;
}

.tag-btn:hover {
  background: #f5f5f5;
}

.tag-btn.selected {
  background: #f77f00;
  color: white;
  border-color: #f77f00;
}
</style>