import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import fs from 'fs'
import path from 'path'

export default defineConfig({
  plugins: [
    svelte(),
    {
      name: 'exclude-php-files',
      apply: 'build',
      enforce: 'post',
    }
  ],
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
