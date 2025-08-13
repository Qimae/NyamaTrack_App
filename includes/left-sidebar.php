<style>
  /* Hide left sidebar on mobile */
  .left-sidebar {
    display: none;
  }

  /* Show left sidebar on desktop */
  @media (min-width: 768px) {
    .left-sidebar {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 250px;
      background-color: #292E38;
      padding: 1rem;
      z-index: 1000;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-content {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      height: 100%;
      justify-content: flex-start;
    }

    .sidebar-link {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      width: 100%;
    }

    .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateX(5px);
    }

    .sidebar-link i {
      font-size: 1.2rem;
      margin-right: 0.75rem;
    }

    .sidebar-link span {
      font-size: 0.95rem;
    }
  }
</style>

<nav class="left-sidebar">
  <div class="sidebar-content">
    <a href="dashboard.php" class="sidebar-link">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="beef_transactions.php" class="sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Beef Transactions</span>
    </a>
    <a href="reports.php" class="sidebar-link">
      <i class="fas fa-file-invoice-dollar"></i>
      <span>Reports</span>
    </a>
    <a href="algorithm_report.php" class="sidebar-link">
      <i class="fas fa-file-invoice-dollar"></i>
      <span>Algorithm Reports</span>
    </a>
    <a href="payments.php" class="sidebar-link">
      <i class="fas fa-money-bill"></i>
      <span>Payments</span>
    </a>
    <a href="profile.php" class="sidebar-link">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
    <a href="logout.php" class="sidebar-link">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </div>
</nav>
