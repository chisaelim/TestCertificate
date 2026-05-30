<template>
  <div class="modal fade" id="student-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <form @submit.prevent="saveStudent()">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងទិន្នន័យសិស្ស</h5>
            <button type="button" class="close" @click="hideStudentModal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="row">
                  <div class="col-lg-3">
                    <CropperModal v-model="studentObj.photo" v-model:current="currentImage"
                      v-model:error="studentErrObj.photo" :width="454" :height="454" />
                  </div>
                  <div class="col-lg-9">
                    <div class="row">
                      <div class="form-group col-lg-6">
                        <label>ឈ្មោះជាអក្សរខ្មែរ</label>
                        <input v-model="studentObj.name_kh" type="text" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.name_kh }">
                        <div class="invalid-feedback">
                          {{ studentErrObj.name_kh }}
                        </div>
                      </div>
                      <div class="form-group col-lg-6">
                        <label>ឈ្មោះជាអក្សរឡាតាំង</label>
                        <input v-model="studentObj.name_en" type="text" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.name_en }">
                        <div class="invalid-feedback">
                          {{ studentErrObj.name_en }}
                        </div>
                      </div>

                    </div>
                    <div class="row">
                      <div class="form-group col-lg-4">
                        <label>ភេទ</label>
                        <select v-model="studentObj.gender_id" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.gender_id }">
                          <option v-for="{ id, gd_kh_full } in genders" :key="id" :value="id">
                            {{ gd_kh_full }}
                          </option>
                        </select>
                        <div class="invalid-feedback">
                          {{ studentErrObj.gender_id }}
                        </div>
                      </div>
                      <div class="form-group col-lg-4">
                        <label>ថ្ងៃខែឆ្នាំកំណើត</label>
                        <VueDatePicker v-model="studentObj.dob" :formats="{ input: 'dd-MM-yyyy' }"
                          model-type="dd-MM-yyyy" :time-config="{ enableTimePicker: false }"
                          :class="{ 'is-invalid': !!studentErrObj.dob }" />
                        <div class="invalid-feedback">
                          {{ studentErrObj.dob }}
                        </div>
                      </div>
                      <div class="form-group col-lg-4">
                        <label>លេខទូរស័ព្ទ</label>
                        <div class="input-group">
                          <input v-model="studentObj.phone" type="text" class="form-control"
                            :class="{ 'is-invalid': !!studentErrObj.phone }">
                          <div class="input-group-append">
                            <div class="input-group-text">
                              <span class="fas fa-phone"></span>
                            </div>
                          </div>
                          <div class="invalid-feedback">
                            {{ studentErrObj.phone }}
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-lg-4">
                        <label>ជនជាតិ</label>
                        <select v-model="studentObj.ethnicity_id" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.ethnicity_id }">
                          <option v-for="{ id, eth_kh } in ethnicities" :key="id" :value="id">
                            {{ eth_kh }}
                          </option>
                        </select>
                        <div class="invalid-feedback">
                          {{ studentErrObj.ethnicity_id }}
                        </div>
                      </div>
                      <div class="form-group col-lg-4">
                        <label>សញ្ជាតិ</label>
                        <select v-model="studentObj.nationality_id" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.nationality_id }">
                          <option v-for="{ id, nat_kh } in nationalities" :key="id" :value="id">
                            {{ nat_kh }}
                          </option>
                        </select>
                        <div class="invalid-feedback">
                          {{ studentErrObj.nationality_id }}
                        </div>
                      </div>
                      <div class="form-group col-lg-4">
                        <label>សាសនា</label>
                        <select v-model="studentObj.religion_id" class="form-control"
                          :class="{ 'is-invalid': !!studentErrObj.religion_id }">
                          <option v-for="{ id, rel_kh } in religions" :key="id" :value="id">
                            {{ rel_kh }}
                          </option>
                        </select>
                        <div class="invalid-feedback">
                          {{ studentErrObj.religion_id }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <h6 class="font-weight-bold mt-2">ទីកន្លែងកំណើត (POB)</h6>
                <div class="row">
                  <div class="form-group col-lg-3">
                    <label>ខេត្ត / រាជធានី</label>
                    <select v-model="studentObj.pob_province_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.pob_province_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in provinces" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.pob_province_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ស្រុក / ខណ្ឌ / ក្រុង</label>
                    <select v-model="studentObj.pob_district_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.pob_district_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in pobDistricts" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.pob_district_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ឃុំ / សង្កាត់</label>
                    <select v-model="studentObj.pob_commune_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.pob_commune_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in pobCommunes" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.pob_commune_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ភូមិ</label>
                    <select v-model="studentObj.pob_village_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.pob_village_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in pobVillages" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.pob_village_id }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <h6 class="font-weight-bold mt-2">អាសយដ្ឋានបច្ចុប្បន្ន (POR)</h6>
                <div class="row">
                  <div class="form-group col-lg-3">
                    <label>ខេត្ត / រាជធានី</label>
                    <select v-model="studentObj.por_province_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.por_province_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in provinces" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.por_province_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ស្រុក / ខណ្ឌ / ក្រុង</label>
                    <select v-model="studentObj.por_district_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.por_district_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in porDistricts" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.por_district_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ឃុំ / សង្កាត់</label>
                    <select v-model="studentObj.por_commune_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.por_commune_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in porCommunes" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.por_commune_id }}
                    </div>
                  </div>
                  <div class="form-group col-lg-3">
                    <label>ភូមិ</label>
                    <select v-model="studentObj.por_village_id" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.por_village_id }">
                      <option :value="null">---none---</option>
                      <option v-for="{ id, name_kh } in porVillages" :key="id" :value="id">
                        {{ name_kh }}
                      </option>
                    </select>
                    <div class="invalid-feedback">
                      {{ studentErrObj.por_village_id }}
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-lg-6">
                    <label>លេខផ្ទះ</label>
                    <input v-model="studentObj.home_no" type="text" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.home_no }">
                    <div class="invalid-feedback">
                      {{ studentErrObj.home_no }}
                    </div>
                  </div>
                  <div class="form-group col-lg-6">
                    <label>លេខផ្លូវ</label>
                    <input v-model="studentObj.street_no" type="text" class="form-control"
                      :class="{ 'is-invalid': !!studentErrObj.street_no }">
                    <div class="invalid-feedback">
                      {{ studentErrObj.street_no }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" @click="hideStudentModal">បោះបង់</button>
            <button type="submit" class="btn btn-primary">រក្សាទុក</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  onCreated: {
    type: Function,
  },
  onUpdated: {
    type: Function,
  },
  onDeleted: {
    type: Function,
  },
});

