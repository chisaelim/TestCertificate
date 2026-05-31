<template>
  <div class="modal fade" id="user-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form @submit.prevent="saveUser()">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ userObj.id ? 'Edit User' : 'Create User' }}</h5>
            <button type="button" class="close" @click="hideUserModal">
              <span>×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="form-group col-lg-6">
                <label>Name</label>
                <input v-model="userObj.name" type="text" class="form-control"
                  :class="{ 'is-invalid': !!userErrObj.name }">
                <div class="invalid-feedback">{{ userErrObj.name }}</div>
              </div>
              <div class="form-group col-lg-6">
                <label>Email</label>
                <input v-model="userObj.email" type="email" class="form-control"
                  :class="{ 'is-invalid': !!userErrObj.email }">
                <div class="invalid-feedback">{{ userErrObj.email }}</div>
              </div>
              <!-- <div class="form-group col-lg-6">
                <label>Level</label>
                <select v-model="userObj.level" class="form-control" :class="{ 'is-invalid': !!userErrObj.level }">
                  <option value="_ADMINISTRATOR_">Administrator</option>
                  <option value="_DOCUMENT_CONTROLLER_">Document Controller</option>
                </select>
                <div class="invalid-feedback">{{ userErrObj.level }}</div>
              </div> -->
            </div>

            <!-- Password fields: always shown on create -->
            <template v-if="userObj.id === null">
              <hr>
              <div class="row">
                <div class="form-group col-lg-6">
                  <label>Password</label>
                  <input v-model="userObj.password" type="password" class="form-control"
                    :class="{ 'is-invalid': !!userErrObj.password }">
                  <div class="invalid-feedback">{{ userErrObj.password }}</div>
                </div>
                <div class="form-group col-lg-6">
                  <label>Confirm Password</label>
                  <input v-model="userObj.password_confirmation" type="password" class="form-control"
                    :class="{ 'is-invalid': !!userErrObj.password }">
                </div>
              </div>
            </template>

            <!-- Optional password reset: collapsed by default on edit -->
            <template v-else>
              <hr>
              <div class="row">
                <div class="col-12 mb-2">
                  <button type="button" class="btn btn-sm btn-outline-warning"
                    @click="showPasswordReset = !showPasswordReset">
                    <i :class="showPasswordReset ? 'fa fa-minus' : 'fa fa-key'" class="mr-1"></i>
                    {{ showPasswordReset ? 'Cancel Password Reset' : 'Reset Password' }}
                  </button>
                </div>
              </div>
              <div v-if="showPasswordReset" class="row">
                <div class="form-group col-lg-6">
                  <label>New Password</label>
                  <input v-model="userObj.new_password" type="password" class="form-control"
                    :class="{ 'is-invalid': !!userErrObj.new_password }">
                  <div class="invalid-feedback">{{ userErrObj.new_password }}</div>
                </div>
                <div class="form-group col-lg-6">
                  <label>Confirm New Password</label>
                  <input v-model="userObj.new_password_confirmation" type="password" class="form-control"
                    :class="{ 'is-invalid': !!userErrObj.new_password_confirmation }">
                  <div class="invalid-feedback">{{ userErrObj.new_password_confirmation }}</div>
                </div>
              </div>
            </template>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" @click="hideUserModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/user';
const userStore = useUserStore();

const props = defineProps({
  onCreated: { type: Function },
  onUpdated: { type: Function },
  onDeleted: { type: Function },
});

const showPasswordReset = ref(false);

const userObj = reactive({
  id: null,
  name: '',
  email: '',
  level: '_DOCUMENT_CONTROLLER_',
  password: '',
  password_confirmation: '',
  new_password: '',
  new_password_confirmation: '',
});
const userErrObj = reactive({
  name: '',
  email: '',
  level: '',
  password: '',
  new_password: '',
  new_password_confirmation: '',
});

const defaultUserObj = JSON.parse(JSON.stringify(userObj));
const defaultUserErrObj = JSON.parse(JSON.stringify(userErrObj));

onMounted(() => {
  $('#user-modal').on('hide.bs.modal', function () {
    Object.assign(userObj, defaultUserObj);
    Object.assign(userErrObj, defaultUserErrObj);
    showPasswordReset.value = false;
  });
});

function showCreateUserModal() {
  Object.assign(userObj, defaultUserObj);
  Object.assign(userErrObj, defaultUserErrObj);
  showPasswordReset.value = false;
  $('#user-modal').modal('show');
}

function hideUserModal() {
  $('#user-modal').modal('hide');
}

async function saveUser() {
  try {
    LoadingModal();
    let response = null;

    if (userObj.id === null) {
      response = await apiCreateUser({
        name: userObj.name,
        email: userObj.email,
        level: userObj.level,
        password: userObj.password,
        password_confirmation: userObj.password_confirmation,
      });
      props.onCreated(response.data.user);
    } else {
      const data = {
        id: userObj.id,
        name: userObj.name,
        email: userObj.email,
        level: userObj.level,
      };
      if (showPasswordReset.value && userObj.new_password) {
        data.new_password = userObj.new_password;
        data.new_password_confirmation = userObj.new_password_confirmation;
      }
      response = await apiUpdateUser(data);
      props.onUpdated(response.data.user);
    }

    hideUserModal();
    return MessageModal({ icon: 'success', title: 'Success', text: response.data.message });
  } catch (error) {
    const { response } = error;
    if (!response) {
      return MessageModal({ icon: 'error', title: 'Error', text: error.message });
    }
    const { status, data } = response;
    if (status === 422) {
      Object.keys(userErrObj).forEach((key) => {
        userErrObj[key] = data.errors?.[key] ? data.errors[key][0] : '';
      });
      return CloseModal();
    }
    return MessageModal({ icon: 'error', title: 'Error', text: data.message });
  }
}

async function viewUser(id) {
  try {
    LoadingModal();
    const response = await apiReadUser(id);
    const user = response.data.user;
    Object.assign(userObj, {
      id: user.id,
      name: user.name,
      email: user.email,
      level: user.level,
      password: '',
      password_confirmation: '',
      new_password: '',
      new_password_confirmation: '',
    });
    Object.assign(userErrObj, defaultUserErrObj);
    showPasswordReset.value = false;
    $('#user-modal').modal('show');
    return CloseModal();
  } catch (error) {
    return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
  }
}

async function removeUser(id) {
  if (id === userStore.id) {
    return MessageModal({ icon: 'warning', title: 'Warning', text: 'You cannot delete your own account.' });
  }

  Swal.fire({
    title: 'Want to delete the user?',
    html: '<pre>Please make a confirmation.</pre>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Yes, Delete it.',
  }).then(async (sw) => {
    if (sw.isConfirmed) {
      try {
        LoadingModal();
        const response = await apiDeleteUser(id);
        const { user, message } = response.data;
        props.onDeleted(user);
        return MessageModal({ icon: 'success', title: 'Success', text: message });
      } catch (error) {
        return MessageModal({ icon: 'error', title: 'Error', text: error.response?.data?.message || error.message });
      }
    }
  });
}

defineExpose({ showCreateUserModal, viewUser, removeUser });
</script>
