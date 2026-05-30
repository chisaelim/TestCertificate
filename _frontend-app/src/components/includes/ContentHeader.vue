<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h1 class="m-0">{{ title }}</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item">
            <router-link :to="{ name: 'dashboard' }">Home</router-link>
          </li>
          <li class="breadcrumb-item active">{{ title }}</li>
        </ol>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "Title",
  },
});
</script>
