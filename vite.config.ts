import { defineConfig } from 'vite'
import { sveltekit } from '@sveltejs/kit/vite'

export default defineConfig({
  plugins: [sveltekit()],
  server: {
    proxy: {
      '/api.php': {
        target: 'https://mzl.test',
        changeOrigin: true,
        rewrite: (path) => path
      }
    }
  }
})
