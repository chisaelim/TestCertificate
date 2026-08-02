<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">ទិន្នន័យសិស្សតាមតំបន់</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">ទំព័រដើម</router-link>
              </li>
              <li class="breadcrumb-item active">ទិន្នន័យសិស្សតាមតំបន់</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="row mx-3 mt-3">
            <div class="form-group col-md-3">
              <GeoSelect v-model="x_province" v-model:options="provinces" label="ខេត្ត / រាជធានី"
                cache-tag="province" />
            </div>
            <div class="form-group col-md-3">
              <GeoSelect v-model="x_district" v-model:options="districts" label="ស្រុក / ខណ្ឌ / ក្រុង"
                cache-tag="district" />
            </div>
            <div class="form-group col-md-3">
              <GeoSelect v-model="x_commune" v-model:options="communes" label="ឃុំ / សង្កាត់" cache-tag="commune" />
            </div>
            <div class="form-group col-md-3">
              <GeoSelect v-model="x_village" v-model:options="villages" label="ភូមិ" cache-tag="village" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងការធ្វើតេស្ត'" :data="student_tests" :columns="student_test_columns" />


          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import emptyImage from '@/assets/images/emptyImage.png';

const x_province = ref(null);
const x_district = ref(null);
const x_commune = ref(null);
const x_village = ref(null);

const provinces = ref([]);
const districts = ref([]);
const communes = ref([]);
const villages = ref([]);
const students = ref([]);

const student_tests = ref([]);
const selectedGeographyId = computed(() =>
  x_village.value?.id ??
  x_commune.value?.id ??
  x_district.value?.id ??
  x_province.value?.id
);
const student_test_columns = [
  {
    accessorKey: "student.photo",
    header: "",
    cell: (cell) =>
      h("img", {
        style: "max-width: 50px",
        class: "profile-user-img img-fluid img-circle",
        src:
          cell.getValue() || emptyImage,
      }),
  },
  {
    accessorKey: "student.name_kh",
    header: "ឈ្មោះសិស្សជាអក្សរខ្មែរ",
  },
  {
    accessorKey: "student.name_en",
    header: "ឈ្មោះសិស្សជាអក្សរឡាតាំង",
  },
  {
    accessorKey: "student.gender.gd_kh_full",
    header: "ភេទ",
  },
  {
    accessorKey: "test.name_kh",
    header: "ឈ្មោះតេស្តជាភាសាខ្មែរ",
  },
  {
    accessorKey: "test.name_en",
    header: "ឈ្មោះតេស្តជាភាសាអង់គ្លេស",
  },
  {
    accessorKey: "status",
    header: "លទ្ធផល",
    cell: ({ row }) =>
      h(
        "a",
        {
          role: "button",
          class: [
            "badge " +
            ((row.original.status === "PENDING" ? "badge-warning" : "") +
              (row.original.status === "PASSED" ? "badge-success" : "") +
              (row.original.status === "FAILED" ? "badge-danger" : "")),
          ],
        },
        row.original.status
      ),
  },
];

onMounted(async () => {
  try {
    LoadingModal();
    await generateProvinces();
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
  }
});

watch(x_province, async (newValue) => {
  districts.value = [];
  x_district.value = null;
  if (newValue) {
    try {
      LoadingModal();
      await generateDistrictsByProvince();
      return CloseModal();
    } catch (error) {
      return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
    }
  }
});

watch(x_district, async (newValue) => {
  communes.value = [];
  x_commune.value = null;
  if (newValue) {
    try {
      LoadingModal();
      await generateCommunesByDistrict();
      return CloseModal();
    } catch (error) {
      return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
    }
  }
});

watch(x_commune, async (newValue) => {
  villages.value = [];
  x_village.value = null;
  if (newValue) {
    try {
      LoadingModal();
      await generateVillagesByCommune();
      return CloseModal();
    } catch (error) {
      return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
    }
  }
});

watch(x_village, async (newValue) => {
  students.value = [];
});

watch(selectedGeographyId, async (newGeographyId, oldGeographyId) => {
  if (newGeographyId === oldGeographyId) {
    return;
  }

  if (!newGeographyId) {
    student_tests.value = [];
    return;
  }

  try {
    LoadingModal();
    await generateStudentsByGeography();
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
  }
});

async function generateProvinces() {
  const res = await apiGetProvinces();
  provinces.value = res.data.provinces;
}
async function generateDistrictsByProvince() {
  const res = await apiGetDistrictsByProvince(x_province.value?.id);
  districts.value = res.data.districts;
}
async function generateCommunesByDistrict() {
  const res = await apiGetCommunesByDistrict(x_district.value?.id);
  communes.value = res.data.communes;
}
async function generateVillagesByCommune() {
  const res = await apiGetVillagesByCommune(x_commune.value?.id);
  villages.value = res.data.villages;
}
async function generateStudentsByGeography() {
  const res = await apiGetStudentTestsByGeography(selectedGeographyId.value);
  student_tests.value = res.data.student_tests;
}
</script>
