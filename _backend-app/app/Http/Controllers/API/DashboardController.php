<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\GetDashboardStatsRequest;
use App\Models\Student;
use App\Models\StudentTest;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(GetDashboardStatsRequest $request)
    {
        $today = Carbon::today()->toDateString();

        // Totals
        $totalStudents = Student::withoutGlobalScopes()->count();
        $totalTests = Test::count();
        $totalStudentTests = StudentTest::count();
        $totalToday = StudentTest::where('issued_date', $today)->count();

        $newStudentsThisMonth = Student::withoutGlobalScopes()
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Registrations over the last 12 months (fill missing months with 0)
        $start = Carbon::now()->startOfMonth()->subMonths(11);
        $rawRegistrations = Student::withoutGlobalScopes()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->reorder()
            ->pluck('count', 'ym');

        $registrationsByMonth = [];
        for ($i = 0; $i < 12; $i++) {
            $d = (clone $start)->addMonths($i);
            $key = $d->format('Y-m');
            $registrationsByMonth[] = [
                'key' => $key,
                'label' => $d->format('M y'),
                'count' => (int) ($rawRegistrations[$key] ?? 0),
            ];
        }

        // Student test status (today + overall)
        $statusToday = $this->statusCounts(StudentTest::where('issued_date', $today));
        $statusOverall = $this->statusCounts(StudentTest::query());

        // Students by gender
        $byGender = Student::withoutGlobalScopes()
            ->leftJoin('genders', 'students.gender_id', '=', 'genders.id')
            ->select(
                DB::raw('COALESCE(genders.gd_kh_full, genders.gd_en_full, "មិនបញ្ជាក់") as label'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('label')
            ->reorder('count', 'desc')
            ->get();

        // Students by religion
        $byReligion = Student::withoutGlobalScopes()
            ->leftJoin('religions', 'students.religion_id', '=', 'religions.id')
            ->select(
                DB::raw('COALESCE(religions.rel_label, religions.rel_kh, religions.rel_en, "មិនបញ្ជាក់") as label'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('label')
            ->reorder('count', 'desc')
            ->get();

        // Students by nationality (top 10)
        $byNationality = Student::withoutGlobalScopes()
            ->leftJoin('nationalities', 'students.nationality_id', '=', 'nationalities.id')
            ->select(
                DB::raw('COALESCE(nationalities.nat_label, nationalities.nat_kh, nationalities.nat_en, "មិនបញ្ជាក់") as label'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('label')
            ->reorder('count', 'desc')
            ->limit(10)
            ->get();

        // Students by ethnicity (top 10)
        $byEthnicity = Student::withoutGlobalScopes()
            ->leftJoin('ethnicities', 'students.ethnicity_id', '=', 'ethnicities.id')
            ->select(
                DB::raw('COALESCE(ethnicities.eth_label, ethnicities.eth_kh, ethnicities.eth_en, "មិនបញ្ជាក់") as label'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('label')
            ->reorder('count', 'desc')
            ->limit(10)
            ->get();

        // Student tests per test (popularity) with pass-rate
        $byTest = StudentTest::join('tests', 'student_tests.test_id', '=', 'tests.id')
            ->select(
                'tests.id as test_id',
                'tests.short_name as label',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'PASSED' THEN 1 ELSE 0 END) as passed"),
                DB::raw("SUM(CASE WHEN status = 'FAILED' THEN 1 ELSE 0 END) as failed"),
                DB::raw("SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending")
            )
            ->groupBy('tests.id', 'tests.short_name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $total = (int) $row->total;
                return [
                    'test_id' => (int) $row->test_id,
                    'label' => $row->label,
                    'total' => $total,
                    'passed' => (int) $row->passed,
                    'failed' => (int) $row->failed,
                    'pending' => (int) $row->pending,
                    'pass_rate' => $total > 0 ? round(((int) $row->passed) * 100 / $total, 2) : 0,
                ];
            });

        return response(
            [
                'totals' => [
                    'students' => $totalStudents,
                    'tests' => $totalTests,
                    'student_tests' => $totalStudentTests,
                    'student_tests_today' => $totalToday,
                    'new_students_this_month' => $newStudentsThisMonth,
                ],
                'registrations_by_month' => $registrationsByMonth,
                'status_today' => $statusToday,
                'status_overall' => $statusOverall,
                'by_gender' => $byGender,
                'by_religion' => $byReligion,
                'by_nationality' => $byNationality,
                'by_ethnicity' => $byEthnicity,
                'by_test' => $byTest,
            ],
            200,
        );
    }

    private function statusCounts($query): array
    {
        $rows = (clone $query)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'PASSED' => (int) ($rows['PASSED'] ?? 0),
            'PENDING' => (int) ($rows['PENDING'] ?? 0),
            'FAILED' => (int) ($rows['FAILED'] ?? 0),
        ];
    }
}
