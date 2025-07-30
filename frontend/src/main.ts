import { createApp } from 'vue';
import Toast from 'vue-toastification';
import { createRouter, createWebHistory } from 'vue-router';
import 'vue-toastification/dist/index.css';

import App from './App.vue';
import CampaignList from './components/CampaignList.vue';
import NewCampaign from './components/NewCampaign.vue';
import DonationForm from './components/DonationForm.vue';
import Login from './components/auth/Login.vue';
import Signup from './components/auth/Signup.vue';
import Success from './components/Success.vue';
import CampaignDetails from './components/CampaignDetails.vue';

import { isLoggedIn, fetchUser } from './composables/useAuth';
import NotFound from './components/NotFound.vue';
import './style.css';


const routes = [
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Signup', component: Signup },
  { path: '/', name: 'Home', component: CampaignList },
  { path: '/new', name: 'NewCampaign', component: NewCampaign },
  { path: '/donate/:id', name: 'Donate', component: DonationForm, props: true },
  { path: '/campaign/:id', name: 'CampaignDetails', component: CampaignDetails, props: true },
  { path: '/success', name: 'Success', component: Success },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
];



async function bootstrap() {
  await fetchUser();

  const router = createRouter({
    history: createWebHistory(),
    routes,
  });

  // Global auth guard
  router.beforeEach((to, from, next) => {
    const publicPages = ['Login', 'Signup', 'NotFound', 'Success'];
    if (publicPages.includes(to.name as string)) {
      next();
    } else {
      if (isLoggedIn()) {
        next();
      } else {
        next({ name: 'Login' });
      }
    }
  });

  const app = createApp(App);
  app.use(router);
  app.use(Toast, { position: 'top-right', timeout: 3000 });
  app.mount('#app');
}

bootstrap();

