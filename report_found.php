<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location_found = $_POST['location_found'];
    $date_found = $_POST['date_found'];
    $finder_name = $_SESSION['full_name'];
    $finder_email = $_SESSION['email'];
    $finder_phone = $_POST['finder_phone'];
    $current_location = $_POST['current_location'];
    
    // Handle file upload
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully
        } else {
            $image = '';
        }
    }
    
    $sql = "INSERT INTO found_items (item_name, description, category, location_found, date_found, finder_name, finder_email, finder_phone, image, current_location, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssi", $item_name, $description, $category, $location_found, $date_found, $finder_name, $finder_email, $finder_phone, $image, $current_location, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $success = "Found item reported successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get categories
$categories = [];
$sql = "SELECT * FROM categories ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background: linear-gradient(45deg, #28a745, #20c997);
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
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-search-location me-2"></i>
                Lost & Found
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_lost.php">Lost Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_found.php">Found Items</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-card p-5">
                        <div class="text-center mb-4">
                            <h2><i class="fas fa-hand-holding-heart text-success me-2"></i>Report Found Item</h2>
                            <p class="text-muted">Help reunite found items with their owners</p>
                        </div>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="item_name" class="form-label">Item Name *</label>
                                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the found item in detail..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location_found" class="form-label">Location Found *</label>
                                    <input type="text" class="form-control" id="location_found" name="location_found" placeholder="e.g., Central Park, Mall, Office" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_found" class="form-label">Date Found *</label>
                                    <input type="date" class="form-control" id="date_found" name="date_found" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="current_location" class="form-label">Current Location *</label>
                                <input type="text" class="form-control" id="current_location" name="current_location" placeholder="Where is the item currently kept?" required>
                                <div class="form-text">Where can the owner collect the item from?</div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Item Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a photo of the found item (optional but helpful)</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="finder_name" class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" id="finder_name" name="finder_name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="finder_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="finder_email" name="finder_email" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="finder_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="finder_phone" name="finder_phone">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Report Found Item
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 