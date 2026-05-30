import Profile from '@/components/auth/Profile.vue';
import ResetPassword from '@/components/auth/ResetPassword.vue';
import SetNewPassword from '@/components/auth/SetNewPassword.vue';
import Signin from '@/components/auth/Signin.vue';
import Signout from '@/components/auth/Signout.vue';
import Signup from '@/components/auth/Signup.vue';
import VerifyEmail from '@/components/auth/VerifyEmail.vue';
import GoogleOAuth from '@/components/google-oauth/GoogleOAuth.vue';
import Dashboard from '@/components/pages/Dashboard.vue';
import { createRouter, createWebHistory } from 'vue-router';


import Navbar from "@/components/includes/Navbar.vue";
import LeftSidebar from "@/components/includes/LeftSidebar.vue";
import RightSidebar from "@/components/includes/RightSidebar.vue";
import Footer from "@/components/includes/Footer.vue";

import Province from '@/components/pages/geographies/Province.vue';
import District from '@/components/pages/geographies/District.vue';
import Commune from '@/components/pages/geographies/Commune.vue';
import Village from '@/components/pages/geographies/Village.vue';

const includeComponents = {
  navbar: Navbar,
  left_sidebar: LeftSidebar,
  right_sidebar: RightSidebar,
  footer: Footer,
};

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'auth.signin',
      component: Signin,
      meta: { guarded: false },
    },
    {
      path: '/signout',
      name: 'auth.signout',
      component: Signout,
      // This route has no guarded meta because it use for both authenticated and unauthenticated users.
      // The authentication state will be handled in the Signout component.
    },
    {
      path: '/signup',
      name: 'auth.signup',
      component: Signup,
      meta: { guarded: false },
    },
    {
      path: '/verify/email',
      name: 'auth.verify.email',
      component: VerifyEmail,
      meta: { guarded: false },
    },
    {
      path: '/reset-password',
      name: 'auth.reset-password',
      component: ResetPassword,
      meta: { guarded: false },
    },
    {
      path: '/set-new-password',
      name: 'auth.set-new-password',
      component: SetNewPassword,
      meta: { guarded: false },
    },
    {
      path: '/google/oauth/callback',
      name: 'auth.google.oauth.callback',
      component: GoogleOAuth,
      meta: { guarded: false },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      components: {
        default: Dashboard,
        ...includeComponents
      },
      meta: { guarded: true },
    },
    {
      path: '/profile',
      name: 'profile',
      components: {
        default: Profile,
        ...includeComponents
      },
      meta: { guarded: true },
    },
    {
      path: '/provinces',
      name: 'provinces',
      components: {
        default: Province,
        ...includeComponents
      },
      meta: { guarded: true, levels: ['_ADMINISTRATOR_'] },

    },
    {
      path: '/districts/:id_province(\\d+)?',
      name: 'districts',
      components: {
        default: District,
        ...includeComponents
      },
      meta: { guarded: true, levels: ['_ADMINISTRATOR_'] },

    },
    {
      path: '/communes/:id_province(\\d+)?/:id_district(\\d+)?',
      name: 'communes',
      components: {
        default: Commune,
        ...includeComponents
      },
      meta: { guarded: true, levels: ['_ADMINISTRATOR_'] },

    },
    {
      path: '/villages/:id_province(\\d+)?/:id_district(\\d+)?/:id_commune(\\d+)?',
      name: 'villages',
      components: {
        default: Village,
        ...includeComponents
      },
      meta: { guarded: true, levels: ['_ADMINISTRATOR_'] },

    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/dashboard',
    }
  ],
})

export default router
