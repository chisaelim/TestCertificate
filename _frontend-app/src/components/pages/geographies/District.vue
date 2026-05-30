<template>
  <div class="content-wrapper">
    <div class="content-header">
      <ContentHeader title="ស្រុក - ខណ្ឌ - ក្រុង" />
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="row mx-3 mt-3">
            <div class="form-group col-md-12">
              <GeoSelect v-model="x_province" v-model:options="provinces" label="Capitals / Provinces"
                cache-tag="province" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងស្រុក - ខណ្ឌ - ក្រុង'" :data="districts" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="district-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form @submit.prevent="saveDistrict()">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងស្រុក - ខណ្ឌ - ក្រុង</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Name (Khmer)</label>
              <input v-model="districtObj.name_kh" type="text" class="form-control"
                :class="{ 'is-invalid': districtErrObj.name_kh !== null }" />
              <div class="invalid-feedback">
                {{ districtErrObj.name_kh }}
              </div>
            </div>

            <div class="form-group">
              <label>Name (English)</label>
              <input v-model="districtObj.name_en" type="text" class="form-control"
                :class="{ 'is-invalid': districtErrObj.name_en !== null }" />
              <div class="invalid-feedback">
                {{ districtErrObj.name_en }}
              </div>
            </div>
            <div class="form-group">
              <label>Name (Latin)</label>
              <input v-model="districtObj.name_latin" type="text" class="form-control"
                :class="{ 'is-invalid': districtErrObj.name_latin !== null }" />
              <div class="invalid-feedback">
                {{ districtErrObj.name_latin }}
              </div>
            </div>
            <div v-if="
              provinces.find(
                ({ id_geography }) => id_geography === districtObj.id_province
              )?.unit_en !== 'Capital'
            " class="form-group">
              <label>ថ្នាក់</label>
              <select v-model="districtObj.unit_en" class="form-control">
                <option value="District">ស្រុក / District / Srok</option>
                <option value="Municipality">ក្រុង / Municipality / Krong</option>
              </select>
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

const provinces = ref([]);
const districts = ref([]);
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
          onClick: () => showDistrictModal(),
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
          onClick: () => removeDistrict(row.original.id_geography),
          class: "btn btn-sm btn-outline-danger mx-1",
        },
        h("i", { class: "fas fa-trash" })
      ),
      // view btn
      h(
        "button",
        {
          onClick: () => viewDistrict(row.original.id_geography),
          class: "btn btn-sm btn-outline-secondary mx-1",
        },
        h("i", { class: "fas fa-pencil-alt" })
      ),
    ],
    enableSorting: false,
  },
];
const districtObj = reactive({
  id_province: computed(() => x_province.value?.id_geography),
  id_district: null,
  name_kh: null,
  name_en: null,
  name_latin: null,
  unit_en: "District", // default value
});
const districtErrObj = reactive({
  name_kh: null,
  name_en: null,
  name_latin: null,
});

const defaultDistrictObj = JSON.parse(
  JSON.stringify(new OBJ(districtObj).omit(["id_province"]))
);
const defaultDistrictErrObj = JSON.parse(JSON.stringify(districtErrObj));

function resetDistrictObj() {
  Object.assign(districtObj, JSON.parse(JSON.stringify(defaultDistrictObj)));
  Object.assign(districtErrObj, JSON.parse(JSON.stringify(defaultDistrictErrObj)));
}

onMounted(async () => {
  $("#district-modal").on("hide.bs.modal", function () {
    resetDistrictObj();
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
  districtObj.id_district = null;
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

async function saveDistrict() {
  try {
    LoadingModal();
    let res = null;
    if (districtObj.id_district === null) {
      res = await apiCreateDistrict(districtObj);
      onDistrictCreated(res.data.district);
    } else {
      res = await apiUpdateDistrict(districtObj);
      onDistrictUpdated(res.data.district);
    }
    hideDistrictModal();
    return MessageModal("success", "Success", res.data.message);
  } catch (error) {
    if (error.response?.status === 422) {
      Object.assign(
        districtErrObj,
        defaultDistrictErrObj,
        mutateErrorObject(error.response.data.errors)
      );
      return CloseModal();
    }
    return ErrorModal(error);
  }
}

async function viewDistrict(id_district) {
  try {
    LoadingModal();
    const res = await apiReadDistrict(id_district);
    Object.assign(districtObj, res.data.district);
    districtObj.id_district = res.data.district.id_geography;
    showDistrictModal();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
}
async function removeDistrict(id_district) {
  $swal
    .fire({
      title: "Want to delete the district ?",
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
          const res = await apiDeleteDistrict(id_district);
          onDistrictDeleted(res.data.district);
          return MessageModal("success", "Success", res.data.message);
        } catch (error) {
          return ErrorModal(error);
        }
      }
    });
}

function onDistrictCreated(created_district) {
  districts.value = [created_district, ...districts.value];
}
function onDistrictUpdated(updated_district) {
  districts.value = districts.value.map((obj) =>
    obj.id_geography !== updated_district.id_geography ? obj : updated_district
  );
}
function onDistrictDeleted(deleted_district) {
  districts.value = districts.value.filter(
    (obj) => obj.id_geography !== deleted_district.id_geography
  );
}

function showDistrictModal() {
  $("#district-modal").modal("show");
}
function hideDistrictModal() {
  $("#district-modal").modal("hide");
}
</script>
