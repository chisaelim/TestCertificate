export function apiGetDashboardStats() {
  return axios.get(`${window.APP_API_URL}/dashboard/stats`);
}
