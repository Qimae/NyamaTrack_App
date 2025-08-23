<style>
  .bottom-sidebar {
    position: fixed;
    bottom: -100px;
    left: 0;
    right: 0;
    background-color: #111826;
    padding: 15px 0;
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.4);
    z-index: 1000;
    transition: all 0.3s ease-in-out;
    opacity: 0;
    visibility: hidden;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .bottom-sidebar.visible {
    bottom: 0;
    opacity: 1;
    visibility: visible;
  }

  .sidebar-content {
    display: flex;
    justify-content: flex-start;
    min-width: max-content;
    width: 100%;
    margin: 0;
    padding: 0 20px;
    gap: 15px;
  }
  
  .sidebar-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #9ca3af;
    text-decoration: none;
    font-size: 0.8rem;
    padding: 10px 15px;
    border-radius: 8px;
    white-space: nowrap;
    transition: all 0.2s ease;
  }
  
  .sidebar-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    transform: translateY(-2px);
  }

  @media (min-width: 768px) {
    .bottom-sidebar {
      display: none;
    }
  }

  @media (max-width: 767px) {
    .sidebar-content {
      padding: 0 15px;
      gap: 10px;
    }
    
    .sidebar-link {
      font-size: 0.7rem;
      padding: 8px 10px;
    }
    
    .sidebar-link i {
      font-size: 1.4rem;
      margin-bottom: 4px;
    }
  }
  
  /* Hide scrollbar for Chrome, Safari and Opera */
  .bottom-sidebar::-webkit-scrollbar {
    display: none;
  }
  
  /* Hide scrollbar for IE, Edge and Firefox */
  .bottom-sidebar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
</style>

<nav class="bottom-sidebar" id="bottomNav">
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
    <a href="subscriptions.php" class="sidebar-link">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const bottomNav = document.getElementById('bottomNav');
  let scrollTimer;
  let lastScrollTop = 0;
  const hideDelay = 5000; // 5 seconds

  // Show/hide on scroll
  window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    
    // Only show if scrolled down a bit
    if (currentScroll > 50) {
      // Clear any existing timeout
      clearTimeout(scrollTimer);
      
      // Show the navbar
      bottomNav.classList.add('visible');
      
      // Set a timeout to hide the navbar after scrolling stops
      scrollTimer = setTimeout(function() {
        bottomNav.classList.remove('visible');
      }, hideDelay);
      
      // Update last scroll position
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }
  });

  // Hide navbar when clicking anywhere on the page
  document.addEventListener('click', function() {
    bottomNav.classList.remove('visible');
  });

  // Show navbar when hovering over it
  bottomNav.addEventListener('mouseenter', function() {
    clearTimeout(scrollTimer);
    bottomNav.classList.add('visible');
  });

  // Hide navbar after mouse leaves (with delay)
  bottomNav.addEventListener('mouseleave', function() {
    scrollTimer = setTimeout(function() {
      bottomNav.classList.remove('visible');
    }, hideDelay);
  });

  // Initial state - hide navbar
  bottomNav.classList.remove('visible');
});
</script>
