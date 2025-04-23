import { defineConfig } from 'eslint/config';
import js from '@eslint/js';
import globals from 'globals';
import pluginVue from 'eslint-plugin-vue';

export default defineConfig([
  {
    files: ['**/*.{js,mjs,cjs,vue}'],
    plugins: {
      js,           // Użyj pluginu dla JavaScript
      vue: pluginVue, // Użyj pluginu dla Vue
    },
    extends: [
      'eslint:recommended',
      'plugin:vue/vue3-recommended', // Wybierz odpowiednią konfigurację dla Vue
      'plugin:prettier/recommended', // Jeśli używasz Prettiera
    ],
    languageOptions: {
      globals: {
        ...globals.browser,
      },
    },
    rules: {
      // Twoje reguły
    },
  },
]);
