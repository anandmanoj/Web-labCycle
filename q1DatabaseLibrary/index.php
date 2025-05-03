<?php include "db.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Entry and Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Enter Book Information</h2>
    <form method="post" action="">
        <label>Accession Number:</label><br>
        <input type="text" name="accession_number" required><br>
        <label>Title:</label><br>
        <input type="text" name="title" required><br>
        <label>Authors:</label><br>
        <input type="text" name="authors" required><br>
        <label>Edition:</label><br>
        <input type="text" name="edition" required><br>
        <label>Publisher:</label><br>
        <input type="text" name="publisher" required><br><br>
        <input type="submit" name="submit" value="Add Book">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $acc = $_POST['accession_number'];
        $title = $_POST['title'];
        $authors = $_POST['authors'];
        $edition = $_POST['edition'];
        $publisher = $_POST['publisher'];

        // Check for existing accession number
        $check_stmt = $conn->prepare("SELECT * FROM books WHERE accession_number = ?");
        $check_stmt->bind_param("s", $acc);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<p style='color: red;'>Error: A book with this accession number already exists!</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO books (accession_number, title, authors, edition, publisher) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $acc, $title, $authors, $edition, $publisher);
            $stmt->execute();
            echo "<p style='color: green;'>Book added successfully!</p>";
            $stmt->close();
        }

        $check_stmt->close();

    }
    ?>

    <h2>Search Book by Title</h2>
    <form method="get" action="">
        <label>Enter Title:</label>
        <input type="text" name="search_title">
        <input type="submit" name="search" value="Search">
    </form>

    <?php
    if (isset($_GET['search'])) {
        $search_title = "%" . $_GET['search_title'] . "%";
        $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ?");
        $stmt->bind_param("s", $search_title);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h3>Search Results:</h3>";
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Accession Number</th>
                        <th>Title</th>
                        <th>Authors</th>
                        <th>Edition</th>
                        <th>Publisher</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['accession_number']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['authors']}</td>
                        <td>{$row['edition']}</td>
                        <td>{$row['publisher']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No books found with that title.</p>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>
