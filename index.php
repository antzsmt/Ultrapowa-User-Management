<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" />
<title>Ultrapowa User Management</title>
</head>

<body>
<div id='canvas'>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
		<input type="text" name="username">
		<input type="submit" value="Submit">
	</form>
	
<?php
//initset
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

//config include
include "config.php";

// Pretty Print function
function prettyPrint( $json ) {
	$result = '';
	$level = 0;
	$in_quotes = false;
	$in_escape = false;
	$ends_line_level = NULL;
	$json_length = strlen( $json );

	for( $i = 0; $i < $json_length; $i++ ) {
		$char = $json[$i];
		$new_line_level = NULL;
		$post = "";
		if( $ends_line_level !== NULL ) {
			$new_line_level = $ends_line_level;
			$ends_line_level = NULL;
		}
		if ( $in_escape ) {
			$in_escape = false;
		} else if( $char === '"' ) {
			$in_quotes = !$in_quotes;
		} else if( ! $in_quotes ) {
			switch( $char ) {
				case '}': case ']':
					$level--;
					$ends_line_level = NULL;
					$new_line_level = $level;
					break;

				case '{': case '[':
					$level++;
				case ',':
					$ends_line_level = $level;
					break;

				case ':':
					$post = " ";
					break;

				case " ": case "\t": case "\n": case "\r":
					$char = "";
					$ends_line_level = $new_line_level;
					$new_line_level = NULL;
					break;
			}
		} else if ( $char === '\\' ) {
			$in_escape = true;
		}
		if( $new_line_level !== NULL ) {
			$result .= "\n".str_repeat( "\t", $new_line_level );
		}
		$result .= $char.$post;
	}

	return $result;
}

// Define players array
$players = array();

if($_POST){
	$playerId = $_POST['playerId'];
	if($_POST['playerclan'] && $_POST['clanID']){
		$clanID = $_POST['clanID'];
		$jsonClanData = json_encode($clanData);
		$updateSql = "UPDATE clan SET Data='".$_POST['playerclan']."' WHERE ClanId='".$clanID."'";
		$conn->query($updateSql);
	}
	
	if($_POST['avatar'] && $_POST['playerId']){
		$avatar = $_POST['avatar'];
		$playerID = $_POST['playerId'];
		$updateSql = "UPDATE player SET Avatar='".$avatar."' WHERE PlayerId='".$playerID."'";
		$conn->query($updateSql);
	}
	
	if($_POST['gameObject'] && $_POST['playerId']){
		$gameObject = $_POST['gameObject'];
		$playerID = $_POST['playerId'];
		$updateSql = "UPDATE player SET GameObjects='".$gameObject."' WHERE PlayerId='".$playerID."'";
		$conn->query($updateSql);
	}
}

//player select
$sql = "SELECT * FROM player";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
	while($row = $result->fetch_assoc()) {

		$playerID = $row['PlayerId'];
		$AccountStatus = $row['AccountStatus'];
		$AccountPrivileges = $row['AccountPrivileges'];
		$LastUpdateTime = $row['LastUpdateTime'];
		
		$avatar = $row["Avatar"];
		$avatarObj = json_decode($row["Avatar"], true);
		$gameobjects = $row["GameObjects"];
		$gameobjectsObj = json_decode($row["GameObjects"], true);
		
		$ClanId = $avatarObj['alliance_id'];
		$playerClan = "SELECT clan.ClanId, clan.LastUpdateTime, clan.Data FROM clan WHERE clan.ClanId=" . $ClanId;
		$playerClanResult = $conn->query($playerClan);
		
		if ($playerClanResult->num_rows > 0) {
			while($playerClanRow = $playerClanResult->fetch_assoc()) {
				$clanData = json_decode($playerClanRow['Data'], true);
				$playerclan = $playerClanRow['Data'];
			}
		} else {
			$playerclan = "geen clan";
		}
	
		$players[] = array(
			"PlayerId" => $row['PlayerId'],
			"AccountStatus" => $row['AccountStatus'],
			"AccountPrivileges" => $row['AccountPrivileges'],
			"LastUpdateTime" => $row['LastUpdateTime'],
			"avatar" => $avatar,
			"avatarObj" => $avatarObj,
			"gameobjects" => $gameobjects,
			"playerclan" => $playerclan,
			"clanID" => $clanData['alliance_id']
		);
	
	}
}

foreach($players as $player){

	$playername = $player['avatarObj']['avatar_name'];
	$ClanId = $player['avatarObj']['alliance_id'];
	$th = $player['avatarObj']['townhall_level'] + 1;
	echo "
		<div id='result'>
			<div id='details'>
				<h5>User details</h5>
				<ul>
				<li>Player id: " . $player["PlayerId"]."</li>
				<li>Player name: " . $playername ."</li>
				<li>Town Hall level: ".$th ."</li>
				<li>Status: " . $player["AccountStatus"]."</li>
				<li>Server Permissions: " . $player["AccountPrivileges"]."</li>
				<li>Last online " . $player["LastUpdateTime"]."</li>
				</ul>
			</div>
			<div id='ta'>
				<h5>Clan info " . $playername . "</h5>
				<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
					<input type='hidden' name='playerId' value='".$player["PlayerId"]."'>
					<input type='hidden' name='clanID' value='".$player["clanID"]."'>
					<textarea class='styled' name='playerclan'>" . prettyPrint( $player['playerclan'] ) . "</textarea>
					<input type='submit' value='Update'>
				</form>
			</div>
			<div class='avatar-buildings'>
				<div class='avatar'>
					<h5>Avatar " . $playername . "</h5>
					<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
						<input type='hidden' name='playerId' value='".$player["PlayerId"]."'>
						<textarea class='styled' name='avatar'>" . prettyPrint($player['avatar']) . "</textarea>
						<input type='submit' value='Update'>
					</form>
				</div>
				<div class='buildings'>
					<h5>Buildings</h5> 
					<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
						<input type='hidden' name='playerId' value='".$player["PlayerId"]."'>
						<textarea class='styled' name='gameObject'>" . prettyPrint($player['gameobjects']) . "</textarea>
						<input type='submit' value='Update'>
					</form>
				</div>
			</div>
		</div>
	";
}

$conn->close();
?>
</div>
</body>
</html>