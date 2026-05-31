export function apiGetTests(keyword = '') {
  return axios.get(`${window.APP_API_URL}/tests/`, {
    params: { keyword },
  });
}
export function apiGetTestsWithDetails() {
  return axios.get(`${window.APP_API_URL}/tests/details`);
}
export function apiCreateTest(data) {
  return axios.post(`${window.APP_API_URL}/tests/create`, data);
}
export function apiReadTest(id) {
  return axios.get(`${window.APP_API_URL}/tests/read/${id}`);
}
export function apiUpdateTest(data) {
  return axios.put(`${window.APP_API_URL}/tests/update`, data);
}
export function apiDeleteTest(id) {
  return axios.delete(`${window.APP_API_URL}/tests/delete/${id}`);
}
