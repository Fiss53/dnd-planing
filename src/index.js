import { createMemoryHistory, createRouter } from 'vue-router'
import App from '@/App.vue'
import SessionForm from '@/components/SessionForm.vue'

const routes = [
    { path: '/', component: App },
    { path: '/create', component: SessionForm },
]

export const router = createRouter({
    history: createMemoryHistory(),
    routes,
})