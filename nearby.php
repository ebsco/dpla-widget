<html>
    <head>
        <title>What's around? - A DPLA Example App</title>
    </head>
    <body>
        
        <?php

            function getStateNameByAbbreviation($state){  
         if ($state=="AK"){ return "Alaska"; }  
         if ($state=="AL"){ return "Alabama"; }  
         if ($state=="AR"){ return "Arkansas"; }  
         if ($state=="AZ"){ return "Arizona"; }  
         if ($state=="CA"){ return "California"; }  
         if ($state=="CO"){ return "Colorado"; }  
         if ($state=="CT"){ return "Connecticut"; }  
         if ($state=="DC"){ return "District of Columbia"; }  
         if ($state=="DE"){ return "Delaware"; }  
         if ($state=="FL"){ return "Florida"; }  
         if ($state=="GA"){ return "Georgia"; }  
         if ($state=="HI"){ return "Hawaii"; }  
         if ($state=="IA"){ return "Iowa"; }  
         if ($state=="ID"){ return "Idaho"; }  
         if ($state=="IL"){ return "Illinois"; }  
         if ($state=="IN"){ return "Indiana"; }  
         if ($state=="KS"){ return "Kansas"; }  
         if ($state=="KY"){ return "Kentucky"; }  
         if ($state=="LA"){ return "Louisiana"; }  
           if ($state=="MA"){ return "Massachusetts"; }  
           if ($state=="MD"){ return "Maryland"; }  
           if ($state=="ME"){ return "Maine"; }  
           if ($state=="MI"){ return "Michigan"; }  
           if ($state=="MN"){ return "Minnesota"; }  
           if ($state=="MO"){ return "Missouri"; }  
           if ($state=="MS"){ return "Mississippi"; }  
           if ($state=="MT"){ return "Montana"; }  
           if ($state=="NC"){ return "North Carolina"; }  
           if ($state=="ND"){ return "North Dakota"; }  
           if ($state=="NE"){ return "Nebraska"; }  
           if ($state=="NH"){ return "New Hampshire"; }  
           if ($state=="NJ"){ return "New Jersey"; }  
           if ($state=="NM"){ return "New Mexico"; }  
           if ($state=="NV"){ return "Nevada"; }  
           if ($state=="NY"){ return "New York"; }  
           if ($state=="OH"){ return "Ohio"; }  
           if ($state=="OK"){ return "Oklahoma"; }  
           if ($state=="OR"){ return "Oregon"; }  
           if ($state=="PA"){ return "Pennsylvania"; }  
           if ($state=="RI"){ return "Rhode Island"; }  
           if ($state=="SC"){ return "South Carolina"; }  
           if ($state=="SD"){ return "South Dakota"; }  
           if ($state=="TN"){ return "Tennessee"; }  
           if ($state=="TX"){ return "Texas"; }  
           if ($state=="UT"){ return "Utah"; }  
           if ($state=="VA"){ return "Virginia"; }  
           if ($state=="VT"){ return "Vermont"; }  
           if ($state=="WA"){ return "Washington"; }  
           if ($state=="WI"){ return "Wisconsin"; }  
           if ($state=="WV"){ return "West Virginia"; }  
         if ($state=="WY"){ return "Wyoming"; }  
      
      }  

            function get_client_ip() {
                $ipaddress = '';
                if (getenv('HTTP_CLIENT_IP'))
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                else if(getenv('HTTP_X_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                else if(getenv('HTTP_X_FORWARDED'))
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                else if(getenv('HTTP_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                else if(getenv('HTTP_FORWARDED'))
                   $ipaddress = getenv('HTTP_FORWARDED');
                else if(getenv('REMOTE_ADDR'))
                    $ipaddress = getenv('REMOTE_ADDR');
                else
                    $ipaddress = 'UNKNOWN';
                return $ipaddress;
            }
            
            function getCity($ip) {
                  require_once('geoplugin.class.php');
             
                  $geo = new geoPlugin();
                  $geo->locate($ip); //Step 1. Locate the IP
                   
                  //Step 2. Get your desired information
                    return $geo->city;                    
            }
            
            function getState($ip) {
                  require_once('geoplugin.class.php');
             
                  $geo = new geoPlugin();
                  $geo->locate($ip); //Step 1. Locate the IP
                   
                  //Step 2. Get your desired information
                    return $geo->region;                    
            }
            
            function getAPIcallIP($ip)
            {
                  //Please note that the class needs to called
                  require_once('geoplugin.class.php');
             
                  $geo = new geoPlugin();
                  $geo->locate($ip); //Step 1. Locate the IP
                   
                  //Step 2. Get your desired information
                    $url = "http://api.dp.la/v2/items?sourceResource.spatial=".urlencode($geo->city)."&sourceResource.spatial.state=".urlencode(getStateNameByAbbreviation($geo->region))."&api_key=4085d1b6bab130d8aa14e4366a765cb1";
                    return $url;                
            }
            
            function getAPIcallAddress($address)
            {
                  //Please note that the class needs to called
                    $session = curl_init("https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=AIzaSyDw8IFA0Kb2GO76xZwcgz0tqMoTV_XKQX4"); 	               	       // Open the Curl session
                    curl_setopt($session, CURLOPT_HEADER, false); 	       // Don't return HTTP headers
                    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);   // Do return the contents of the call
                    $xml = curl_exec($session); 	                       // Make the call
                    curl_close($session); 				       // And close the session
                   
                    	$result = json_decode($xml);

                        $components = $result->results[0]->address_components;
                    foreach ($components as $val) {
                        if ($val->types[0] == 'locality') {
                           $city = $val->long_name;
                        }
                        if ($val->types[0] == 'administrative_area_level_1') {
                            $state = $val->long_name;
                        }
                        
                    }
                  //Step 2. Get your desired information
                    $url = "http://api.dp.la/v2/items?sourceResource.spatial=".urlencode($city)."&sourceResource.spatial.state=".urlencode($state)."&api_key=4085d1b6bab130d8aa14e4366a765cb1";
                    return $url;                
            }
            
            
            $ipadd = get_client_ip();
            if (isset($_GET['address'])) {
                $url = getAPIcallAddress($_GET['address']);               
            } else {
                $url = getAPIcallIP($ipadd);
            }        
        
        ?>
<form action="nearby.php" method="get">
    <input type="text" name="address" value="<?php
    
    if (isset($_GET['address'])) {
       echo $_GET['address'];
    } else {
       echo getCity($ipadd) . ", " . getState($ipadd);    
    }
    ?>" placeholder="Address or Zip Code" /> <input type="submit" value="go" />
</form>
        <?php
            
        echo $url;
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
		if ($count <= 100) {
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
	    echo '<div id="moreLink"><a href="http://dp.la/" target="_blank">Find More at the Digital Public Library of America</a>';
	} else {
	    // no results case
	    echo '<p><a href="http://dp.la" target="_blank"><img src="dpla_logo.jpg" style="margin:0 auto;max-width:120px;max-height:120px;"></a></p><p>The Digital Public Library of America (DPLA) brings together the riches of America\'s libraries, archives, and museums, and makes them freely available to the world. It strives to contain the full breadth of human expression, from the written word, to works of art and culture, to records of America\'s heritage, to the efforts and data of science.</p><p><strong><a href="http://dp.la" target="_blank">Visit the DPLA</a></strong></p>';
	}


        ?>
    </body>
</html>