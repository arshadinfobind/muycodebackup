<?php
header('Content-Type: application/json');

// Include configuration file
require "config.php";

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

// Get query parameters
$queryParams = $_GET;
$jsonData = json_encode($queryParams);

// Get the client IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Get the current time in UTC
$time = gmdate("Y-m-d H:i:s");

// Function to check for matches in CSV
function check_csv_match($postData) {
    $file = fopen('sample.csv', 'r');
    $matches = [];

    while (($line = fgetcsv($file)) !== FALSE) {
        foreach ($line as $word) {
            foreach ($postData as $field => $value) {
                if (strpos($value, $word) !== FALSE) {
                    $matches[] = "Field '$field' contains the word '$word'";
                }
            }
        }
    }

    fclose($file);

    if (!empty($matches)) {
        return ['status' => 'error', 'messages' => $matches];
    } else {
        return ['status' => 'success', 'message' => 'No matches found'];
    }
}

// Check for matches in the CSV file
$result = check_csv_match($input);

// Insert the log request into the database
$data = json_encode($input);
$sql = "INSERT INTO log_request (entry_time, method, parameter, query_params, ip_address) VALUES ('$time', '{$_SERVER["REQUEST_METHOD"]}', '$data', '$jsonData', '$ip_address')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['log' => 'New record inserted successfully', 'result' => $result]);
} else {
    echo json_encode(['log' => 'Error: ' . $sql . ' - ' . $conn->error, 'result' => $result]);
}

// Close the database connection
$conn->close();

exit();
?>
