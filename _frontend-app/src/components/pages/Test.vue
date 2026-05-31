<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">តេស្ត</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">ទំព័រដើម</router-link>
              </li>
              <li class="breadcrumb-item active">តេស្ត</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងតេស្ត'" :data="tests" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="test-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form @submit.prevent="saveTest()">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងព័ត៌មានតេស្ត</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>ឈ្មោះជាភាសាខ្មែរ</label>
              <input v-model="testObj.name_kh" type="text" class="form-control"
                :class="{ 'is-invalid': !!testErrObj.name_kh }">
              <div class="invalid-feedback">
                {{ testErrObj.name_kh }}
              </div>
            </div>
            <div class="form-group">
              <label>ឈ្មោះជាភាសាអង់គ្លេស</label>
              <input v-model="testObj.name_en" type="text" class="form-control"
                :class="{ 'is-invalid': !!testErrObj.name_en }">
              <div class="invalid-feedback">
                {{ testErrObj.name_en }}
              </div>
            </div>
            <div class="form-group">
              <label>ឈ្មោះកាត់ជាអក្សរអង់គ្លេស</label>
              <input v-model="testObj.short_name" type="text" class="form-control"
                :class="{ 'is-invalid': !!testErrObj.short_name }">
              <div class="invalid-feedback">
                {{ testErrObj.short_name }}
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">បោះបង់</button>
            <button type="submit" class="btn btn-primary">រក្សាទុក</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>

const tests = ref([]);
const columns = [
  {
    accessorKey: 'name_kh',
    header: 'ឈ្មោះជាភាសាខ្មែរ',
  },
  {
    accessorKey: 'name_en',
    header: 'ឈ្មោះជាភាសាអង់គ្លេស',
  },
  {
    accessorKey: 'short_name',
    header: 'ឈ្មោះកាត់ជាអក្សរអង់គ្លេស',
  },
  {
    accessorFn: ({ creator, created_at }) => creator.name + created_at,
    header: 'បង្កើតដោយ',
    cell: ({ row }) => [
      h('div', row.original.created_at),
      h('div', row.original.creator.name)
    ],
  },
  {
    accessorFn: ({ updater, updated_at }) => updater.name + updated_at,
    header: 'កែប្រែដោយ',
    cell: ({ row }) => [
      h('div', row.original.updated_at),
      h('div', row.original.updater.name)
    ],
  },
  {
    accessorKey: 'action',
    header: () => [
      'ជម្រើស',
      h('button',
        {
          onClick: () => $('#test-modal').modal('show'),
          class: 'btn btn-sm btn-success ml-3'
        },
        'បង្កើតថ្មី'
      )
    ],
    cell: ({
      row
    }) => [
        // delete btn
        h('button',
          {
            onClick: () => removeTest(row.original.id),
            class: 'btn btn-sm btn-outline-danger mx-1'
          },
          h('i', { class: 'fa fa-trash' })
        ),
        // view btn
        h('button',
          {
            onClick: () => viewTest(row.original.id),
            class: 'btn btn-sm btn-secondary mx-1'
          },
          h('i', { class: 'fa fa-eye' })
        ),
      ],
    enableSorting: false,
    enableGlobalFilter: false,
  }
];

const testObj = reactive({
  id: null,
  name_en: "",
  name_kh: "",
  short_name: "",
});
const testErrObj = reactive({
  name_en: "",
  name_kh: "",
  short_name: "",
});

const defaultTestObj = JSON.parse(JSON.stringify(testObj));
const defaultTestErrObj = JSON.parse(JSON.stringify(testErrObj));

onMounted(async () => {
  $('#test-modal').on('hide.bs.modal', function () {
    Object.assign(testObj, defaultTestObj);
    Object.assign(testErrObj, defaultTestErrObj);
  });
  try {
    LoadingModal();
    await generateTests();
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
});

async function generateTests() {
  const response = await apiGetTestsWithDetails();
  tests.value = response.data.tests;
}
async function saveTest() {
  try {
    LoadingModal();
    let response = null;
    if (testObj.id === null) {
      response = await apiCreateTest(testObj);
      onTestCreated(response.data.test);
    } else {
      response = await apiUpdateTest(testObj);
      onTestUpdated(response.data.test);
    }
    $('#test-modal').modal('hide');
    return MessageModal({ icon: 'success', title: 'Success', text: response.data.message });
  } catch (error) {
    const { response } = error;
    if (!response) {
      return MessageModal({ icon: "error", title: "Error", text: error.message });
    }
    const { status, data } = response;
    if (status === 422) {
      Object.keys(testErrObj).forEach((key) => {
        testErrObj[key] = data.errors[key]
          ? data.errors[key][0]
          : "";
      });
      return CloseModal();
    }
    return MessageModal({ icon: "error", title: "Error", text: data.message });
  }
}
async function viewTest(id) {
  try {
    LoadingModal();
    const response = await apiReadTest(id);
    Object.assign(testObj, response.data.test);
    $('#test-modal').modal('show');
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
}
async function removeTest(id) {
  Swal.fire({
    title: 'Want to delete the test ?',
    html: '<pre>' + "Please make a confirmation." + '</pre>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Yes, Delete it.'
  }).then(async (sw) => {
    if (sw.isConfirmed) {
      try {
        LoadingModal();
        const response = await apiDeleteTest(id);
        onTestDeleted(response.data.test);
        return MessageModal({ icon: 'success', title: 'Success', text: response.data.message });
      } catch (error) {
        return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
      }
    }
  });
}

const onTestCreated = (test) => {
  tests.value = [...tests.value, test];
};
const onTestUpdated = (test) => {
  tests.value = tests.value.map(obj => obj.id !== test.id ? obj : test);
};
const onTestDeleted = (test) => {
  tests.value = tests.value.filter(obj => obj.id !== test.id);
};
</script>
