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
	$GLOBALS['format'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%d' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3 '>" . '%d\'' . "</div></div>";

	$GLOBALS['tramankunftformat'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%d' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3'><div class='bus-parent'><div class='icon-bus3'></div></div></div></div>";
	
	
	//Array wird Zentral initialisiert damit die Daten hinzugefügt werden pro Tram und nicht 3 mal ausgeführt wird. (Beispiel: 10, 10 20, 10 20 30)
	$GLOBALS['warteZeit'] = [];

	class Haltestelle
	{
		// In dieser Funktion habe ich meine Daten für die Abfahrt -> Loop durch, wandle sie in timestamps um und frage ab,
		// ob die Abfahrts Zeit grösser ist als der Momentane Timestamp, wenn ja --> initialisiere ich die nächste funktion,
		// gebe die parameter weiter die gebraucht werden und dan break; um den Loop zu beenden. 
		function wartezeitVorbereitung($tramnr)
		{

			$arr = [
				17 => [

					"0845", "1130", "1032", "1225", "1232", "1239", "1246", "1253",
					"1300", "1307", "1314", "1321", "1328", "1335", "1342", "1349", "1356",
					"1403", "1410", "1417", "1424", "1431", "1438", "1445", "1452", "1530"
				],
				5 => [

					"0840", "1040", "2220", "2227", "2234", "2241", "2248", "2255",
					"2302", "2309", "2316", "2323", "2330", "2337", "2344", "2351", "2358"
				],
				13 => [
					"0837", "1020", "1100", "1623", "1630", "1637", "1644", "1651", "1658",
					"1705", "1712", "1719", "1726", "1733", "1740", "1747", "1754",
					"1801", "1808", "1815", "1822", "1829", "1836", "1843", "1850", "1857"

				],
			];
			for ($i = 0; $i < count($arr[$tramnr]); $i++) {
				$tramAbfahrtTime = strtotime($arr[$tramnr][$i]);
				if ($tramAbfahrtTime > $GLOBALS['timestamp']) {
					$this->wartezeitBerechnung($tramnr, $tramAbfahrtTime);
					break;
				}
			}

		}
		// Hier benutze ich die übergebene AbfahrtsZeit und den Momentanen Timestamp um die Wartezeit zu berechnen.
		// Diese Warte Zeit füge ich dem Array 'warteZeit' + gebe noch die Tramnummer als Key mit,
		// danach Initialisiere ich die nächste Funktion und gebe die Parameter mit.
		function wartezeitBerechnung($tramnr, $abfahrtsZeit)
		{

			$warteZeit = $abfahrtsZeit - $GLOBALS['timestamp'];
			$GLOBALS['warteZeit'][$tramnr] = $warteZeit;
			$this->printdauer($warteZeit, $tramnr);

		}

		//Hier definiere ich die Endhaltestellen der Trams und meine Ausgaben (sprintf),
		// und erstelle die Abfrage ob das Tram bald Ankommt oder noch nicht da ist und gebe dan die passende Variable (die mit sprintf) mit.
		function printdauer($wartezeitankunft, $tramnr)
		{
			$endhaltestellen = [17 => "Werdhölzli", 13 => "Frankental", 5 => "Laubegg"];


//			print_r($GLOBALS['warteZeit']);
			$trBD = sprintf($GLOBALS['tramankunftformat'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft);
			$trND = sprintf($GLOBALS['format'], $tramnr, $endhaltestellen[$tramnr], $wartezeitankunft / 60);
			if ($wartezeitankunft < 30) {
				echo $trBD;
			} else {

				echo $trND;
			}
		}
		//In dieser Funktion sortiere ich den Array mit den Wartezeiten loop durch und gebe diese aus.
		//Diese Function ist von den anderen abgelegen* und wird deswegen nur 1 mal ausgeführt. 
		
		// *(sie wird icht von einer anderen funktion initialisiert. nur 1 mal von mir am Schluss der Funktion aufrufe**)
		function printit()
		{
			asort($GLOBALS['warteZeit']);
			foreach ($GLOBALS['warteZeit'] as $i) {
				echo $i . " ";
			}
		}
		

	}
	//Hier sind meine Function Calls.
	$haltestelle = new Haltestelle;
	$haltestelle->wartezeitVorbereitung(17);
	$haltestelle->wartezeitVorbereitung(5);
	$haltestelle->wartezeitVorbereitung(13);
	$haltestelle->printit();   // ** Hier 

	?>
</div>
</body>
</html>

<!--$arr = array(-->
<!--				array('tramnummer' => 17, 'abfahrten' =>[-->
<!---->
<!--					"1025", "1030", "1032", "1225", "1232", "1239", "1246", "1253",-->
<!--					"1300", "1307", "1314", "1321", "1328", "1335", "1342", "1349", "1356",-->
<!--					"1403", "1410", "1417", "1424", "1431", "1438", "1445", "1452", "1459"-->
<!--				]),-->
<!--				array('tramnummer' => 5, 'abfahrten' =>[-->
<!---->
<!--					"2206", "2213", "2220", "2227", "2234", "2241", "2248", "2255",-->
<!--					"2302", "2309", "2316", "2323", "2330", "2337", "2344", "2351", "2358"-->
<!--				]),-->
<!--					array('tramnummer' => 13, 'abfahrten' =>[-->
<!--					"1602", "1609", "1616", "1623", "1630", "1637", "1644", "1651", "1658",-->
<!--					"1705", "1712", "1719", "1726", "1733", "1740", "1747", "1754",-->
<!--					"1801", "1808", "1815", "1822", "1829", "1836", "1843", "1850", "1857"-->
<!---->
<!--				]),-->
<!--			);-->