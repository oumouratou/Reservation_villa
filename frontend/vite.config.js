import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

const backendUrl = 'http://127.0.0.1:8000'
const proxiedRoutes = [
  '/api',
  '/auth',
  '/villas',
  '/my-villas',
  '/villa-reservations',
  '/reservations',
  '/messages',
  '/my-disputes',
  '/my-amenities',
  '/amenities',
  '/disputes',
  '/reviews',
  '/earnings',
  '/payments',
  '/notifications',
]

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    proxy: Object.fromEntries(
      proxiedRoutes.map((path) => [
        path,
        {
          target: backendUrl,
          changeOrigin: true,
        },
      ]),
    ),
  },
})
