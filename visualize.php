<?require('Board.php');?>

<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<style>
	table {
		float: right;
		margin-right: 100px;
	}
	td {
		width: 75px;
		height: 75px;
	}
	form {
		margin-left: 100px;
	}
	.dark {
		background: #d28a4a;
	}
	.light {
		background: #ffce9f;
	}
	.red {
		background: red;
	}
</style>

<?php
	$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
	$id = $_GET['id'];
	$boardJSON = json_decode(file_get_contents("$url/play.php?method=print&id=$id"));
	if ($boardJSON->status != 'OK') {
		echo $boardJSON->message;
		exit();
	}
	$turn = $boardJSON->turn;
	$board = new Board($id, $boardJSON->board, ($turn == "White" ? 0 : 1));
	$state = $board->toString();
	$gameState = $boardJSON->gameState;
	$pointer = 0;
	echo "<table>";
	for ($i = 0; $i < 8; $i++) {
		echo "<tr>";
		for ($j = 0; $j < 8; $j++) {
			echo "<td id=".$pointer." ";
			if ($i % 2 == $j % 2) {
				echo "class=light";
			} else {
				echo "class=dark";
			}
			echo " onclick='setValue(".$i.", ".$j.")'>";
			$cur = $state[$pointer];
			if ($cur != '.') {
				if (ctype_upper($cur)) {
					$cur .= "B";
				}
				echo "<img src='img/".$cur.".png'>";
			}
			echo "</td>";
			$pointer++;
		}
		echo "</tr>";
	}
	echo "</table>";
?>

<script>
	startRow = -1;
	startCol = -1;
	endRow = -1;
	endCol = -1;
	function setValue(r, c) {
		if (startRow !== -1) {
			document.getElementById('startRow').value = '';
			document.getElementById('startCol').value = '';
			endRow = r;
			endCol = c;
			$.get(`play.php?method=makeTurn&id=<?=$id?>&start=${chr(ord('A') + startCol)+chr(ord('7') - startRow)}&end=${chr(ord('A') + endCol)+chr(ord('7') - endRow)}`, 
				{}, function(results){
				turnJSON = JSON.parse(results);
				if (turnJSON['status'] === 'OK') {
					location.reload();
				}
			});
			document.getElementById(startRow * 8 + startCol).setAttribute('class', (startRow % 2 == startCol % 2 ? 'light' : 'dark'));
			startRow = -1;
			startCol = -1;
		} else {
			document.getElementById('startRow').value = r;
			document.getElementById('startCol').value = c;
			startRow = r;
			startCol = c;
			document.getElementById(r * 8 + c).setAttribute('class', 'red');
		}
	}

	function ord(string) {
	    return string.charCodeAt(0);
	}

	function chr(ascii) {
	    return String.fromCharCode(ascii);
	}

</script>

<form action="play.php" method="GET">
	<br>
	<input value="<?=$gameState?>"><br><br>
	<input value="<?=$turn?>"><br><br>
	<input name="method" value="makeTurn"> <input name="id" value="<?=$id?>"><br><br>
	<input name="startRow" id="startRow" autocomplete="off"> <input name="startCol" id="startCol" autocomplete="off"><br><br>
	<input name="endRow" id="endRow" autocomplete="off"> <input name="endCol" id="endCol" autocomplete="off"><br><br>
	<button type="submit" id="submit">OK</button>
</form>