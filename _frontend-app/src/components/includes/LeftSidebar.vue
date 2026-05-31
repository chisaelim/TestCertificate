<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <router-link to="/" class="brand-link">
      <img :src="logoImage" alt="Chat System Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Chat System</span>
    </router-link>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img :src="userStore.profile_thumbnail || emptyImage" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <router-link :to="{ name: 'profile' }" class="d-block">{{ userStore.name }}</router-link>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <router-link :to="{ name: 'dashboard' }" active-class="active" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </router-link>
          </li>
          <li class="nav-header">
            Certificate Management
          </li>

          <li class="nav-item">
            <router-link :to="{ name: 'student-tests' }" active-class="active" class="nav-link">
              <i class="nav-icon fas fa-user-graduate"></i>
              <p>Student Tests</p>
            </router-link>
          </li>


          <li class="nav-header">
            Academic Management
          </li>

          <li class="nav-item">
            <router-link :to="{ name: 'students' }" active-class="active" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Students</p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link :to="{ name: 'tests' }" active-class="active" class="nav-link">
              <i class="nav-icon fas fa-vial"></i>
              <p>Tests</p>
            </router-link>
          </li>

          <template v-if="userStore.isAdministrator">
            <li class="nav-header">
              Administration
            </li>

            <li class="nav-item">
              <router-link :to="{ name: 'users' }" active-class="active" class="nav-link">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>User Management</p>
              </router-link>
            </li>
          </template>

        </ul>
      </nav>
    </div>
  </aside>
</template>
<script setup>
import emptyImage from '@/assets/images/emptyImage.png';
import logoImage from '@/assets/images/logoImage.webp';
import { useUserStore } from '@/stores/user';
const route = useRoute();

const userStore = useUserStore();

function syncTreeviewActiveState() {
  $('[data-widget="treeview"]').Treeview("init");
  const $navItems = $('li.nav-item:has(ul.nav-treeview)');
  $navItems.removeClass("menu-is-opening menu-open");
  $navItems.children("ul.nav-treeview").hide();
  $navItems.has("a.nav-link.active").addClass("menu-is-opening menu-open").children("ul.nav-treeview").show();
}

onMounted(() => {
  nextTick(syncTreeviewActiveState);
});

watch(() => route.fullPath, () => nextTick(syncTreeviewActiveState));
</script>
