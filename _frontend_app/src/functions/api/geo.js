// Public asset endpoints
export function apiGetProvinces() {
  return axios.get(`${window.APP_API_URL}/provinces`);
}
export function apiGetDistrictsByProvince(id) {
  return axios.get(`${window.APP_API_URL}/districts/by/province/${id}`);
}
export function apiGetCommunesByDistrict(id) {
  return axios.get(`${window.APP_API_URL}/communes/by/district/${id}`);
}
export function apiGetVillagesByCommune(id) {
  return axios.get(`${window.APP_API_URL}/villages/by/commune/${id}`);
}
