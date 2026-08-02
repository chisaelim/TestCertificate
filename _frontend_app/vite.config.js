import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    Components({
      dirs: ['./src/**'],
      extensions: ['vue'],
      dts: true,
    }),
    AutoImport({
      imports: [
        {
          'vue': [
            'createApp',
            'computed',
            'onMounted',
            'ref',
            'reactive',
            'watch',
            'onBeforeUpdate',
            'h',
            'onBeforeMount',
            'nextTick',
            'useAttrs',
            'onBeforeUnmount',
            'onUnmounted',
            'onUpdated',
          ],
        },
        {
          'vue-router': [
            'createRouter',
            'createWebHistory',
            'useRouter',
            'useRoute'
          ],
        },
      ],
      dirs: [
        './src/**',
      ],
      dts: true,
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    hmr: {
      protocol: 'ws',
      port: 5173,
    },
    watch: {
      usePolling: true,
      useFsEvents: true,
      interval: 1000,
    }
  }
})
