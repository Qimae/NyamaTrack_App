<?php
// Include configuration
require_once '../config.php';

// Initialize variables
$message = '';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';

// Check for error messages from process_payment.php
if (isset($_GET['error'])) {
  $errorMsg = urldecode($_GET['error']);
  $message = '<div class="alert alert-danger">' . htmlspecialchars($errorMsg) . '</div>';
}

// Start the session and check authentication
session_start();

// Check if user is logged in, redirect to login if not
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Get business name from session
$business_name = isset($_SESSION['business_name']) ? htmlspecialchars($_SESSION['business_name']) : 'My Butchery';

// Debug information (remove in production)
if (!isset($_SESSION['business_name'])) {
  error_log('Business name not found in session. Available session data: ' . print_r($_SESSION, true));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NyamaTrack — <?php echo $business_name; ?> Subscription | Nyamatrack.co.ke</title>
  <meta name="description" content="Subscribe your butchery to NyamaTrack. Flexible pricing, M‑Pesa and card options. Nyamatrack.co.ke" />
  <link rel="stylesheet" href="../utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <link rel="stylesheet" href="../utils/becken.css">
  <script src="../js/subscription_script.js"></script>

</head>

<body>
  <?php include 'left-sidebar.php'; ?>
  <?php include 'bottom-sidebar.php'; ?>
  <div class="bg" aria-hidden="true">
    <div class="orb red"></div>
    <div class="orb amber"></div>
    <div class="grid-overlay"></div>
  </div>

  <main class="py-4 dashboard">
    <div class="container-fluid px-3">
      <section class="hero mb-4">
        <h1>Subscribe Your Butchery</h1>
        <p class="muted">Secure billing with M‑Pesa and Cards. Choose a plan that scales with your meat business.</p>
      </section>

      <div class="row g-4 mx-0">
        <div class="col-lg-6 px-2">
          <div class="card h-100 m-0">
            <div class="row" style="justify-content:space-between; align-items:center">
              <h3 id="plans" class="muted">Plans</h3>
              <div class="switch" role="tablist" aria-label="Billing cadence">
                <button id="bill-monthly" class="active" role="tab" aria-selected="true">Monthly</button>
                <button id="bill-annual" role="tab" aria-selected="false">Annual <span class="badge" style="margin-left:6px">Save 15%</span></button>
              </div>
            </div> <br>
            <div class="plans" id="plan-list">
              <div class="plan" role="tab" aria-selected="true">
                <h4>Basic</h4>
                <p class="muted">For small butcheries</p>
                <div class="price">KES 1,500</div>
                <ul>
                  <li>1000kg per month</li>
                  <li>Less than KES 500,000 sales per month</li>
                </ul>
                <button class="btn primary">Select Plan</button>
              </div>
              <div class="plan" role="tab" aria-selected="false">
                <h4>Pro</h4>
                <p class="muted">For medium butcheries</p>
                <div class="price">KES 2,500</div>
                <ul>
                  <li>2000kg per month</li>
                  <li>Less than KES 1,000,000 sales per month</li>
                </ul>
                <button class="btn primary">Select Plan</button>
              </div>
              <div class="plan" role="tab" aria-selected="false">
                <h4>Enterprise</h4>
                <p class="muted">For large butcheries</p>
                <div class="price">KES 3,500</div>
                <ul>
                  <li>5000kg per month</li>
                  <li>Less than KES 5,000,000 sales per month</li>
                </ul>
                <button class="btn primary">Select Plan</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6 px-2">
          <aside class="card h-100 m-0" aria-labelledby="checkout">
            <form method="POST" action="process_payment.php">
              <h3 id="checkout" class="muted">Checkout</h3>
              <div class="field">
                <label for="butchery-name">Butchery Name:</label>
                <input id="butchery-name" name="butchery-name" value="<?php echo htmlspecialchars($business_name); ?>" readonly class="readonly-input" />
              </div>

              <div class="field">
                <label for="plan-type">Plan Type:</label>
                <select id="plan-type" name="plan-type" aria-describedby="plan-type-note">
                  <option value="monthly">Monthly</option>
                  <option value="annual">Annual</option>
                </select>
              </div>
              <div class="field">
                <label for="amount">Amount:</label>
                <input id="amount" type="number" name="amount" placeholder="e.g., KES 1,500" readonly />
              </div>

              <div id="mpesa-fields" class="row" style="margin-top:8px">
                <div class="field">
                  <label for="phone">M‑Pesa Phone Number:</label>
                  <input id="phone" inputmode="numeric" pattern="0[17]\d{8}" title="Please enter a valid Kenyan phone number starting with 07" value="<?php echo htmlspecialchars($phone); ?>" name="phone" placeholder="07XXXXXXXX" required />
                </div>
              </div>

              <div class="card" style="margin-top:14px">
                <div class="summary">
                  <div class="row" style="justify-content:space-between; align-items:center">
                    <strong class="muted">Order Summary</strong>
                    <span class="pill" id="selected-plan-pill">No plan selected</span>
                  </div>
                  <div class="kpis">
                    <div class="kpi">
                      <div class="muted">Subtotal</div>
                      <div class="value" id="subtotal">KES 0</div>
                    </div>
                    <div class="kpi">
                      <div class="muted">Discount</div>
                      <div class="value" id="discount">KES 0</div>
                    </div>
                    <div class="kpi">
                      <div class="muted">Total</div>
                      <div class="value" id="total">KES 0</div>
                    </div>
                  </div>
                </div>
              </div> <br>

              <button type="submit" class="btn primary text-white block" id="pay-btn" aria-live="polite" disabled>Select a plan to continue</button>
              <p class="muted" style="margin-top:8px; font-size:12px">By subscribing you agree to NyamaTrack Terms and Privacy. Prices include VAT where applicable.</p>
          </aside>
          </form>
        </div>
      </div>
    </div><br><br><br>
  </main>
</body>

</html>