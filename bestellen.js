function bestellung(value){
	"use strict";
	
	var neueB = document.getElementById("Warenkorbselect");
	var option = document.createElement("option");
	var att = document.getElementById(value);
	var tmp = document.createAttribute("data-preis");
	
		tmp.value = parseFloat(att.textContent);
	option.setAttributeNode(tmp);
	option.text = value;
	neueB.add(option);
	
	updatepreis();
	check();
}
function loeschen(){
	"use strict";
	var neueB = document.getElementById("Warenkorbselect");
	var tester = document.getElementById("Warenkorbselect").selectedIndex;
	var ware = document.getElementById("Warenkorbselect").options;
	
	
	
	
	if(tester >= 0){
	
	
	}
	for(var i = ware.length-1 ; i >=0;i--){
		if(ware[i].selected){
			neueB.remove(i);
			
				
		}
		
		
	}
	updatepreis();
	check();
}
function alleloeschen(){
	"use strict";
	var Warenkorb = document.getElementById("Warenkorbselect");
	
	while (Warenkorb.firstChild != null)
	  Warenkorb.removeChild(Warenkorbselect.firstChild);
	preis.textContent = "0.00";
	check();
}
function Initialisieren(){
	"use strict";
	/*var ID = window.setInterval(check, 1000);*/
	check();
}

function check(){
	"use strict";
	var neueB = document.getElementById("Warenkorbselect").lastChild;
	var Eingabe = document.getElementById("Addr").value;
	
	if(neueB != null){
	  if((neueB.index == null) || (Eingabe == "")) document.getElementById("Bestellen").disabled = true;
	  else document.getElementById("Bestellen").disabled = false;
	} else document.getElementById("Bestellen").disabled = true;
}

function updatepreis(){
	"use strict";
	var ware = document.getElementById("Warenkorbselect").options;
	var summe = 0.00;
	for(var i = 0;i < ware.length;i++){
		var add = ware[i].getAttribute("data-preis");
	        
	
	        summe = parseFloat(summe) + parseFloat(add);
	        preis.textContent = summe.toFixed(2);
		
	}
	
	
}

function wkselect(){
	"use strict";
	var ware = document.getElementById("Warenkorbselect").options;
	
	for(var i = ware.length-1 ; i >=0;i--){
		ware[i].selected = true;
		
		
	}
	
}