import App from './App.svelte'
import './app.css'

const app = new App({
  target: document.getElementById('app')!
})

// Register service worker for PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js').then(
      (registration) => {
        console.log('[PWA] Service Worker registered:', registration)

        // Check for updates periodically
        setInterval(() => {
          registration.update()
        }, 60000)
      },
      (error) => {
        console.log('[PWA] Service Worker registration failed:', error)
      }
    )
  })
}

export default app
