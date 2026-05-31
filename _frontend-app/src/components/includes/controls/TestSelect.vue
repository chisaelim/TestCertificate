<template>
  <VueMultiSelect v-model="model" :options="options" track-by="id" label="name_en" :allow-empty="true"
    :searchable="true" :deselect-label="'Selected'" :custom-label="customName">
  </VueMultiSelect>
</template>

<script setup>
const model = defineModel({ required: true });
const props = defineProps({
  options: {
    type: Array,
    required: true,
  }
});
function customName({ name_en, name_kh }) {
  return name_kh + ' - ' + name_en;
}
</script>