const currentImage = ref(null);

const genders = ref([]);
const nationalities = ref([]);
const ethnicities = ref([]);
const religions = ref([]);
const provinces = ref([]);
const pobDistricts = ref([]);
const pobCommunes = ref([]);
const pobVillages = ref([]);

const porDistricts = ref([]);
const porCommunes = ref([]);
const porVillages = ref([]);

const studentObj = reactive({
  id: null,
  name_en: null,
  name_kh: null,
  dob: null,
  home_no: null,
  street_no: null,
  phone: null,
  photo: null,
  gender_id: 1,
  nationality_id: 1,
  ethnicity_id: 1,
  religion_id: 1,
  pob_province_id: null,
  pob_district_id: null,
  pob_commune_id: null,
  pob_village_id: null,
  por_province_id: null,
  por_district_id: null,
  por_commune_id: null,
  por_village_id: null,
});
const studentErrObj = reactive({
  name_en: null,
  name_kh: null,
  dob: null,
  home_no: null,
  street_no: null,
  phone: null,
  photo: null,
  gender_id: null,
  nationality_id: null,
  ethnicity_id: null,
  religion_id: null,
  pob_province_id: null,
  pob_district_id: null,
  pob_commune_id: null,
  pob_village_id: null,
  por_province_id: null,
  por_district_id: null,
  por_commune_id: null,
  por_village_id: null,
});

const defaultStudentObj = JSON.parse(JSON.stringify(studentObj));
const defaultStudentErrObj = JSON.parse(JSON.stringify(studentErrObj));

