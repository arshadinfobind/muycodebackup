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
        <label for="start_time">Start Time:</label>
        <input type="datetime-local" id="start_time" name="start_time" required >
     

        <label for="end_time">End Time:</label>
        <input type="datetime-local" id="end_time" name="end_time" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">

        <label for="transaction_id">transaction_id:</label>
        <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter transaction_id" value="<?php echo isset($_GET['transaction_id']) ? $_GET['transaction_id'] : ''; ?>">

        <label for="payment_ref">payment_ref:</label>
        <input type="text" id="payment_ref" name="payment_ref" placeholder="Enter payment_ref" value="<?php echo isset($_GET['payment_ref']) ? $_GET['payment_ref'] : ''; ?>">

        

        <button type="submit" name="submit">Search</button>
    </form>
    <br>
</body>
</html>

<?php

// Include database configuration file
require "config.php";

// Check if form is submitted
if (isset($_GET['submit'])) {
    // Retrieve start and end time from the form
    $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
    $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
    $email = $_GET['email']; // Retrieve email from the form
    $transaction_id = $_GET['transaction_id'];
    $payment_ref = $_GET['payment_ref'];

    
    // Construct the SQL query to select entries within the specified time range and email
    $query = "SELECT * FROM log_request WHERE entry_time BETWEEN '$start_time' AND '$end_time' AND parameter LIKE '%$email%' AND parameter LIKE '%$transaction_id%' AND parameter LIKE '%$payment_ref%'";
    
    // Execute the query and fetch results
    $result = mysqli_query($conn, $query);

    // Check if any rows were returned
    if (mysqli_num_rows($result) > 0) {
        // Display the results
        while ($row = mysqli_fetch_assoc($result)) {
            // Decode JSON data from the 'parameter' column
            $parameter_data = json_decode($row['parameter'], true);

            echo "parameters: " . $row['parameter'] . "<br>";
            echo "query_parameters: " . $row['query_params'] . "<br>";
            echo "Entry Time: " . $row['entry_time'] . "<br>";
            echo "Method Type: " . $row['method'] . "<br>";
            echo "IP: " . $row['ip_address'] . "<br>";

            echo "<br>";
            echo "<hr>";

            echo "<br>";
            // foreach ($parameter_data as $key => $value) {
            //     echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "<br>";
            // }
                      
        }
    } else {

        echo "If no entries found within the specified time range, retrieve the latest data<br>";
        $latest_query = "SELECT * FROM log_request ORDER BY entry_time DESC LIMIT 1";
        $latest_result = mysqli_query($conn, $latest_query);

        if (mysqli_num_rows($latest_result) > 0) {
            // Display the latest entry
            $latest_row = mysqli_fetch_assoc($latest_result);
            // Decode JSON data from the 'parameter' column
            $parameter_data = json_decode($latest_row['parameter'], true);

            echo "parameters: " . $latest_row['parameter'] . "<br>";
            echo "query_parameters: " . $latest_row['query_params'] . "<br>";
            echo "Entry Time: " . $latest_row['entry_time'] . "<br>";
            echo "Method Type: " . $latest_row['method'] . "<br>";
            echo "IP: " . $latest_row['ip_address'] . "<br>";

            echo "<br>";
            echo "<hr>";
        } else {
            echo "No entries found for the specified time and email, and no latest data available.";
        }
    }
}
?>
