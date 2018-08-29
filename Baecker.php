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
	private $RIDArray = array();
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
		
		$sql = "SELECT * FROM bestelltepizza WHERE Status < 3";
		$recordset = $this->database->query ($sql);
		if (!$recordset)
			throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);

		// read selected records into result array
		
		$record = $recordset->fetch_assoc();
		$i = 0;
		while ($record) {
			$this->StatusArray[$i] = $record["Status"];
			$this->RIDArray[$i] = $record["PizzaID"];
			$this->NameArray[$i] = htmlspecialchars($record["fPizzaName"]);
			$i++;
			
			
			
			
			$record = $recordset->fetch_assoc();
		}
		$recordset->free();
		
	}

	protected function generateView() {
		$viewData = $this->getViewData();
		$this->generatePageHeader('Baecker');
		echo <<<EOT
		<body id="body" onload="Initialisieren();">
  
  <script src="bestellen.js"> </script>
    <article class="Hauptmenue">
	
      <header><h1>Baecker</h1></header>
	  <form action="http://localhost/ewa/Baecker.php" id="formbaecker" accept-charset="UTF-8" method="get">
	    <input type='hidden' name='post_id' value=temp>
		<table class="Baeckerstatus">
	      <tr>
		    <th></th>
		    <th>bestellt</th>
		    <th>im Ofen</th>
		    <th>fertig</th>		 
		  </tr>
EOT;

		for ($i = 0; $i < sizeof($this->StatusArray); $i++){
			$name = $this->NameArray[$i];
			$status = $this->StatusArray[$i];
			$radioid = $this->RIDArray[$i];
			
			echo "<tr>";
			echo "<td>$name</td>";
			switch ($status) {
				case 0:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="0" checked /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="1" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="2" /></td>
EOT;
					break;
				case 1:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="0" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="1" checked /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="2" /></td>
EOT;
					break;
				case 2:
					echo <<<EOT
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="0" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="1" /></td>
					<td><input type="radio" name="$radioid" onclick="document.forms['formbaecker'].submit();" value="2" checked /></td>
EOT;
					break;
			}
			echo "</tr>";
			
			
		}
		
	    
		  
		  echo <<<EOT
	    </table>
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
		
		if (isset($_GET['post_id'])) {
		echo "test";			
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
				echo $sub;
				echo $st;
				echo "test";
				$sql = "UPDATE bestelltepizza SET Status = \"$st\" WHERE PizzaID = \"$sub\"";

				if ($this->database->query($sql) === TRUE) {
					//echo "New record created successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $this->database->error;
				}
				
			}
			
			header('Location: '. $_SERVER['PHP_SELF']);
		}
		// to do: call processReceivedData() for all member variables
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
