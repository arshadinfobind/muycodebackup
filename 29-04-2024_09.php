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
        <input type="date" id="start_date" name="start_date" required value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
     
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

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
    require "config.php";

    $limit = 5;
    $page = isset($_GET["page"]) ? $_GET["page"] : 1;
    $start = ($page - 1) * $limit;

    if (isset($_GET["submit"])) {
        $start_date = $_GET["start_date"];
        $end_date = $_GET["end_date"];
        $email = $_GET["email"];
        $transaction_id = $_GET["transaction_id"];
        $payment_ref = $_GET["payment_ref"];

        $query = "SELECT * FROM log_request 
                  WHERE DATE(entry_time) BETWEEN '$start_date' AND '$end_date' 
                  AND parameter LIKE '%$email%' 
                  AND parameter LIKE '%$transaction_id%' 
                  AND parameter LIKE '%$payment_ref%'
                  ORDER BY entry_time DESC";

        $result = mysqli_query($conn, $query);
        $total_rows = mysqli_num_rows($result);
        $total_pages = ceil($total_rows / $limit);

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $query .= " LIMIT $start, $limit";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $parameter_data = json_decode($row["parameter"], true);

                echo "ID: " . $row["id"] . "<br>";
                // echo "Date: " . $row['entry_time'] . "<br>";
                echo "Date: " .
                    date("Y-m-d", strtotime($row["entry_time"])) .
                    "<br>";
                echo "Query Parameters: " . $row["query_params"] . "<br>";
                echo "Method: " . $row["method"] . "<br>";
                echo "Parameters: " . $row["parameter"] . "<br>";
                echo "<hr>";
            }

            // Pagination links
            echo "<br>";
            echo "<div>";
            if ($page > 1) {
                echo "<a href='?page=" .
                    ($page - 1) .
                    "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit='>Previous</a>";
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='?page=" .
                    $i .
                    "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit='>" .
                    $i .
                    "</a>";
            }
            if ($page < $total_pages) {
                echo "<a href='?page=" .
                    ($page + 1) .
                    "&start_date=$start_date&end_date=$end_date&email=$email&transaction_id=$transaction_id&payment_ref=$payment_ref&submit='>Next</a>";
            }
            echo "</div>";
        } else {
            echo "No entries found within the specified date range.";
        }
    } 
    elseif(!isset($_GET["submit"])){
        // echo "Hello Everyone";
        $query = "SELECT * FROM log_request ORDER BY entry_time DESC LIMIT $limit";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if there are any results
    if (mysqli_num_rows($result) > 0) {
        // Loop through the results and display them
        while ($row = mysqli_fetch_assoc($result)) {
            // Display the data as needed
            echo "ID: " . $row['id'] . "<br>";
            echo "Date: " . date('Y-m-d', strtotime($row['entry_time'])) . "<br>";
            echo "Query Parameters: " . $row['query_params'] . "<br>";
            echo "Parameters: " . $row['parameter'] . "<br>";
            echo "<hr>";
        }
    }
}

    

?>

</body>
