export function apiGetStudents() {
  return axios.get(`${window.APP_API_URL}/students`);
}
export function apiGetStudentsWithDetails() {
  return axios.get(`${window.APP_API_URL}/students/details`);
}
export function apiCreateStudent(data) {
  return axios.post(`${window.APP_API_URL}/students/create`, data);
}
export function apiReadStudent(id) {
  return axios.get(`${window.APP_API_URL}/students/read/${id}`);
}
export function apiUpdateStudent(data) {
  return axios.put(`${window.APP_API_URL}/students/update`, data);
}
export function apiDeleteStudent(id) {
  return axios.delete(`${window.APP_API_URL}/students/delete/${id}`);
}
