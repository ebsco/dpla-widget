<html>
    <head>
        <title>Digital Public Library of America Widget - Results List</title>
        <style type="text/css">
            a {
                text-decoration:none;
            }
            img {
                
                max-width:55px;
                max-height:55px;
                padding:4px;
                background-color:white;
                border:1px solid silver;
                
                box-shadow:2px 2px 3px #666;
                -webkit-box-shadow:2px 2px 3px #666;
            }
            #moreLink {
                clear:both;
                margin-top:8px;
                font-size:12px;
                font-family: arial,sans-serif;
            }
	    td {
                font-size:12px;
                font-family: arial,sans-serif;
	    }
	    p {
                font-size:12px;
                font-family: arial,sans-serif;		
	    }
        </style>
    </head>
    <body>
        
<?php
	// check for a search term and a DPLA API Key
        if (!(isset($_GET['q'])))
            die("No Search Term Set.");
	if (!(isset($_GET['apikey'])))
	    die("No API Key set.");
	$query = $_GET['q'];
	
	// remove any limiters from beginning of screen. these show up when you click on a subject heading or author, etc., in your results, or they may be manually typed in
	// e.g., transform "AU Smith" to "Smith"
	$limiter = substr($query,0,3);
        if (($limiter == "TI ") or ($limiter == "AU ") or ($limiter == "TX ") or ($limiter == "SU ") or ($limiter == "SO ") or ($limiter == "AB ") or ($limiter == "IS ") or ($limiter == "IB ") or ($limiter == "DE ") or ($limiter == "SA ") or ($limiter == "KW ") or ($limiter == "ZU ") or ($limiter == "ZK ") or ($limiter == "AR ")) {
	    $query = substr($query,3);    
	}
	
	// strip any limiters within the text
	$query = str_replace(" TI "," ",$query);
	$query = str_replace(" AU "," ",$query);
	$query = str_replace(" TX "," ",$query);
	$query = str_replace(" SU "," ",$query);
	$query = str_replace(" SO "," ",$query);
	$query = str_replace(" AB "," ",$query);
	$query = str_replace(" IS "," ",$query);
	$query = str_replace(" IB "," ",$query);
	$query = str_replace(" DE "," ",$query);
	$query = str_replace(" SA "," ",$query);
	$query = str_replace(" KW "," ",$query);
	$query = str_replace(" ZU "," ",$query);
	$query = str_replace(" ZK "," ",$query);
	$query = str_replace(" AR "," ",$query);

	// encode the query
	$query = urlencode($query);
	
	$apikey = $_GET['apikey'];
	
	// construct both the API call and a link to send users to if they want more
        $url = "http://api.dp.la/v2/items?q=".$query."&api_key=" .$apikey;
        $moreLink = "http://dp.la/search?q=".$query;
        
        $session = curl_init($url); 	               	       // Open the Curl session
	curl_setopt($session, CURLOPT_HEADER, false); 	       // Don't return HTTP headers
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);   // Do return the contents of the call
	$xml = curl_exec($session); 	                       // Make the call
	curl_close($session); 				       // And close the session
        
	// create a json object to traverse
	$result = json_decode($xml);
	
	// get number of results
	$numresults = intval($result->count);
	$count = 0;
	
	if ($numresults > 0) {	
	    echo '<table border="0" cellspacing="3" width="160">';
	    foreach ($result->docs as $doc) {
		// for each result (up to 4), show an image, a title, and have both link to the item in DPLA
		$count++;
		if ($count <= 4) {
		    $link = $doc->isShownAt;
		    $title = $doc->sourceResource->title;

		    // sometimes I find an array in the title node - this will grab the first title
		    if (is_array($title)) {
			$title = $title[0];			
		    }

		    // if there is an image, it's here
		    if (isset($doc->object)) {
			$image = $doc->object;
		    }
		    
		    // if not, show placeholder image
		    if (!(isset($image))) {
			$image = "http://dp.la/assets/icon-text.gif";
		    }
		    
		    // for display in tiny area, truncate long titles
		    if (strlen($title) > 36) {
			$title = substr($title,0,34) . "...";
		    }
			
		    // display item
		    echo '<tr><td><a href="'.$link.'" target="_blank"><img src="' . $image . '" width="55" /></a></td><td><a href="' . $link . '" target="_blank">' . $title . '</a></td></tr>';
		}
	    }
	    echo '</table>';
	    // provide link to jump out to DPLA
	    echo '<div id="moreLink"><a href="' . $moreLink . '" target="_blank">Find More at the Digital Public Library of America</a>';
	} else {
	    // no results case
	    echo '<p><a href="http://dp.la" target="_blank"><img src="dpla_logo.jpg" style="margin:0 auto;max-width:120px;max-height:120px;"></a></p><p>The Digital Public Library of America (DPLA) brings together the riches of America\'s libraries, archives, and museums, and makes them freely available to the world. It strives to contain the full breadth of human expression, from the written word, to works of art and culture, to records of America\'s heritage, to the efforts and data of science.</p><p><strong><a href="http://dp.la" target="_blank">Visit the DPLA</a></strong></p>';
	}
?>

    </body>
</html>
