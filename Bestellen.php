<?php	// UTF-8 marker äöüÄÖÜß€

// This is a template for top level classes, which represent a complete web page and
// which are called directly by the user.
// The order of methods might correspond to the order of thinking during implementation.

require_once './Page.php';

	
// to do: change name 'PageTemplate' throughout this file
class Bestellen extends Page
{

	private $PizzenArray = array();
	private $BildArray = array();
	private $PreisArray = array();

	// to do: declare attributes (e.g. references for member variables representing substructures/blocks)

	protected function __construct() {
		parent::__construct();
		// to do: instantiate attribute objects
	}

	protected function __destruct() {
		// to do: if necessary, destruct attribute objects representing substructures/blocks
		parent::__destruct();
	}

	protected function getViewData() {
		//abfrage von Daten aus der Datenbank
		// to do: fetch data for this view from the database
		
		$sql = "SELECT * FROM angebot";
		$recordset = $this->database->query ($sql);
		if (!$recordset)
			throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

		// read selected records into result array
		
		$record = $recordset->fetch_assoc();
		$i = 0;
		while ($record) {
			$this->PizzenArray[$i] = htmlspecialchars($record["PizzaName"]);
			$this->BildArray[$i] = $record["Bilddatei"];
			$this->PreisArray[$i] = $record["Preis"];
			$i++;
			
			
			
			
			$record = $recordset->fetch_assoc();
		}
		$recordset->free();
		
		
		
	}

	protected function generateView() {
		//Generierung und ausgabe einer html seite mittels getViewData
		$viewData = $this->getViewData();
		$this->generatePageHeader('Bestellen');
		// to do: call generateView() for all member variables
		// to do: output view of this page
		
		echo <<<EOT
  
  <body id="body" onload="Initialisieren();">
	<script src="bestellen.js"> </script>
	<div>
	  <header>
	  
	  <h1>Bestellung!</h1></header>
      <section class="Hauptmenue">
  	    <table class="Pizzen">
EOT;
		
		for ($i = 0; $i < sizeof($this->PizzenArray); $i++){
			$name = $this->PizzenArray[$i];
			$bild = $this->BildArray[$i];
			$preis = $this->PreisArray[$i];
		
		echo <<<EOT
	      <tr>
			<td><input type="image" alt="" src="$bild" width="50" height="50" onclick="bestellung('$name');"/></td>
			<td>$name <span id="$name">$preis</span>€</td>
		  </tr>
EOT;
		}	  
		  echo <<<EOT
	    </table>	 
     </section>
	    <section class="Warenkorb">
	  <form action="http://localhost/ewa/Bestellen.php" id="form1" accept-charset="UTF-8" method="post">
		<select id = "Warenkorbselect" name = "Warenkorbselect[]" size="5" multiple ></select>  
		<p><span id="preis">0.00</span>€</p>
		
	      <input type="text" id="Addr" name="Adresse" value="" onkeyup="check();" placeholder="Ihre Adresse"  />
	      <input type="reset" name="alldelete" value="alle Loeschen" onclick="alleloeschen();" />
	      <input type="button" name="delete" value="Auswahl Löschen" onclick="loeschen();" />
		  <input type='hidden' name='post_id' value=temp>
		  <input type="submit" id="Bestellen" name="order" value="Bestellen" onclick="wkselect()" />
	   </form>
	 </section>
	</div>
  </body>


EOT;
		
		//https://www.fbi.h-da.de/cgi-bin/Echo.pl
		//http://localhost/ewa/Bestellen.php
		$this->generatePageFooter();
		
	}

	protected function processReceivedData() {
		//session_destroy();
		session_start();
		//echo session_id();
		parent::processReceivedData();
		//print_r($_POST);
		if (isset($_POST['post_id'])) {
		if (isset($_POST["Warenkorbselect"])) {
			$add = $this->database->real_escape_string($_POST['Adresse']);
			$sql = "INSERT INTO bestellung (Adresse) VALUES ('".$add."')";

			if ($this->database->query($sql) === TRUE) {
			} else {
				echo "Error: " . $sql . "<br>" . $this->database->error;
			}
			
			$sql = "SELECT * FROM bestellung ORDER BY BestellungID DESC LIMIT 1";
			$recordset = $this->database->query ($sql);
			if (!$recordset)
				throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

			// read selected records into result array
			$record = $recordset->fetch_assoc();
			$lastid = $this->database->real_escape_string($record["BestellungID"]);
			
			$_session["bid"] = $lastid; 
			$_session["sid"] = session_id();
			
			//echo session_id();
			setcookie(session_id(), $lastid, time() + (86400 * 30), "/");
			
			
			foreach($_POST['Warenkorbselect'] as $ware){
				$resware = $this->database->real_escape_string($ware);
				$sql = "INSERT INTO bestelltepizza (fBestellungID, fPizzaName, Status) VALUES ('".$lastid."' , '".$resware."' , '0')";

				if ($this->database->query($sql) === TRUE) {
					//echo "New record created successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $this->database->error;
				}
			}
		
		}
		//header('Location: '. $_SERVER['PHP_SELF']);
		header('Location: http://localhost/ewa/Kunde.php');
	}	
		// Auswerten der übermittelten Daten
		// to do: call processReceivedData() for all member variables
	}

	public static function main() {
		try {
			$page = new Bestellen();
			$page->processReceivedData();
			$page->generateView();
			
		}
		catch (Exception $e) {
			header("Content-type: text/plain; charset=UTF-8");
			echo $e->getMessage();
		}
	}
}

Bestellen::main();
