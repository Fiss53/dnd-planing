import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig(({ command }) => ({
  // Le site est deploye dans un sous-dossier /planning/ a cote de WordPress
  // (qui occupe la racine du domaine). En dev on reste a la racine locale
  // pour plus de simplicite.
  base: command === 'build' ? '/planning/' : '/',
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    // En dev, le PHP tourne separement (ex: php -S localhost:8000 -t backend).
    // Ce proxy fait passer les appels /api par le meme port que le front,
    // donc le navigateur les voit comme "same-origin" (pas de CORS a gerer,
    // les cookies de session PHP fonctionnent normalement).
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ''),
      },
    },
  },
}))
