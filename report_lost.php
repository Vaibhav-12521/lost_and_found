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
    $location_lost = $_POST['location_lost'];
    $date_lost = $_POST['date_lost'];
    $contact_name = $_SESSION['full_name'];
    $contact_email = $_SESSION['email'];
    $contact_phone = $_POST['contact_phone'];
    $reward = $_POST['reward'];
    
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
    
    $sql = "INSERT INTO lost_items (item_name, description, category, location_lost, date_lost, contact_name, contact_email, contact_phone, image, reward, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssdi", $item_name, $description, $category, $location_lost, $date_lost, $contact_name, $contact_email, $contact_phone, $image, $reward, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $success = "Lost item reported successfully!";
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
    <title>Report Lost Item - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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
                            <h2><i class="fas fa-plus-circle text-primary me-2"></i>Report Lost Item</h2>
                            <p class="text-muted">Help us find your lost item by providing detailed information</p>
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
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe your lost item in detail..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location_lost" class="form-label">Location Lost *</label>
                                    <input type="text" class="form-control" id="location_lost" name="location_lost" placeholder="e.g., Central Park, Mall, Office" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_lost" class="form-label">Date Lost *</label>
                                    <input type="date" class="form-control" id="date_lost" name="date_lost" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Item Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a photo of your item (optional but helpful)</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="contact_name" class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contact_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contact_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="contact_phone" name="contact_phone">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="reward" class="form-label">Reward Amount (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="reward" name="reward" min="0" step="0.01" placeholder="0.00">
                                </div>
                                <div class="form-text">Offer a reward to encourage people to help find your item</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Report Lost Item
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