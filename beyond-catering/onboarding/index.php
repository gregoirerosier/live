<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once dirname(__DIR__, 2) . '/config/db.php';
$pdo = db();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/ecosystem.php';
beyond_nav_bootstrap('Beyond Catering');

$vendor_id = (int) $_SESSION['user_id'];
$_SESSION['vendor_id'] = $vendor_id;

$stmt = $pdo->prepare("SELECT * FROM onboarding_progress WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$progress = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$progress) {
    $stmt = $pdo->prepare("INSERT INTO onboarding_progress (vendor_id, current_step, completed_steps) VALUES (?, 1, JSON_ARRAY())");
    $stmt->execute([$vendor_id]);

    $stmt = $pdo->prepare("SELECT * FROM onboarding_progress WHERE vendor_id = ?");
    $stmt->execute([$vendor_id]);
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);
}

$currentStep = (int)($progress['current_step'] ?? 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beyond Catering Onboarding</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="onboarding.css">
</head>
<body>

<div class="wizard-shell">

    <aside class="sidebar">
        <div class="brand">🍽 Beyond<br><span>Catering</span></div>

        <div class="progress-box">
            <small>Setup Progress</small>
            <strong id="progressText">10% Complete</strong>
            <div class="bar">
                <div id="progressBar"></div>
            </div>
        </div>

        <nav>
            <button class="step-link active" data-step="1">✓ Welcome</button>
            <button class="step-link" data-step="2">2 Business Info</button>
            <button class="step-link" data-step="3">3 Branding</button>
            <button class="step-link" data-step="4">4 Theme</button>
            <button class="step-link" data-step="5">5 Menu</button>
            <button class="step-link" data-step="6">6 Payments</button>
            <button class="step-link" data-step="7">7 Domain</button>
            <button class="step-link" data-step="8">8 Hours</button>
            <button class="step-link" data-step="9">9 Delivery</button>
            <button class="step-link" data-step="10">🚀 Publish</button>
        </nav>

        <div class="save-status" id="saveStatus">Auto-saved</div>
    </aside>

    <main class="wizard-main">

        <header class="topbar">
            <div>
                <small id="stepCounter">Step 1 of 10</small>
                <h1 id="stepTitle">Welcome to Beyond Catering</h1>
                <p id="stepSubtitle">Let’s get your restaurant online.</p>
            </div>
            <a href="../dashboard/index.php" class="exit-btn">Save & Exit</a>
        </header>

        <section class="content-grid">

            <form id="wizardForm" class="card">

                <div class="step-panel active" data-step="1">
                    <h2>Welcome 👋</h2>
                    <p>You’re about to launch your restaurant website, menu, ordering system, and dashboard.</p>

                    <div class="checklist">
                        <span>✓ Professional website</span>
                        <span>✓ Online ordering</span>
                        <span>✓ Stripe payments</span>
                        <span>✓ Mobile-ready dashboard</span>
                    </div>
                </div>

                <div class="step-panel" data-step="2">
                    <label>Business Name</label>
                    <input name="business_name" placeholder="Zak's Kitchen">

                    <label>Business Type</label>
                    <select name="business_type">
                        <option value="restaurant">Restaurant</option>
                        <option value="caterer">Caterer</option>
                        <option value="food_truck">Food Truck</option>
                        <option value="bakery">Bakery</option>
                        <option value="cafe">Café</option>
                        <option value="meal_prep">Meal Prep</option>
                    </select>

                    <label>Phone Number</label>
                    <input name="phone" placeholder="(555) 123-4567">

                    <label>Business Email</label>
                    <input name="email" type="email" placeholder="hello@example.com">

                    <label>Address</label>
                    <input name="address" placeholder="123 Main Street">
                </div>

                <div class="step-panel" data-step="3">
                    <label>Logo URL</label>
                    <input name="logo_url" placeholder="/uploads/logo.png">

                    <label>Cover Photo URL</label>
                    <input name="cover_url" placeholder="/uploads/cover.jpg">

                    <label>Gallery Image URLs</label>
                    <textarea name="gallery_urls" placeholder="One image URL per line"></textarea>
                </div>

                <div class="step-panel" data-step="4">
                    <label>Choose Theme</label>
                    <div class="theme-grid">
                        <label><input type="radio" name="theme" value="modern" checked> Modern</label>
                        <label><input type="radio" name="theme" value="dark"> Dark</label>
                        <label><input type="radio" name="theme" value="luxury"> Luxury</label>
                        <label><input type="radio" name="theme" value="classic"> Classic</label>
                    </div>

                    <label>Brand Color</label>
                    <input name="brand_color" type="color" value="#ff6a00">
                </div>

                <div class="step-panel" data-step="5">
                    <label>Menu Category</label>
                    <input name="menu_category" placeholder="Dinner">

                    <label>First Menu Item</label>
                    <input name="item_name" placeholder="Grilled Salmon">

                    <label>Description</label>
                    <textarea name="item_description" placeholder="Fresh, bold, delicious."></textarea>

                    <label>Price</label>
                    <input name="item_price" placeholder="24.99">
                </div>

                <div class="step-panel" data-step="6">
                    <h2>Connect Stripe</h2>
                    <p>Accept credit cards, Apple Pay, Google Pay, and online orders.</p>
                    <input name="stripe_status" type="hidden" value="skipped">
                    <button type="button" class="secondary-btn" onclick="markStripe()">Mark Stripe Connected</button>
                </div>

                <div class="step-panel" data-step="7">
                    <label>Website Address</label>
                    <input name="subdomain" placeholder="zakskitchen">

                    <label>Existing Domain</label>
                    <input name="custom_domain" placeholder="zakskitchenofficial.com">
                </div>

                <div class="step-panel" data-step="8">
                    <label>Business Hours</label>
                    <textarea name="business_hours" placeholder="Mon-Fri 9AM-8PM&#10;Sat-Sun 10AM-6PM"></textarea>
                </div>

                <div class="step-panel" data-step="9">
                    <label><input type="checkbox" name="pickup" value="1"> Pickup</label>
                    <label><input type="checkbox" name="delivery" value="1"> Delivery</label>
                    <label><input type="checkbox" name="dine_in" value="1"> Dine-In</label>

                    <label>Delivery Radius</label>
                    <input name="delivery_radius" placeholder="10 km">

                    <label>Delivery Fee</label>
                    <input name="delivery_fee" placeholder="4.99">
                </div>

                <div class="step-panel" data-step="10">
                    <h2>Ready to publish 🚀</h2>
                    <p>Review your setup and launch your restaurant website.</p>
                    <button type="button" class="publish-btn" onclick="publishSite()">Publish Website</button>
                </div>

                <div class="buttons">
                    <button type="button" id="backBtn">← Back</button>
                    <button type="button" id="nextBtn">Continue →</button>
                </div>

            </form>

            <aside class="preview card">
                <small>Live Preview</small>
                <div class="site-preview">
                    <div class="preview-hero">
                        <strong id="previewName">Your Restaurant</strong>
                        <h2>Delicious food,<br><span>made with love</span></h2>
                        <p>Fresh ingredients, bold flavors, unforgettable experience.</p>
                        <button>Order Now</button>
                    </div>

                    <div class="preview-menu">
                        <h3>Our Favorites</h3>
                        <div class="mini-item">
                            <div></div>
                            <span id="previewItem">Menu Item</span>
                            <strong id="previewPrice">$0.00</strong>
                        </div>
                    </div>
                </div>
            </aside>

        </section>
    </main>
</div>

<script>
let currentStep = <?= $currentStep ?>;
const totalSteps = 10;

const titles = {
    1: ["Welcome to Beyond Catering", "Let’s get your restaurant online."],
    2: ["Tell us about your business", "This information personalizes your website."],
    3: ["Upload your branding", "Add your logo, cover image, and gallery."],
    4: ["Choose your style", "Pick the look and feel of your restaurant site."],
    5: ["Build your menu", "Start with your first category and menu item."],
    6: ["Accept payments", "Connect Stripe or skip for now."],
    7: ["Choose your website address", "Use a Beyond Catering subdomain or your own domain."],
    8: ["Set business hours", "Let customers know when you’re open."],
    9: ["Delivery and pickup", "Choose how customers receive orders."],
    10: ["Review and publish", "Launch your restaurant website."]
};

function showStep(step) {
    currentStep = step;

    document.querySelectorAll('.step-panel').forEach(panel => {
        panel.classList.toggle('active', Number(panel.dataset.step) === step);
    });

    document.querySelectorAll('.step-link').forEach(link => {
        link.classList.toggle('active', Number(link.dataset.step) === step);
    });

    document.getElementById('stepCounter').textContent = `Step ${step} of ${totalSteps}`;
    document.getElementById('stepTitle').textContent = titles[step][0];
    document.getElementById('stepSubtitle').textContent = titles[step][1];

    const percent = Math.round((step / totalSteps) * 100);
    document.getElementById('progressText').textContent = `${percent}% Complete`;
    document.getElementById('progressBar').style.width = `${percent}%`;

    document.getElementById('backBtn').style.display = step === 1 ? 'none' : 'inline-flex';
    document.getElementById('nextBtn').style.display = step === 10 ? 'none' : 'inline-flex';

    saveStep();
}

function getFormData() {
    const form = document.getElementById('wizardForm');
    const data = new FormData(form);
    data.append('current_step', currentStep);
    return data;
}

function saveStep() {
    document.getElementById('saveStatus').textContent = 'Saving...';

    fetch('save-step.php', {
        method: 'POST',
        body: getFormData()
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('saveStatus').textContent = data.success ? 'Auto-saved just now' : 'Save failed';
    })
    .catch(() => {
        document.getElementById('saveStatus').textContent = 'Connection issue';
    });
}

function updatePreview() {
    const businessName = document.querySelector('[name="business_name"]')?.value || 'Your Restaurant';
    const itemName = document.querySelector('[name="item_name"]')?.value || 'Menu Item';
    const price = document.querySelector('[name="item_price"]')?.value || '0.00';
    const color = document.querySelector('[name="brand_color"]')?.value || '#ff6a00';

    document.getElementById('previewName').textContent = businessName;
    document.getElementById('previewItem').textContent = itemName;
    document.getElementById('previewPrice').textContent = '$' + price;
    document.documentElement.style.setProperty('--accent', color);
}

function markStripe() {
    document.querySelector('[name="stripe_status"]').value = 'connected';
    saveStep();
    alert('Stripe marked as connected.');
}

function publishSite() {
    fetch('publish.php', {
        method: 'POST',
        body: getFormData()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '../dashboard/index.php?published=1';
        } else {
            alert(data.message || 'Publish failed.');
        }
    });
}

document.getElementById('nextBtn').addEventListener('click', () => {
    if (currentStep < totalSteps) showStep(currentStep + 1);
});

document.getElementById('backBtn').addEventListener('click', () => {
    if (currentStep > 1) showStep(currentStep - 1);
});

document.querySelectorAll('.step-link').forEach(link => {
    link.addEventListener('click', () => showStep(Number(link.dataset.step)));
});

document.querySelectorAll('input, textarea, select').forEach(field => {
    field.addEventListener('input', () => {
        updatePreview();
        clearTimeout(window.saveTimer);
        window.saveTimer = setTimeout(saveStep, 700);
    });
});

showStep(currentStep);
updatePreview();
</script>

</body>
</html>
