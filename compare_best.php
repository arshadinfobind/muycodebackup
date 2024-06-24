<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=utf-8>
    <title>COMPALEX - database schema compare tool</title>
    <script src="public/js/jquery.min.js"></script>
    <script src="public/js/functional.js"></script>
    <style type="text/css" media="all">
        @import url(public/css/style.css);
    </style>
</head>
<body>
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
                    echo '<a href="index.php?action=' . $li . '" ' . ($li == $_REQUEST['action'] ? 'class="active"' : '') . '>' . $li . '</a>&nbsp;';
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
                <h2>Database1</h2>
                <h4 style="color: darkred; margin-top: 2px;"><?php echo DATABASE_DESCRIPTION ?></h4>
                <span><?php $spath = explode("@", FIRST_DSN); echo end($spath); ?></span>
            </td>
            <td width="50%">
                <h2>Database2</h2>
                <h4 style="color: darkred; margin-top: 2px;"><?php echo DATABASE_DESCRIPTION_SECONDARY ?></h4>
                <span><?php $spath = explode("@", SECOND_DSN); echo end($spath); ?></span>
            </td>
        </tr>

        <?php 
        // Establish connections to both databases before the loop
        $conn_primary = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        $conn_secondary = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_SECONDARY);

        // Check connections
        if ($conn_primary->connect_error) {
            die("Connection failed: " . $conn_primary->connect_error);
        }
        if ($conn_secondary->connect_error) {
            die("Connection failed: " . $conn_secondary->connect_error);
        }

        // Fetch table names for both databases
        $result_primary = $conn_primary->query("SHOW TABLES");
        $result_secondary = $conn_secondary->query("SHOW TABLES");

        // Convert table names to a simpler array
        $tables_primary = array_map('current', $result_primary->fetch_all(MYSQLI_ASSOC));
        $tables_secondary = array_map('current', $result_secondary->fetch_all(MYSQLI_ASSOC));

        // Pre-fetch additional table info for both databases
        $additionalTableInfo_primary = [];
        $additionalTableInfo_secondary = [];
        
        foreach ($tables_primary as $table) {
            $result = $conn_primary->query("SELECT COUNT(*) AS count FROM $table");
            $additionalTableInfo_primary[$table] = ['rows' => $result->fetch_assoc()['count']];
        }

        foreach ($tables_secondary as $table) {
            $result = $conn_secondary->query("SELECT COUNT(*) AS count FROM $table");
            $additionalTableInfo_secondary[$table] = ['rows' => $result->fetch_assoc()['count']];
        }

        foreach ($tables as $tableName => $data) { 
        ?>
            <tr class="data">
                <?php 
                foreach (array('fArray', 'sArray') as $blockType) { 
                    $database_name = ($blockType == "fArray") ? DATABASE_NAME : DATABASE_NAME_SECONDARY;
                    $additionalTableInfo = ($blockType == "fArray") ? $additionalTableInfo_primary : $additionalTableInfo_secondary;
                ?>
                    <td class="type-<?php echo $_REQUEST['action']; ?>">
                        <h3><?php echo $tableName; ?> 
                            <sup style="color: red;">
                                <?php 
                                if ($data != null && isset($data[$blockType]) && $data[$blockType] != null) {
                                    echo count($data[$blockType]); 
                                } 
                                ?>
                            </sup>
                        </h3>
                        <div class="table-additional-info">
                            <?php 
                            if (isset($additionalTableInfo[$tableName])) { 
                            ?>
                                <b>rows - <?php echo $additionalTableInfo[$tableName]['rows']; ?></b><br>
                            <?php 
                            } 
                            ?>
                        </div>
                        <?php if ($data[$blockType]) { ?>
                            <ul>
                                <?php foreach ($data[$blockType] as $fieldName => $tparam) { ?>
                                    <li <?php if (isset($tparam['isNew']) && $tparam['isNew']) { echo 'style="color: red;" class="new"'; } ?>>
                                        <b style="white-space: pre"><?php echo $fieldName; ?></b>
                                        <span <?php if (isset($tparam['changeType']) && $tparam['changeType']) { ?>style="color: red;" class="new"<?php } ?>>
                                            <?php echo $tparam['dtype']; ?>
                                        </span>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <?php if ($data != null && isset($data[$blockType]) && $data[$blockType] != null && count($data[$blockType]) && in_array($_REQUEST['action'], array('tables', 'views'))) { ?>
                            <a target="_blank" onclick="Data.getTableData('index.php?action=rows&baseName=<?php echo $basesName[$blockType]; ?>&tableName=<?php echo $tableName; ?>'); return false;" href="#" class="sample-data">
                                Sample data (<?php echo SAMPLE_DATA_LENGTH; ?> rows)
                            </a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php 
        } 
        // Close connections
        $conn_primary->close();
        $conn_secondary->close();
        ?>
    </table>
    <p>&nbsp;</p>
    <hr />
    <p>For more information go to <a href="http://compalex.net" target="_blank">compalex.net</a></p>
</div>
</body>
</html>
