<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    <style>
        /* General Styles */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; line-height: 1.6; }
        h1, h2 { text-align: center; color: #333; }
        label { display: inline-block; width: 150px; font-weight: bold; color: #444; }
        input[type="text"], input[type="number"] { width: 60%; padding: 10px; margin: 5px 0; 
            border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        button { padding: 10px 15px; margin-top: 10px; background-color: #28a745; 
            color: white; border: none; border-radius: 4px; cursor: pointer; }
        button[type="button"] { background-color: #dc3545; /* Remove Book button color */ }
        button:hover { background-color: #218838; /* Hover effect for buttons */ }
        button[type="button"]:hover { background-color: #c82333; /* Hover effect for Remove button */ }
        form { max-width: 600px; margin: 20px auto; background-color: #fff; padding: 20px;
             box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px; }
        #adminResponse { margin-top: 20px; padding: 10px; background-color: #f8d7da;
            color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; display: none; }
    </style>
</head>
<body>
    <h1>Book Management System</h1>
    <!-- Admin Section -->
<section id="adminSection">
    <h2>Admin: Manage Books</h2>
    <form id="bookForm" method="POST">
        <label for="bookId">Book ID (for Update/Remove):</label>
        <input type="number" id="bookId" name="bookId"><br><br>
        <label for="Title">Title:</label>
        <input type="text" id="Title" name="Title" required><br><br>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required><br><br>
        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required><br><br>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>
        <button type="submit" name="action" value="addOrUpdate">Add/Update Book</button>
        <button type="button" onclick="removeBook()">Remove Book</button>
    </form>
    <div id="adminResponse"></div>
</section>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost';
$db = 'book_management';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully";
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $bookId = $_POST['bookId'] ?? null;
    $title = $_POST['Title'] ?? null;
    $author = $_POST['author'] ?? null;
    $category = $_POST['category'] ?? null;
    $price = $_POST['price'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    if ($action === 'addOrUpdate') {
        // Process Add or Update
        if ($title && $author && $category && $price !== null && $quantity !== null) {
            $query = $bookId ? "UPDATE book SET title=?, author=?, category=?, price=?, quantity=? WHERE book_id=?" : 
                                "INSERT INTO book (title, author, category, price, quantity) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($bookId ? "sssdii" : "ssddi", $title, $author, $category, $price, $quantity, $bookId);
            $stmt->execute();
            $message = $stmt->affected_rows ? "Book successfully " . ($bookId ? "updated!" : "added!") : "Error updating/adding book.";
            $stmt->close();
        } else {
            $message = "Please fill all fields.";
        }
    } elseif ($action === 'remove' && $bookId) {
        $stmt = $conn->prepare("DELETE FROM book WHERE book_id=?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $message = $stmt->affected_rows ? "Book removed successfully!" : "Error removing book.";
        $stmt->close();
    }
    echo $message;
}
$conn->close();
?>

<script>
    function removeBook() {
    const bookId = document.getElementById('bookId').value;
    if (!bookId) {
        document.getElementById('adminResponse').innerText = "Please enter a Book ID to remove.";
        document.getElementById('adminResponse').style.display = 'block';
        return;
    }

    const formData = new FormData();
    formData.append('bookId', bookId);
    formData.append('action', 'remove');

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);  // Log the response to the console for debugging
        document.getElementById('adminResponse').innerText = data;
        document.getElementById('adminResponse').style.display = 'block';
    })
    .catch(error => {
        document.getElementById('adminResponse').innerText = "An error occurred: " + error;
        document.getElementById('adminResponse').style.display = 'block';
    });
}

</script>

</body>
</html>
