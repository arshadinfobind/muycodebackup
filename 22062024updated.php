


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>COMPALEX - database schema compare tool</title>
    <script src="public/js/jquery.min.js"></script>
    <script src="public/js/functional.js"></script>
    <style type="text/css" media="all">
        @import url("public/css/style.css");
    </style>
</head>

<body>
<?php
// Define your database connections
$conn1 = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$conn2 = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_SECONDARY);

// Check connection
if ($conn1->connect_error || $conn2->connect_error) {
    die("Connection failed: " . $conn1->connect_error . " / " . $conn2->connect_error);
}

// Function to fetch additional table info
function fetchAdditionalInfo($conn, $tableName, $blockType) {
    $additionalInfo = array();

    // Example: Fetching row count
    $sql = "SELECT COUNT(*) AS row_count FROM " . $tableName;
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $additionalInfo['row_count'] = $row['row_count'];
    }

    // You can add more queries here to fetch additional info based on your requirements

    return $additionalInfo;
}

// Initialize tables arrays for each database
$tables1 = array();
$tables2 = array();

// Populate $tables1 with table names from DATABASE_NAME
$sqlTables1 = "SHOW TABLES";
$resultTables1 = $conn1->query($sqlTables1);
if ($resultTables1 && $resultTables1->num_rows > 0) {
    while ($rowTables1 = $resultTables1->fetch_row()) {
        $tables1[] = $rowTables1[0];
    }
}

// Populate $tables2 with table names from DATABASE_NAME_SECONDARY
$sqlTables2 = "SHOW TABLES";
$resultTables2 = $conn2->query($sqlTables2);
if ($resultTables2 && $resultTables2->num_rows > 0) {
    while ($rowTables2 = $resultTables2->fetch_row()) {
        $tables2[] = $rowTables2[0];
    }
}
?>
<div class="modal-background" onclick="Data.hideTableData(); return false;">
    <div class="modal">
        <iframe src="" frameborder="0"></iframe>
    </div>
</div>

<div class="compare-database-block">
    <h1>Compalex<span style="color: red;">.net</span></h1>
    <h3>Database schema compare tool</h3>
    <table class="table">
        <tr class="panel">
            <td>
                <?php
                switch (DRIVER) {
                    case 'oci8':
                    case 'oci':
                    case 'mysql':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes');
                        break;
                    case 'sqlserv':
                    case 'mssql':
                    case 'dblib':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes');
                        break;
                    case 'pgsql':
                        $buttons = array('tables', 'views', 'functions', 'indexes');
                        break;
                    default:
                        $buttons = array('tables', 'views');
                        break;
                }

                if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'tables';
                foreach ($buttons as $li) {
                    echo '<a href="index.php?action=' . $li . '"  ' . ($li == $_REQUEST['action'] ? 'class="active"' : '') . '>' . $li . '</a>&nbsp;';
                }
                ?>
            </td>
            <td class="sp">
                <a href="#" onclick="Data.showAll(this); return false;" class="active">all</a>
                <a href="#" onclick="Data.showDiff(this); return false;">changed</a>
            </td>
        </tr>
    </table>
    <table class="table">
        <tr class="header">
            <td width="50%">
                <h2><?php echo DATABASE_NAME ?></h2>
                <h4 style="color: darkred; margin-top: 2px;"><?php echo DATABASE_DESCRIPTION ?></h4>
                <span><?php echo end(explode("@", FIRST_DSN)); ?></span>
            </td>
            <td width="50%">
                <h2><?php echo DATABASE_NAME_SECONDARY ?></h2>
                <h4 style="color: darkred; margin-top: 2px;"><?php echo DATABASE_DESCRIPTION_SECONDARY ?></h4>
                <span><?php echo end(explode("@", SECOND_DSN)); ?></span>
            </td>
        </tr>
        <?php foreach ($tables1 as $tableName) { ?>
            <tr class="data">
                <?php foreach (array('fArray', 'sArray') as $blockType) { ?>
                    <td class="type-<?php echo $_REQUEST['action']; ?>">
                        <h3><?php echo $tableName; ?> <sup style="color: red;"><?php echo ($blockType == 'fArray' ? DATABASE_NAME : DATABASE_NAME_SECONDARY); ?></sup></h3>
                        <div class="table-additional-info">
                            <?php
                            $additionalInfo = fetchAdditionalInfo(($blockType == 'fArray' ? $conn1 : $conn2), $tableName, $blockType);
                            if (!empty($additionalInfo)) {
                                foreach ($additionalInfo as $paramKey => $paramValue) {
                                    echo "<b>{$paramKey}</b>: {$paramValue}<br />";
                                }
                            }
                            ?>
                        </div>
                        <?php
                        $sqlFields = "DESCRIBE " . $tableName;
                        $resultFields = ($blockType == 'fArray' ? $conn1->query($sqlFields) : $conn2->query($sqlFields));
                        if ($resultFields && $resultFields->num_rows > 0) {
                            echo '<ul>';
                            while ($rowFields = $resultFields->fetch_assoc()) {
                                $fieldName = $rowFields['Field'];
                                // Example: Checking if field exists in the other database
                                $changeType = '';
                                if ($blockType == 'fArray') {
                                    $sqlCheck = "DESCRIBE " . $tableName . " " . $fieldName;
                                    $resultCheck = $conn2->query($sqlCheck);
                                    if (!$resultCheck || $resultCheck->num_rows == 0) {
                                        $changeType = 'Missing in ' . DATABASE_NAME_SECONDARY;
                                    }
                                }
                                if ($blockType == 'sArray') {
                                    $sqlCheck = "DESCRIBE " . $tableName . " " . $fieldName;
                                    $resultCheck = $conn1->query($sqlCheck);
                                    if (!$resultCheck || $resultCheck->num_rows == 0) {
                                        $changeType = 'Missing in ' . DATABASE_NAME;
                                    }
                                }
                                echo '<li ' . (!empty($changeType) ? 'style="color: red;" class="new"' : '') . '><b style="white-space: pre">' . $fieldName . '</b>';
                                if (!empty($changeType)) {
                                    echo '<span style="color: red;" class="new">' . $changeType . '</span>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                        <?php if (in_array($_REQUEST['action'], array('tables', 'views'))) { ?>
                            <a target="_blank" onclick="Data.getTableData('index.php?action=rows&baseName=<?php echo ($blockType == 'fArray' ? DATABASE_NAME : DATABASE_NAME_SECONDARY); ?>&tableName=<?php echo $tableName; ?>'); return false;"
                               href="#" class="sample-data">Sample data (<?php echo SAMPLE_DATA_LENGTH; ?> rows)</a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
    <p>&nbsp;</p>
    <hr />
    <p>For more information go to <a href="http://compalex.net" target="_blank">compalex.net</a></p>
</div>
</body>
</html>

<?php
// Close connections
$conn1->close();
$conn2->close();
?>
