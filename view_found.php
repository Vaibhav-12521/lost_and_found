<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM found_items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - Found Item Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .item-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 50px 0;
        }
        .item-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .contact-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
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

    <!-- Item Header -->
    <section class="item-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                    <p class="lead mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Found at: <?php echo htmlspecialchars($item['location_found']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Found on: <?php echo date('F d, Y', strtotime($item['date_found'])); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-success fs-6"><?php echo htmlspecialchars($item['category']); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Item Details -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Item Image and Description -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php if($item['image']): ?>
                                <img src="uploads/<?php echo $item['image']; ?>" class="img-fluid item-image w-100 mb-4" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 300px; border-radius: 10px;">
                                    <i class="fas fa-image fa-4x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <h3>Description</h3>
                            <p class="lead"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-info-circle text-success me-2"></i>Item Details</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></li>
                                        <li><strong>Location Found:</strong> <?php echo htmlspecialchars($item['location_found']); ?></li>
                                        <li><strong>Date Found:</strong> <?php echo date('F d, Y', strtotime($item['date_found'])); ?></li>
                                        <li><strong>Reported:</strong> <?php echo date('F d, Y', strtotime($item['date_reported'])); ?></li>
                                        <li><strong>Current Location:</strong> <?php echo htmlspecialchars($item['current_location']); ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user text-success me-2"></i>Status</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Status:</strong> 
                                            <span class="badge bg-<?php echo ($item['status'] == 'available') ? 'success' : (($item['status'] == 'claimed') ? 'warning' : 'secondary'); ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </li>
                                        <li><strong>Item ID:</strong> #<?php echo $item['id']; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-4">
                    <div class="contact-card mb-4">
                        <h4><i class="fas fa-user text-success me-2"></i>Finder Information</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($item['finder_name']); ?></p>
                        <p><strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($item['finder_email']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($item['finder_email']); ?>
                            </a>
                        </p>
                        <?php if($item['finder_phone']): ?>
                            <p><strong>Phone:</strong> 
                                <a href="tel:<?php echo htmlspecialchars($item['finder_phone']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($item['finder_phone']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="mailto:<?php echo htmlspecialchars($item['finder_email']); ?>" class="btn btn-custom">
                                <i class="fas fa-envelope me-2"></i>Contact Finder
                            </a>
                            <?php if($item['finder_phone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($item['finder_phone']); ?>" class="btn btn-outline-success">
                                    <i class="fas fa-phone me-2"></i>Call Finder
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Similar Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Similar Found Items</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sql = "SELECT * FROM found_items WHERE category = ? AND id != ? AND status = 'available' ORDER BY date_reported DESC LIMIT 3";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("si", $item['category'], $item['id']);
                            $stmt->execute();
                            $similar_result = $stmt->get_result();
                            
                            if ($similar_result->num_rows > 0):
                                while($similar = $similar_result->fetch_assoc()):
                            ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <?php if($similar['image']): ?>
                                            <img src="uploads/<?php echo $similar['image']; ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($similar['item_name']); ?></h6>
                                        <small class="text-muted">Found on <?php echo date('M d, Y', strtotime($similar['date_found'])); ?></small>
                                    </div>
                                    <a href="view_found.php?id=<?php echo $similar['id']; ?>" class="btn btn-sm btn-outline-success">View</a>
                                </div>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <p class="text-muted">No similar items found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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