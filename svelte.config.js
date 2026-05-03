import adapter from '@sveltejs/adapter-static'
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte'
import { copyFileSync } from 'fs'
import { resolve } from 'path'

export default {
  preprocess: vitePreprocess(),
  kit: {
    adapter: adapter({ fallback: 'index.html' }),
    hooks: {
      'build.complete': async () => {
        // Copy .htaccess to build directory for production routing
        const htaccessSrc = resolve('.htaccess')
        const htaccessDest = resolve('build/.htaccess')
        copyFileSync(htaccessSrc, htaccessDest)
        console.log('✓ Copied .htaccess to build directory')

        // Copy api.php to build directory
        const phpSrc = resolve('public/api.php')
        const phpDest = resolve('build/api.php')
        copyFileSync(phpSrc, phpDest)
        console.log('✓ Copied api.php to build directory')

        // Copy scan.php to build directory
        const scanSrc = resolve('public/scan.php')
        const scanDest = resolve('build/scan.php')
        copyFileSync(scanSrc, scanDest)
        console.log('✓ Copied scan.php to build directory')
      }
    }
  }
}
