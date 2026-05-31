<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-lg-6">
            <h1 class="m-0">ព័ត៌មានការធ្វើតេស្តរបស់និសិត្ស</h1>
          </div>
          <div class="col-lg-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">ទំព័រដើម</router-link>
              </li>
              <li class="breadcrumb-item active">ព័ត៌មានការធ្វើតេស្តរបស់និសិត្ស</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="card text-center">
          <div class="row mx-3 mt-3">
            <div class="col-md-12">
              <div class="form-group">
                <label>ថ្ងៃធ្វើតេស្ត</label>
                <VueDatePicker v-model="issued_date" :formats="{ input: 'dd-MM-yyyy' }" model-type="dd-MM-yyyy"
                  :time-config="{ enableTimePicker: false }" />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <button @click="openCertificateModal()" class="btn btn-sm btn-primary float-right">
                  វិញ្ញាបនបត្រ
                  <i class="bi bi-patch-check"></i>
                </button>
              </div>
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
  <div class="modal fade" id="test-detail-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <form @submit.prevent="saveStudentTest()">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងព័ត៌មានតេស្តរបស់សិស្ស</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>ថ្ងៃធ្វើតេស្ត</label>
                  <VueDatePicker v-model="issued_date" :formats="{ input: 'dd-MM-yyyy' }" model-type="dd-MM-yyyy"
                    :time-config="{ enableTimePicker: false }" disabled
                    :class="{ 'is-invalid': !!studentTestErrObj.issued_date || dupli_student_test }" />
                  <div class="invalid-feedback">
                    {{ studentTestErrObj.issued_date || 'ព័ត៌មានធ្វើតេស្តរបស់សិស្សម្នាក់នេះមានរួចហើយ។' }}
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <StudentSelect v-model="x_student" v-model:options="students" :disabled="studentTestObj.id !== null" />
            </div>
            <div class="form-group">
              <label>ឈ្មោះតេស្ត</label>
              <TestSelect v-model="x_test" v-model:options="tests" />

            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              បោះបង់
            </button>
            <button :disabled="studentTestObj.student_id === null ||
              studentTestObj.test_id === null ||
              dupli_student_test
              " type="submit" class="btn btn-primary">
              រក្សាទុក
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal fade" id="certificate-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form @submit.prevent="generateCertificates()">
          <div class="modal-header">
            <h5 class="modal-title">ការគ្រប់គ្រងវិញ្ញាបនបត្ររបស់សិស្ស</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <CustomTable :title="'បញ្ជីឈ្មោះសិស្សបានបញ្ចប់ការសិក្សា'" :data="passed_student_tests"
              :columns="passed_columns" :maxSize="true" />
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              បោះបង់
            </button>
            <button type="submit" class="btn btn-primary">
              បង្កើតវិញ្ញាបនបត្រ
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="PDF-MODAL" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          <iframe style="width: 100%; height: calc(100vh - 100px); min-height: 500px;"></iframe>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import emptyImage from '@/assets/images/emptyImage.png';

const issued_date = ref(moment().format('DD-MM-YYYY'));
const student_tests = ref([]);
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
          onClick:
            row.original.status === "PENDING"
              ? null
              : () => changeStudentTestStatus(row.original.id, "PENDING"),
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
  {
    accessorKey: "action",
    header: () => [
      "ជម្រើស",
      h(
        "button",
        {
          onClick: () => $("#test-detail-modal").modal("show"),
          class: "btn btn-sm btn-success ml-3",
        },
        "ចុះឈ្មោះបន្ថែម"
      ),
    ],
    cell: ({ row }) => [
      // delete btn
      h(
        "button",
        {
          onClick: () => removeStudentTest(row.original.id),
          class: "btn btn-sm btn-outline-danger mx-1",
        },
        h("i", { class: "fa fa-trash" })
      ),
      // view btn
      h(
        "button",
        {
          onClick: () => viewStudentTest(row.original.id),
          class: "btn btn-sm btn-secondary mx-1",
        },
        h("i", { class: "fa fa-eye" })
      ),
      row.original.status !== "PASSED"
        ? h(
          "button",
          {
            onClick: () =>
              changeStudentTestStatus(row.original.id, "PASSED"),
            class: "btn btn-sm btn-success mx-1",
          },
          h("i", { class: "fa fa-check-circle" })
        )
        : null,
      row.original.status !== "FAILED"
        ? h(
          "button",
          {
            onClick: () =>
              changeStudentTestStatus(row.original.id, "FAILED"),
            class: "btn btn-sm btn-danger mx-1",
          },
          h("i", { class: "fa fa-times-circle" })
        )
        : null,
    ],
    enableSorting: false,
    enableGlobalFilter: false,
  },
];

