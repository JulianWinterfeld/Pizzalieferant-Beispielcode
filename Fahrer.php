<?php	// UTF-8 marker äöüÄÖÜß€

// This is a template for top level classes, which represent a complete web page and
// which are called directly by the user.
// The order of methods might correspond to the order of thinking during implementation.

require_once './Page.php';

// to do: change name 'PageTemplate' throughout this file
class PageTemplate extends Page
{
	
	
	private $AdressArray = array();
	private $RIDArray = array();
	private $CheckArray = array();
	private $PListArray = array(array());
	private $PreisArray = array();
	private $StatusArray = array();
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
		// to do: fetch data for this view from the database
		$sql = "SELECT * FROM bestellung WHERE BestellungID in (select fBestellungID from BestelltePizza group by fBestellungID)";
		$recordset = $this->database->query ($sql);
		if (!$recordset)
			throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

		// read selected records into result array
		
		$record = $recordset->fetch_assoc();
		$i = 0;
		while ($record) {
			$this->AdressArray[$i] = htmlspecialchars($record["Adresse"]);
			
			$radioid = $record["BestellungID"];
			$this->RIDArray[$i] = $radioid;
			


		



			$checkst = 1;
			$tsql = "SELECT * FROM bestelltepizza WHERE fBestellungID = \"$radioid\"";
		$trecordset = $this->database->query ($tsql);
		if (!$trecordset)
			throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);
		
	
		$preis = 0.0;
		
		$trecord = $trecordset->fetch_assoc();
		$j = 0;
		while ($trecord) {
			//$this->PListArray[$i] = array();
			$pizzen = $trecord["PizzaID"];
			$status = $trecord["Status"];
			$this->StatusArray[$i] = $status;
			$name = htmlspecialchars($trecord["fPizzaName"]);
			
			$this->PListArray[$i][$j] = $name;
			//echo "i = $i";
			//echo "j = $j";
			//echo $this->PListArray[$i][$j];
			$psql = "SELECT * FROM angebot WHERE PizzaName = \"$name\"";
			$precordset = $this->database->query ($psql);
			if (!$precordset)
				throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

			// read selected records into result array
			$precord = $precordset->fetch_assoc();
			$preis = $preis + $precord["Preis"];
			
			if ($status < 2) {
				$checkst = 0;
			}
			
			
			$j++;
			$trecord = $trecordset->fetch_assoc();
		}
		$this->PListArray[$i][$j] = "ENDE";
		$trecordset->free();
			$this->PreisArray[$i] = sprintf("%01.2f",$preis);
			
			
			
			$this->CheckArray[$i] = $checkst;
			$i++;
			
			
			
			$record = $recordset->fetch_assoc();
			
		}
		$recordset->free();
		
		
	}

	protected function generateView() {
		$viewData = $this->getViewData();
		$this->generatePageHeader('Fahrer');
		echo <<<EOT
		<body id="body" onload="Initialisieren();">
  <script src="bestellen.js"> </script>
    <article class="Hauptmenue">
      <header><h1>Fahrer</h1></header>
	  <form action="http://localhost/ewa/Fahrer.php" id="formfahrer" accept-charset="UTF-8" method="get">
	  <input type='hidden' name='post_id' value=temp>
EOT;
		
		for ($i = 0; $i < sizeof($this->AdressArray); $i++){
		//echo count($this->PListArray[$i]);	
			$adresse = $this->AdressArray[$i];
			$radioid = $this->RIDArray[$i];
			$checkst = $this->CheckArray[$i];
			$preis = $this->PreisArray[$i];
			$status = $this->StatusArray[$i];
			if ($checkst == 1){
			echo <<<EOT
			<section>
			<h1>$adresse</h1>
EOT;
			
			echo "<p>Pizzen: ";
			$sizeinner = array();
			$sizeinner = $this->PListArray[$i];
			//echo sizeof($sizeinner);
			//echo "TEST->";
			//$innertmp = sizeof($sizeinner);
			//echo $innertmp;
			$j = 0;
			//echo $this->PListArray[$i][$j];
			while ($this->PListArray[$i][$j] != "ENDE") {
				
				$name = $this->PListArray[$i][$j];
				//echo $this->PListArray[$i][$j];
				
				echo $name;
				echo ", ";
				$j++;
			}
			
			echo "</p>";
			echo "<p>Preis: $preis €</p>";
			
			echo <<<EOT
			<table>
	        <tr>
			  <th>gebacken</th>
		      <th>unterwegs</th>
		      <th>ausgeliefert</th>
		    </tr>
EOT;
			
			echo "<tr>";
			//echo $status;
		if ($checkst == 1){
				
			
			
			
					
			
		switch ($status) {
				case 2:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="2" checked /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="3" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="4" /></td>
EOT;
					break;
				case 3:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="2" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="3" checked /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="4" /></td>
EOT;
					break;
				case 4:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="2" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="3" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="4" checked /></td>
EOT;
					break;
			}
			} else {
				
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="2" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="3" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formfahrer'].submit();" value="4" /></td>
EOT;
			}
			echo "</tr>";
			echo <<<EOT
			</table>
	    </section>
EOT;
	  }
		}
	    
		echo <<<EOT
	  </form>
    </article>
  </body>
EOT;
		
		
		
		// to do: call generateView() for all member variables
		// to do: output view of this page
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
		
		parent::processReceivedData();
		$url1=$_SERVER['REQUEST_URI'];
		header("Refresh: 5; URL=$url1");
		// to do: call processReceivedData() for all member variables
		
	if (isset($_GET['post_id'])) {
		   
		
			
			$str = $_SERVER['QUERY_STRING'];
			$strlen = strlen($str);
				for ($i = 13; $i <=$strlen; $i++){
				$sub = substr($str, $i, 1);
				if(substr($str, $i+1, 1) != "="){
					$i++;
					$sub = $sub . substr($str, $i, 1);
				}
				if(substr($str, $i+1, 1) != "="){
					$i++;
					$sub = $sub . substr($str, $i, 1);
				}
				$st = substr($str, $i+2, 1);
				$i = $i + 3;
				echo $st;
				$sql = "UPDATE bestelltepizza SET Status = \"$st\" WHERE fBestellungID = \"$sub\"";
				
		if ($this->database->query($sql) === TRUE) {
					//echo "New record created successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $this->database->error;
				}
				
				}
				
				header('Location: '. $_SERVER['PHP_SELF']); 
	}
	
		
	}

	public static function main() {
		try {
			$page = new PageTemplate();
			$page->processReceivedData();
			$page->generateView();
		}
		catch (Exception $e) {
			header("Content-type: text/plain; charset=UTF-8");
			echo $e->getMessage();
		}
	}
}

PageTemplate::main();
