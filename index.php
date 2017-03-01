<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">

	<?php
	
	//reload variable definieren -> abfrage ob sie gesetzt ist wenn ja -> $_GET['autoreload'] else -> null setzten
	//else autoreload true setzen
	
	$reload = (isset($_GET['autoreload'])) ? $_GET['autoreload'] : null;
	
	//abfrage ob reload True ist, wenn ja -> autoreload aktivieren
	
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

			//Zeit Variabeln definieren
			$timestampSys = time();
			//Gleiche abfrage wue bei autoreload
			$timestampUrl = (isset($_GET["time"])) ? $_GET["time"] : null;

			//damit es immer eine Zeit gibt die benutzt wird frage ich ab ob die URL $VAR gleich null ist, true -> systemzeit nehmen, else -> url var in timestamp umwandeln.
			if ($timestampUrl == null) {
				$timestamp = $timestampSys;
			} else {
				$timestamp = strtotime($timestampUrl);
			}
			$uhrzeit = date("H:i", $timestamp);

			?>
			
<!--			Click auf uhr ändert den metatag und den Link auf autoreload on/off-->
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

	//format für meine ausgaben definieren Fall 1: tram braucht > 30s Fall2: < 30s (%vars sind die werte die mit dem sprintf definition ausgetausch werden)
	
	$GLOBALS['standardFormat'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%d' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3 '>" . '%d\'' . "</div></div>";

	$GLOBALS['tramAnkunftFormat'] = "<div class='row'><div class='tramnr col-xs-offset-2 col-xs-1'>" . '%d' . "</div>" .
		"<div class='endhaltestelle col-xs-6'>" . '%s' . "</div>" .
		"<div class='wartezeit col-xs-pull-2 col-xs-3'><div class='bus-parent'><div class='icon-bus3'></div></div></div></div>";

	// Array wird Zentral initialisiert damit die Daten hinzugefügt werden pro Tram und nicht 3 mal ausgeführt wird. (Beispiel: 10, 10 20, 10 20 30)

	$GLOBALS['warteZeit'] = [];

	class Haltestelle
	{

		// Iterativ
		// In dieser Funktion habe ich meine Daten für die Abfahrt -> Loop durch, wandle sie in timestamps um und frage ab,
		// ob die Abfahrts Zeit grösser ist als der Momentane Timestamp, wenn ja --> initialisiere ich die nächste funktion,
		// gebe die parameter weiter (wartezeitBerechnung) die gebraucht werden und dan break; um den Loop zu beenden.

		function wartezeitVorbereitung($tramnr)
		{

			$arr = [
				17 => [
					"0845", "1130", "1032", "1225", "1232", "1239", "1246", "1253",
					"1300", "1307", "1314", "1321", "1328", "1335", "1342", "1349", "1356",
					"1403", "1410", "1417", "1424", "1431", "1438", "1445", "1452", "1530", "1625"
				],

				5 => [
					"0840", "1040", "1612", "2227", "2234", "2241", "2248", "2255",
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

		// Iterativ
		// Hier benutze ich die übergebene AbfahrtsZeit und den Momentanen Timestamp um die Wartezeit zu berechnen.
		// Diese Warte Zeit füge ich dem Array 'warteZeit' + gebe noch die Tramnummer als Key mit,
		// danach Initialisiere ich die nächste Funktion (printdauer) und gebe die Parameter mit.

		function wartezeitBerechnung($tramnr, $abfahrtsZeit)
		{

			$warteZeit = $abfahrtsZeit - $GLOBALS['timestamp'];
			$GLOBALS['warteZeit'][$tramnr] = $warteZeit;

		}



		// Iterativ
		// Hier definiere ich die Endhaltestellen der Trams und meine Ausgaben (sprintf),
		// und erstelle die Abfrage ob das Tram bald Ankommt oder noch nicht da ist und gebe dan die passende Variable (die mit sprintf) mit.

		function printdauer($wartezeitankunft, $tramnr)
		{

			$endhaltstellen = [17 => "Werdhölzli", 13 => "Frankental", 5 => "Laubegg"];
			$trBD = sprintf($GLOBALS['tramAnkunftFormat'], $tramnr, $endhaltstellen[$tramnr], $GLOBALS['warteZeit'][$tramnr]);
			$trND = sprintf($GLOBALS['standardFormat'], $tramnr, $endhaltstellen[$tramnr], $GLOBALS['warteZeit'][$tramnr] / 60);

			if ($wartezeitankunft < 30) {

				echo $trBD;

			} else {

				echo $trND;

			}

		}

		// Nicht Iterativ
		// In dieser Funktion sortiere ich den Array mit den Wartezeiten loop durch und gebe diese aus.
		// Diese Function ist von den anderen abgelegen* und wird deswegen nur 1 mal ausgeführt.
		// *(sie wird icht von einer anderen funktion initialisiert. nur 1 mal von mir am Schluss der Funktion aufrufe**)
		// gibt die sortierten werte an printdauer weiter

		function printit()
		{

			asort($GLOBALS['warteZeit']);
			foreach ($GLOBALS['warteZeit'] as $key => $val) {
				$this->printdauer($val, $key);
			}

		}

	}

	//Hier sind meine Function Calls.

	$haltestelle = new Haltestelle;

	//*Wird an wartezeitVorbereitung weitergegeben mit tramnr als parameter**

	$haltestelle->wartezeitVorbereitung(17);
	$haltestelle->wartezeitVorbereitung(5);
	$haltestelle->wartezeitVorbereitung(13);

	//***********************************************************************

	$haltestelle->printit();   // ** Hier 

	?>
</div>
</body>
</html>