<?php
// Shared MySQL database connection for PaisaLedger.
function getDb(): PDO {
    $host = '127.0.0.1';
    $dbName = 'paisa_ledger';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $serverDsn = "mysql:host={$host};charset={$charset}";
        $serverPdo = new PDO($serverDsn, $username, $password, $options);
        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $dbDsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";
        $pdo = new PDO($dbDsn, $username, $password, $options);

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS transactions (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                type ENUM('income', 'expense') NOT NULL,
                amount DECIMAL(10, 2) NOT NULL,
                description VARCHAR(500) NULL,
                date DATE NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        // Ensure table is reachable (debug aid)
        $pdo->query('SELECT 1')->fetchColumn();

        return $pdo;
    } catch (Throwable $e) {
        // Show connection/SQL error to help fix missing data issues
        http_response_code(500);
        echo "<pre style='background:#fee2e2;color:#991b1b;padding:12px;border:1px solid #fecaca;'>DB ERROR: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
        exit;
    }
}

