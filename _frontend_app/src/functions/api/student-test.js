export function apiGetStudentTestsByStudent(id) {
  return axios.get(`${window.APP_API_URL}/student-tests/by/student/${id}`);
}
export function apiGetStudentTestsWithDetailsByStudent(id) {
  return axios.get(`${window.APP_API_URL}/student-tests/details/by/student/${id}`);
}
export function apiGetStudentTestsByIssuedDate(date) {
  return axios.get(`${window.APP_API_URL}/student-tests/by/issued-date/${date}`);
}
export function apiGetStudentTestsWithDetailsByIssuedDate(date) {
  return axios.get(`${window.APP_API_URL}/student-tests/details/by/issued-date/${date}`);
}
export function apiCreateStudentTest(data) {
  return axios.post(`${window.APP_API_URL}/student-tests/create`, data);
}
export function apiReadStudentTest(id) {
  return axios.get(`${window.APP_API_URL}/student-tests/read/${id}`);
}
export function apiUpdateStudentTest(data) {
  return axios.put(`${window.APP_API_URL}/student-tests/update`, data);
}
export function apiDeleteStudentTest(id) {
  return axios.delete(`${window.APP_API_URL}/student-tests/delete/${id}`);
}
export function apiGetPassedStudentTestsForCertificates(passed_ids) {
  return axios.get(`${window.APP_API_URL}/student-tests/passed-for-certificates`, {
    params: {
      passed_ids: passed_ids
    }
  });
}

export function apiChangeStudentTestStatus(data) {
  return axios.patch(`${window.APP_API_URL}/student-tests/change/status`, data);
}

export function apiGetStudentTestsByGeography(id) {
  return axios.get(`${window.APP_API_URL}/student-tests/by/geography/${id}`);
}
