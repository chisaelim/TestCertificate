<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">Home</router-link>
              </li>
              <li class="breadcrumb-item active">User Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <CustomTable :title="'User Management'" :data="users" :columns="columns" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <UserModal ref="UserModalRef" :onCreated="onUserCreated" :onUpdated="onUserUpdated" :onDeleted="onUserDeleted" />
</template>

<script setup>
import emptyImage from '@/assets/images/emptyImage.png';
const UserModalRef = ref();

const users = ref([]);
const columns = [
  {
    accessorKey: 'profile_thumbnail',
    header: '',
    cell: (cell) =>
      h('img', {
        style: 'max-width: 50px',
        class: 'profile-user-img img-fluid img-circle',
        src: cell.getValue() || emptyImage,
      }),
    enableSorting: false,
    enableGlobalFilter: false,
  },
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'email',
    header: 'Email',
  },
  {
    accessorKey: 'level',
    header: 'Level',
    cell: ({ row }) => {
      const level = row.original.level;
      const isAdmin = level === '_ADMINISTRATOR_';
      return h(
        'span',
        { class: `badge badge-${isAdmin ? 'danger' : 'info'}` },
        isAdmin ? 'Administrator' : 'Document Controller',
      );
    },
  },
  {
    accessorKey: 'action',
    header: () => [
      'Action',
      h(
        'button',
        {
          onClick: () => UserModalRef.value.showCreateUserModal(),
          class: 'btn btn-sm btn-success ml-3',
        },
        'Create User',
      ),
    ],
    cell: ({ row }) => [
      h('button', {
        onClick: () => UserModalRef.value.removeUser(row.original.id),
        class: 'btn btn-sm btn-outline-danger mx-1',
      }, h('i', { class: 'fa fa-trash' })),
      h('button', {
        onClick: () => UserModalRef.value.viewUser(row.original.id),
        class: 'btn btn-sm btn-secondary mx-1',
      }, h('i', { class: 'fa fa-eye' })),
    ],
    enableSorting: false,
    enableGlobalFilter: false,
  },
];

onMounted(async () => {
  try {
    LoadingModal();
    await generateUsers();
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
  }
});

async function generateUsers() {
  const response = await apiGetUsers();
  users.value = response.data.users;
}

const onUserCreated = (user) => {
  users.value = [...users.value, user];
};
const onUserUpdated = (user) => {
  users.value = users.value.map((obj) => (obj.id !== user.id ? obj : user));
};
const onUserDeleted = (user) => {
  users.value = users.value.filter((obj) => obj.id !== user.id);
};
</script>