const dupli_student_test = computed(() =>
  student_tests.value?.find(
    ({ id, test, student }) =>
      id !== studentTestObj.id &&
      student.id === studentTestObj.student_id &&
      test.id === studentTestObj.test_id
  )
);

const tests = ref([]);
const x_test = computed({
  get: () => tests.value.find(({ id }) => id === studentTestObj.test_id),
  set: (value) => (studentTestObj.test_id = value ? value?.id : null),
});

const students = ref([]);
const x_student = computed({
  get: () =>
    students.value.find(({ id }) => id === studentTestObj.student_id),
  set: (value) => (studentTestObj.student_id = value ? value?.id : null),
});

const studentTestObj = reactive({
  id: null,
  test_id: null,
  student_id: null,
  issued_date: computed(() => issued_date.value),
});
const studentTestErrObj = reactive({
  issued_date: null,
});

const defaultStudentTestObj = JSON.parse(JSON.stringify(studentTestObj));
const defaultStudentTestErrObj = JSON.parse(JSON.stringify(studentTestErrObj));

onMounted(async () => {
  $("#test-detail-modal").on("hidden.bs.modal", function () {
    if (!$(this).is(":visible")) {
      Object.assign(studentTestObj, defaultStudentTestObj);
      Object.assign(studentTestErrObj, defaultStudentTestErrObj);
    }
  });
  try {
    LoadingModal();
    await Promise.all([
      generateTests(),
      generateStudents(),
      generateStudentTestsByIssuedDate(),
    ]);
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }


});
watch(issued_date, async () => {
  try {
    LoadingModal();
    await generateStudentTestsByIssuedDate();
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
});

async function generateStudentTestsByIssuedDate() {
  const response = await apiGetStudentTestsWithDetailsByIssuedDate(issued_date.value);
  student_tests.value = response.data.student_tests;
}
async function generateTests() {
  const response = await apiGetTests();
  tests.value = response.data.tests;
}
async function generateStudents() {
  const response = await apiGetStudentsWithDetails();
  students.value = response.data.students;
}

async function saveStudentTest() {
  try {
    LoadingModal();
    let response = null;
    if (studentTestObj.id === null) {
      response = await apiCreateStudentTest(studentTestObj);
      onStudentTestCreated(response.data.student_test);
    } else {
      response = await apiUpdateStudentTest(studentTestObj);
      onStudentTestUpdated(response.data.student_test);
    }
    $("#test-detail-modal").modal("hide");
    return MessageModal({ icon: 'success', title: 'Success', text: response.data.message });
  } catch (error) {
    const { response } = error;
    if (!response) {
      return MessageModal({ icon: "error", title: "Error", text: error.message });
    }
    const { status, data } = response;
    if (status === 422) {
      Object.keys(studentTestErrObj).forEach((key) => {
        studentTestErrObj[key] = data.errors[key] ? data.errors[key][0] : null;
      });
      return CloseModal();
    }
    return MessageModal({ icon: "error", title: "Error", text: data.message });
  }
}
async function viewStudentTest(id) {
  try {
    LoadingModal();
    const response = await apiReadStudentTest(id);
    Object.assign(studentTestObj, response.data.student_test);
    $("#test-detail-modal").modal("show");
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
}
async function removeStudentTest(id) {
  Swal.fire({
    title: "Want to delete the test detail?",
    html: "<pre>" + "Please make a confirmation." + "</pre>",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    confirmButtonText: "Yes, Delete it.",
  }).then(async (sw) => {
    if (sw.isConfirmed) {
      try {
        LoadingModal();
        const response = await apiDeleteStudentTest(id);
        const { student_test, message } = response.data;
        onStudentTestDeleted(student_test);
        return MessageModal({ icon: 'success', title: 'Success', text: message });
      } catch (error) {
        return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
      }
    }
  });
}
async function changeStudentTestStatus(id, status) {
  try {
    LoadingModal();
    const response = await apiChangeStudentTestStatus({ id, status });
    const { student_test, message } = response.data;
    onStudentTestUpdated(student_test);
    return MessageModal({ icon: 'success', title: 'Success', text: message });
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
}
async function generateCertificates() {
  try {
    LoadingModal();
    const response = await apiGetPassedStudentTestsForCertificates(
      passed_ids.value
    );
    await generateStudentTestCertificatesPDF(response.data.student_tests);
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: "error", title: "Error", text: error.response?.data?.message || error.message });
  }
}

const onStudentTestCreated = (student_test) => {
  student_tests.value = [...student_tests.value, student_test];
};
const onStudentTestUpdated = (student_test) => {
  student_tests.value = student_tests.value.map((obj) =>
    obj.id !== student_test.id ? obj : student_test
  );
};
const onStudentTestDeleted = (student_test) => {
  student_tests.value = student_tests.value.filter(
    (obj) => obj.id !== student_test.id
  );
};

const passed_student_tests = computed(
  () => student_tests.value?.filter(({ status }) => status === "PASSED") ?? []
);
const passed_columns = [
  {
    accessorKey: "id",
    header: () =>
      h(
        "div",
        {
          class: "icheck-primary d-inline",
        },
        [
          h("input", {
            type: "checkbox",
            id: "checkbox-all",
            checked: passed_student_tests.value.every(({ id }) =>
              passed_ids.value.includes(id)
            ),
            onChange: ({ target: { checked } }) => {
              if (checked) {
                passed_ids.value = passed_student_tests.value.map(
                  ({ id }) => id
                );
              } else {
                passed_ids.value = [];
              }
            },
          }),
          h(
            "label",
            {
              for: "checkbox-all",
            },
            "ជ្រើសទាំងអស់"
          ),
        ]
      ),
    cell: (cell) =>
      h(
        "div",
        {
          class: "icheck-primary d-inline",
        },
        [
          h("input", {
            type: "checkbox",
            id: "checkbox-" + cell.getValue(),
            checked: passed_ids.value.includes(cell.getValue()),
            onChange: ({ target: { checked } }) => {
              if (checked) {
                passed_ids.value.push(cell.getValue());
              } else {
                passed_ids.value = passed_ids.value.filter(
                  (id) => id !== cell.getValue()
                );
              }
            },
          }),
          h("label", {
            for: "checkbox-" + cell.getValue(),
          }),
        ]
      ),
    enableSorting: false,
    enableGlobalFilter: false,
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
];
const passed_ids = ref([]);
function openCertificateModal() {
  $("#certificate-modal").modal("show");
}

import el1 from '@/assets/images/elements/element1.png';
async function generateStudentTestCertificatesPDF(passed_test_details) {
  try {
    const toDataUrl = (url) => fetch(url)
      .then(r => r.blob())
      .then(blob => new Promise((res, rej) => {
        const reader = new FileReader();
        reader.onloadend = () => res(reader.result);
        reader.onerror = rej;
        reader.readAsDataURL(blob);
      }));

    const el1DataUrl = await toDataUrl(el1);

    let dots = [
      "................................",
      "............................................................................",
      ".......................................................",
      "...........................................",
      ".........................................................................................................",
      ".......................................................................................................",
      ".............................................",
      "................................................................................................................",
      "........................................................................................................",
      "......................................................................................................",
      ".........................",
      ".....................",
      ".....................",
      "...................................................................",
      "........................................................................",
      "...............................",
      ".................................................................................................................",
      "........................................................................................................................",
      ".......................................................................",
      "...................................................................................................................",
      "............................",
      ".................................",
      ".................................",
      ".....................",
      ".................................",
      "................................."
    ];
    const enRowObject = (label, value, underline) => ({
      columns: [
        {
          width: 'auto',
          text: label,
        },
        {
          width: '*',
          stack: [
            {
              text: underline,
              margin: [0, 1, 0, 0],
            },
            {
              text: value,
              bold: true,
              margin: [0, -11.5, 0, 0],
            }
          ],
          alignment: 'center'
        }
      ],
      // margin: [0, 0, 0, 7],
      margin: [0, -1.50, 0, 7]
    });
    const khRowObject = (label, value, underline) => ({
      columns: [
        {
          width: 'auto',
          text: label,
        },
        {
          width: '*',
          stack: [
            {
              text: underline,
              margin: [0, 1, 0, 0],
            },
            {
              text: value,
              font: 'KhmerOSMoul',
              fonts: 8,
              margin: [0, -18.5, 0, 0],
            }
          ],
          alignment: 'center'
        }
      ],
      margin: [0, -5, 0, 3.75]
    });
    const khmerMonths = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
    const enMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const formatDate = (dateStr) => {
      const [day, month, year] = dateStr.split('-');
      const idx = parseInt(month) - 1;
      return { en: `${day} ${enMonths[idx]} ${year}`, kh: `${day} ${khmerMonths[idx]} ${year}` };
    };
    let page = 0;
    const data = [];
    for (const { id, issued_date, expired_date, student: s, test } of passed_test_details) {
      const issuedDate = formatDate(issued_date);
      const expiredDate = formatDate(expired_date);
      const dobDate = formatDate(s.dob);
      const issued_date_en = issuedDate.en;
      const issued_date_kh = issuedDate.kh;
      const expired_date_en = expiredDate.en;
      const expired_date_kh = expiredDate.kh;
      const student = {
        name_en: s.name_en,
        name_kh: s.name_kh,
        gd_en: s.gender.gd_en,
        gd_kh: s.gender.gd_kh,
        dob_en: dobDate.en,
        dob_kh: dobDate.kh,
        pob_province: s.pob_province,
      };
      page++;
      let index = 0;
      data.push({
        columns: [
          {
            width: '50%',
            stack: [
              enRowObject(
                'Seeing minutes of the Assessment Committee meeting on',
                issued_date_en,
                dots[index++]
              ),
              enRowObject(
                'Of competency assessment at',
                'National Polytechnic Institute of Cambodia',
                dots[index++]
              ),
              {
                columns: [
                  {
                    width: '60%',
                    stack: [
                      enRowObject(
                        'Certifies that',
                        student.name_en,
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      enRowObject(
                        'Sex',
                        student.gd_en,
                        dots[index++]
                      ),
                    ]
                  }
                ],
              },
              enRowObject(
                'Date of Birth',
                student.dob_en,
                dots[index++]
              ),
              enRowObject(
                'Place of Birth',
                student.pob_province.name_latin,
                dots[index++]
              ),
              enRowObject(
                'Has qualified to deserve the National Qualification',
                'Vocational Certificate',
                dots[index++]
              ),
              enRowObject(
                'Skills of',
                test.name_en,
                dots[index++]
              ),
              enRowObject(
                'This certificate is presented to the bearer for which all the rights and privileges.',
                '',
                ''
              ),
              enRowObject(
                'Date of Issue',
                issued_date_en,
                dots[index++]
              ),
              enRowObject(
                'Date of Expiry',
                expired_date_en,
                dots[index++]
              ),
            ],
            style: 'body_en',
            // margin: [10, 0],
            margin: [5, -2]
          },
          {
            width: '50%',
            stack: [
              {
                columns: [
                  {
                    width: '60%',
                    stack: [
                      khRowObject(
                        'បានឃើញកំណត់ហេតុប្រជុំ ចុះថ្ងៃទី',
                        issued_date_kh.split(' ')[0],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ខែ',
                        issued_date_kh.split(' ')[1],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ឆ្នាំ',
                        issued_date_kh.split(' ')[2],
                        dots[index++]
                      ),
                    ]
                  }
                ],
              },
              khRowObject(
                'របស់គណៈកម្មការវាយតម្លៃសមត្ថភាពនៅ',
                'វិទ្យាស្ថានជាតិ',
                dots[index++]
              ),
              {
                columns: [
                  {
                    width: '70%',
                    stack: [
                      khRowObject(
                        'បញ្ជាក់ថា៖',
                        student.name_kh,
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ភេទ',
                        student.gd_kh,
                        dots[index++]
                      ),
                    ]
                  }
                ],
              },
              khRowObject(
                'កើតថ្ងៃទី',
                student.dob_kh,
                dots[index++]
              ),
              khRowObject(
                'នៅ',
                student.pob_province.name_kh,
                dots[index++]
              ),
              khRowObject(
                'មានសមត្ថភាពទទួលបានគុណវុឌ្ឍិជាតិ',
                'វិញ្ញាបនបត្រវិជ្ជាជីវៈ',
                dots[index++]
              ),
              khRowObject(
                'ជំនាញ',
                test.name_kh,
                dots[index++]
              ),
              khRowObject(
                'គុណវុឌ្ឍិនេះផ្តល់ជូនសាមីជនដើម្បីប្រើប្រាស់តាមតម្រូវការដែលអាចប្រើបាន។',
                '',
                ''
              ),
              {
                columns: [
                  {
                    width: '40%',
                    stack: [
                      khRowObject(
                        'ផ្តល់ជូននៅថ្ងៃទី',
                        issued_date_kh.split(' ')[0],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ខែ',
                        issued_date_kh.split(' ')[1],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ឆ្នាំ',
                        issued_date_kh.split(' ')[2],
                        dots[index++]
                      ),
                    ]
                  }
                ],
              },
              {
                columns: [
                  {
                    width: '40%',
                    stack: [
                      khRowObject(
                        'សុពលភាពដល់ថ្ងៃទី',
                        expired_date_kh.split(' ')[0],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ខែ',
                        expired_date_kh.split(' ')[1],
                        dots[index++]
                      ),
                    ]
                  },
                  {
                    width: '*',
                    stack: [
                      khRowObject(
                        'ឆ្នាំ',
                        expired_date_kh.split(' ')[2],
                        dots[index++]
                      ),
                    ]
                  }
                ],
              },
            ],
            style: 'body_kh',
            // margin: [10, 0],
            margin: [5, -2],
          },
        ],
        pageBreak: page < passed_test_details.length ? 'after' : ''
      })
    }
    const docDefinition = {
      info: {
        title: 'Certificate of Recognition',
        author: 'Chisae LIM',
        subject: 'Certificate of Recognition',
        keywords: 'Certificate of Recognition',
      },
      header: [
        {
          columns: [
            {
              width: '*',
              text: ''
            },
            {
              width: '45%',
              stack: [
                {
                  text: 'ព្រះរាជាណាចក្រកម្ពុជា',
                  style: 'header_kh',
                },
                {
                  text: 'KINGDOM OF CAMBODIA',
                  style: 'header_en',
                },
                {
                  text: 'ជាតិ សាសនា ព្រះមហាក្សត្រ',
                  style: 'header_kh',
                  font: 'KhmerOSBattambong',
                  bold: true
                },
                {
                  text: 'NATION RELIGION KING',
                  style: 'header_en',
                  bold: false,
                },
                {
                  image: 'el1',
                  width: 150,
                  margin: [0, -25, 0, -25]
                },

              ],
              alignment: 'center',
              margin: [0, 65, 0, 0]
            }
          ],
        },
        {
          columns: [
            {
              width: '50%',
              stack: [
                {
                  text: 'ក្រសួងការងារ និងបណ្តុះបណ្តាលវិជ្ជាជីវៈ',
                  style: 'header_kh',
                },
                {
                  text: 'MINISTRY OF LABOUR AND VOCATIONAL TRAINING',
                  style: 'header_en',
                },
                {
                  image: 'el1',
                  width: 150,
                  margin: [0, -25, 0, -25]
                },
              ],
              alignment: 'center',
              margin: [0, -45, 0, 0]
            }
          ],
        },
        {
          stack: [
            {
              text: 'វិញ្ញាបនបត្រទទួលស្គាល់ការសិក្សា',
              style: 'header_kh',
              fontSize: 13,
            },
            {
              text: 'CERTIFICATE OF RECOGNITION OF PRIOR LEARNING',
              style: 'header_en',
            },
            {
              text: 'វិទ្យាស្ថានជាតិ',
              style: 'header_kh',
            },
            {
              text: 'NATIONAL POLYTECHNIC INSTITUTE OF CAMBODIA',
              style: 'header_en',
            },
          ],
          alignment: 'center',
          margin: [0, -5, 0, 0]
        }
      ],
      footer: [
        {
          columns: [
            {
              width: '*',
              text: ''
            },
            {
              width: '56%',
              stack: [
                {
                  text: 'ថ្ងៃ..............................ខែ....................ឆ្នាំ..............................ព.ស. ២៥........',
                  style: 'body_kh',
                },
                {
                  text: 'រាជធានីភ្នំពេញ, ថ្ងៃទី...............ខែ...............ឆ្នាំ២០........',
                  style: 'body_kh',
                },
                {
                  text: 'នាយកវិទ្យាស្ថាន',
                  style: 'header_kh',
                  margin: [0, 0, 0, 0]
                },
                {
                  text: 'PRESIDENT',
                  style: 'header_en',
                  bold: true,
                },

              ],
              margin: [0, -10, 0, 0],
              alignment: 'center',
            }
          ],
        },
        {
          text: 'លេខ:...........................',
          style: 'body_kh',
          alignment: 'center',
          margin: [0, -40, 0, 0]
        },
        {
          canvas:
            [{
              type: 'rect',
              x: 842 / 2 - 30,
              y: 10,
              w: 60,
              h: 90,
              lineWidth: 1,
              lineColor: 'black',
            }],
        },
        {
          text: '4 x 6',
          absolutePosition: {
            x: 0, // center horizontally (adjust as needed)
            y: 90 - 10 // center vertically in the rectangle
          },
          alignment: 'center',
          fontSize: 12 // or any size you want
        },
      ],
      styles: {
        header_kh: {
          font: 'KhmerOSMoul',
          fontSize: 11,
        },
        header_en: {
          font: 'Arial',
          fontSize: 10,
          bold: true
        },
        body_en: {
          font: 'Arial',
          fontSize: 9.5,
        },
        body_kh: {
          font: 'KhmerOSBattambong',
          fontSize: 9.5,
        }
      },
      images: {
        el1: el1DataUrl,
      },
      pageSize: 'A4',
      pageOrientation: 'landscape',
      pageMargins: [80, 215, 80, 210],
      content: data,
      // pageBreakBefore: function (currentNode, followingNodesOnPage, nodesOnNextPage, previousNodesOnPage) {
      //     overWriteColumnStack(currentNode);
      //     return currentNode.headlineLevel === 1 && followingNodesOnPage.length === 0;
      // }
    };
    // const overWriteColumnStack = (node) => {
    //     if ('columns' in node) {
    //         if (node.columns.length === 2 && 'stack' in node.columns[1] && node.columns[1].stack[0].text === '.') {
    //             console.log(node)
    //             const calWidth = node.columns[1]._calcWidth;
    //             const stack1 = node.columns[1].stack[0]._minWidth;
    //             let temp = '';
    //             for (let i = 1; i < calWidth / stack1; i++) {
    //                 temp += '.';
    //             }
    //             node.columns[1].stack[0].text = temp;
    //             console.log(temp)
    //             dots.push(temp);
    //         }
    //     }
    //     console.log(dots)
    // }

    const pdfDocGenerator = pdfMake.createPdf(docDefinition);
    const dataUrl = await pdfDocGenerator.getDataUrl();
    const iframe = document.querySelector('iframe');
    iframe.src = dataUrl;
    $('#PDF-MODAL').modal('show');
  } catch (error) {
    console.log(error);

  }
}

</script>
