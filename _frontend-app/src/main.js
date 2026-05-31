import axios from 'axios';
window.axios = axios;
import jquery from 'jquery';
window.$ = window.jQuery = jquery;
import Swal from 'sweetalert2';
window.Swal = Swal;
import { uuidv7 } from "uuidv7";
window.uuid = uuidv7;
import moment from 'moment';
window.moment = moment;
import pdfMake from 'pdfmake';
import vfsFonts from '@/assets/vfs_fonts.js';
pdfMake.addVirtualFileSystem(vfsFonts);
pdfMake.addFonts({
  Roboto: {
    normal: 'Roboto-Regular.ttf',
    bold: 'Roboto-Bold.ttf',
    italics: 'Roboto-Italic.ttf',
    bolditalics: 'Roboto-BoldItalic.ttf',
  },
  Arial: {
    normal: 'ARIAL.TTF',
    bold: 'ARIALBD.TTF',
    italics: 'ARIALI.TTF',
    bolditalics: 'ARIALBI.TTF',
  },
  Times: {
    normal: 'times.ttf',
    bold: 'timesbd.ttf',
    italics: 'timesi.ttf',
    bolditalics: 'timesbi.ttf',
  },
  KhmerOSMoul: {
    normal: 'KHMER OS MOUL REGULAR.ttf',
  },
  KhmerOSBattambong: {
    normal: 'KHMER OS BATTAMBANG REGULAR.ttf',
    bold: 'KHMER OS BATTAMBANG - BOLD.ttf',
  },
});
window.pdfMake = pdfMake;

import { LoadingModal, MessageModal, CloseModal } from './functions/swal';
window.LoadingModal = LoadingModal;
window.MessageModal = MessageModal;
window.CloseModal = CloseModal;

window.APP_URL = import.meta.env.VITE_APP_URL;
window.APP_API_URL = import.meta.env.VITE_APP_API_URL;
window.APP_VERIFY_EMAIL_URL = import.meta.env.VITE_APP_VERIFY_EMAIL_URL;
window.APP_RESET_PASSWORD_URL = import.meta.env.VITE_APP_RESET_PASSWORD_URL;
window.APP_GOOGLE_OAUTH_CALLBACK_URL = import.meta.env.VITE_APP_GOOGLE_OAUTH_CALLBACK_URL;

import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from './App.vue'
import router from './router'
import { useUserStore } from '@/stores/user';
import { apiVerify } from '@/functions/api/auth';
import VueMultiSelect from 'vue-multiselect';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const app = createApp(App)

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);
app.use(router);
app.component('VueMultiSelect', VueMultiSelect);
app.component('VueDatePicker', VueDatePicker);
app.mount('#app');


const userStore = useUserStore(pinia);
// Set up Axios interceptor to add Authorization header dynamically
// Only when the token is available and not already set in the request
axios.interceptors.request.use((config) => {
  const token = userStore.getSanctumToken();
  if (token && !config.headers.Authorization) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

router.beforeEach(async (to, from) => {
  const { guarded } = to.meta;
  if (guarded === undefined) { // if the route is not guarded, we don't need to verify the token
    return;
  }

  try {
    const response = await apiVerify();
    const { data } = response;
    userStore.setState(data.user);
  } catch (error) {
    userStore.reset();
  }

  if (guarded && !userStore.isAuthenticated) { // if the route is guarded and the user is not authenticated, redirect to signin page
    return { name: 'auth.signin' };
  }
  if (!guarded && userStore.isAuthenticated) { // if the route is not guarded and the user is authenticated, redirect to dashboard page
    return { name: 'dashboard' };
  }
});
