<template>
  <label>{{ label }}</label>
  <VueMultiselect v-bind="$attrs" v-model="model" :options="options" track-by="id" label="name_kh" :allow-empty="true"
    :searchable="true" :deselect-label="''" :select-label="''">
  </VueMultiselect>

</template>
<script setup>
useAttrs({
  inheritAttrs: false,
});
const model = defineModel({
  type: [Object, null],
  default: null,
  required: true,
});
const options = defineModel('options', { required: true, default: () => [] });
const props = defineProps({
  cacheTag: {
    type: String,
    default: 'geography',
  },
  label: {
    type: String,
    default: 'Geography',
  },
  paramKey: {
    type: String,
    default: 'xxx-',
  },
});

const cacheKey = computed(() => `${props.paramKey}${props.cacheTag}`);

watch(model, (value) => {
  applyCachedValue(value);
});

watch(
  () => props.options,
  (options) => {
    if (options.length > 0) {
      const url = new URL(window.location.href);
      const key = url.searchParams.get(cacheKey.value);

      model.value = options.find((option) => option.id == key) ?? options[0];
      return;
    }
    model.value = null;
  },
  { deep: true }
);

const applyCachedValue = (selected) => {
  const url = new URL(window.location.href);
  if (!selected) {
    url.searchParams.delete(cacheKey.value);
  } else {
    url.searchParams.set(cacheKey.value, selected.id);
  }
  window.history.replaceState({}, '', url);
};
onBeforeUnmount(() => {
  const url = new URL(window.location.href);
  url.searchParams.delete(cacheKey.value);
  window.history.replaceState({}, '', url);
});
</script>
