<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JD Financial | Smart Money Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero {
            text-align: center;
            padding: 80px 20px 50px;
            max-width: 800px;
            margin: 0 auto;
        }
        .hero-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.4);
            border-radius: 30px;
            color: #a5b4fc;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 35px;
        }
        .cta-btn {
            display: inline-block;
            background: var(--accent-gradient);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 800;
            box-shadow: 0 10px 30px var(--accent-glow);
            transition: all 0.3s var(--ease-spring);
        }
        .cta-btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.6);
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 60px;
        }
        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 30px;
            border-radius: 20px;
            text-align: left;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 28px;
            margin-bottom: 15px;
        }
        .feature-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .feature-card p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div class="brand">
            <div class="brand-logo">JD</div>
            <h1>JD Financial</h1>
        </div>
        <a href="index.php" class="cta-btn" style="padding: 10px 20px; font-size: 14px;">Open App →</a>
    </header>

    <section class="hero">
        <div class="hero-badge">✨ Next-Gen Financial Management</div>
        <h1>Take Control of Your Wealth with JD Financial</h1>
        <p>Track your daily income, monitor expenses in real-time, set monthly budget limits, and download visual analytics report in seconds.</p>
        <a href="index.php" class="cta-btn">Launch Dashboard →</a>
    </section>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Real-time Tracking</h3>
            <p>Log transactions instantly with automatic inflow/outflow balance calculation.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Visual Analytics</h3>
            <p>Dynamic 3D doughnut charts breakdown your category-wise monthly expenses.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚠️</div>
            <h3>Monthly Budget Alert</h3>
            <p>Smart alerts notify you before you exceed your set monthly budget limits.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📥</div>
            <h3>One-Click CSV Export</h3>
            <p>Download complete transaction statements in Excel-ready CSV format anytime.</p>
        </div>
    </div>
</div>

</body>
</html>