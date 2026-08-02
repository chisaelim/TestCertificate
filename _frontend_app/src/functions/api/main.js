
export function apiGetAllGenders() {
  return axios.get(`${window.APP_API_URL}/assets/all/genders`);
}
export function apiGetAllEthnicities() {
  return axios.get(`${window.APP_API_URL}/assets/all/ethnicities`);
}
export function apiGetAllNationalities() {
  return axios.get(`${window.APP_API_URL}/assets/all/nationalities`);
}
export function apiGetAllReligions() {
  return axios.get(`${window.APP_API_URL}/assets/all/religions`);
}
