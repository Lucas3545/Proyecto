<?php
// Test database connection
include('./includes/config.php');

echo "<h2>Testing Database Connection</h2>";

try {
    $conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
        echo "<p><strong>Check:</strong></p>";
        echo "<ul>";
        echo "<li>Is MAMP running?</li>";
        echo "<li>Is MySQL service started?</li>";
        echo "<li>Database name: " . $DB_NAME . "</li>";
        echo "<li>Username: " . $DB_USERNAME . "</li>";
        echo "</ul>";
        die();
    }
    
    echo "<p style='color: green;'>✅ Connected successfully to database: <strong>" . $DB_NAME . "</strong></p>";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'users' exists</p>";
        
        // Check table structure
        $columns = $conn->query("DESCRIBE users");
        echo "<h3>Table Structure:</h3><ul>";
        while ($row = $columns->fetch_assoc()) {
            echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Table 'users' does not exist. Run the SQL script to create it.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
