<?php
function check_spams_in_csv($csv_file_path, $data) {
    $file_handle = fopen($csv_file_path, 'r');
    if ($file_handle === false) {
        return false; // if File not found
    }

    $spamFound = false;

    while (!feof($file_handle)) {
        $line = fgetcsv($file_handle, 0, ',');
        if ($line) {
            foreach ($data as $spam_word) {
                if (in_array($spam_word, $line)) {
                    $spamFound = true;
                    break 2; // Exit both loops if spam is found
                }
            }
        }
    }

    fclose($file_handle);
    return $spamFound;
}

?>
