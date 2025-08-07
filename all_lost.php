<?php
session_start();
include 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Filters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$sql = "SELECT * FROM lost_items WHERE 1=1";
$params = [];
$types = "";

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if (!empty($search_query)) {
    $sql .= " AND (item_name LIKE ? OR description LIKE ? OR location_lost LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $types .= "sss";
}

// Get total count
$count_sql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_items = $stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_items / $per_page);

// Get items
$sql .= " ORDER BY date_reported DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories for filter
$categories = [];
$cat_sql = "SELECT * FROM categories ORDER BY name";
$cat_result = $conn->query($cat_sql);
if ($cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Lost Items - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 0;
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
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
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
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
                        <a class="nav-link active" href="all_lost.php">Lost Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_found.php">Found Items</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3">All Lost Items</h1>
                    <p class="lead mb-0">Browse through all reported lost items</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="report_lost.php" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Report Lost Item
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="py-4">
        <div class="container">
            <div class="filter-section">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search items...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($category_filter == $cat['name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="all_lost.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Items Grid -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Showing <?php echo $total_items; ?> lost items</h4>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">Page <?php echo $page; ?> of <?php echo $total_pages; ?></p>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="row">
                    <?php while($item = $result->fetch_assoc()): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card card-hover h-100">
                                <?php if($item['image']): ?>
                                    <img src="uploads/<?php echo $item['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($item['category']); ?></span>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></small>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($item['location_lost']); ?>
                                            </small>
                                            <?php if($item['reward'] > 0): ?>
                                                <span class="badge bg-warning">$<?php echo number_format($item['reward'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="badge <?php echo $item['status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?>">
                                                <i class="fas fa-<?php echo $item['status'] == 'lost' ? 'search' : 'check-circle'; ?> me-1"></i>
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                            
                                            <?php if (isset($_SESSION['user_id']) && $item['user_id'] == $_SESSION['user_id'] && $item['status'] == 'lost'): ?>
                                                <a href="mark_as_found.php?id=<?php echo $item['id']; ?>" 
                                                   class="btn btn-mark-found btn-sm">
                                                    <i class="fas fa-check-circle me-1"></i>Mark as Found
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid mt-3">
                                            <a href="view_lost.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>No lost items found</h3>
                    <p class="text-muted">Try adjusting your search criteria or browse all items.</p>
                    <a href="all_lost.php" class="btn btn-primary">View All Items</a>
                </div>
            <?php endif; ?>
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