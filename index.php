<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" />
<title>JSON DB Updater</title>
</head>

<body>
<div id='canvas'>
<?php
//initset
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
//config include
include "config.php";

$sql = "SELECT * FROM player";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
		
		$avatar = (json_decode($row["Avatar"], true));
		$playername = $avatar['avatar_name'];
		$th = ($avatar['townhall_level']+1);
		echo"
		<div id='result'><div id='details'><h5>User details</h5>
        <ul>
		<li>Player id: " . $row["PlayerId"]."</li>
		<li>Player name: " . $playername ."</li>
		<li>Town Hall level: ".$th ."</li>
		<li>Status: " . $row["AccountStatus"]."</li>
		<li>Server Permissions: " . $row["AccountPrivileges"]."</li>
		<li>Last online " . $row["LastUpdateTime"]."</li></ul>
		</div>
		<div id='ta'>";
		//$avatar = $row["Avatar"];
		//$avatar = json_decode($avatar);
		echo"<h5>Avatar " . $playername . "</h5><textarea id='styled'>" . json_encode($avatar, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."</textarea>";
		$gameobjects = $row["GameObjects"];
		$gameobjects = json_decode($gameobjects);
		echo"<h5>Buildings</h5><textarea id='styled'>" . json_encode($gameobjects, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."</textarea>";
		echo "</div></div>";
    }
} else {
    echo "There are 0 results";
}
$conn->close();
?>
</div>
</body>

</html>
