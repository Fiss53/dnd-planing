<template>
  <div class="tags">
    <button v-for="tag in tags" :key="tag" :class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
      {{ tag }}
    </button>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue"

const props = defineProps({
  sessions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(["update:tags"])

const selectedTags = ref([])

const tags = computed(() => {
  const newTags = props.sessions.flatMap(session => session.tags || [])

  return [...new Set(newTags)].sort()
})

const toggleTag = (tag) => {
  if (selectedTags.value.includes(tag)) {
    selectedTags.value = selectedTags.value.filter(t => t !== tag)
  } else {
    selectedTags.value.push(tag)
  }
}

watch(selectedTags, () => {
  emit("update:tags", selectedTags.value)
}, { deep: true })
</script>

<style>
.tags button {
  padding: 6px 12px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
  margin: 4px;
}
</style>