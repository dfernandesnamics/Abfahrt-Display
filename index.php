<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
	<?php
	$reload = (isset($_GET['autoreload'])) ? $_GET['autoreload'] : null;
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
			$timestampUrl = (isset($_GET["time"])) ? $_GET["time"] : null;


			if ($timestampUrl == null) {
				$timestamp = $timestampSys;
			} else {
				$timestamp = strtotime($timestampUrl);
			}
			$uhrzeit = date("H:i", $timestamp);

			?>

			<a href="
			<?php
			$url = null;
			if ($reload == 'true') {
				$url = ".";
			} elseif (!isset($reload)) {
				$url = "?autoreload=true";
			}
			echo $url;
			?>
				">
				<h1 style='text-align:center'>
					<?php
					echo $uhrzeit;
					?>
				</h1>
			</a>
		</div>


	</div>
	<?php
	$GLOBALS['format'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%s' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3 '>" . '%d\'' . "</div></div>";

	$GLOBALS['busankunftformat'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%s' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3'><div class='bus-parent'><div class='icon-bus3'></div></div></div></div>";


	class Haltestelle
	{
		function wartezeitVorbereitung($tramnr, $wartezeit)
		{
			$arr = [
				17
				=>
					[

						"1204", "1211", "1218", "1225", "1232", "1239", "1246", "1253",
						"1300", "1307", "1314", "1321", "1328", "1335", "1342", "1349", "1356",
						"1403", "1410", "1417", "1424", "1431", "1438", "1445", "1452", "1459",
					],
				5
				=>
					[

						"2206", "2213", "2220", "2227", "2234", "2241", "2248", "2255",
						"2302", "2309", "2316", "2323", "2330", "2337", "2344", "2351", "2358"
					],
				13
				=>
					[
						"1602", "1609", "1616", "1623", "1630", "1637", "1644", "1651", "1658",
						"1705", "1712", "1719", "1726", "1733", "1740", "1747", "1754",
						"1801", "1808", "1815", "1822", "1829", "1836", "1843", "1850", "1857",

					],
			];
			for ($i = 0; $i < 100; $i++) {
				strtotime($arr[13][$i]);
				if ($GLOBALS['timestamp'] % $arr[13][0] <= $wartezeit) {
					$this->wartezeitBerechnung($tramnr, $wartezeit);
				}
			}
			echo $GLOBALS['timestamp'] % $arr[13][0];

		}

		function wartezeitBerechnung($tramnr, $warteminuten)
		{
			$difference = $GLOBALS['timestamp'] % ($warteminuten * 60);
			$wartesekunden = ($warteminuten * 60) - $difference;

			$arr = [];
			$this->printdauer($wartesekunden, $tramnr);

		}

		function printdauer($wartezeitankunft, $tramnr)
		{
			$endhaltestellen = [17 => "Werdhölzli", 13 => "Frankental", 5 => "Laubegg"];
			if ($wartezeitankunft < 30) {
				echo sprintf($GLOBALS['busankunftformat'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft);
			} else {

				echo sprintf($GLOBALS['format'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft / 60);
			}
		}

	}


	$haltestelle = new Haltestelle;

	$haltestelle->wartezeitBerechnung(17, 7);
	$haltestelle->wartezeitBerechnung(13, 10);
	$haltestelle->wartezeitBerechnung(5, 15);
	?>
</div>
</body>
</html>