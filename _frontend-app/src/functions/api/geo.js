// Public asset endpoints
export function apiGetProvinces() {
  return axios.get(`${window.APP_API_URL}/api/provinces`);
}
export function apiGetDistrictsByProvinceID(id_geography) {
  return axios.get(`${window.APP_API_URL}/api/districts/by/province/${id_geography}`);
}
export function apiGetCommunesByDistrictID(id_geography) {
  return axios.get(`${window.APP_API_URL}/api/communes/by/district/${id_geography}`);
}
export function apiGetVillagesByCommuneID(id_geography) {
  return axios.get(`${window.APP_API_URL}/api/villages/by/commune/${id_geography}`);
}

// Province CRUD
export function apiCreateProvince(data) {
  return axios.post(`${window.APP_API_URL}/api/provinces/create`, data);
}
export function apiUpdateProvince(data) {
  return axios.put(`${window.APP_API_URL}/api/provinces/update`, data);
}
export function apiReadProvince(id_province) {
  return axios.get(`${window.APP_API_URL}/api/provinces/read/${id_province}`);
}
export function apiDeleteProvince(id_province) {
  return axios.delete(`${window.APP_API_URL}/api/provinces/delete/${id_province}`);
}

// District CRUD
export function apiCreateDistrict(data) {
  return axios.post(`${window.APP_API_URL}/api/districts/create`, data);
}
export function apiUpdateDistrict(data) {
  return axios.put(`${window.APP_API_URL}/api/districts/update`, data);
}
export function apiReadDistrict(id_district) {
  return axios.get(`${window.APP_API_URL}/api/districts/read/${id_district}`);
}
export function apiDeleteDistrict(id_district) {
  return axios.delete(`${window.APP_API_URL}/api/districts/delete/${id_district}`);
}

// Commune CRUD
export function apiCreateCommune(data) {
  return axios.post(`${window.APP_API_URL}/api/communes/create`, data);
}
export function apiUpdateCommune(data) {
  return axios.put(`${window.APP_API_URL}/api/communes/update`, data);
}
export function apiReadCommune(id_commune) {
  return axios.get(`${window.APP_API_URL}/api/communes/read/${id_commune}`);
}
export function apiDeleteCommune(id_commune) {
  return axios.delete(`${window.APP_API_URL}/api/communes/delete/${id_commune}`);
}

// Village CRUD
export function apiCreateVillage(data) {
  return axios.post(`${window.APP_API_URL}/api/villages/create`, data);
}
export function apiUpdateVillage(data) {
  return axios.put(`${window.APP_API_URL}/api/villages/update`, data);
}
export function apiReadVillage(id_village) {
  return axios.get(`${window.APP_API_URL}/api/villages/read/${id_village}`);
}
export function apiDeleteVillage(id_village) {
  return axios.delete(`${window.APP_API_URL}/api/villages/delete/${id_village}`);
}
