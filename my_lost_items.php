<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY date_reported DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$lost_items = [];
while ($row = $result->fetch_assoc()) {
    $lost_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Lost Items - Lost & Found</title>
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
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .status-lost {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
        .status-found {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .btn-mark-found {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            color: white;
        }
        .btn-mark-found:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
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
                <ul class="navbar-nav me-auto">
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
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="my_lost_items.php"><i class="fas fa-list me-2"></i>My Lost Items</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white">
                        <i class="fas fa-list me-2"></i>My Lost Items
                    </h2>
                    <a href="report_lost.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Report New Item
                    </a>
                </div>

                <?php if (empty($lost_items)): ?>
                    <div class="card">
                        <div class="card-body empty-state">
                            <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                            <h4>No Lost Items Yet</h4>
                            <p class="text-muted">You haven't reported any lost items yet.</p>
                            <a href="report_lost.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Report Your First Lost Item
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($lost_items as $item): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <?php if ($item['image']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                             class="card-img-top" alt="Item Image" 
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                        <p class="card-text text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($item['category']); ?>
                                                </small>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i><?php echo htmlspecialchars($item['date_lost']); ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($item['location_lost']); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge <?php echo $item['status'] == 'lost' ? 'status-lost' : 'status-found'; ?>">
                                                <i class="fas fa-<?php echo $item['status'] == 'lost' ? 'search' : 'check-circle'; ?> me-1"></i>
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                            
                                            <?php if ($item['status'] == 'lost'): ?>
                                                <a href="mark_as_found.php?id=<?php echo $item['id']; ?>" 
                                                   class="btn btn-mark-found btn-sm">
                                                    <i class="fas fa-check-circle me-1"></i>Mark as Found
                                                </a>
                                            <?php else: ?>
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Found!
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>Reported: <?php echo date('M j, Y', strtotime($item['date_reported'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
