<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtors Circle - Coming Soon </title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="Realtors Circle - Coming Soon">
    <meta name="description" content="Join Realtors Circle, an exclusive platform for real estate professionals. Connect, manage properties, and grow your business. Be the first to know when we launch!">
    <meta name="keywords" content="Real Estate, Realtors Circle, Property Management, Real Estate Network, Coming Soon">
    <meta name="author" content="Realtors Circle Team">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://realtorscircle.com/">
    <meta property="og:title" content="Realtors Circle - Coming Soon">
    <meta property="og:description" content="Exclusive waitlist for Realtors Circle. Be among the first to experience the future of real estate networking.">
    <meta property="og:image" content="https://realtorscircle.com/img/favicon.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://realtorscircle.com/">
    <meta property="twitter:title" content="Realtors Circle - Coming Soon">
    <meta property="twitter:description" content="Exclusive waitlist for Realtors Circle. Be among the first to experience the future of real estate networking.">
    <meta property="twitter:image" content="https://realtorscircle.com/img/favicon.jpg">

    <!-- Favicon and Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
  
    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/coming-soon.css">
    <link rel="stylesheet" href="css/waitlist.css">
</head>

<body>
   
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="{{asset('img/logo.png')}}" style="width:200px">
            </a>
        </div>
    </nav>


    <!--YEILD--->
    @yield('content')

     <footer class="footer-section py-4 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-brand">
                         <img src="{{asset('img/favicon.png')}}" style="width:100px">Realtor Circle
                    </div>
                    <p class="mb-0 text-muted">Revolutionizing real estate, one connection at a time.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="https://web.facebook.com/profile.php?id=61562754255807" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/realtors_circle/" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

 
    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    

    <script src="js/coming-soon.js"></script>
     <script src="js/waitlist.js" defer></script>
</body>
</html>
