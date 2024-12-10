<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversion Ratio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<?php
// Database credentials, very secure lol
$host = "192.168.1.11";
$dbname = "thesixthstring";
$username = "default";
$password = "rEN28Sd8?W|L6FquVky>";

try {
    // Database connection
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Conversion ratio query, never touching it again
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
    $result = $pdo->query($conversionRatio);

    // Fetch data
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $data = [];
    }

    // Catch exception
} catch (PDOException $e) {
    echo "<tr><td colspan='3'>Connection failed: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>

<div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Conversion ratio</h2>

        <p class="mt-4 text-gray-500 sm:text-xl">
            View daily conversion ratios.
        </p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 lg:grid-cols-4">
        <!--        Creates a div per data point-->
        <?php if (count($data) > 0): ?>
            <?php foreach ($data as $row): ?>
                <div class="flex flex-col rounded-lg border border-gray-100 px-4 py-8 text-center">
                    <dt class="order-last text-lg font-medium text-gray-500">
                        Date: <?php echo htmlspecialchars($row['log_date']); ?></dt>
                    <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl"><?php echo number_format($row['conversion_ratio'], 2); ?>
                        %
                    </dd>
                    <p class="mt-2 text-gray-500">
                        Change: <?php echo $row['percentage_change'] ? number_format($row['percentage_change'], 2) . '%' : 'N/A'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">Not enough data available.</p>
        <?php endif; ?>
    </dl>
</div>
</body>
</html>
