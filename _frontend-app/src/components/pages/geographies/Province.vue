<template>
  <div class="content-wrapper">
    <div class="content-header">
      <ContentHeader title="ខេត្ត - រាជធានី" />
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងខេត្ត - រាជធានី'" :data="provinces" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="province-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form @submit.prevent="saveProvince()">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងខេត្ត - រាជធានី</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Name (Khmer)</label>
              <input v-model="provinceObj.name_kh" type="text" class="form-control"
                :class="{ 'is-invalid': provinceErrObj.name_kh !== null }" />
              <div class="invalid-feedback">
                {{ provinceErrObj.name_kh }}
              </div>
            </div>

            <div class="form-group">
              <label>Name (English)</label>
              <input v-model="provinceObj.name_en" type="text" class="form-control"
                :class="{ 'is-invalid': provinceErrObj.name_en !== null }" />
              <div class="invalid-feedback">
                {{ provinceErrObj.name_en }}
              </div>
            </div>
            <div class="form-group">
              <label>Name (Latin)</label>
              <input v-model="provinceObj.name_latin" type="text" class="form-control"
                :class="{ 'is-invalid': provinceErrObj.name_latin !== null }" />
              <div class="invalid-feedback">
                {{ provinceErrObj.name_latin }}
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
const provinces = ref([]);
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
          onClick: () => showProvinceModal(),
          class: "btn btn-sm btn-success ml-3",
        },
        "Create New់"
      ),
    ],
    cell: ({ row }) => [
      // delete btn
      h(
        "button",
        {
          onClick: () => removeProvince(row.original.id_geography),
          class: "btn btn-sm btn-outline-danger mx-1",
        },
        h("i", { class: "fas fa-trash" })
      ),
      // view btn
      h(
        "button",
        {
          onClick: () => viewProvince(row.original.id_geography),
          class: "btn btn-sm btn-outline-secondary mx-1",
        },
        h("i", { class: "fas fa-pencil-alt" })
      ),
    ],
    enableSorting: false,
  },
];
const provinceObj = reactive({
  id_province: null,
  name_kh: null,
  name_en: null,
  name_latin: null,
});
const provinceErrObj = reactive({
  name_kh: null,
  name_en: null,
  name_latin: null,
});

const defaultProvinceObj = JSON.parse(JSON.stringify(provinceObj));
const defaultProvinceErrObj = JSON.parse(JSON.stringify(provinceErrObj));

function resetProvinceObj() {
  Object.assign(provinceObj, JSON.parse(JSON.stringify(defaultProvinceObj)));
  Object.assign(provinceErrObj, JSON.parse(JSON.stringify(defaultProvinceErrObj)));
}

onMounted(async () => {
  $("#province-modal").on("hide.bs.modal", function () {
    resetProvinceObj();
  });
  try {
    LoadingModal();
    await generateProvinces();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
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

async function saveProvince() {
  try {
    LoadingModal();
    let res = null;
    if (provinceObj.id_province === null) {
      res = await apiCreateProvince(provinceObj);
      onProvinceCreated(res.data.province);
    } else {
      res = await apiUpdateProvince(provinceObj);
      onProvinceUpdated(res.data.province);
    }
    hideProvinceModal();
    return MessageModal("success", "Success", res.data.message);
  } catch (error) {
    if (error.response.status === 422) {
      Object.assign(
        provinceErrObj,
        defaultProvinceErrObj,
        mutateErrorObject(error.response.data.errors)
      );
      return CloseModal();
    }
    return ErrorModal(error);
  }
}

async function viewProvince(id_province) {
  try {
    LoadingModal();
    const res = await apiReadProvince(id_province);
    Object.assign(provinceObj, res.data.province);
    provinceObj.id_province = res.data.province.id_geography;
    showProvinceModal();
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
}
async function removeProvince(id_province) {
  $swal
    .fire({
      title: "Want to delete the province ?",
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
          const res = await apiDeleteProvince(id_province);
          onProvinceDeleted(res.data.province);
          return MessageModal("success", "Success", res.data.message);
        } catch (error) {
          return ErrorModal(error);
        }
      }
    });
}

function onProvinceCreated(created_province) {
  provinces.value = [created_province, ...provinces.value];
}
function onProvinceUpdated(updated_province) {
  provinces.value = provinces.value.map((obj) =>
    obj.id_geography !== updated_province.id_geography ? obj : updated_province
  );
}
function onProvinceDeleted(deleted_province) {
  provinces.value = provinces.value.filter(
    (obj) => obj.id_geography !== deleted_province.id_geography
  );
}

function showProvinceModal() {
  $("#province-modal").modal("show");
}
function hideProvinceModal() {
  $("#province-modal").modal("hide");
}
</script>
