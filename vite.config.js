import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
  ],
  server: {
    hmr: { host: '144.126.142.6.sslip.io' }, // útil si usas Vite dev sobre el dominio
  },
})
