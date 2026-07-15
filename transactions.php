<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

$pdo = getDb();

try {
    $transactions = $pdo->query('SELECT id, type, amount, description, date FROM transactions ORDER BY date DESC, id DESC')
                        ->fetchAll(PDO::FETCH_ASSOC);

    $incomeTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'income'")
                             ->fetchColumn();
    $expenseTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'expense'")
                              ->fetchColumn();
    $balance = $incomeTotal - $expenseTotal;
} catch (Throwable $e) {
    $transactions = [];
    $incomeTotal = 0.0;
    $expenseTotal = 0.0;
    $balance = 0.0;
    // Debug: show error on page
    $pageError = $e->getMessage();
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Transactions - PaisaLedger</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 24px; background: #f6f7fb; color: #1f2937; }
        .wrap { max-width: 980px; margin: 0 auto; }
        h1 { font-size: 22px; margin: 0 0 16px; }
        .nav { display:flex; gap: 10px; margin-bottom: 16px; }
        .nav a { color:#2563eb; font-weight:700; text-decoration:none; }
        .nav a:hover { text-decoration:underline; }
        .summary { display:grid; grid-template-columns: repeat(3, minmax(160px, 1fr)); gap: 12px; margin-bottom: 16px; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; }
        .label { color:#6b7280; font-size:12px; font-weight:700; margin-bottom:6px; }
        .value { font-size:22px; font-weight:800; }
        table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; }
        th, td { padding:10px 12px; border-bottom:1px solid #eef2f7; text-align:left; font-size:14px; }
        th { background:#f3f4f6; font-weight:800; }
        tr:last-child td { border-bottom:none; }
        .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-weight:800; font-size:12px; }
        .income { background:#dcfce7; color:#166534; border:1px solid #86efac; }
        .expense { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
        .muted { color:#6b7280; font-size:12px; font-weight:600; }
        @media (max-width: 640px) {
            .summary { grid-template-columns: 1fr; }
            th, td { font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Transactions</h1>
    <div class="nav">
        <a href="index.php">Add Transaction</a>
        <a href="transactions.php">View Transactions</a>
    </div>

    <?php if (isset($pageError) && $pageError): ?>
        <div class="err" style="margin-bottom:16px;">DB Select Error: <?php echo htmlspecialchars($pageError); ?></div>
    <?php endif; ?>


    <div class="summary">
        <div class="card">
            <div class="label">Total Income</div>
            <div class="value"><?php echo htmlspecialchars(number_format($incomeTotal, 2, '.', '')); ?></div>
        </div>
        <div class="card">
            <div class="label">Total Expense</div>
            <div class="value"><?php echo htmlspecialchars(number_format($expenseTotal, 2, '.', '')); ?></div>
        </div>
        <div class="card">
            <div class="label">Balance</div>
            <div class="value"><?php echo htmlspecialchars(number_format($balance, 2, '.', '')); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:70px;">ID</th>
                <th style="width:120px;">Type</th>
                <th style="width:140px;">Amount</th>
                <th>Description</th>
                <th style="width:120px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$transactions): ?>
                <tr>
                    <td colspan="5" class="muted" style="padding:18px 12px;">No transactions found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?php echo (int)$t['id']; ?></td>
                        <td>
                            <?php
                                $type = $t['type'];
                                $cls = ($type === 'income') ? 'income' : 'expense';
                                $label = ($type === 'income') ? 'Income' : 'Expense';
                            ?>
                            <span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars($label); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars(number_format((float)$t['amount'], 2, '.', '')); ?></td>
                        <td><?php echo htmlspecialchars((string)$t['description']); ?></td>
                        <td><?php echo htmlspecialchars((string)$t['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
