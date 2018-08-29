<?php	// UTF-8 marker äöüÄÖÜß€

// This is a template for top level classes, which represent a complete web page and
// which are called directly by the user.
// The order of methods might correspond to the order of thinking during implementation.

require_once './Page.php';

// to do: change name 'PageTemplate' throughout this file
class PageTemplate extends Page
{
	private $NameArray = array();
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
		$lastid = $_COOKIE[session_id()];
		
		$sql = "SELECT * FROM bestelltepizza WHERE fBestellungID = \"$lastid\"";
		$recordset = $this->database->query ($sql);
		if (!$recordset)
			throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

		// read selected records into result array
		$radioid = 0;
		$record = $recordset->fetch_assoc();
		$i = 0;
		while ($record) {
			$this->StatusArray[$i] = $record["Status"];
			$this->NameArray[$i] = htmlspecialchars($record["fPizzaName"]);
			$i++;
			
			$record = $recordset->fetch_assoc();
		}
		$recordset->free();
		
	}

	protected function generateView() {
		$viewData = $this->getViewData();
		$this->generatePageHeader('Kunde');
		echo <<<EOT
  <body id="body" onload="Initialisieren();">
    <script src="bestellen.js"> </script>
	
	
    <article class="Hauptmenue">
      <header><h1>Kunde</h1></header>
	    <form action="https://www.fbi.h-da.de/cgi-bin/Echo.pl" id="form1" accept-charset="UTF-8" method="get">
	      <table class="Baeckerstatus">
	        <tr>	
		      <th></th>
		      <th>bestellt</th>
		      <th>im Ofen</th>
		      <th>fertig</th>
		      <th>unterwegs</th>
			  <th>geliefert</th>
		    </tr>
EOT;
		
		$radioid = 0;
		for ($i = 0; $i < sizeof($this->StatusArray); $i++){
			$name = $this->NameArray[$i];
			$status = $this->StatusArray[$i];
			
			
			echo "<tr>";
			echo "<td>$name</td>";
			switch ($status) {
				case 0:
					echo <<<EOT
					<td><input type="radio" name="$radioid" value="0" checked disabled /></td>
					<td><input type="radio" name="$radioid" value="1" disabled /></td>
					<td><input type="radio" name="$radioid" value="2" disabled /></td>
					<td><input type="radio" name="$radioid" value="3" disabled /></td>
					<td><input type="radio" name="$radioid" value="4" disabled /></td>
EOT;
					break;
				case 1:
					echo <<<EOT
					<td><input type="radio" name="$radioid" value="0" disabled /></td>
					<td><input type="radio" name="$radioid" value="1" checked disabled /></td>
					<td><input type="radio" name="$radioid" value="2" disabled /></td>
					<td><input type="radio" name="$radioid" value="3" disabled /></td>
					<td><input type="radio" name="$radioid" value="4" disabled /></td>
EOT;
					break;
				case 2:
					echo <<<EOT
					<td><input type="radio" name="$radioid" value="0" disabled /></td>
					<td><input type="radio" name="$radioid" value="1" disabled /></td>
					<td><input type="radio" name="$radioid" value="2" checked disabled /></td>
					<td><input type="radio" name="$radioid" value="3" disabled /></td>
					<td><input type="radio" name="$radioid" value="4" disabled /></td>
EOT;
					break;
				case 3:
					echo <<<EOT
					<td><input type="radio" name="$radioid" value="0" disabled /></td>
					<td><input type="radio" name="$radioid" value="1" disabled /></td>
					<td><input type="radio" name="$radioid" value="2" disabled /></td>
					<td><input type="radio" name="$radioid" value="3" checked disabled /></td>
					<td><input type="radio" name="$radioid" value="4" disabled /></td>
EOT;
					break;
				case 4:
					echo <<<EOT
					<td><input type="radio" name="$radioid" value="0" disabled /></td>
					<td><input type="radio" name="$radioid" value="1" disabled /></td>
					<td><input type="radio" name="$radioid" value="2" disabled /></td>
					<td><input type="radio" name="$radioid" value="3" disabled /></td>
					<td><input type="radio" name="$radioid" value="4" checked disabled /></td>
EOT;
					break;
			}
			echo "</tr>";
			
			
			$radioid++;
		}
	
	        
			
			echo <<<EOT
	      </table>
	    </form>
	  <a href="Bestellen.php">Neue Bestellung</a>
    </article>
  </body>
EOT;
		
		
		// to do: call generateView() for all member variables
		// to do: output view of this page
		$this->generatePageFooter();
	}

	protected function processReceivedData() {
		session_start();
		parent::processReceivedData();
		// to do: call processReceivedData() for all member variables
		//echo session_id();
		
		$url1=$_SERVER['REQUEST_URI'];
		header("Refresh: 5; URL=$url1");
		
		
		
		
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