onMounted(async () => {
  $('#student-modal').on('hide.bs.modal', function () {
    Object.assign(studentObj, JSON.parse(JSON.stringify(defaultStudentObj)));
    Object.assign(studentErrObj, JSON.parse(JSON.stringify(defaultStudentErrObj)));
  });
  try {
    LoadingModal();
    await Promise.all([
      generateGenders(),
      generateNationalities(),
      generateEthnicities(),
      generateReligions(),
      generateProvinces()
    ]);
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
});
watch(() => studentObj.pob_province_id, async (nv, ov) => {
  console.log(nv);
  const response = await generateDistrictsByProvince(nv);
  pobDistricts.value = response.data.districts;
  if (!pobDistricts.value.find(d => d.id === studentObj.pob_district_id)) {
    studentObj.pob_district_id = null;
  }
});
watch(() => studentObj.pob_district_id, async (nv, ov) => {
  const response = await generateCommunesByDistrict(nv);
  pobCommunes.value = response.data.communes;
  if (!pobCommunes.value.find(c => c.id === studentObj.pob_commune_id)) {
    studentObj.pob_commune_id = null;
  }
});
watch(() => studentObj.pob_commune_id, async (nv, ov) => {
  const response = await generateVillagesByCommune(nv);
  pobVillages.value = response.data.villages;
  if (!pobVillages.value.find(v => v.id === studentObj.pob_village_id)) {
    studentObj.pob_village_id = null;
  }
});

// POR geography watchers
watch(() => studentObj.por_province_id, async (nv, ov) => {
  const response = await generateDistrictsByProvince(nv);
  porDistricts.value = response.data.districts;
  if (!porDistricts.value.find(d => d.id === studentObj.por_district_id)) {
    studentObj.por_district_id = null;
  }
});
watch(() => studentObj.por_district_id, async (nv, ov) => {
  const response = await generateCommunesByDistrict(nv);
  porCommunes.value = response.data.communes;
  if (!porCommunes.value.find(c => c.id === studentObj.por_commune_id)) {
    studentObj.por_commune_id = null;
  }
});
watch(() => studentObj.por_commune_id, async (nv, ov) => {
  const response = await generateVillagesByCommune(nv);
  porVillages.value = response.data.villages;
  if (!porVillages.value.find(v => v.id === studentObj.por_village_id)) {
    studentObj.por_village_id = null;
  }
});

async function buildFormData(data, includePhoto) {
  const form = new FormData();
  Object.entries(data).forEach(([key, value]) => {
    if (key === 'photo') return;
    if (value !== null && value !== undefined) form.append(key, value);
  });
  if (includePhoto && data.photo) {
    const blob = await (await fetch(data.photo)).blob();
    form.append('photo', blob, 'photo.jpg');
  }
  return form;
}

async function saveStudent() {
  try {
    LoadingModal();
    let res = null;
    if (studentObj.id === null) {
      res = await apiCreateStudent(await buildFormData(studentObj, true));
      props.onCreated(res.data.student);
    } else {
      const photoChanged = currentImage.value !== studentObj.photo;
      res = await apiUpdateStudent(await buildFormData(studentObj, photoChanged));
      props.onUpdated(res.data.student);
    }
    hideStudentModal();
    return MessageModal({ icon: "success", title: "Success", text: res.data.message });
  } catch (error) {
    const { response } = error;
    if (!response) {
      return MessageModal({ icon: "error", title: "Error", text: error.message });
    }
    const { status, data } = response;
    if (status === 422) {
      Object.keys(studentErrObj).forEach((key) => {
        studentErrObj[key] = data.errors[key]
          ? data.errors[key][0]
          : "";
      });
      return CloseModal();
    }
    return MessageModal({ icon: "error", title: "Error", text: data.message });
  }
}
async function viewStudent(id) {
  try {
    LoadingModal();
    const res = await apiReadStudent(id);
    const { photo, ...newStudentObj } = res.data.student;
    Object.assign(studentObj, newStudentObj);
    studentObj.photo = photo;
    currentImage.value = photo;
    showStudentModal();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
}
async function removeStudent(id) {
  Swal.fire({
    title: 'Want to delete the student ?',
    html: '<pre>' + "Please make a confirmation." + '</pre>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Yes, Delete it.'
  }).then(async (sw) => {
    if (sw.isConfirmed) {
      try {
        LoadingModal();
        const res = await apiDeleteStudent(id);
        const { student, message } = res.data;
        props.onDeleted(student);
        return MessageModal({ icon: "success", title: "Success", text: message });
      } catch (error) {
        return ErrorModal(error);
      }
    }
  });
}

async function generateGenders() {
  try {
    const res = await apiGetAllGenders();
    genders.value = res.data.genders;
  } catch (error) {
    throw error;
  }
}
async function generateNationalities() {
  try {
    const res = await apiGetAllNationalities();
    nationalities.value = res.data.nationalities;
  } catch (error) {
    throw error;
  }
}
async function generateEthnicities() {
  try {
    const res = await apiGetAllEthnicities();
    ethnicities.value = res.data.ethnicities;
  } catch (error) {
    throw error;
  }
}
async function generateReligions() {
  try {
    const res = await apiGetAllReligions();
    religions.value = res.data.religions;
  } catch (error) {
    throw error;
  }
}
async function generateProvinces() {
  try {
    const res = await apiGetProvinces();
    provinces.value = res.data.provinces;
  } catch (error) {
    throw error;
  }
}
async function generateDistrictsByProvince(id) {
  try {
    const res = await apiGetDistrictsByProvince(id);
    return res;
  } catch (error) {
    throw error;
  }
}
async function generateCommunesByDistrict(id) {
  try {
    const res = await apiGetCommunesByDistrict(id);
    return res;
  } catch (error) {
    throw error;
  }
}
async function generateVillagesByCommune(id) {
  try {
    const res = await apiGetVillagesByCommune(id);
    return res;
  } catch (error) {
    throw error;
  }
}

const showStudentModal = () => $('#student-modal').modal('show');
const hideStudentModal = () => $('#student-modal').modal('hide');

defineExpose({
  showStudentModal,
  hideStudentModal,
  removeStudent,
  viewStudent
});
</script>
