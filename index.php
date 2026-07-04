<?php
// PaisaLedger: Simple financial ledger app using local SQLite (ledger.db)
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

$pdo = getDb();

// Ye variables form message aur form values ko store karne ke liye hain.
$message = '';
$errors = [];
$formType = 'income';
$formAmount = '';
$formDescription = '';

// Ye check karta hai ki user ne form submit kiya ya nahi.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Ye form data ko safely fetch karta hai.
    $formType = isset($_POST['type']) ? trim((string)$_POST['type']) : 'income';
    $formAmount = isset($_POST['amount']) ? trim((string)$_POST['amount']) : '';
    $formDescription = isset($_POST['description']) ? trim((string)$_POST['description']) : '';

    // Ye date set karne ke liye hai (server time). YYYY-MM-DD format maintain karte hain.
    $today = date('Y-m-d');

    // Validation: type income/expense hona chahiye.
    if (!in_array($formType, ['income', 'expense'], true)) {
        $errors[] = 'Type sirf income ya expense hona chahiye.';
    }

    // Validation: amount number hona chahiye aur > 0.
    $amountNum = filter_var($formAmount, FILTER_VALIDATE_FLOAT);
    if ($amountNum === false || $amountNum <= 0) {
        $errors[] = 'Amount ek positive number hona chahiye.';
    }

    // Validation: description optional hai, but length limit rakhte hain.
    if ($formDescription !== '' && mb_strlen($formDescription) > 500) {
        $errors[] = 'Description 500 characters se zyada nahi honi chahiye.';
    }

    // Agar errors nahi hain, to insert query run hoti hai.
    if (!$errors) {
        // Ye SQL query prepared statement ke saath naya transaction record insert karti hai.
        $stmt = $pdo->prepare(
            'INSERT INTO transactions (type, amount, description, date) VALUES (:type, :amount, :description, :date)'
        );

        $stmt->execute([
            ':type' => $formType,
            ':amount' => $amountNum,
            ':description' => $formDescription,
            ':date' => $today,
        ]);

        $message = 'Transaction successfully add ho gaya.';

        // Insert ke baad form clear kar dete hain.
        $formAmount = '';
        $formDescription = '';
        $formType = $formType;
    } else {
        // Agar validation fail hui, to message me errors show karte hain.
        $message = 'Please sahi details fill karein.';
    }
}

// Ye transactions ko database se fetch karta hai taaki HTML table me display ho sake.
$transactions = $pdo->query('SELECT id, type, amount, description, date FROM transactions ORDER BY date DESC, id DESC')
                    ->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PaisaLedger</title>
    <style>
        /* Basic clean UI styling (inline CSS) */
        body { font-family: Arial, Helvetica, sans-serif; margin: 24px; background: #f6f7fb; color: #1f2937; }
        .wrap { max-width: 980px; margin: 0 auto; }
        h1 { font-size: 22px; margin: 0 0 16px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
        label { display:block; margin: 10px 0 6px; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; box-sizing: border-box; }
        .row { display:flex; gap: 12px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 180px; }
        button { margin-top: 14px; padding: 10px 14px; border: none; border-radius: 8px; background: #2563eb; color: #fff; cursor:pointer; font-weight: 700; }
        button:hover { background: #1d4ed8; }
        .msg { margin: 10px 0 0; padding: 10px 12px; border-radius: 8px; background:#ecfeff; border: 1px solid #a5f3fc; color:#0e7490; font-weight:600; }
        .err { margin: 10px 0 0; padding: 10px 12px; border-radius: 8px; background:#fef2f2; border: 1px solid #fecaca; color:#b91c1c; font-weight:700; }

        table { width: 100%; border-collapse: collapse; background:#fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow:hidden; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eef2f7; text-align:left; font-size: 14px; }
        th { background:#f3f4f6; font-weight: 800; }
        tr:last-child td { border-bottom: none; }
        .badge { display:inline-block; padding: 4px 10px; border-radius: 999px; font-weight: 800; font-size: 12px; }
        .income { background:#dcfce7; color:#166534; border:1px solid #86efac; }
        .expense { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
        .muted { color:#6b7280; font-size: 12px; font-weight:600; }
        .footer-note { margin-top: 10px; color:#6b7280; font-size: 12px; font-weight:600; }
        .nav { display:flex; gap: 10px; margin-bottom: 16px; }
        .nav a { color:#2563eb; font-weight:700; text-decoration:none; }
        .nav a:hover { text-decoration:underline; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>PaisaLedger</h1>
    <div class="nav">
        <a href="index.php">Add Transaction</a>
        <a href="transactions.php">View Transactions</a>
    </div>

    <div class="card">
        <!-- Ye form user se income/expense data collect karta hai -->
        <form method="post" action="">
            <div class="row">
                <div class="col">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="income" <?php echo ($formType === 'income') ? 'selected' : ''; ?>>Income</option>
                        <option value="expense" <?php echo ($formType === 'expense') ? 'selected' : ''; ?>>Expense</option>
                    </select>
                </div>

                <div class="col">
                    <label for="amount">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0" value="<?php echo htmlspecialchars((string)$formAmount); ?>" required />
                </div>
            </div>

            <label for="description">Description (Optional)</label>
            <input id="description" name="description" type="text" maxlength="500" placeholder="Jaise: salary, rent, etc" value="<?php echo htmlspecialchars((string)$formDescription); ?>" />

            <!-- Ye button form submit karta hai -->
            <button type="submit">Add Transaction</button>

            <?php if ($message): ?>
                <?php if ($errors): ?>
                    <div class="err"> <?php echo htmlspecialchars($message); ?>
                        <?php if ($errors): ?>
                            <div class="muted" style="margin-top:6px;">
                                <?php echo htmlspecialchars(implode(' ', $errors)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="msg"> <?php echo htmlspecialchars($message); ?> </div>
                <?php endif; ?>
            <?php endif; ?>
        </form>

        <div class="footer-note">Note: Data MySQL database <b>paisa_ledger</b> me store hota hai.</div>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        <!-- Ye section database se aayi hui transactions ko table me dikhata hai -->
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
</div>
</body>
</html>

