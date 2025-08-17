document.addEventListener('DOMContentLoaded', function () {
  const monthlyButton = document.getElementById('bill-monthly');
  const annualButton = document.getElementById('bill-annual');

  // Plan data with monthly and annual prices (annual is 15% off)
  const plans = [{
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
  monthlyButton.addEventListener('click', function (e) {
    console.log('Monthly button clicked');
    e.preventDefault();
    toggleBilling(false);
  });

  annualButton.addEventListener('click', function (e) {
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
      planEl.addEventListener('click', function (e) {
        console.log('Plan clicked:', index, e.target);
        // Don't trigger if clicking on the button inside the plan
        if (e.target.tagName === 'BUTTON') return;
        selectPlan(planEl, index);
      });

      // Add click handler to the button
      const button = planEl.querySelector('button');
      if (button) {
        button.addEventListener('click', function (e) {
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