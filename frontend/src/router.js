import { createRouter } from 'vue-router';
import { createWebHistory } from 'vue-router';
import DefaultLayout from './Layouts/DefaultLayout.vue';
import Tools from './Pages/Tools/Tools.vue';
import Login from './Pages/Login.vue';
import NotFound from './Pages/NotFound.vue';
import Dashboard from './Pages/Dashboard.vue';
import Customers from './Pages/Customers/Customers.vue';
import Offers from './Pages/Offers/Offers.vue';
import Employees from './Pages/Employees/Employees.vue';
import Coatings from './Pages/Coatings/Coatings.vue';
import useUserStore from './store/user';
import Settings from './Pages/Settings/Settings.vue';

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      { path: '/', name: 'Dashboard', component: Dashboard },
      { path: '/klienci', name: 'Customers', component: Customers },
      { path: '/oferty', name: 'Offers', component: Offers },
      { path: '/narzedzia', name: 'Tools', component: Tools },
      { path: '/pokrycia', name: 'Coatings', component: Coatings },
      {
        path: '/pracownicy',
        name: 'Employees',
        component: Employees,
        beforeEnter: (to, from, next) => {
          const userStore = useUserStore();
          const user = userStore.user;
          if (
            user &&
            (user.roles.includes('admin') ||
              user.roles.includes('regeneration'))
          ) {
            next();
          } else {
            next('/');
          }
        },
      },
      {
        path: '/ustawienia',
        name: 'Settings',
        component: Settings,
        beforeEnter: (to, from, next) => {
          const userStore = useUserStore();
          const user = userStore.user;
          if (
            user &&
            (user.roles.includes('admin') ||
              user.roles.includes('regeneration'))
          ) {
            next();
          } else {
            next('/');
          }
        },
      },
    ],
    beforeEnter: async (to, from, next) => {
      try {
        const userStore = useUserStore();
        await userStore.fetchUser();
        next();
      } catch (error) {
        console.error('Failed to fetch user', error);
        next('/login');
      }
    },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
