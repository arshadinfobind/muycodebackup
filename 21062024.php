<?php


if (!isset($_COOKIE['primaryDB']) || !isset($_COOKIE['secondaryDB'])) {
    // Redirect to select_dbs.php if either of the cookies is not set
    header('Location: select_dbs.php');

   
}

else{


require_once 'config.php';

// Print all cookies
echo "<h2>Cookies:</h2>";
if(count($_COOKIE) > 0) {
    echo "<ul>";
    foreach ($_COOKIE as $key => $value) {
        echo "<li><strong>$key:</strong> $value</li>";
    }
    echo "</ul>";
} else {
    echo "No cookies found.";
}

    $destinationUrl = "https://www.infobind.net/aud_infob_global_tools/compalex/select_dbs.php"; 
    echo '<a href="' . htmlspecialchars($destinationUrl, ENT_QUOTES) . '">Click here to Select other databases</a>';

try {
    if (!defined('FIRST_DSN')) throw new Exception('Check your config.php file and uncomment settings section for your database');
    if (!strpos(FIRST_DSN, '://')) throw new Exception('Wrong dsn format');

    $pdsn = explode('://', FIRST_DSN);
    define('DRIVER', $pdsn[0]);

    if (!file_exists(DRIVER_DIR . DRIVER . '.php')) throw new Exception('Driver ' . DRIVER . ' not found');

    $firstDsnSplited = explode('/', FIRST_DSN);
    $secondDsnSplited = explode('/', SECOND_DSN);

    define('FIRST_BASE_NAME', end($firstDsnSplited));
    define('SECOND_BASE_NAME', end($secondDsnSplited));

    // abstract class
    require_once DRIVER_DIR . 'abstract.php';
    require_once DRIVER_DIR . DRIVER . '.php';

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'tables';
    
    $additionalTableInfo = array();
    switch ($action) {
        case "tables":
            $tables = Driver::getInstance()->getCompareTables();
            $additionalTableInfo = Driver::getInstance()->getAdditionalTableInfo();
            break;
        case "views":
            $tables = Driver::getInstance()->getCompareViews();
            break;
        case "procedures":
            $tables = Driver::getInstance()->getCompareProcedures();
            break;
        case "functions":
            $tables = Driver::getInstance()->getCompareFunctions();
            break;
        case "indexes":
            $tables = Driver::getInstance()->getCompareKeys();
            break;
        case "triggers":
            $tables = Driver::getInstance()->getCompareTriggers();
            break;
        case "rows":
            $rows = Driver::getInstance()->getTableRows($_REQUEST['baseName'], $_REQUEST['tableName']);
            break;
    }

    
    $basesName = array(
        'fArray' => FIRST_BASE_NAME,
        'sArray' => SECOND_BASE_NAME
    );
    
    if ($action == 'rows') {
        require_once TEMPLATE_DIR . 'rows.php';
    } else {
        require_once TEMPLATE_DIR . 'compare.php';
    }

} catch (Exception $e) {
    include_once TEMPLATE_DIR . 'error.php';
}
}
