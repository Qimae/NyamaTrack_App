<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NyamaTrack — Subscription | Nyamatrack.co.ke</title>
  <meta name="description" content="Subscribe your butchery to NyamaTrack. Flexible pricing, M‑Pesa and card options. Nyamatrack.co.ke" />
  <link rel="stylesheet" href="utils/becken.css">
  <link rel="stylesheet" href="utils/styles.css">
</head>

<body>
  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
  <div class="bg" aria-hidden="true">
    <div class="orb red" style="left:-120px; top:-120px"></div>
    <div class="orb amber" style="right:-120px; bottom:-120px"></div>
    <div class="grid-overlay"></div>
  </div>
  <header>
    <div class="container">
      <nav aria-label="Primary">
        <div class="brand">
          <div class="brand-badge" aria-hidden="true"></div>
          <a href="subscription.php" aria-label="NyamaTrack home">NyamaTrack</a>
          <span class="pill">Nyamatrack.co.ke</span>
        </div>
        <div class="nav-links">
          <a href="subscription.php" aria-current="page">Subscription</a>
          <a href="algorithm_report.php">Algorithm Reports</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <h1>Subscribe Your Butchery</h1>
      <p class="muted">Secure billing with M‑Pesa and Cards. Choose a plan that scales with your meat business.</p>
    </section>

    <section class="content" aria-labelledby="plans">
      <div class="card">
        <div class="row" style="justify-content:space-between; align-items:center">
          <h3 id="plans">Plans</h3>
          <div class="switch" role="tablist" aria-label="Billing cadence">
            <button id="bill-monthly" class="active" role="tab" aria-selected="true">Monthly</button>
            <button id="bill-annual" role="tab" aria-selected="false">Annual <span class="badge" style="margin-left:6px">Save 17%</span></button>
          </div>
        </div>
        <div class="plans" id="plan-list"></div>
      </div>

      <aside class="card" aria-labelledby="checkout">
        <h3 id="checkout">Checkout</h3>
        <div class="field">
          <label for="butchery-name">Butchery Name</label>
          <input id="butchery-name" placeholder="e.g., Kayole Prime Meats" autocomplete="organization" />
        </div>
        <div class="field">
          <label for="email">Billing Email</label>
          <input id="email" type="email" placeholder="you@butchery.co.ke" autocomplete="email" />
        </div>
        <div class="field">
          <label for="payment-method">Payment Method</label>
          <select id="payment-method" aria-describedby="pm-note">
            <option value="mpesa">M‑Pesa STK Push</option>
            <option value="card">Debit/Credit Card</option>
            <option value="invoice">Invoice (Bank Transfer)</option>
          </select>
          <small id="pm-note" class="muted">We never store card or M‑Pesa PINs.</small>
        </div>

        <div id="mpesa-fields" class="row" style="margin-top:8px">
          <div class="field">
            <label for="mpesa-phone">M‑Pesa Phone (SafariCom)</label>
            <input id="mpesa-phone" inputmode="numeric" placeholder="07XXXXXXXX" />
          </div>
        </div>
        <div id="card-fields" class="row" style="display:none; margin-top:8px">
          <div class="field"><label>Card Number</label><input placeholder="4242 4242 4242 4242" inputmode="numeric" /></div>
          <div class="field"><label>Expiry</label><input placeholder="MM/YY" inputmode="numeric" /></div>
          <div class="field"><label>CVC</label><input placeholder="CVC" inputmode="numeric" /></div>
        </div>

        <div class="card" style="margin-top:14px">
          <div class="summary">
            <div class="row" style="justify-content:space-between; align-items:center">
              <strong>Order Summary</strong>
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
        </div>

        <button class="btn primary block" id="pay-btn" aria-live="polite" disabled>Select a plan to continue</button>
        <p class="muted" style="margin-top:8px; font-size:12px">By subscribing you agree to NyamaTrack Terms and Privacy. Prices include VAT where applicable.</p>
      </aside>
    </section>

    <section class="card" style="margin-top:24px">
      <h3>Why NyamaTrack?</h3>
      <ul>
        <li>Daily and algorithmic reports to keep your butchery profitable.</li>
        <li>Kenyan-first billing with M‑Pesa support.</li>
        <li>Unlimited outlets per business on Business plan and above.</li>
      </ul>
    </section>
  </main>

  <div class="dialog" id="payment-dialog" role="dialog" aria-modal="true" aria-labelledby="pmt-title" aria-describedby="pmt-desc">
    <div class="panel">
      <div class="row" style="justify-content:space-between; align-items:center">
        <strong id="pmt-title">Processing Payment</strong>
        <button class="btn ghost" id="close-dialog" aria-label="Close dialog">Close</button>
      </div>
      <p class="muted" id="pmt-desc">If paying via M‑Pesa, approve the STK push on your phone.</p>
      <div class="row" style="align-items:center">
        <div class="spinner" aria-hidden="true"></div> <span id="pmt-status">Awaiting confirmation…</span>
      </div>
    </div>
  </div>

  <footer>
    © <span id="year"></span> NyamaTrack — Nyamatrack.co.ke
  </footer>


</body>

</html>