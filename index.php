<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
	<?php
	$reload = $_GET['autoreload'];
	if ($reload == "true") {
		echo "<meta http-equiv=\"refresh\" content=\"5\">";
	}
	?>
	<title>Tram Abfahrten</title>
	<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/fonts/style.css">

</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="time-banner col-lg-12">
			<?php


			$timestampSys = time();
			$timestampUrl = $_GET["time"];


			if ($timestampUrl == null) {
				$timestamp = $timestampSys;
			} else {
				$timestamp = strtotime($timestampUrl);
			}
			$uhrzeit = date("H:i", $timestamp);

			switch ($reload) {
				case "true":
					$link = "http://tramabfahrten.nx/";
					break;
				case null:
					$link = "http://tramabfahrten.nx/?autoreload=true";
					break;
				default:
					$link = "http://tramabfahrten.nx/";
			}
			
			echo "<a href='$link'>" .
				"<h1 style='text-align: center'>"
				. $uhrzeit .
				"</h1>" .
				"</a>"
			?>

		</div>
	</div>
	<div class="row">
		<?php
		$GLOBALS['format'] = "<div class='tramnr col-xs-offset-2 col-xs-1'>" . '%s' . "</div>" .
			"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
			"<div class='wartezeit col-xs-pull-2 col-xs-3 '>" . '%d\'' . "</div>";

		$GLOBALS['busankunftformat'] = "<div class='tramnr col-xs-offset-2 col-xs-1'>" . '%s' . "</div>" .
			"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
			"<div class='wartezeit col-xs-pull-2 col-xs-3 icon-bus3'></div>";

		class Haltestelle
		{

			function wartezeitBerechnung($tramnr, $warteminuten)
			{
				$difference = $GLOBALS['timestamp'] % ($warteminuten * 60);
				$wartesekunden = ($warteminuten * 60) - $difference;
				$this->printdauer($wartesekunden, $tramnr);

			}

			function printdauer($wartezeitankunft, $tramnr)
			{
				$endhaltestellen = [2 => "Farbhof", 14 => "Triemli", 3 => "Albisrieden"];
				if ($wartezeitankunft < 30) {
					echo sprintf($GLOBALS['busankunftformat'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft);
				} else {

					echo sprintf($GLOBALS['format'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft / 60);
				}
			}

		}


		$haltestelle = new Haltestelle;
		?>
		<?php
		$haltestelle->wartezeitBerechnung(2, 10);
		?>
	</div>
	<div class="row">

		<?php
		$haltestelle->wartezeitBerechnung(14, 5);
		?>
	</div>
	<div class="row">
		<?php
		$haltestelle->wartezeitBerechnung(3, 2);
		?>
	</div>
</div>
</body>
</html>