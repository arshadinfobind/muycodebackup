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
        <?php foreach ($tables1 as $key => $tableName1) { ?>
            <?php
            // Fetch number of columns for each table in DATABASE_NAME
            $sqlColumns1 = "SHOW COLUMNS FROM " . $tableName1;
            $resultColumns1 = $conn1->query($sqlColumns1);
            $numColumns1 = ($resultColumns1) ? $resultColumns1->num_rows : 0;

            // Fetch number of columns for the corresponding table in DATABASE_NAME_SECONDARY
            $tableName2 = isset($tables2[$key]) ? $tables2[$key] : '';
            $sqlColumns2 = "SHOW COLUMNS FROM " . $tableName2;
            $resultColumns2 = $conn2->query($sqlColumns2);
            $numColumns2 = ($resultColumns2) ? $resultColumns2->num_rows : 0;
            ?>
            <tr class="data">
                <td class="type-<?php echo $_REQUEST['action']; ?>">
                    <h3><?php echo $tableName1; ?> <sup style="color: red;"><?php echo $numColumns1; ?> columns</sup></h3>
                    <div class="table-additional-info">
                        <?php
                        // Example: Fetching additional info (row count)
                        $additionalInfo1 = fetchAdditionalInfo($conn1, $tableName1, 'fArray');
                        if (!empty($additionalInfo1)) {
                            echo '<b>Rows:</b> ' . $additionalInfo1['row_count'] . '<br>';
                            // You can display more additional information here
                        }
                        ?>
                    </div>
                </td>
                <td class="type-<?php echo $_REQUEST['action']; ?>">
                    <h3><?php echo $tableName2; ?> <sup style="color: red;"><?php echo $numColumns2; ?> columns</sup></h3>
                    <div class="table-additional-info">
                        <?php
                        // Example: Fetching additional info (row count)
                        $additionalInfo2 = fetchAdditionalInfo($conn2, $tableName2, 'sArray');
                        if (!empty($additionalInfo2)) {
                            echo '<b>Rows:</b> ' . $additionalInfo2['row_count'] . '<br>';
                            // You can display more additional information here
                        }
                        ?>
                    </div>
                </td>
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
