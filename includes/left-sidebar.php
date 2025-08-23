<style>
  /* Left sidebar styles */
  .left-sidebar {
    display: none;
  }

  /* Show left sidebar on desktop */
  @media (min-width: 800px) {
    .left-sidebar {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 250px;
      background-color: #161616;
      padding: 1rem;
      z-index: 1000;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      border-right: 1px solid #374151;
      overflow-y: auto;
    }

    .left-sidebar-content {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      height: 100%;
      justify-content: flex-start;
      padding-bottom: 2rem;
    }

    .left-sidebar-link {
      color: #9ca3af;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      width: 100%;
    }

    .left-sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      transform: translateX(5px);
    }
    
    .left-sidebar-link i {
      width: 24px;
      margin-right: 10px;
      text-align: center;
    }
    
    .left-sidebar-link.active {
      background-color: rgba(59, 130, 246, 0.2);
      color: #3b82f6;
    }

    .left-sidebar-link i {
      font-size: 1.2rem;
      margin-right: 0.75rem;
    }

    .left-sidebar-link span {
      font-size: 0.95rem;
    }
  }
</style>

<nav class="left-sidebar">
  <div class="left-sidebar-content">
    <a href="dashboard.php" class="left-sidebar-link">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="beef_transactions.php" class="left-sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Beef Transactions</span>
    </a>
    <a href="goat_transactions.php" class="left-sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Goat Transactions</span>
    </a>
    <a href="reports.php" class="left-sidebar-link">
      <i class="fas fa-file-invoice-dollar"></i>
      <span>Reports</span>
    </a>
    <a href="algorithm_report.php" class="left-sidebar-link">
      <i class="fas fa-chart-line"></i>
      <span>Algo Reports</span>
    </a>
    <a href="mpesa/subscription.php" class="left-sidebar-link">
      <i class="fas fa-money-bill"></i>
      <span>Subscription</span>
    </a>
    <a href="profile.php" class="left-sidebar-link">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
    <a href="logout.php" class="left-sidebar-link">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </div>
</nav>
