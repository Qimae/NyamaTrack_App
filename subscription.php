<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NyamaTrack — <?php
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
?> <?php echo $business_name; ?> Subscription | Nyamatrack.co.ke</title>
  <meta name="description" content="Subscribe your butchery to NyamaTrack. Flexible pricing, M‑Pesa and card options. Nyamatrack.co.ke" />
  <link rel="stylesheet" href="utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <link rel="stylesheet" href="utils/becken.css">
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const monthlyButton = document.getElementById('bill-monthly');
      const annualButton = document.getElementById('bill-annual');
      
      // Plan data with monthly and annual prices (annual is 15% off)
      const plans = [
        { 
          name: 'Basic',
          monthlyPrice: 1500,
          monthlyLimit: '1000kg per month',
          monthlySales: 'Less than KES 500,000 sales per month',
          annualLimit: '12,000kg per year',
          annualSales: 'Less than KES 6,000,000 sales per year'
        },
        { 
          name: 'Pro',
          monthlyPrice: 2500,
          monthlyLimit: '2000kg per month',
          monthlySales: 'Less than KES 1,000,000 sales per month',
          annualLimit: '24,000kg per year',
          annualSales: 'Less than KES 12,000,000 sales per year'
        },
        { 
          name: 'Enterprise',
          monthlyPrice: 3500,
          monthlyLimit: '5000kg per month',
          monthlySales: 'Less than KES 5,000,000 sales per month',
          annualLimit: '60,000kg per year',
          annualSales: 'Less than KES 60,000,000 sales per year'
        }
      ];

      // Format currency
      function formatCurrency(amount) {
        return 'KES ' + amount.toLocaleString('en-US');
      }

      // Calculate annual price with 15% discount
      function getAnnualPrice(monthlyPrice) {
        return Math.round(monthlyPrice * 12 * 0.85);
      }

      // Update plan display based on billing cycle
      function updatePlans(isAnnual) {
        const planElements = document.querySelectorAll('.plan');
        
        planElements.forEach((planEl, index) => {
          const plan = plans[index];
          const priceEl = planEl.querySelector('.price');
          const limitEl = planEl.querySelector('li:first-child');
          const salesEl = planEl.querySelector('li:last-child');
          
          if (isAnnual) {
            priceEl.textContent = formatCurrency(getAnnualPrice(plan.monthlyPrice)) + '/year';
            limitEl.textContent = plan.annualLimit;
            salesEl.textContent = plan.annualSales;
            // Add a badge to show savings
            if (!planEl.querySelector('.savings-badge')) {
              const savings = plan.monthlyPrice * 12 - getAnnualPrice(plan.monthlyPrice);
              const badge = document.createElement('div');
              badge.className = 'savings-badge';
              badge.textContent = `Save ${formatCurrency(savings)}`;
              planEl.insertBefore(badge, priceEl.nextSibling);
            }
          } else {
            priceEl.textContent = formatCurrency(plan.monthlyPrice) + '/month';
            limitEl.textContent = plan.monthlyLimit;
            salesEl.textContent = plan.monthlySales;
            // Remove savings badge if exists
            const badge = planEl.querySelector('.savings-badge');
            if (badge) {
              badge.remove();
            }
          }
        });
      }

      // Toggle between monthly and annual billing
      function toggleBilling(isAnnual) {
        if (isAnnual) {
          monthlyButton.classList.remove('active');
          monthlyButton.setAttribute('aria-selected', 'false');
          annualButton.classList.add('active');
          annualButton.setAttribute('aria-selected', 'true');
        } else {
          monthlyButton.classList.add('active');
          monthlyButton.setAttribute('aria-selected', 'true');
          annualButton.classList.remove('active');
          annualButton.setAttribute('aria-selected', 'false');
        }
        updatePlans(isAnnual);
      }

      // Store selected plan
      let selectedPlan = null;
      let isAnnual = false;

      // Handle plan selection
      function selectPlan(planElement, planIndex) {
        console.log('Selecting plan:', planIndex, plans[planIndex]?.name);
        
        // Remove selected class from all plans
        const allPlans = document.querySelectorAll('.plan');
        console.log('Found', allPlans.length, 'plan elements in selectPlan');
        
        allPlans.forEach((p, i) => {
          p.classList.remove('selected');
          console.log('Removed selected class from plan', i);
        });
        
        // Add selected class to clicked plan
        planElement.classList.add('selected');
        console.log('Added selected class to plan', planIndex);
        
        // Update selected plan
        selectedPlan = plans[planIndex];
        console.log('Updated selectedPlan:', selectedPlan);
        
        // Update checkout form
        updateCheckoutForm();
      }

      // Update checkout form with selected plan details
      function updateCheckoutForm() {
        if (!selectedPlan) return;
        
        const amountInput = document.getElementById('amount');
        const planTypeSelect = document.getElementById('plan-type');
        const selectedPlanPill = document.getElementById('selected-plan-pill');
        const subtotalEl = document.querySelector('#subtotal');
        const totalEl = document.querySelector('#total');
        const discountEl = document.querySelector('#discount');
        
        if (isAnnual) {
          const annualPrice = getAnnualPrice(selectedPlan.monthlyPrice);
          const monthlyTotal = selectedPlan.monthlyPrice * 12;
          const discount = monthlyTotal - annualPrice;
          
          amountInput.value = annualPrice;
          planTypeSelect.value = 'annual';
          selectedPlanPill.textContent = selectedPlan.name + ' (Annual)';
          
          // Update order summary
          subtotalEl.textContent = 'KES ' + monthlyTotal.toLocaleString();
          discountEl.textContent = '-KES ' + discount.toLocaleString();
          totalEl.textContent = 'KES ' + annualPrice.toLocaleString();
        } else {
          amountInput.value = selectedPlan.monthlyPrice;
          planTypeSelect.value = 'monthly';
          selectedPlanPill.textContent = selectedPlan.name + ' (Monthly)';
          
          // Update order summary
          subtotalEl.textContent = 'KES ' + selectedPlan.monthlyPrice.toLocaleString();
          discountEl.textContent = 'KES 0';
          totalEl.textContent = 'KES ' + selectedPlan.monthlyPrice.toLocaleString();
        }
        
        // Enable pay button
        document.getElementById('pay-btn').disabled = false;
      }

      // Toggle between monthly and annual billing
      function toggleBilling(annual) {
        console.log('Toggling billing to:', annual ? 'Annual' : 'Monthly');
        isAnnual = annual;
        if (isAnnual) {
          monthlyButton.classList.remove('active');
          monthlyButton.setAttribute('aria-selected', 'false');
          annualButton.classList.add('active');
          annualButton.setAttribute('aria-selected', 'true');
        } else {
          monthlyButton.classList.add('active');
          monthlyButton.setAttribute('aria-selected', 'true');
          annualButton.classList.remove('active');
          annualButton.setAttribute('aria-selected', 'false');
        }
        updatePlans(isAnnual);
        
        // Update checkout form if a plan is selected
        if (selectedPlan) {
          console.log('Updating checkout form after toggle');
          updateCheckoutForm();
        } else {
          console.log('No plan selected, skipping checkout form update');
        }
      }

      // Event listeners for billing toggle
      console.log('Adding event listeners for billing toggle');
      monthlyButton.addEventListener('click', function(e) {
        console.log('Monthly button clicked');
        e.preventDefault();
        toggleBilling(false);
      });
      
      annualButton.addEventListener('click', function(e) {
        console.log('Annual button clicked');
        e.preventDefault();
        toggleBilling(true);
      });
      
      // Add click handlers to plan cards
      function initializePlanSelection() {
        console.log('Initializing plan selection...');
        const planElements = document.querySelectorAll('.plan');
        console.log('Found', planElements.length, 'plan elements');
        
        planElements.forEach((planEl, index) => {
          console.log('Adding click handler for plan', index);
          
          // Add click handler to the plan card
          planEl.addEventListener('click', function(e) {
            console.log('Plan clicked:', index, e.target);
            // Don't trigger if clicking on the button inside the plan
            if (e.target.tagName === 'BUTTON') return;
            selectPlan(planEl, index);
          });
          
          // Add click handler to the button
          const button = planEl.querySelector('button');
          if (button) {
            button.addEventListener('click', function(e) {
              console.log('Button clicked in plan', index);
              e.stopPropagation(); // Prevent the card click handler from firing
              selectPlan(planEl, index);
            });
          } else {
            console.warn('No button found in plan', index);
          }
        });
      }
      
      // Initialize when DOM is fully loaded
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePlanSelection);
      } else {
        initializePlanSelection();
      }

      // Initialize with monthly plans
      updatePlans(false);
    });
  </script>
  <style>
    .readonly-input {
      cursor: not-allowed;
      opacity: 0.9;
    }
    .plan {
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .plan:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .plan.selected {
      border: 2px solid #4caf50;
      background-color: rgba(76, 175, 80, 0.05);
    }
    .savings-badge {
      background: #4caf50;
      color: white;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 0.8em;
      display: inline-block;
      margin: 5px 0;
    }
    .plan {
      margin-bottom: 20px;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      color: var(--muted);
    }
    .plan h4 {
      margin: 0 0 5px 0;
    }
    .plan .price {
      font-size: 1.5em;
      font-weight: bold;
      margin: 10px 0;
    }
    .plan ul {
      padding-left: 20px;
      margin: 10px 0;
    }
    .plan button {
      width: 100%;
    }
  </style>

</head>

<body>
  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
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
              <input id="amount" type="number" name="amount" placeholder="e.g., KES 1,500" />
            </div>           

            <div id="mpesa-fields" class="row" style="margin-top:8px">
              <div class="field">
                <label for="mpesa-phone">M‑Pesa Phone Number:</label>
                <input id="mpesa-phone" inputmode="numeric" name="mpesa-phone" placeholder="07XXXXXXXX" />
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

            <button class="btn primary text-white block" id="pay-btn" aria-live="polite" disabled>Select a plan to continue</button>
            <p class="muted" style="margin-top:8px; font-size:12px">By subscribing you agree to NyamaTrack Terms and Privacy. Prices include VAT where applicable.</p>
          </aside>
        </div>
      </div>
    </div>
  </main>  
</body>
</html>