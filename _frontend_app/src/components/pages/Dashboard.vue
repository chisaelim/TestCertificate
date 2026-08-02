<template>
  <div class="content-wrapper" style="min-height: 1175px;">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">ផ្ទាំងគ្រប់គ្រង</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">ទំព័រដើម</a></li>
              <li class="breadcrumb-item active">ផ្ទាំងគ្រប់គ្រង</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <!-- Stat cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ totals.students }}</h3>
                <p>សិស្សសរុប</p>
              </div>
              <div class="icon"><i class="fa fa-users"></i></div>
              <router-link :to="{ name: 'students' }" class="small-box-footer">
                មើលលម្អិត <i class="fa fa-arrow-circle-right"></i>
              </router-link>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ totals.tests }}</h3>
                <p>មុខវិជ្ជាតេស្ត</p>
              </div>
              <div class="icon"><i class="fa fa-graduation-cap"></i></div>
              <router-link :to="{ name: 'tests' }" class="small-box-footer">
                មើលលម្អិត <i class="fa fa-arrow-circle-right"></i>
              </router-link>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ totals.student_tests }}</h3>
                <p>ការធ្វើតេស្តសរុប</p>
              </div>
              <div class="icon"><i class="fa fa-clipboard-check"></i></div>
              <router-link :to="{ name: 'student-tests' }" class="small-box-footer">
                មើលលម្អិត <i class="fa fa-arrow-circle-right"></i>
              </router-link>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ totals.new_students_this_month }}</h3>
                <p>សិស្សចុះឈ្មោះខែនេះ</p>
              </div>
              <div class="icon"><i class="fa fa-user-plus"></i></div>
              <router-link :to="{ name: 'students' }" class="small-box-footer">
                មើលលម្អិត <i class="fa fa-arrow-circle-right"></i>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Row: registrations over time + status overall -->
        <div class="row">
          <div class="col-lg-8">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">សិស្សចុះឈ្មោះក្នុង ១២ ខែចុងក្រោយ</h3>
              </div>
              <div class="card-body">
                <apexchart type="area" height="320"
                  :options="registrationsOptions" :series="registrationsSeries" />
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card card-outline card-warning">
              <div class="card-header">
                <h3 class="card-title">ស្ថានភាពការធ្វើតេស្តសរុប</h3>
              </div>
              <div class="card-body">
                <apexchart type="donut" height="320"
                  :options="statusOptions" :series="statusSeries" />
              </div>
            </div>
          </div>
        </div>

        <!-- Row: tests popularity (pass/fail/pending stacked) -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">ការធ្វើតេស្តតាមមុខវិជ្ជា</h3>
              </div>
              <div class="card-body">
                <apexchart type="bar" height="340"
                  :options="testsOptions" :series="testsSeries" />
              </div>
            </div>
          </div>
        </div>

        <!-- Row: gender + religion -->
        <div class="row">
          <div class="col-lg-6">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title">សិស្សតាមភេទ</h3>
              </div>
              <div class="card-body">
                <apexchart type="pie" height="320"
                  :options="genderOptions" :series="genderSeries" />
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card card-outline card-success">
              <div class="card-header">
                <h3 class="card-title">សិស្សតាមសាសនា</h3>
              </div>
              <div class="card-body">
                <apexchart type="donut" height="320"
                  :options="religionOptions" :series="religionSeries" />
              </div>
            </div>
          </div>
        </div>

        <!-- Row: nationality + ethnicity -->
        <div class="row">
          <div class="col-lg-6">
            <div class="card card-outline card-secondary">
              <div class="card-header">
                <h3 class="card-title">សិស្សតាមសញ្ជាតិ (ច្រើនជាងគេ ១០)</h3>
              </div>
              <div class="card-body">
                <apexchart type="bar" height="320"
                  :options="nationalityOptions" :series="nationalitySeries" />
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">សិស្សតាមជនជាតិ (ច្រើនជាងគេ ១០)</h3>
              </div>
              <div class="card-body">
                <apexchart type="bar" height="320"
                  :options="ethnicityOptions" :series="ethnicitySeries" />
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from "@/stores/user";
import { apiGetDashboardStats } from "@/functions/api/dashboard";

const userStore = useUserStore();

const stats = ref({
  totals: { students: 0, tests: 0, student_tests: 0, student_tests_today: 0, new_students_this_month: 0 },
  registrations_by_month: [],
  status_today: { PASSED: 0, PENDING: 0, FAILED: 0 },
  status_overall: { PASSED: 0, PENDING: 0, FAILED: 0 },
  by_gender: [],
  by_religion: [],
  by_nationality: [],
  by_ethnicity: [],
  by_test: [],
});

