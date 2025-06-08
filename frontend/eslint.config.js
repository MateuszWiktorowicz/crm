import { defineConfig } from 'eslint/config';
import js from '@eslint/js';
import globals from 'globals';
import pluginVue from 'eslint-plugin-vue';

export default defineConfig([
  {
    files: ['**/*.{js,mjs,cjs,vue}'],
    plugins: {
      js,
      vue: pluginVue,
    },
    extends: ['eslint:recommended', 'plugin:vue/vue3-recommended', 'plugin:prettier/recommended'],
    languageOptions: {
      globals: {
        ...globals.browser,
      },
    },
    rules: {
      'no-invalid-this': 'error', // <<< Dodaj tę regułę tutaj
      // inne reguły...
    },
  },
]);
