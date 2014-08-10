<?php
	//$inputFile = fopen($_GET['url'],"r");
	$inputFile = fopen("example.html","r");
	
	$tagToClose = "\w+";
	$pattern = "/\<(\w+)\s*[^\>\<]*data-uf=[\"']([^\"']*)[\"'][^\>\<]*>|\<\/(".$tagToClose.")>/";
	
	
	$thisMatch = array();
	while(!feof($inputFile)) {
		preg_match($pattern,fgets($inputFile),$thisMatch);
		if($thisMatch[1] != "") {
			$tagToClose = $thisMatch[1];
			$pattern = "/\<(\w+)\s*[^\>\<]*data-uf=[\"']([^\"']*)[\"'][^\>\<]*>|\<\/(".$tagToClose.")>/";
			}
		print_r($thisMatch);
		}//Sort of works atm but need to be able to halt the file pointer when a regex match is found...
	?>