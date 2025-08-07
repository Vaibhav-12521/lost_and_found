<?php
session_start();
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM lost_items WHERE id = ?";
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
    <title><?php echo htmlspecialchars($item['item_name']); ?> - Lost Item Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .item-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
        }
        .btn-mark-found {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-mark-found:hover {
            background: linear-gradient(45deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
            color: white;
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
                        Lost at: <?php echo htmlspecialchars($item['location_lost']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Lost on: <?php echo date('F d, Y', strtotime($item['date_lost'])); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($item['category']); ?></span>
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
                                    <h5><i class="fas fa-info-circle text-primary me-2"></i>Item Details</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></li>
                                        <li><strong>Location Lost:</strong> <?php echo htmlspecialchars($item['location_lost']); ?></li>
                                        <li><strong>Date Lost:</strong> <?php echo date('F d, Y', strtotime($item['date_lost'])); ?></li>
                                        <li><strong>Reported:</strong> <?php echo date('F d, Y', strtotime($item['date_reported'])); ?></li>
                                        <?php if($item['reward'] > 0): ?>
                                            <li><strong>Reward:</strong> $<?php echo number_format($item['reward'], 2); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user text-primary me-2"></i>Status</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Status:</strong> 
                                            <span class="badge bg-<?php echo ($item['status'] == 'lost') ? 'warning' : (($item['status'] == 'found') ? 'success' : 'secondary'); ?>">
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
                        <h4><i class="fas fa-phone text-primary me-2"></i>Contact Information</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($item['contact_name']); ?></p>
                        <p><strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($item['contact_email']); ?>
                            </a>
                        </p>
                        <?php if($item['contact_phone']): ?>
                            <p><strong>Phone:</strong> 
                                <a href="tel:<?php echo htmlspecialchars($item['contact_phone']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($item['contact_phone']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>" class="btn btn-custom">
                                <i class="fas fa-envelope me-2"></i>Contact Owner
                            </a>
                            <?php if($item['contact_phone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($item['contact_phone']); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-phone me-2"></i>Call Owner
                                </a>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['user_id']) && $item['user_id'] == $_SESSION['user_id'] && $item['status'] == 'lost'): ?>
                                <a href="mark_as_found.php?id=<?php echo $item['id']; ?>" class="btn btn-mark-found">
                                    <i class="fas fa-check-circle me-2"></i>Mark as Found
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Similar Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Similar Lost Items</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sql = "SELECT * FROM lost_items WHERE category = ? AND id != ? AND status = 'lost' ORDER BY date_reported DESC LIMIT 3";
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
                                        <small class="text-muted">Lost on <?php echo date('M d, Y', strtotime($similar['date_lost'])); ?></small>
                                    </div>
                                    <a href="view_lost.php?id=<?php echo $similar['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
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