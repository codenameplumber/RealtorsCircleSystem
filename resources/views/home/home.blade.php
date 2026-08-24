@extends('home.header')
@section('content')
   
    <section class="hero-section d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4">
                            The Future of Real Estate is 
                            <span class="text-primary">Coming Soon</span>
                        </h1>
                        <p class="lead mb-4">
                            Join the most innovative platform connecting realtors, agents, and clients. 
                            Experience seamless property management, advanced analytics, and unparalleled networking opportunities.
                        </p>
                        
          
                        <div class="countdown-timer mb-5 d-none">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="days">00</div>
                                        <div class="countdown-label">Days</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="hours">00</div>
                                        <div class="countdown-label">Hours</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="minutes">00</div>
                                        <div class="countdown-label">Minutes</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="countdown-item">
                                        <div class="countdown-number" id="seconds">00</div>
                                        <div class="countdown-label">Seconds</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                 
                        <div class="signup-form">
                            <h5 class="mb-3">Be the first to know when we launch!</h5>
                               
                                <div class="col-md-4">
                                    <a href="/waitlist">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-bell me-2"></i>Notify Me
                                        </button>
                                    </a>
                                </div>
                            
                            <p class="small text-muted mt-2">
                                <i class="fas fa-lock me-1"></i>
                                We respect your privacy. No spam, ever.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <div class="floating-card">
                            <i class="fas fa-building fa-3x text-primary mb-3"></i>
                            <h4>Modern Platform</h4>
                            <p>Cutting-edge technology for real estate professionals</p>
                        </div>
                        <div class="floating-card delay-1">
                            <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                            <h4>Advanced Analytics</h4>
                            <p>Data-driven insights to boost your performance</p>
                        </div>
                        <div class="floating-card delay-2">
                            <i class="fas fa-users fa-3x text-info mb-3"></i>
                            <h4>Network & Connect</h4>
                            <p>Build meaningful relationships in the industry</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="fw-bold">What's Coming</h2>
                    <p class="lead text-muted">Revolutionary features designed for modern real estate professionals</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h5>Property Management</h5>
                        <p class="text-muted">Comprehensive tools to manage your property listings with ease</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h5>Client Relations</h5>
                        <p class="text-muted">Advanced CRM system to nurture and manage client relationships</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5>Mobile First</h5>
                        <p class="text-muted">Fully responsive design that works perfectly on all devices</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

  
    <section class="social-proof-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h3 class="fw-bold mb-4">Join Thousands of Real Estate Professionals</h3>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-number" data-target="5000">0</div>
                            <div class="stat-label">Pre-registered Users</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="150">0</div>
                            <div class="stat-label">Partner Agencies</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="25">0</div>
                            <div class="stat-label">Cities</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection