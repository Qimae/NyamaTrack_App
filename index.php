<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NyamaTrack_App</title>
  <link rel="manifest" href="manifest.json" />
  <meta name="theme-color" content="#007bff" />
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('service-worker.js').then(function(registration) {
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <main>
    <h1>Welcome to NyamaTrack_App</h1>
    <p>This is the main content area.</p>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
