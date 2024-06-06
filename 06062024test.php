<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require 'lib.php';
    $config = require 'config.php';

  
    $post_data_json = json_encode($_POST);
    $post_data = json_decode($post_data_json, true);
    $input_value = $post_data['input1'];

   
    $config['spam_keywords'][] = $input_value;


    $isSpam = check_spams_in_csv($config['csv_file_path'], $config['spam_keywords']);

    if ($isSpam) {
        echo "Spam found: " . $input_value . "<br>";
    } else {
        echo "No spam found.";
    }
}
?>

<!-- HTML form to send POST data -->
<form method="post">
    <input type="text" name="input1" placeholder="Enter value to check">
    <button type="submit">Check Spam</button>
</form>
