export function apiGetStudents(keyword = '') {
  return axios.get(`${window.APP_API_URL}/students/`, {
    params: { keyword },
  });
}
export function apiGetStudentsWithDetails() {
  return axios.get(`${window.APP_API_URL}/students/details`);
}
export function apiCreateStudent(data) {
  return axios.post(`${window.APP_API_URL}/students/create`, data);
}
export function apiReadStudent(id_student) {
  return axios.get(`${window.APP_API_URL}/students/read/${id_student}`);
}
export function apiUpdateStudent(data) {
  return axios.put(`${window.APP_API_URL}/students/update`, data);
}
export function apiDeleteStudent(id_student) {
  return axios.delete(`${window.APP_API_URL}/students/delete/${id_student}`);
}
