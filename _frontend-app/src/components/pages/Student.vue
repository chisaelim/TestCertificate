<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h1 class="m-0">Students</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">Home</router-link>
              </li>
              <li class="breadcrumb-item active">Students</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'តារាងទិន្នន័យសិស្ស'" :data="students" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <StudentModal ref="StudentModalRef" :onCreated="onStudentCreated" :onUpdated="onStudentUpdated"
    :onDeleted="onStudentDeleted" />
</template>

<script setup>

const StudentModalRef = ref();

const students = ref([]);
const columns = [
  {
    accessorKey: "photo",
    header: "",
    cell: (cell) =>
      h("img", {
        style: "max-width: 50px",
        class: "profile-user-img img-fluid img-circle",
        src:
          cell.getValue() !== null
            ? "/storage/images/students/thumbnails/" + cell.getValue()
            : "/assets/images/emptyProfile.png",
        alt: "student image",
      }),
  },
  {
    accessorKey: 'name_kh',
    header: 'ឈ្មោះជាអក្សរខ្មែរ',
  },
  {
    accessorKey: 'name_en',
    header: 'ឈ្មោះជាអក្សរឡាតាំង',
  },
  {
    accessorKey: 'gender.gender_kh_full',
    header: 'ភេទ',
  },
  {
    accessorKey: 'phone',
    header: 'លេខទូរស័ព្ទ',
  },
  {
    accessorKey: 'action',
    header: () => [
      'ជម្រើស',
      h('button',
        {
          onClick: () => StudentModalRef.value.showStudentModal(),
          class: 'btn btn-sm btn-success ml-3'
        },
        'ចុះឈ្មោះ'
      )
    ],
    cell: ({
      row
    }) => [
        // delete btn
        h('button',
          {
            onClick: () => StudentModalRef.value.removeStudent(row.original.id_student),
            class: 'btn btn-sm btn-outline-danger mx-1'
          },
          h('i', { class: 'fa fa-trash' })
        ),
        // view btn
        h('button',
          {
            onClick: () => StudentModalRef.value.viewStudent(row.original.id_student),
            class: 'btn btn-sm btn-secondary mx-1'
          },
          h('i', { class: 'fa fa-eye' })
        ),
      ],
    enableSorting: false,
    enableGlobalFilter: false,
  }
];


onMounted(async () => {
  try {
    LoadingModal();
    await Promise.all([
      generateStudents(),
    ]);
    return CloseModal();
  } catch (error) {
    return ErrorModal(error);
  }
});

async function generateStudents() {
  try {
    const res = await apiGetStudentsWithDetails();
    students.value = res.data.students;
  } catch (error) {
    throw error;
  }
}

const onStudentCreated = (student) => {
  students.value = [...students.value, student];
};
const onStudentUpdated = (student) => {
  students.value = students.value.map(obj => obj.id_student !== student.id_student ? obj : student);
};
const onStudentDeleted = (student) => {
  students.value = students.value.filter(obj => obj.id_student !== student.id_student);
};
</script>
