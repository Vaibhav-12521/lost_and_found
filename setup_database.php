<?php
// Database setup script
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS lost_found_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("lost_found_db");

// Create users table first (since other tables will reference it)
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create lost_items table with user_id column
$sql = "CREATE TABLE IF NOT EXISTS lost_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    location_lost VARCHAR(255),
    date_lost DATE,
    date_reported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    contact_name VARCHAR(255),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(20),
    image VARCHAR(255),
    status ENUM('lost', 'found', 'closed') DEFAULT 'lost',
    reward DECIMAL(10,2) DEFAULT 0.00,
    user_id INT(11),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Lost items table created successfully<br>";
} else {
    echo "Error creating lost_items table: " . $conn->error . "<br>";
}

// Create found_items table with user_id column
$sql = "CREATE TABLE IF NOT EXISTS found_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    location_found VARCHAR(255),
    date_found DATE,
    date_reported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finder_name VARCHAR(255),
    finder_email VARCHAR(255),
    finder_phone VARCHAR(20),
    image VARCHAR(255),
    status ENUM('available', 'claimed', 'closed') DEFAULT 'available',
    current_location VARCHAR(255),
    user_id INT(11),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Found items table created successfully<br>";
} else {
    echo "Error creating found_items table: " . $conn->error . "<br>";
}

// Create categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "Categories table created successfully<br>";
} else {
    echo "Error creating categories table: " . $conn->error . "<br>";
}

// Insert default categories
$categories = [
    ['Electronics', 'Electronic devices like phones, laptops, tablets'],
    ['Jewelry', 'Rings, necklaces, watches, and other jewelry'],
    ['Clothing', 'Clothes, shoes, bags, and accessories'],
    ['Documents', 'ID cards, passports, certificates, books'],
    ['Keys', 'House keys, car keys, office keys'],
    ['Pets', 'Lost pets and animals'],
    ['Other', 'Other miscellaneous items']
];

foreach ($categories as $category) {
    $sql = "INSERT IGNORE INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category[0], $category[1]);
    $stmt->execute();
}

echo "Default categories inserted successfully<br>";

// Create mark_as_found table
$sql = "CREATE TABLE IF NOT EXISTS mark_as_found (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    lost_item_id INT(11) NOT NULL,
    found_by_user_id INT(11),
    found_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    found_location VARCHAR(255),
    notes TEXT,
    status ENUM('pending', 'confirmed', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (lost_item_id) REFERENCES lost_items(id) ON DELETE CASCADE,
    FOREIGN KEY (found_by_user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Mark as found table created successfully<br>";
} else {
    echo "Error creating mark_as_found table: " . $conn->error . "<br>";
}

// Add user_id columns to existing tables if they don't exist
// Function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Add user_id column to lost_items table if it doesn't exist
if (!columnExists($conn, 'lost_items', 'user_id')) {
    $sql = "ALTER TABLE lost_items ADD COLUMN user_id INT(11)";
    if ($conn->query($sql) === TRUE) {
        echo "user_id column added to lost_items table<br>";
    } else {
        echo "Error adding user_id column to lost_items: " . $conn->error . "<br>";
    }
} else {
    echo "user_id column already exists in lost_items table<br>";
}

// Add user_id column to found_items table if it doesn't exist
if (!columnExists($conn, 'found_items', 'user_id')) {
    $sql = "ALTER TABLE found_items ADD COLUMN user_id INT(11)";
    if ($conn->query($sql) === TRUE) {
        echo "user_id column added to found_items table<br>";
    } else {
        echo "Error adding user_id column to found_items: " . $conn->error . "<br>";
    }
} else {
    echo "user_id column already exists in found_items table<br>";
}

$conn->close();
echo "<hr>";
echo "<h3>🎉 Database setup completed successfully!</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px;'>";
echo "<strong>All tables created with latest features:</strong><br>";
echo "• Users table with authentication<br>";
echo "• Lost items table with user tracking<br>";
echo "• Found items table with user tracking<br>";
echo "• Categories table with default categories<br>";
echo "• Mark as found table for tracking found items<br>";
echo "</div>";
?> 