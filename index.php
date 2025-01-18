<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syncgo - Plan Your Trips</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Library for Animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f8f9;
        }
        .navbar {
            background-color: #003f2f;
        }
        .navbar a, .navbar-brand {
            color: white !important;
        }
        .carousel-inner img {
            height: 600px;
            object-fit: cover;
        }
        .section {
            padding: 60px 0;
        }
        .section h2 {
            color: #003f2f;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .icon-box {
            text-align: center;
            margin: 20px 0;
        }
        .icon-box i {
            font-size: 3.5rem;
            color: #00695c;
            margin-bottom: 15px;
        }
        .icon-box p {
            font-size: 1.1rem;
            color: #555;
        }
        .footer {
            background-color: #003f2f;
            color: white;
            padding: 40px 0;
        }
        .footer h5 {
            color: #80cbc4;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .footer a {
            color: #80cbc4;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer p {
            color: #b2dfdb;
        }
        .btn-custom {
            background-color: #00695c;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
        }
        .btn-custom:hover {
            background-color: #004d40;
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">Syncgo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Destinations</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Join</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Carousel -->
<div id="tripCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://img.freepik.com/free-photo/woman-holiday-journey-travel-relaxation_53876-42668.jpg" class="d-block w-100" alt="Trip 1">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="animate__animated animate__fadeInDown">Discover New Destinations</h5>
                <p class="animate__animated animate__fadeInUp">Plan your next adventure with Syncgo.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://img.freepik.com/premium-photo/gondola-lifts-moving-mountain-with-green-trees-area-sun-moon-lake-ropeway-yuchi-township-nantou-county-taiwan_43263-2147.jpg" class="d-block w-100" alt="Trip 2">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="animate__animated animate__fadeInDown">Connect with Fellow Travelers</h5>
                <p class="animate__animated animate__fadeInUp">Meet people with similar interests and plan trips together.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#tripCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#tripCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<!-- Why Choose Syncgo -->
<div class="container section">
    <h2 class="text-center" data-aos="fade-up">Why Choose Syncgo?</h2>
    <div class="row text-center">
        <div class="col-md-4 icon-box" data-aos="zoom-in">
            <i class="fas fa-map-marked-alt"></i>
            <h4>Plan Your Trips</h4>
            <p>Easily set destinations and schedules for your travels.</p>
        </div>
        <div class="col-md-4 icon-box" data-aos="zoom-in" data-aos-delay="200">
            <i class="fas fa-users"></i>
            <h4>Meet New People</h4>
            <p>Join groups and communities to travel with like-minded people.</p>
        </div>
        <div class="col-md-4 icon-box" data-aos="zoom-in" data-aos-delay="400">
            <i class="fas fa-comments"></i>
            <h4>Stay Connected</h4>
            <p>Chat with your group and keep everyone updated in real time.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>About Syncgo</h5>
                <p>Syncgo helps you plan your trips and meet like-minded travelers to make your adventures unforgettable.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>Email: support@syncgo.com</p>
                <p>Phone: +123 456 7890</p>
                <p>Follow us on:</p>
                <p>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
    });
</script>
</body>
</html>
