
<?php

require "config2.php";


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected databases
    $primaryDB = $_POST['primaryDB'];
    $secondaryDB = $_POST['secondaryDB'];

    // Set cookies for selected databases
    setcookie("primaryDB", $primaryDB, time() + (86400 * 30), "/"); // 30 days
    setcookie("secondaryDB", $secondaryDB, time() + (86400 * 30), "/");

    // Redirect to the desired page
    // header("Location: https://www.infobind.net/aud_infob_global_tools/compalex");
    /*
	$domain = "infobind.net";
    $path = "/aud_infob_global_tools/compalex";
    $url = "https://" . $domain . $path;
	//header("Location: $url");
	//print "<pre>"; print_r($_SERVER);
	*/
	header("Location: /aud_infob_global_tools/compalex");
    exit();
}

// Check if databases are already selected
// if (isset($_COOKIE['primaryDB']) && isset($_COOKIE['secondaryDB'])) {
//     // Redirect to the desired page
//     header("Location: https://www.infobind.net/aud_infob_global_tools/compalex");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Databases</title>
</head>
<body>
    <h2>Select Databases</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="primaryDB">Select Primary Database:</label>
        <select name="primaryDB" id="primaryDB">
            <?php foreach ($databases as $database): ?>
                <option value="<?php echo $database; ?>"><?php echo $database; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="secondaryDB">Select Secondary Database:</label>
        <select name="secondaryDB" id="secondaryDB">
            <?php foreach ($databases as $database): ?>
                <option value="<?php echo $database; ?>"><?php echo $database; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Save">
    </form>
</body>
</html>