const totals = computed(() => stats.value.totals);

// Registrations
const registrationsSeries = computed(() => [
  { name: "សិស្សថ្មី", data: stats.value.registrations_by_month.map((b) => b.count) },
]);
const registrationsOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: "inherit" },
  xaxis: { categories: stats.value.registrations_by_month.map((b) => b.label) },
  yaxis: { labels: { formatter: (v) => Math.round(v) } },
  dataLabels: { enabled: false },
  stroke: { curve: "smooth", width: 3 },
  colors: ["#007bff"],
  fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
  grid: { borderColor: "#eee" },
  tooltip: { y: { formatter: (v) => `${v} នាក់` } },
}));

// Status (overall)
const statusSeries = computed(() => {
  const s = stats.value.status_overall;
  return [s.PASSED, s.PENDING, s.FAILED];
});
const statusOptions = computed(() => ({
  chart: { fontFamily: "inherit" },
  labels: ["ជាប់", "កំពុងរង់ចាំ", "ធ្លាក់"],
  colors: ["#28a745", "#ffc107", "#dc3545"],
  legend: { position: "bottom" },
  dataLabels: { enabled: true, formatter: (v) => `${Math.round(v)}%` },
  plotOptions: {
    pie: {
      donut: {
        labels: {
          show: true,
          total: { show: true, label: "សរុប", formatter: () => String(totals.value.student_tests) },
        },
      },
    },
  },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

// Tests popularity (stacked PASSED/PENDING/FAILED per test)
const testsSeries = computed(() => [
  { name: "ជាប់",         data: stats.value.by_test.map((t) => t.passed) },
  { name: "កំពុងរង់ចាំ", data: stats.value.by_test.map((t) => t.pending) },
  { name: "ធ្លាក់",        data: stats.value.by_test.map((t) => t.failed) },
]);
const testsOptions = computed(() => ({
  chart: { type: "bar", stacked: true, toolbar: { show: false }, fontFamily: "inherit" },
  plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: "55%" } },
  xaxis: { categories: stats.value.by_test.map((t) => t.label) },
  dataLabels: { enabled: false },
  colors: ["#28a745", "#ffc107", "#dc3545"],
  legend: { position: "top" },
  grid: { borderColor: "#eee" },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

// Gender (pie)
const genderSeries  = computed(() => stats.value.by_gender.map((r) => r.count));
const genderOptions = computed(() => ({
  chart: { fontFamily: "inherit" },
  labels: stats.value.by_gender.map((r) => r.label),
  colors: ["#17a2b8", "#e83e8c", "#6f42c1", "#fd7e14"],
  legend: { position: "bottom" },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

// Religion (donut)
const religionSeries  = computed(() => stats.value.by_religion.map((r) => r.count));
const religionOptions = computed(() => ({
  chart: { fontFamily: "inherit" },
  labels: stats.value.by_religion.map((r) => r.label),
  colors: ["#28a745", "#20c997", "#17a2b8", "#6610f2", "#fd7e14", "#6c757d"],
  legend: { position: "bottom" },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

// Nationality (horizontal bar, top 10)
const nationalitySeries  = computed(() => [
  { name: "ចំនួនសិស្ស", data: stats.value.by_nationality.map((r) => r.count) },
]);
const nationalityOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: "inherit" },
  xaxis: { categories: stats.value.by_nationality.map((r) => r.label) },
  plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
  dataLabels: { enabled: true },
  colors: ["#6c757d"],
  grid: { borderColor: "#eee" },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

// Ethnicity (vertical bar, top 10)
const ethnicitySeries  = computed(() => [
  { name: "ចំនួនសិស្ស", data: stats.value.by_ethnicity.map((r) => r.count) },
]);
const ethnicityOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: "inherit" },
  xaxis: { categories: stats.value.by_ethnicity.map((r) => r.label) },
  plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: "55%" } },
  dataLabels: { enabled: false },
  colors: ["#dc3545"],
  grid: { borderColor: "#eee" },
  noData: { text: "មិនមានទិន្នន័យ" },
}));

onMounted(async () => {
  try {
    LoadingModal();
    const response = await apiGetDashboardStats();
    stats.value = response.data;
    CloseModal();
  } catch (error) {
    MessageModal({
      icon: "error",
      title: "Error",
      text: error.response?.data?.message || error.message,
    });
  }
});
</script>
