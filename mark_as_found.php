<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$error = '';
$success = '';
if (isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
    
    // Get item details and verify it belongs to the logged-in user
    $stmt = $conn->prepare("SELECT * FROM lost_items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        header("Location: index.php");
        exit();
    }
    
    $item = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $found_location = trim($_POST['found_location']);
    $notes = trim($_POST['notes']);
    
    if (empty($found_location)) {
        $error = "Please enter where you found the item.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update lost item status to 'found'
            $stmt = $conn->prepare("UPDATE lost_items SET status = 'found' WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            
            // Create found item record
            $stmt = $conn->prepare("INSERT INTO found_items (item_name, description, category, location_found, date_found, finder_name, finder_email, finder_phone, image, user_id, status) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, 'claimed')");
            $stmt->bind_param("ssssssssi", 
                $item['item_name'], 
                $item['description'], 
                $item['category'], 
                $found_location,
                $_SESSION['full_name'],
                $_SESSION['email'],
                $_POST['finder_phone'],
                $item['image'],
                $_SESSION['user_id']
            );
            $stmt->execute();
            
            // Record the mark as found action
            $stmt = $conn->prepare("INSERT INTO mark_as_found (lost_item_id, found_by_user_id, found_location, notes, status) VALUES (?, ?, ?, ?, 'confirmed')");
            $stmt->bind_param("iiss", $item_id, $_SESSION['user_id'], $found_location, $notes);
            $stmt->execute();
            
            $conn->commit();
            $success = "Item marked as found successfully! It has been moved to the found items section.";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Item as Found - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .item-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-check-circle me-2"></i>Mark Item as Found</h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <br><br>
                                <a href="index.php" class="btn btn-primary">Go to Home</a>
                            </div>
                        <?php else: ?>
                            
                            <div class="item-details">
                                <h5><i class="fas fa-info-circle me-2"></i>Item Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Item Name:</strong> <?php echo htmlspecialchars($item['item_name']); ?></p>
                                        <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                                        <p><strong>Lost Location:</strong> <?php echo htmlspecialchars($item['location_lost']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Lost Date:</strong> <?php echo htmlspecialchars($item['date_lost']); ?></p>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                                    </div>
                                </div>
                                <?php if ($item['image']): ?>
                                    <div class="text-center mt-3">
                                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" class="img-fluid" style="max-height: 200px; border-radius: 10px;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="found_location" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Where did you find it? *
                                    </label>
                                    <input type="text" class="form-control" id="found_location" name="found_location" 
                                           placeholder="e.g., Home, Office, Park, etc." required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="finder_phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Your Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="finder_phone" name="finder_phone" 
                                           placeholder="Your contact number" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Additional Notes
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Any additional details about finding the item..."></textarea>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check-circle me-2"></i>Mark as Found
                                    </button>
                                    <a href="index.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                </div>
                            </form>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
