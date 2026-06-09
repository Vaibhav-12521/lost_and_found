<?php
echo "<h2>🚀 Lost & Found Application Installation</h2>";  

$mysql_connection = @mysqli_connect("localhost", "root", ""); 
if (!$mysql_connection) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>";
    echo "❌ Error: MySQL connection failed!<br>";
    echo "Please make sure:<br>";
    echo "1. XAMPP is installed<br>";
    echo "2. MySQL service is running<br>";
    echo "3. Apache service is running<br>";
    echo "</div>";
    exit();
}

echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px;'>";
echo "✅ MySQL connection successful!<br>";
echo "</div>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS lost_found";
if (mysqli_query($mysql_connection, $sql)) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px;'>";
    echo "✅ Database 'lost_found' created successfully!<br>";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>";
    echo "❌ Error creating database: " . mysqli_error($mysql_connection) . "<br>";
    echo "</div>";
}

mysqli_close($mysql_connection);

// Run database setup
echo "<h3>📊 Setting up database tables...</h3>";
include 'setup_database.php';

echo "<hr>";
echo "<h3>🎉 Installation Complete!</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px;'>";
echo "<strong>Your application is ready to use!</strong><br><br>";
echo "🔗 <a href='index.php' style='color: blue;'>Go to Home Page</a><br>";
echo "🔗 <a href='signup.php' style='color: blue;'>Sign Up</a><br>";
echo "🔗 <a href='signin.php' style='color: blue;'>Sign In</a><br>";
echo "</div>";

echo "<h4>📋 Available URLs:</h4>";
echo "<ul>";
echo "<li><strong>Home:</strong> http://localhost/retry3/</li>";
echo "<li><strong>Sign Up:</strong> http://localhost/retry3/signup.php</li>";
echo "<li><strong>Sign In:</strong> http://localhost/retry3/signin.php</li>";
echo "<li><strong>Profile:</strong> http://localhost/retry3/profile.php</li>";
echo "<li><strong>Report Lost:</strong> http://localhost/retry3/report_lost.php</li>";
echo "<li><strong>Report Found:</strong> http://localhost/retry3/report_found.php</li>";
echo "</ul>";
?>
