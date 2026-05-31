export function apiGetUsers() {
  return axios.get(`${window.APP_API_URL}/users`);
}
export function apiCreateUser(data) {
  return axios.post(`${window.APP_API_URL}/users/create`, data);
}
export function apiReadUser(id) {
  return axios.get(`${window.APP_API_URL}/users/read/${id}`);
}
export function apiUpdateUser(data) {
  return axios.put(`${window.APP_API_URL}/users/update`, data);
}
export function apiDeleteUser(id) {
  return axios.delete(`${window.APP_API_URL}/users/delete/${id}`);
}
