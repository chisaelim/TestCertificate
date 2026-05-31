<template>
  <div class="card card-primary card-outline">
    <div class="card-body box-profile">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>សិស្ស</label>
            <VueMultiSelect v-model="model" :options="options" track-by="id" label="name_kh" :allow-empty="true"
              :searchable="true" :deselect-label="''" :select-label="''" :disabled="disabled"
              :custom-label="customName">
            </VueMultiSelect>

          </div>
        </div>
        <div class="col-md-6">
          <div class="d-flex justify-content-center align-items-center">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle m-3" :src="model?.photo || emptyImage"
                alt="User profile picture">

              <h3 class="profile-username text-center">{{ model?.name_kh ?? '-------' }}</h3>
              <h3 class="profile-username text-center">{{ model?.name_en ?? '-------' }}</h3>
            </div>
          </div>
          <div class="m-3">
            <a v-if="model?.id" @click="StudentModalRef.viewStudent(model?.id)"
              class="btn btn-primary btn-block"><b>កែប្រែ</b></a>
            <a v-if="model?.id" @click="StudentModalRef.removeStudent(model?.id)"
              class="btn btn-danger btn-block"><b>លុបឈ្មោះ</b></a>
          </div>
        </div>
        <div class="col-md-6">
          <ul class="list-group mb-3">
            <li class="list-group-item">
              <a @click="StudentModalRef.showStudentModal" class="btn btn-success btn-block"><b>ចុះឈ្មោះថ្មី</b></a>
            </li>
            <li class="list-group-item">
              <b>ភេទ</b>
              <h6 class="float-right">{{ model?.gender?.gd_kh_full }}</h6>
            </li>
            <li class="list-group-item">
              <b>ថ្ងៃខែឆ្នាំកំណើត</b>
              <h6 class="float-right">{{ model?.dob }}</h6>
            </li>
            <li class="list-group-item">
              <b>លេខទូរស័ព្ទ</b>
              <h6 class="float-right">{{ model?.phone }}</h6>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <StudentModal ref="StudentModalRef" :onCreated="onStudentCreated" :onUpdated="onStudentUpdated"
    :onDeleted="onStudentDeleted" />
</template>

<script setup>
import emptyImage from "@/assets/images/emptyImage.png";

const StudentModalRef = ref();

const model = defineModel({ required: true });
const options = defineModel('options', { required: true });
const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },

});

const onStudentCreated = (student) => {
  options.value = [...options.value, student];
  model.value = student;
};
const onStudentUpdated = (student) => {
  options.value = options.value.map(obj => obj.id !== student.id ? obj : student);
  model.value = student;
};
const onStudentDeleted = (student) => {
  options.value = options.value.filter(obj => obj.id !== student.id);
  model.value = null;
};

function customName({ name_en, name_kh }) {
  return name_kh + ' (' + name_en + ')';
}
</script>
