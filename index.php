<?php
// --- DATABASE SETUP ---
$db = new PDO('sqlite:tracker.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    amount REAL NOT NULL,
    type TEXT NOT NULL,
    category TEXT NOT NULL,
    trans_date DATE NOT NULL
)");

// --- CSV EXPORT HANDLER ---
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=JD_Financial_Report_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Title', 'Amount (INR)', 'Type', 'Category', 'Date']);
    
    $exportRows = $db->query("SELECT * FROM transactions ORDER BY trans_date DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($exportRows as $row) {
        fputcsv($output, [$row['id'], $row['title'], $row['amount'], ucfirst($row['type']), $row['category'], $row['trans_date']]);
    }
    fclose($output);
    exit;
}

// --- ACTION HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaction'])) {
    $stmt = $db->prepare("INSERT INTO transactions (title, amount, type, category, trans_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'],
        floatval($_POST['amount']),
        $_POST['type'],
        trim($_POST['category']),
        $_POST['trans_date']
    ]);
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: index.php");
    exit;
}

// --- DATA FETCHING ---
$filterType = $_GET['filter_type'] ?? 'all';

$sql = "SELECT * FROM transactions";
if ($filterType === 'income' || $filterType === 'expense') {
    $sql .= " WHERE type = " . $db->quote($filterType);
}
$sql .= " ORDER BY trans_date DESC, id DESC";

$transactions = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$totalIncome = $db->query("SELECT SUM(amount) FROM transactions WHERE type='income'")->fetchColumn() ?: 0;
$totalExpense = $db->query("SELECT SUM(amount) FROM transactions WHERE type='expense'")->fetchColumn() ?: 0;
$netBalance = $totalIncome - $totalExpense;

// --- MONTHLY BUDGET CALCULATIONS ---
$monthlyBudget = 25000; 
$currentMonth = date('Y-m');
$thisMonthExpense = $db->query("SELECT SUM(amount) FROM transactions WHERE type='expense' AND strftime('%Y-%m', trans_date) = '$currentMonth'")->fetchColumn() ?: 0;
$budgetUsedPercent = min(100, round(($thisMonthExpense / $monthlyBudget) * 100));
$isOverBudget = $thisMonthExpense > $monthlyBudget;

// Chart Data
$chartStmt = $db->query("SELECT category, SUM(amount) as total FROM transactions WHERE type='expense' GROUP BY category");
$chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);

$chartLabels = array_column($chartData, 'category');
$chartValues = array_column($chartData, 'total');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JD Financial | Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container">
    <header>
        <div class="brand">
            <div class="brand-logo" style="font-size: 18px;">JD</div>
            <h1>JD Financial</h1>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="index.php?export=csv" class="filter-btn active" style="background: var(--accent-gradient); color: white;" title="Download Excel Sheet">📥 Export CSV</a>
            <div class="user-profile">
                <span class="live-dot"></span>
                Live Wallet
            </div>
        </div>
    </header>

    <!-- Monthly Budget Alert Banner -->
    <div class="glass-panel" style="margin-bottom: 25px; padding: 20px; border-color: <?= $isOverBudget ? 'var(--expense)' : 'var(--border)' ?>;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
                <strong style="font-size: 14px; color: <?= $isOverBudget ? 'var(--expense)' : 'var(--text-main)' ?>;">
                    <?= $isOverBudget ? '⚠️ Monthly Budget Exceeded!' : '📊 Monthly Budget Status' ?>
                </strong>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                    Spent ₹<?= number_format($thisMonthExpense, 2) ?> of ₹<?= number_format($monthlyBudget, 2) ?> limit
                </div>
            </div>
            <span style="font-weight: 800; font-size: 18px; color: <?= $isOverBudget ? 'var(--expense)' : '#a5b4fc' ?>;">
                <?= $budgetUsedPercent ?>%
            </span>
        </div>
        <div style="width: 100%; background: rgba(10, 14, 23, 0.7); height: 8px; border-radius: 10px; overflow: hidden; position: relative;">
            <div style="width: <?= $budgetUsedPercent ?>%; background: <?= $isOverBudget ? 'linear-gradient(90deg, #f43f5e, #fb7185)' : 'var(--accent-gradient)' ?>; height: 100%; transition: width 1s var(--ease-smooth); box-shadow: 0 0 12px <?= $isOverBudget ? 'var(--expense-glow)' : 'var(--accent-glow)' ?>;"></div>
        </div>
    </div>

    <!-- 3D Interactive Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card balance" data-tilt>
            <p>Total Net Worth</p>
            <h2>₹<?= number_format($netBalance, 2) ?></h2>
        </div>
        <div class="metric-card income" data-tilt>
            <p>Total Inflow</p>
            <h2>+₹<?= number_format($totalIncome, 2) ?></h2>
        </div>
        <div class="metric-card expense" data-tilt>
            <p>Total Outflow</p>
            <h2>-₹<?= number_format($totalExpense, 2) ?></h2>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="glass-panel">
            <h2 class="panel-title">Add Entry</h2>
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="title" placeholder="Client Payment, Coffee..." required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Transaction Type</label>
                    <select name="type" required>
                        <option value="expense">Expense (-)</option>
                        <option value="income">Income (+)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" placeholder="Salary, Food, Investment" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="trans_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <button type="submit" name="add_transaction" class="btn-submit">Confirm Transaction</button>
            </form>
        </div>

        <div>
            <div class="glass-panel" style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="panel-title" style="margin:0;">Recent Activity</h2>
                    <div class="filter-bar">
                        <a href="index.php?filter_type=all" class="filter-btn <?= $filterType === 'all' ? 'active' : '' ?>">All</a>
                        <a href="index.php?filter_type=income" class="filter-btn <?= $filterType === 'income' ? 'active' : '' ?>">Inflow</a>
                        <a href="index.php?filter_type=expense" class="filter-btn <?= $filterType === 'expense' ? 'active' : '' ?>">Outflow</a>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>TRANSACTION</th>
                            <th>CATEGORY</th>
                            <th>AMOUNT</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="4" style="text-align:center; padding: 30px; color: var(--text-muted);">No transactions recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $index => $t): ?>
                                <tr style="animation-delay: <?= $index * 0.05 ?>s;">
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($t['title']) ?></div>
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;"><?= $t['trans_date'] ?></div>
                                    </td>
                                    <td><span style="font-size: 13px; font-weight: 500; color: var(--text-muted);"><?= htmlspecialchars($t['category']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $t['type'] ?>">
                                            <?= $t['type'] === 'income' ? '+' : '-' ?>₹<?= number_format($t['amount'], 2) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="index.php?delete=<?= $t['id'] ?>" class="btn-delete" onclick="return confirm('Delete record?')" title="Delete">✕</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="glass-panel">
                <h2 class="panel-title">Category Distribution</h2>
                <div class="chart-wrapper">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('expenseChart').getContext('2d');
    const categories = <?= json_encode($chartLabels) ?>;
    const amounts = <?= json_encode($chartValues) ?>;

    if (categories.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: amounts,
                    backgroundColor: ['#6366f1', '#10b981', '#f43f5e', '#a855f7', '#f59e0b', '#06b6d4'],
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1400,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 12 } }
                    }
                },
                cutout: '75%'
            }
        });
    } else {
        document.querySelector('.chart-wrapper').innerHTML = '<p style="color: var(--text-muted); font-size: 13px;">No expense data available for visualization.</p>';
    }

    document.querySelectorAll('[data-tilt]').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -10;
            const rotateY = ((x - centerX) / centerX) * 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
    });
</script>

</body>
</html>