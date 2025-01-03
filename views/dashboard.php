<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversieratio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php
// Database credentials
try {
    // Establish connection
    $mysqli = getDbConnection();

    // Conversion ratio query
    $conversionRatio = <<<SQL
WITH daily_counts AS (
    SELECT
        DATE(a.access_time) AS log_date,
        COUNT(DISTINCT a.id_user) AS daily_accesses,
        COALESCE(COUNT(DISTINCT o.id_user), 0) AS daily_orders
    FROM
        user_access_logs a
    LEFT JOIN
        `order` o
    ON
        a.id_user = o.id_user AND DATE(a.access_time) = DATE(o.order_date)
    GROUP BY
        DATE(a.access_time)
),
conversion_ratios AS (
    SELECT
        log_date,
        daily_accesses,
        daily_orders,
        (CASE
            WHEN daily_accesses = 0 THEN 0
            ELSE (daily_orders * 100.0 / daily_accesses)
        END) AS conversion_ratio
    FROM
        daily_counts
),
daily_changes AS (
    SELECT
        log_date,
        conversion_ratio,
        LAG(conversion_ratio) OVER (ORDER BY log_date) AS prev_conversion_ratio,
        (CASE
            WHEN LAG(conversion_ratio) OVER (ORDER BY log_date) IS NULL THEN NULL
            ELSE ((conversion_ratio - LAG(conversion_ratio) OVER (ORDER BY log_date)) * 100.0 / LAG(conversion_ratio) OVER (ORDER BY log_date))
        END) AS percentage_change
    FROM
        conversion_ratios
)
SELECT
    log_date,
    conversion_ratio,
    percentage_change
FROM
    daily_changes
ORDER BY
    log_date
SQL;

    // Execute the query
    $conversion = $mysqli->query($conversionRatio);

    // Prepare data for graphs
    $log_dates = [];
    $conversion_ratios = [];
    $percentage_changes = [];

    if ($conversion) {
        foreach ($conversion as $row) {
            $log_dates[] = $row['log_date'];
            $conversion_ratios[] = $row['conversion_ratio'];
            $percentage_changes[] = $row['percentage_change'] ?? null;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Conversieratio</h2>

        <p class="mt-4 text-gray-500 sm:text-xl">
            Bekijk dagelijkse conversieratio's.
        </p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 lg:grid-cols-4">
        <?php if (!empty($log_dates)): ?>
            <?php foreach ($conversion as $row): ?>
                <div class="flex flex-col rounded-lg border border-gray-100 px-4 py-8 text-center">
                    <dt class="order-last text-lg font-medium text-gray-500">
                        Datum: <?php echo htmlspecialchars($row['log_date']); ?></dt>
                    <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl">
                        <?php echo number_format($row['conversion_ratio'], 2); ?>%
                    </dd>
                    <p class="mt-2 text-gray-500">
                        Verandering: <?php echo $row['percentage_change'] ? number_format($row['percentage_change'], 2) . '%' : 'N/B'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">Niet genoeg data beschikbaar.</p>
        <?php endif; ?>
    </dl>

    <div class="mt-12">
        <h3 class="text-2xl font-bold text-gray-900 sm:text-3xl text-center">Grafieken</h3>

        <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Graph 1: Daily Conversion Ratio -->
            <div class="flex flex-col rounded-lg border border-gray-100 px-4 py-8 text-center">
                <canvas id="conversionRatioChart"></canvas>
            </div>

            <!-- Graph 2: Percentage Change -->
            <div class="flex flex-col rounded-lg border border-gray-100 px-4 py-8 text-center">
                <canvas id="percentageChangeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Data for graphs
    const logDates = <?php echo json_encode($log_dates); ?>;
    const conversionRatios = <?php echo json_encode($conversion_ratios); ?>;
    const percentageChanges = <?php echo json_encode($percentage_changes); ?>;

    // Graph 1: Daily Conversion Ratio
    new Chart(document.getElementById('conversionRatioChart'), {
        type: 'line',
        data: {
            labels: logDates,
            datasets: [{
                label: 'Dagelijkse Conversieratio (%)',
                data: conversionRatios,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Dagelijkse Conversieratio'
                }
            }
        }
    });

    // Graph 2: Percentage Change
    new Chart(document.getElementById('percentageChangeChart'), {
        type: 'bar',
        data: {
            labels: logDates,
            datasets: [{
                label: 'Procentuele Verandering (%)',
                data: percentageChanges,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Dagelijkse Procentuele Verandering'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
