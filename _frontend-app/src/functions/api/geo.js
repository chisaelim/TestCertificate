// Public asset endpoints
export function apiGetProvinces() {
  return axios.get(`${window.APP_API_URL}/provinces`);
}
export function apiGetDistrictsByProvinceID(id_geography) {
  return axios.get(`${window.APP_API_URL}/districts/by/province/${id_geography}`);
}
export function apiGetCommunesByDistrictID(id_geography) {
  return axios.get(`${window.APP_API_URL}/communes/by/district/${id_geography}`);
}
export function apiGetVillagesByCommuneID(id_geography) {
  return axios.get(`${window.APP_API_URL}/villages/by/commune/${id_geography}`);
}
