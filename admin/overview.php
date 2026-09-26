<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireRole('admin');

$page_title = 'Overview';

// Fetch stats
$stats = [
    'students' => $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'teachers' => $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
    'classes' => $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn(),
    'subjects' => $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn()
];

// Fetch attendance stats
$attendance_stats = $pdo->query("SELECT status, COUNT(*) as count FROM attendance GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$present_count = $attendance_stats['Present'] ?? 0;
$absent_count = $attendance_stats['Absent'] ?? 0;
$late_count = $attendance_stats['Late'] ?? 0;

// Fetch fee stats for the current year
$fee_stats = $pdo->query("
    SELECT DATE_FORMAT(payment_date, '%b') as month, SUM(amount_paid) as total
    FROM fee_payments
    WHERE YEAR(payment_date) = YEAR(CURDATE())
    GROUP BY MONTH(payment_date), DATE_FORMAT(payment_date, '%b')
    ORDER BY MONTH(payment_date)
")->fetchAll(PDO::FETCH_KEY_PAIR);

if (empty($fee_stats)) {
    $fee_months = [date('M')]; // Default to current month if no data
    $fee_amounts = [0];
} else {
    $fee_months = array_keys($fee_stats);
    $fee_amounts = array_values($fee_stats);
}

$fee_months_json = json_encode($fee_months);
$fee_amounts_json = json_encode($fee_amounts);

include '../includes/header.php';
?>

<!-- Content Row: Stats -->
<div class="row">
    <!-- Students Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Students</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $stats['students'] ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-primary-soft"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teachers Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Teachers</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $stats['teachers'] ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-success-soft"><i class="fas fa-chalkboard-teacher"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Classes</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $stats['classes'] ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-info-soft"><i class="fas fa-chalkboard"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Subjects</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $stats['subjects'] ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-warning-soft"><i class="fas fa-book"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row: Charts -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student Attendance Overview</h6>
            </div>
            <div class="card-body text-center">
                <canvas id="attendanceChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Fee Collection (<?= date('Y') ?>)</h6>
            </div>
            <div class="card-body text-center">
                <canvas id="feesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx1 = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [<?= $present_count ?>, <?= $absent_count ?>, <?= $late_count ?>],
                backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e']
            }]
        }
    });

    const ctx2 = document.getElementById('feesChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?= $fee_months_json ?>,
            datasets: [{
                label: 'Collection',
                data: <?= $fee_amounts_json ?>,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
