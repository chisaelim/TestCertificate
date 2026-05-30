<template>
  <div class="content-wrapper">
    <div class="content-header">
      <ContentHeader title="ឃុំ - សង្កាត់" />
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="row mx-3 mt-3">
            <div class="form-group col-md-6">
              <GeoSelect v-model="x_province" v-model:options="provinces" label="Capitals / Provinces"
                cache-tag="province" />
            </div>
            <div class="form-group col-md-6">
              <GeoSelect v-model="x_district" v-model:options="districts" label="Municipalities / Sections / Districts"
                cache-tag="district" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងឃុំ - សង្កាត់'" :data="communes" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="commune-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form @submit.prevent="saveCommune()">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងឃុំ - សង្កាត់</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Name (Khmer)</label>
              <input v-model="communeObj.name_kh" type="text" class="form-control"
                :class="{ 'is-invalid': communeErrObj.name_kh !== null }" />
              <div class="invalid-feedback">
                {{ communeErrObj.name_kh }}
              </div>
            </div>

            <div class="form-group">
              <label>Name (English)</label>
              <input v-model="communeObj.name_en" type="text" class="form-control"
                :class="{ 'is-invalid': communeErrObj.name_en !== null }" />
              <div class="invalid-feedback">
                {{ communeErrObj.name_en }}
              </div>
            </div>
            <div class="form-group">
              <label>Name (Latin)</label>
              <input v-model="communeObj.name_latin" type="text" class="form-control"
                :class="{ 'is-invalid': communeErrObj.name_latin !== null }" />
              <div class="invalid-feedback">
                {{ communeErrObj.name_latin }}
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>

const x_province = ref(null);
const x_district = ref(null);

const provinces = ref([]);
const districts = ref([]);
const communes = ref([]);
const columns = [
  {
    accessorKey: "name_kh",
    header: "Name (Khmer)",
  },
  {
    accessorKey: "name_en",
    header: "Name (English)",
  },
  {
    accessorKey: "name_latin",
    header: "Name (Latin)",
  },

  {
    accessorKey: "action",
    header: () => [
      "ជម្រើស",
      h(
        "button",
        {
          onClick: () => showCommuneModal(),
          class: "btn btn-sm btn-success ml-3",
        },
        "Create New"
      ),
    ],
    cell: ({ row }) => [
      // delete btn
      h(
        "button",
        {
          onClick: () => removeCommune(row.original.id_geography),
          class: "btn btn-sm btn-outline-danger mx-1",
        },
        h("i", { class: "fas fa-trash" })
      ),
      // view btn
      h(
        "button",
        {
          onClick: () => viewCommune(row.original.id_geography),
          class: "btn btn-sm btn-outline-secondary mx-1",
        },
        h("i", { class: "fas fa-pencil-alt" })
      ),
    ],
    enableSorting: false,
  },
];
const communeObj = reactive({
  id_province: computed(() => x_province.value?.id_geography),
  id_district: computed(() => x_district.value?.id_geography),
  id_commune: null,
  name_kh: null,
  name_en: null,
  name_latin: null,
});
const communeErrObj = reactive({
  name_kh: null,
  name_en: null,
  name_latin: null,
});

const defaultCommuneObj = JSON.parse(
  JSON.stringify(new OBJ(communeObj).omit(["id_province", "id_district"]))
);
const defaultCommuneErrObj = JSON.parse(JSON.stringify(communeErrObj));

function resetCommuneObj() {
  Object.assign(communeObj, JSON.parse(JSON.stringify(defaultCommuneObj)));
  Object.assign(communeErrObj, JSON.parse(JSON.stringify(defaultCommuneErrObj)));
}

onMounted(async () => {
  $("#commune-modal").on("hide.bs.modal", function () {
    resetCommuneObj();
  });
  try {
    LoadingModal();
    await generateProvinces();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
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
      return ErrorModal(error);
    }
  }
});

watch(x_district, async (newValue) => {
  communes.value = [];
  communeObj.id_commune = null;
  if (newValue) {
    try {
      LoadingModal();
      await generateCommunesByDistrict();
      return CloseModal();
    } catch (error) {
      return ErrorModal(error);
    }
  }
});

async function generateProvinces() {
  try {
    const res = await apiGetProvinces();
    provinces.value = res.data.provinces;
  } catch (error) {
    throw error;
  }
}
async function generateDistrictsByProvince() {
  try {
    const res = await apiGetDistrictsByProvinceID(x_province.value?.id_geography);
    districts.value = res.data.districts;
  } catch (error) {
    throw error;
  }
}
async function generateCommunesByDistrict() {
  try {
    const res = await apiGetCommunesByDistrictID(x_district.value?.id_geography);
    communes.value = res.data.communes;
  } catch (error) {
    throw error;
  }
}

async function saveCommune() {
  try {
    LoadingModal();
    let res = null;
    if (communeObj.id_commune === null) {
      res = await apiCreateCommune(communeObj);
      onCommuneCreated(res.data.commune);
    } else {
      res = await apiUpdateCommune(communeObj);
      onCommuneUpdated(res.data.commune);
    }
    hideCommuneModal();
    return MessageModal("success", "Success", res.data.message);
  } catch (error) {
    if (error.response?.status === 422) {
      Object.assign(
        communeErrObj,
        defaultCommuneErrObj,
        mutateErrorObject(error.response.data.errors)
      );
      return CloseModal();
    }
    return ErrorModal(error);
  }
}

async function viewCommune(id_commune) {
  try {
    LoadingModal();
    const res = await apiReadCommune(id_commune);
    Object.assign(communeObj, res.data.commune);
    communeObj.id_commune = res.data.commune.id_geography;
    showCommuneModal();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
}
async function removeCommune(id_commune) {
  $swal
    .fire({
      title: "Want to delete the commune ?",
      html: "<pre>" + "Please make a confirmation." + "</pre>",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      confirmButtonText: "Yes, Delete it.",
    })
    .then(async (sw) => {
      if (sw.isConfirmed) {
        try {
          LoadingModal();
          const res = await apiDeleteCommune(id_commune);
          onCommuneDeleted(res.data.commune);
          return MessageModal("success", "Success", res.data.message);
        } catch (error) {
          return ErrorModal(error);
        }
      }
    });
}

function onCommuneCreated(created_commune) {
  communes.value = [created_commune, ...communes.value];
}
function onCommuneUpdated(updated_commune) {
  communes.value = communes.value.map((obj) =>
    obj.id_geography !== updated_commune.id_geography ? obj : updated_commune
  );
}
function onCommuneDeleted(deleted_commune) {
  communes.value = communes.value.filter(
    (obj) => obj.id_geography !== deleted_commune.id_geography
  );
}

function showCommuneModal() {
  $("#commune-modal").modal("show");
}
function hideCommuneModal() {
  $("#commune-modal").modal("hide");
}
</script>
