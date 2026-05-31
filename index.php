<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found - Find Your Belongings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
        }
        .search-box {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 15px 25px;
            color: white;
        }
        .search-box::placeholder {
            color: rgba(255,255,255,0.7);
        }
        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-search-location me-2"></i>
                Lost & Found
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#lost">Lost Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#found">Found Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#report">Report</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="my_lost_items.php"><i class="fas fa-list me-2"></i>My Lost Items</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="signin.php"><i class="fas fa-sign-in-alt me-1"></i>Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signup.php"><i class="fas fa-user-plus me-1"></i>Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Lost Something? Found Something?</h1>
            <p class="lead mb-5">Connect with your community to find lost items or return found belongings</p>
            
            <!-- Search Box -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-6">
                    <form action="search.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control search-box" name="q" placeholder="Search for lost or found items...">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <a href="report_lost.php" class="btn btn-custom btn-lg me-3">
                        <i class="fas fa-plus-circle me-2"></i>Report Lost Item
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="report_found.php" class="btn btn-custom btn-lg">
                        <i class="fas fa-hand-holding-heart me-2"></i>Report Found Item
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-search feature-icon mb-3"></i>
                            <h5 class="card-title">Easy Search</h5>
                            <p class="card-text">Search through thousands of lost and found items with our powerful search engine.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-shield-alt feature-icon mb-3"></i>
                            <h5 class="card-title">Secure & Private</h5>
                            <p class="card-text">Your personal information is protected and only shared when you choose to connect.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-hover h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-users feature-icon mb-3"></i>
                            <h5 class="card-title">Community Driven</h5>
                            <p class="card-text">Join our community of helpful people working together to reunite items with owners.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Items Section -->
    <section class="py-5 bg-light" id="lost">
        <div class="container">
            <h2 class="text-center mb-5">Recent Lost Items</h2>
            <div class="row">
                <?php
                include 'config.php';
                $sql = "SELECT * FROM lost_items ORDER BY date_reported DESC LIMIT 6";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card card-hover h-100">';
                        if($row['image']) {
                            echo '<img src="uploads/' . $row['image'] . '" class="card-img-top" alt="' . $row['item_name'] . '">';
                        } else {
                            echo '<div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">';
                            echo '<i class="fas fa-image fa-3x"></i>';
                            echo '</div>';
                        }
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($row['item_name']) . '</h5>';
                        echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                        echo '<p class="text-muted"><small>Lost on: ' . date('M d, Y', strtotime($row['date_reported'])) . '</small></p>';
                        echo '<a href="view_lost.php?id=' . $row['id'] . '" class="btn btn-primary">View Details</a>';
                        echo '</div></div></div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><p>No lost items reported yet.</p></div>';
                }
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="all_lost.php" class="btn btn-outline-primary">View All Lost Items</a>
            </div>
        </div>
    </section>

    <!-- Recent Found Items Section -->
    <section class="py-5" id="found">
        <div class="container">
            <h2 class="text-center mb-5">Recent Found Items</h2>
            <div class="row">
                <?php
                $sql = "SELECT * FROM found_items ORDER BY date_found DESC LIMIT 6";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card card-hover h-100">';
                        if($row['image']) {
                            echo '<img src="uploads/' . $row['image'] . '" class="card-img-top" alt="' . $row['item_name'] . '">';
                        } else {
                            echo '<div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">';
                            echo '<i class="fas fa-image fa-3x"></i>';
                            echo '</div>';
                        }
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($row['item_name']) . '</h5>';
                        echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                        echo '<p class="text-muted"><small>Found on: ' . date('M d, Y', strtotime($row['date_found'])) . '</small></p>';
                        echo '<a href="view_found.php?id=' . $row['id'] . '" class="btn btn-success">View Details</a>';
                        echo '</div></div></div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><p>No found items reported yet.</p></div>';
                }
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="all_found.php" class="btn btn-outline-success">View All Found Items</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-search-location me-2"></i>Lost & Found</h5>
                    <p>Helping communities reunite with their lost belongings.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Lost & Found. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
