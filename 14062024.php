<?php


// function shutdown_function() {
//     $error = error_get_last();
//     if ($error !== NULL && $error['type'] === E_ERROR) {
      
//         // $serverConfigFile = "/opt/aud_infob_global/scripts/config" . $_SERVER['HTTP_HOST'] . ".php";
//         $serverConfigFile = "/opt/aud_infob_global/scripts/config/" . $_SERVER['HTTP_HOST'] . ".php";
//         // echo "$serverConfigFile";


        
        
//         if (file_exists($serverConfigFile)) {
//             // echo "hello";
//             include($serverConfigFile);
//             // Check if 'log_errors' is set and true for this server
//             if (isset($config['log_errors']) && $config['log_errors'] == 1) {
//                 $errorString = date('Y-m-d H:i:s') . " - Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n";
//                 file_put_contents("/opt/aud_infob_global/scripts/global_errors/fatal_errors.log", $errorString, FILE_APPEND | LOCK_EX);
//             }
//         }
   
       
//     }
// }

function shutdown_function() {
    $error = error_get_last();
    if ($error !== NULL && $error['type'] === E_ERROR) {
        $errorString = date('Y-m-d H:i:s') . " - Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n";
        file_put_contents("/opt/aud_infob_global/tools/global_errors/fatal_errors.log", $errorString, FILE_APPEND | LOCK_EX);
    }
}

register_shutdown_function('shutdown_function');


// register_shutdown_function('shutdown_function');


?>
