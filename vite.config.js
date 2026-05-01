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
      generateBundle(_options, bundle) {
        // Remove PHP files from the bundle
        Object.keys(bundle).forEach(fileName => {
          if (fileName.endsWith('.php')) {
            delete bundle[fileName]
          }
        })
      },
      writeBundle() {
        // Remove any .php files that were copied to dist
        const distDir = 'dist'
        if (fs.existsSync(distDir)) {
          const removePhpFiles = (dir) => {
            const files = fs.readdirSync(dir)
            files.forEach(file => {
              const filePath = path.join(dir, file)
              const stat = fs.statSync(filePath)
              if (stat.isDirectory()) {
                removePhpFiles(filePath)
              } else if (file.endsWith('.php')) {
                fs.unlinkSync(filePath)
              }
            })
          }
          removePhpFiles(distDir)
        }
      }
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
