import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'

export default defineConfig({
  plugins: [svelte()],
  server: {
    proxy: {
      '/api.php': {
        target: 'http://mzl.test',
        changeOrigin: true,
        rewrite: (path) => path
      }
    }
  }
})
