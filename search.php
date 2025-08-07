<?php
include 'config.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

$lost_items = [];
$found_items = [];

if (!empty($search_query)) {
    // Search in lost items
    $sql = "SELECT * FROM lost_items WHERE 
            (item_name LIKE ? OR description LIKE ? OR location_lost LIKE ?)";
    $params = ["%$search_query%", "%$search_query%", "%$search_query%"];
    $types = "sss";
    
    if (!empty($category_filter)) {
        $sql .= " AND category = ?";
        $params[] = $category_filter;
        $types .= "s";
    }
    
    $sql .= " ORDER BY date_reported DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $lost_items[] = $row;
    }
    
    // Search in found items
    $sql = "SELECT * FROM found_items WHERE 
            (item_name LIKE ? OR description LIKE ? OR location_found LIKE ?)";
    $params = ["%$search_query%", "%$search_query%", "%$search_query%"];
    $types = "sss";
    
    if (!empty($category_filter)) {
        $sql .= " AND category = ?";
        $params[] = $category_filter;
        $types .= "s";
    }
    
    $sql .= " ORDER BY date_reported DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $found_items[] = $row;
    }
}

// Get categories for filter
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
    <title>Search Results - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .search-header {
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

    <!-- Search Header -->
    <section class="search-header">
        <div class="container">
            <div class="text-center">
                <h1 class="display-5 fw-bold mb-4">Search Results</h1>
                <form method="GET" action="search.php">
                    <div class="row justify-content-center">
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control search-box" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search for lost or found items...">
                                <button class="btn btn-light" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="row justify-content-center">
                        <div class="col-md-3 mb-2">
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($category_filter == $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-select" name="type">
                                <option value="">All Types</option>
                                <option value="lost" <?php echo ($type_filter == 'lost') ? 'selected' : ''; ?>>Lost Items</option>
                                <option value="found" <?php echo ($type_filter == 'found') ? 'selected' : ''; ?>>Found Items</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($search_query)): ?>
                <div class="row">
                    <!-- Lost Items Results -->
                    <div class="col-md-6">
                        <h3 class="mb-4">
                            <i class="fas fa-search text-primary me-2"></i>
                            Lost Items (<?php echo count($lost_items); ?>)
                        </h3>
                        
                        <?php if (empty($lost_items)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No lost items found matching your search.
                            </div>
                        <?php else: ?>
                            <?php foreach($lost_items as $item): ?>
                                <div class="card card-hover mb-3">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <?php if($item['image']): ?>
                                                <img src="uploads/<?php echo $item['image']; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                            <?php else: ?>
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                                                <p class="text-muted"><small>Lost on: <?php echo date('M d, Y', strtotime($item['date_reported'])); ?></small></p>
                                                <a href="view_lost.php?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Found Items Results -->
                    <div class="col-md-6">
                        <h3 class="mb-4">
                            <i class="fas fa-hand-holding-heart text-success me-2"></i>
                            Found Items (<?php echo count($found_items); ?>)
                        </h3>
                        
                        <?php if (empty($found_items)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No found items found matching your search.
                            </div>
                        <?php else: ?>
                            <?php foreach($found_items as $item): ?>
                                <div class="card card-hover mb-3">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <?php if($item['image']): ?>
                                                <img src="uploads/<?php echo $item['image']; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                            <?php else: ?>
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                                                <p class="text-muted"><small>Found on: <?php echo date('M d, Y', strtotime($item['date_reported'])); ?></small></p>
                                                <a href="view_found.php?id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>Enter a search term to find lost or found items</h3>
                    <p class="text-muted">Search by item name, description, or location</p>
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