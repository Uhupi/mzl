import { copyFileSync, mkdirSync } from 'fs'
import { resolve } from 'path'

const buildDir = resolve('build')
mkdirSync(buildDir, { recursive: true })

// Copy .htaccess
copyFileSync('.htaccess', resolve(buildDir, '.htaccess'))
console.log('✓ Copied .htaccess to build directory')

// Copy api.php
copyFileSync('public/api.php', resolve(buildDir, 'api.php'))
console.log('✓ Copied api.php to build directory')

// Copy scan.php
copyFileSync('public/scan.php', resolve(buildDir, 'scan.php'))
console.log('✓ Copied scan.php to build directory')
