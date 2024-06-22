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
// Establish connections
$conn1 = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$conn2 = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_SECONDARY);

// Check connection
if ($conn1->connect_error || $conn2->connect_error) {
    die("Connection failed: " . $conn1->connect_error . " / " . $conn2->connect_error);
}

// Fetch table names and row counts for DATABASE_NAME
$tables1 = array();
$sqlTables1 = "SHOW TABLES";
$resultTables1 = $conn1->query($sqlTables1);
if ($resultTables1 && $resultTables1->num_rows > 0) {
    while ($rowTables1 = $resultTables1->fetch_row()) {
        $tables1[] = $rowTables1[0];
    }
}

// Fetch table names and row counts for DATABASE_NAME_SECONDARY
$tables2 = array();
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
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes', 'triggers');
                        break;
                    case 'sqlserv':
                    case 'mssql':
                    case 'dblib':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes');
                        break;
                    case 'pgsql':
                        $buttons = array('tables', 'views', 'functions', 'indexes');
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
                <h4 style="color: darkred; margin-top: 2px; "><?php echo DATABASE_DESCRIPTION ?></h4>
                <span><?php $spath = explode("@", FIRST_DSN);
                    echo end($spath); ?></span>
            </td>
            <td  width="50%">
                <h2><?php echo DATABASE_NAME_SECONDARY ?></h2>
                <h4 style="color: darkred; margin-top: 2px; "><?php echo DATABASE_DESCRIPTION_SECONDARY ?></h4>
                <span><?php $spath = explode("@", SECOND_DSN);
                    echo end($spath); ?></span>
            </td>
        </tr>
       
    <?php foreach ($tables1 as $key => $tableName1) { ?>
        <tr class="data">
            <td class="type-<?php echo $_REQUEST['action']; ?>">
                <h3><?php echo $tableName1; ?> <sup style="color: red;"><?php 
                $sqlCount1 = "SELECT COUNT(*) AS count FROM " . $tableName1;
                $resultCount1 = $conn1->query($sqlCount1);
                $rowCount1 = ($resultCount1) ? $resultCount1->fetch_assoc()['count'] : 0;
                echo $rowCount1; 
                ?></sup></h3>
                <div class="table-additional-info">
                    <?php
                    // Display additional information for DATABASE_NAME tables
                    if (isset($additionalTableInfo[$tableName1]['fArray'])) {
                        echo '<b>Rows: </b>' . $rowCount1 . '<br>';
                        // Add more additional information if needed
                    }
                    ?>
                </div>
            </td>
            <td class="type-<?php echo $_REQUEST['action']; ?>">
                <?php if (isset($tables2[$key])) {
                    $tableName2 = $tables2[$key];
                    ?>
                    <h3><?php echo $tableName2; ?> <sup style="color: red;"><?php 
                    $sqlCount2 = "SELECT COUNT(*) AS count FROM " . $tableName2;
                    $resultCount2 = $conn2->query($sqlCount2);
                    $rowCount2 = ($resultCount2) ? $resultCount2->fetch_assoc()['count'] : 0;
                    echo $rowCount2; 
                    ?></sup></h3>
                    <div class="table-additional-info">
                        <?php
                        // Display additional information for DATABASE_NAME_SECONDARY tables
                        if (isset($additionalTableInfo[$tableName2]['sArray'])) {
                            echo '<b>Rows: </b>' . $rowCount2 . '<br>';
                            // Add more additional information if needed
                        }
                        ?>
                    </div>
                <?php } else { ?>
                    <h3><sup style="color: red;">Table not found</sup></h3>
                <?php } ?>
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
