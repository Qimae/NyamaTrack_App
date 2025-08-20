<style>
  /* Hide bottom sidebar on desktop */
  .bottom-sidebar {
    display: none;
  }

  /* Show bottom sidebar only on mobile devices */
  @media (max-width: 767px) {
    .bottom-sidebar {
      display: block;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: #111826;
      padding: 1rem;
      z-index: 1000;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
      border-top: 1px solid #374151;
    }
    
    .sidebar-content {
      display: flex;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      overflow-x: auto;
      padding: 0 1rem;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    
    .sidebar-content::-webkit-scrollbar {
      display: none;
    }

    .sidebar-link {
      color: #fff;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      min-width: 80px;
      margin: 0 0.5rem;
    }
    
    .sidebar-link:hover {
      background-color:var(--primary);
      transform: translateY(-2px);
    }
    
    .sidebar-link i {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }
    
    .sidebar-link span {
      font-size: 0.9rem;
      text-align: center;
      white-space: nowrap;
    }
  }
</style>

<nav class="bottom-sidebar">
  <div class="sidebar-content">
    <a href="dashboard.php" class="sidebar-link">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="blockings.php" class="sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Blockings</span>
    </a>
    <a href="butcheries.php" class="sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Butcheries</span>
    </a>
    <a href="admin_employees.php" class="sidebar-link">
      <i class="fas fa-credit-card"></i>
      <span>Employees</span>
    </a> 
    <a href="processing.php" class="sidebar-link">
      <i class="fas fa-money-bill"></i>
      <span>Subscriptions</span>
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
