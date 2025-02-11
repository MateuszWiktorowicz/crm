<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import axiosClient from '../axios.js';
import router from '../router.js';
import { computed, onMounted } from 'vue';
import useUserStore from '../store/user.js';
import ApplicationLogo from '../components/ApplicationLogo.vue';

const userStore = useUserStore();

const user = computed(() => userStore.user);
const navigation = computed(() => userStore.navigation);

onMounted(() => {
  console.log(user.value);
  userStore.fetchDictionaries();
});

function logout() {
  axiosClient.post('/logout').then(response => {
    router.push({ name: 'Login' });
  });
}
</script>


<template>
  <div class="min-h-full">
    <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <ApplicationLogo />
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <RouterLink 
                  v-for="item in navigation" 
                  :key="item.name" 
                  :to="item.to" 
                  :class="[$route.name === item.to.name ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'rounded-md px-3 py-2 text-sm font-medium']" 
                  :aria-current="$route.name === item.to.name ? 'page' : undefined">
                  {{ item.name }}
                </RouterLink>
              </div>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">

              <!-- Profile dropdown -->
              <Menu as="div" class="relative ml-3">
                <div>
                  <MenuButton class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                    <span class="absolute -inset-1.5" />
                    <span class="sr-only">Otwórz menu</span>
                    <p class="text-white">{{ user.email }}</p>
                  </MenuButton>
                </div>
                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                  <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 focus:outline-hidden">
                    <MenuItem v-slot="{ active }">
                      <button @click="logout" :class="['block px-4 py-2 text-sm text-gray-700']">Wyloguj</button>
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <div class="-mr-2 flex md:hidden">
            <!-- Mobile menu button -->
            <DisclosureButton class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
              <span class="absolute -inset-0.5" />
              <span class="sr-only">Otwórz menu</span>
              <Bars3Icon v-if="!open" class="block size-6" aria-hidden="true" />
              <XMarkIcon v-else class="block size-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="md:hidden">
        <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
          <RouterLink v-for="item in navigation" 
          :key="item.name" 
          :to="item.to" 
          :class="[$route.name === item.to.name ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'block rounded-md px-3 py-2 text-base font-medium']" :aria-current="$route.name === item.to.name ? 'page' : undefined">{{ item.name }}</RouterLink>
        </div>
        <div class="border-t border-gray-700 pt-4 pb-3">
          <div class="mt-3 space-y-1 px-2">
            <DisclosureButton class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
              <button @click="logout">Wyloguj</button>
            </DisclosureButton>
          </div>
        </div>
      </DisclosurePanel>
    </Disclosure>
    <router-view />

  </div>
</template>


<style scoped>

</style>