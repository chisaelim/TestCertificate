<template>
  <div class="content-wrapper">
    <div class="content-header">
      <ContentHeader title="Villages" />
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="row mx-3 mt-3">
            <div class="form-group col-md-4">
              <GeoSelect v-model="x_province" v-model:options="provinces" label="Capitals / Provinces"
                cache-tag="province" />
            </div>
            <div class="form-group col-md-4">
              <GeoSelect v-model="x_district" v-model:options="districts" label="Municipalities / Sections / Districts"
                cache-tag="district" />
            </div>
            <div class="form-group col-md-4">
              <GeoSelect v-model="x_commune" v-model:options="communes" label="Quaters / Communes"
                cache-tag="commune" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'Villages Table'" :data="villages" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="village-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form @submit.prevent="saveVillage()">
          <div class="modal-header">
            <h5 class="modal-title">Manage Villages</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Name (Khmer)</label>
              <input v-model="villageObj.name_kh" type="text" class="form-control"
                :class="{ 'is-invalid': villageErrObj.name_kh !== null }" />
              <div class="invalid-feedback">
                {{ villageErrObj.name_kh }}
              </div>
            </div>

            <div class="form-group">
              <label>Name (English)</label>
              <input v-model="villageObj.name_en" type="text" class="form-control"
                :class="{ 'is-invalid': villageErrObj.name_en !== null }" />
              <div class="invalid-feedback">
                {{ villageErrObj.name_en }}
              </div>
            </div>
            <div class="form-group">
              <label>Name (Latin)</label>
              <input v-model="villageObj.name_latin" type="text" class="form-control"
                :class="{ 'is-invalid': villageErrObj.name_latin !== null }" />
              <div class="invalid-feedback">
                {{ villageErrObj.name_latin }}
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
const x_commune = ref(null);

const provinces = ref([]);
const districts = ref([]);
const communes = ref([]);
const villages = ref([]);
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
          onClick: () => showVillageModal(),
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
          onClick: () => removeVillage(row.original.id_geography),
          class: "btn btn-sm btn-outline-danger mx-1",
        },
        h("i", { class: "fas fa-trash" })
      ),
      // view btn
      h(
        "button",
        {
          onClick: () => viewVillage(row.original.id_geography),
          class: "btn btn-sm btn-outline-secondary mx-1",
        },
        h("i", { class: "fas fa-pencil-alt" })
      ),
    ],
    enableSorting: false,
  },
];
const villageObj = reactive({
  id_province: computed(() => x_province.value?.id_geography),
  id_district: computed(() => x_district.value?.id_geography),
  id_commune: computed(() => x_commune.value?.id_geography),
  id_village: null,
  name_kh: null,
  name_en: null,
  name_latin: null,
});
const villageErrObj = reactive({
  name_kh: null,
  name_en: null,
  name_latin: null,
});

const defaultVillageObj = JSON.parse(
  JSON.stringify(new OBJ(villageObj).omit(["id_province", "id_district", "id_commune"]))
);
const defaultVillageErrObj = JSON.parse(JSON.stringify(villageErrObj));

function resetVillageObj() {
  Object.assign(villageObj, JSON.parse(JSON.stringify(defaultVillageObj)));
  Object.assign(villageErrObj, JSON.parse(JSON.stringify(defaultVillageErrObj)));
}

onMounted(async () => {
  $("#village-modal").on("hide.bs.modal", function () {
    resetVillageObj();
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
  x_commune.value = null;
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

watch(x_commune, async (newValue) => {
  villages.value = [];
  villageObj.id_village = null;
  if (newValue) {
    try {
      LoadingModal();
      await generateVillagesByCommune();
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
async function generateVillagesByCommune() {
  try {
    const res = await apiGetVillagesByCommuneID(x_commune.value?.id_geography);
    villages.value = res.data.villages;
  } catch (error) {
    throw error;
  }
}

async function saveVillage() {
  try {
    LoadingModal();
    let res = null;
    if (villageObj.id_village === null) {
      res = await apiCreateVillage(villageObj);
      onVillageCreated(res.data.village);
    } else {
      res = await apiUpdateVillage(villageObj);
      onVillageUpdated(res.data.village);
    }
    hideVillageModal();
    return MessageModal("success", "Success", res.data.message);
  } catch (error) {
    if (error.response?.status === 422) {
      Object.assign(
        villageErrObj,
        defaultVillageErrObj,
        mutateErrorObject(error.response.data.errors)
      );
      return CloseModal();
    }
    return ErrorModal(error);
  }
}

async function viewVillage(id_village) {
  try {
    LoadingModal();
    const res = await apiReadVillage(id_village);
    Object.assign(villageObj, res.data.village);
    villageObj.id_village = res.data.village.id_geography;
    showVillageModal();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
}
async function removeVillage(id_village) {
  $swal
    .fire({
      title: "Want to delete the village ?",
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
          const res = await apiDeleteVillage(id_village);
          onVillageDeleted(res.data.village);
          return MessageModal("success", "Success", res.data.message);
        } catch (error) {
          return ErrorModal(error);
        }
      }
    });
}

function onVillageCreated(created_village) {
  villages.value = [created_village, ...villages.value];
}
function onVillageUpdated(updated_village) {
  villages.value = villages.value.map((obj) =>
    obj.id_geography !== updated_village.id_geography ? obj : updated_village
  );
}
function onVillageDeleted(deleted_village) {
  villages.value = villages.value.filter(
    (obj) => obj.id_geography !== deleted_village.id_geography
  );
}

function showVillageModal() {
  $("#village-modal").modal("show");
}
function hideVillageModal() {
  $("#village-modal").modal("hide");
}
</script>
