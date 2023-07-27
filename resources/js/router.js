import VueRouter from 'vue-router';
import store from './store/index.js'

import { SET_PAGE_TITLE, SET_PAGE_CTA } from './constants';

const Dashboard = () => import('./views/Dashboard');
const Login = () => import('./views/auth/Login');
const ResetPassword = () => import('./views/auth/ResetPassword');
const SetPassword = () => import('./views/auth/SetPassword');
const NotFound = () => import('./views/NotFound.vue');

const Contacten = () => import('./views/contacten/index.vue');
const ContactenCreate = () => import('./views/contacten/create.vue');
const ContactenEdit = () => import('./views/contacten/edit.vue');

const Delivery = () => import('./views/delivery/index.vue');
const DeliveryOverview = () => import('./views/delivery/overview.vue');
const DeliveryDetail = () => import('./views/delivery/detail.vue');

const History = () => import('./views/history/index.vue');

const Borderels = () => import('./views/borderels/index.vue');



// Routes
const routes = [
  { path: '/',                    name: 'Home', component: Dashboard, meta: { requiresLogin: true } },
  { path: '/dashboard',           name: 'Dashboard', component: Dashboard, meta: { requiresLogin: true } },
  { path: '/login',               name: 'Login', component: Login },
  { path: '/reset-wachtwoord',    name: 'ResetPassword', component: ResetPassword },
  { path: '/nieuw-wachtwoord',    name: 'SetPassword', component: SetPassword },

  { path: '/users',               name: 'Contacten', component: Contacten, meta: { requiresLogin: true } },
  { path: '/users/create',        name: 'ContactenCreate', component: ContactenCreate, meta: { requiresLogin: true } },
  { path: '/users/edit/:id',      name: 'ContactenEdit', component: ContactenEdit, meta: { requiresLogin: true } },

  { path: '/history',               name: 'History', component: History, meta: { requiresLogin: true } },

  { path: '/delivery',               name: 'Delivery', component: Delivery, meta: { requiresLogin: true } },
  { path: '/delivery/overview',      name: 'DeliveryOverview', component: DeliveryOverview, meta: { requiresLogin: true } },
  { path: '/delivery/:id',          name: 'DeliveryDetail', component: DeliveryDetail, meta: { requiresLogin: true } },

  { path: '/borderels',               name: 'Contacten', component: Borderels, meta: { requiresLogin: true } },

  { path: '*',            name: 'NotFound', component: NotFound }
];

const router = new VueRouter({
  routes,
  scrollBehavior(to, from, savedPosition) {
    return { x: 0, y: 0 };
  },
});

router.beforeEach((to, from, next) => {
  const key = JSON.parse(localStorage.getItem('key'));
  
  store.commit('appStore/' + SET_PAGE_TITLE, null);
  store.commit('appStore/' + SET_PAGE_CTA, null);

  if(store.getters.isAuthenticated) {
    next();
  } else {
    const now = new Date().getTime();
    const alive = now - 3600000; // 1 hour in milliseconds
    if (to.matched.some(record => record.meta.requiresLogin) && (!key || alive > key.expires)) {
      next('/login');
    } else {
      next();
    }
  }
});

export default router;
