<?php
// Shared database connection for PaisaLedger.
function getDb(): PDO {
    $dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'ledger.db';
    $dsn = 'sqlite:' . $dbFile;

    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            amount REAL NOT NULL,
            description TEXT,
            date TEXT NOT NULL
        );
    ");

    return $pdo;
}
