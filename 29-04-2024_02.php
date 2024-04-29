
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Entries by Time</title>
</head>
<body>
    <h2>Search Entries by Time</h2>
    <form action="" method="GET">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required >
     

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">

        <label for="transaction_id">Transaction ID:</label>
        <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="<?php echo isset($_GET['transaction_id']) ? $_GET['transaction_id'] : ''; ?>">

        <label for="payment_ref">Payment Reference:</label>
        <input type="text" id="payment_ref" name="payment_ref" placeholder="Enter payment reference" value="<?php echo isset($_GET['payment_ref']) ? $_GET['payment_ref'] : ''; ?>">

        <button type="submit" name="submit">Search</button>
    </form>
    <br>

    <?php
    // Include database configuration file
    require "config.php";

    // Pagination
    $limit = 5; // Records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Check if form is submitted
    if (isset($_GET['submit'])) {
        // Retrieve start and end date from the form
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
        $email = $_GET['email'];
        $transaction_id = $_GET['transaction_id'];
        $payment_ref = $_GET['payment_ref'];

        // Construct the SQL query to select entries within the specified date range and other parameters
        $query = "SELECT * FROM log_request 
                  WHERE DATE(entry_time) BETWEEN '$start_date' AND '$end_date' 
                  AND parameter LIKE '%$email%' 
                  AND parameter LIKE '%$transaction_id%' 
                  AND parameter LIKE '%$payment_ref%'
                  ORDER BY entry_time DESC
                  LIMIT $start, $limit";

        // Execute the query and fetch results
        $result = mysqli_query($conn, $query);

        // Check if any rows were returned
        if (mysqli_num_rows($result) > 0) {
            // Display the results in a table format
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Date</th><th>Query Parameters</th><th>Parameters</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                // Decode JSON data from the 'parameter' column
                $parameter_data = json_decode($row['parameter'], true);

                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['entry_time'] . "</td>";
                echo "<td>" . $row['query_params'] . "</td>";
                echo "<td>" . $row['parameter'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Pagination links
            $query = "SELECT COUNT(*) AS count FROM log_request";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $total_pages = ceil($row['count'] / $limit);

            echo "<br>";
            echo "<div>";
            if ($page > 1) {
                echo "<a href='?page=".($page - 1)."'>Previous</a>";
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='?page=".$i."'>".$i."</a>";
            }
            if ($page < $total_pages) {
                echo "<a href='?page=".($page + 1)."'>Next</a>";
            }
            echo "</div>";
        } else {
            echo "No entries found within the specified date range.";
        }
    }
    ?>
