<?php

require_once 'db.php';

$mysqli = getDbConnection();

$createTableQuery = "
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        version VARCHAR(255) NOT NULL,
        checksum VARCHAR(64) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(version)
    )
";

if (!$mysqli->query($createTableQuery)) {
    die("Error creating migrations table: " . $mysqli->error);
}

$migrationFiles = glob(__DIR__ . '/migrations/*.sql');

foreach ($migrationFiles as $file) {
    $version = basename($file, '.sql');
    $checksum = hash_file('sha256', $file);

    $stmt = $mysqli->prepare("SELECT checksum FROM migrations WHERE version = ?");
    $stmt->bind_param("s", $version);
    $stmt->execute();
    $stmt->bind_result($existingChecksum);
    $stmt->fetch();
    $stmt->close();

    if ($existingChecksum) {
        if ($existingChecksum !== $checksum) {
            die("Migration {$version} checksum mismatch! Aborting.");
        }
    } else {
        // Apply the migration
        echo "Applying migration: {$version}\n";
        $migrationQuery = file_get_contents($file);
        if (!$mysqli->query($migrationQuery)) {
            die("Error applying migration {$version}: " . $mysqli->error);
        }

        $stmt = $mysqli->prepare("INSERT INTO migrations (version, checksum) VALUES (?, ?)");
        $stmt->bind_param("ss", $version, $checksum);
        if (!$stmt->execute()) {
            die("Error recording migration {$version}: " . $stmt->error);
        }
        $stmt->close();
    }
}

$mysqli->close();
