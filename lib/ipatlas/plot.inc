<?

$version = "1.0";

// check for bad agents immidietly
if($blockbadagents == 1) {
  // those metaquery assholes at t-dialin and others can't
  // get another dumber using the default user-agent, can they?

  $agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];

  if(
     strstr("libwww-perl", $agent) ||
     strstr("lwp-trivial", $agent) ||
     strstr("LWP::Simple", $agent) ||
     strstr("PHP/", $agent)) {

    // goodbye
    exit;

  }

}

function s10_rfc_1918_ip($in) {
      
      if (ereg('^(127\.)', $in, $part)) {
	return TRUE;
      }

      if (ereg('^(10\.)', $in, $part)) {
	return TRUE;
      }
      
      if (ereg('^(192\.168\.)', $in, $part)) {
      return TRUE;
      }

      if (ereg('^(172\.)' . '([0-9]{1,3})', $in, $part)) {
	  if(($part[2] > 15) and ($part[2] < 32)) {
	       return TRUE;
	  }
      }

   return FALSE;

}

function stuffanalyze($stuff) {

$stuff2[0] = array_values(preg_grep ("/CITY:/", $stuff)) or die("Sorry, but the lookup for this IP address failed! (CITY)");
$stuff2[1] = array_values(preg_grep ("/STATE:/", $stuff)) or die("Sorry, but the lookup for this IP address failed! (STATE)");
$stuff2[2] = array_values(preg_grep ("/COUNTRY:/", $stuff)) or die("Sorry, but the lookup for this IP address failed! (COUNTRY)");
$stuff2[3] = array_values(preg_grep ("/LAT:/", $stuff)) or die("Sorry, but the lookup for this IP address failed! (LAT)");
$stuff2[4] = array_values(preg_grep ("/LONG:/", $stuff)) or die("Sorry, but the lookup for this IP address failed! (LONG)");

// all the stuff2 values are actually arrays, making the following code look like crap

// the power of $count

 if(isset($stuff2[0][0])) {

for ($count = 0; $count < count($stuff2); $count++) {
  $stuff2[$count] = $stuff2[$count][0];
  $stuff2[$count] = ucwords(strtolower(trim(substr(substr($stuff2[$count], 15), 0, -5))));
}

 } else {

   // if no data was found 
   $stuff2 = array("bad", "bad", "bad", "bad", "bad");

 }

$count = 0;

 return $stuff2;

}

function getlatdata($ip) {

global $firewall_host;
global $firewall_port;
global $use_firewall;


  $data = array("");

if ($use_firewall) {

     $fp = fsockopen ($firewall_host, $firewall_port, $errno, $errstr, 30) or die("Could not open socket to proxy");
     if (!$fp) {
       echo "$errstr ($errno)<br>\n";
     } else {
       fputs ($fp, "GET http://netgeo.caida.org/perl/netgeo.cgi?target=$ip HTTP/1.0\r\nHost: netgeo.caida.org\r\n\r\n") or die("Could not write to socket");
       while (!feof($fp)) {
           $packet = fgets ($fp,128) or die("Could not read from socket");
           array_push($data, $packet) or die("Could not push data into array");
       }
       fclose ($fp);
     }

} else {

$fp = fsockopen ("netgeo.caida.org", 80, $errno, $errstr, 30) or die("Could not open socket to caida.org");
if (!$fp) {
    echo "$errstr ($errno)<br>\n";
} else {
    fputs ($fp, "GET /perl/netgeo.cgi?target=$ip HTTP/1.0\r\nHost: netgeo.caida.org\r\n\r\n") or die("Could not write to socket");
    while (!feof($fp)) {
        $packet = fgets ($fp,128) or die("Could not read from socket");
	array_push($data, $packet) or die("Could not push socket data into array");
    }
    fclose ($fp);
}

}

 // make it an array

return $data;

}

function finddot($name, $cssdots, $defaultdot) {
    foreach($cssdots as $x) {
        list($filename, , $width, $height) = split(":", $x);
	if($filename == $name) { $found = 1; $return = array($name, $width, $height); }
	}
if(isset($found)) {
return $return;
} else {
$dott = $cssdots[$defaultdot];
list($dott, , $width, $height) = split(":", $dott);
return array($dott, $width, $height);
}
}

function getlocationcoords($lat, $lon, $width, $height) {
	// some cartographers weren't mathematicians, up is apparently negative to them
    	$lat = ($lat * -1);

	$lat = ($lat + 90);
	$lon = ($lon + 180);
	$x = ($lon * ($width / 360));
	$y = ($lat * ($height / 180));

	$x = round($x);
	$y = round($y);
	return array($x, $y);
}

function getimagecoords($earthimages, $image) {
    foreach($earthimages as $x) {
	list($file, , $width, $height) = split(":", $x);
 	if($file == $image) {
	    $coords = array("$width", "$height");
	    return $coords;
	}
    }
}

function validcookie($cookie) {
    if(preg_match("/.*.:.*.:.*.:.*.:.*./", $cookie)) {
       return TRUE;
    } else {
       return FALSE;
    }
}

function isvalidimage($cookie, $earthimages, $defaultimage) {
#   list(, , , $setearthimage) = split(":", $cookie);
    $setearthimage = $cookie;
       if(isset($setearthimage)) {
           // check if the image is one actually defined, not a cookie edit
	   foreach($earthimages as $image) {
	       list($testearthimage, , , ) = split(":", $image);
	       if($testearthimage == $setearthimage) {
	           $found = 1;
	       }
	   }
	   if(isset($found)) {
               return $setearthimage;
	   } else {
	       return $earthimages[$defaultimage];
	   }
       } else {
           return $earthimages[$defaultimage];
       }
}



function istheregd() {
  global $trygd;
  if($trygd == 1) {
        if (@ImageTypes() & IMG_PNG) {
  	 	 return TRUE;
	} else {
   		 return FALSE;
	}
  } else {
                 return FALSE;
  }
}

function checkbrowser($agent) {
	if (strstr($agent, "Mozilla/4.7") || strstr($agent, "Opera 6") || strstr($agent, "Opera/6")) {
  	 	 return FALSE;
	} else {
   		 return TRUE;
	}
}

function shouldrun($agent) {

  // could cause probs... dunno
  global $HTTP_COOKIE_VARS;

  // check for new format of cookie with 6 parameters
  if(@preg_match("/.*.:.*.:.*.:.*.:.*.:.*./", $HTTP_COOKIE_VARS["atlasprefs"])) {
    list( , , , , , $drawmode) = split(":", $HTTP_COOKIE_VARS["atlasprefs"]);
  } else {
    $drawmode = "";
  }

  // don't try to understand below, it figures out whether
  // to run it in css or gd based of prefs, server ability, and user agent.

 if(istheregd() && ($drawmode == "1")) {
   return TRUE;
 } elseif($drawmode == "0") {
   return FALSE;
 } elseif(checkbrowser($agent) && istheregd()) {
   return TRUE;
 } elseif(!checkbrowser($agent)) {
   return FALSE;
 } elseif (!istheregd()) {
   return FALSE;
 }


}

function s10_validate_ip($in) {
  if (is_string($in) && ereg('^([0-9]{1,3})\.([0-9]{1,3})\.' .
                             '([0-9]{1,3})\.([0-9]{1,3})$' ,
                            $in, $part)) {
     if ($part[1] <= 255 && $part[2] <= 255 &&
         $part[3] <= 255 && $part[4] <= 255)
         return TRUE;
  }
  return FALSE;
 }

function getstuff($address, $local) {

  $address = trim($address);

# some people still think that urls are hostnames
$address = str_replace("http://", "", $address);
$address = preg_replace("/\/.*$/", "", $address);

// Security checks
$address = escapeshellcmd($address);

$values = array();

$values["address"] = $address;

if(eregi("[a-z]", $address)){
$ipaddress = gethostbyname($address);

 if($ipaddress == $address) {

$values["validity"] = "no";

 }

$values["hostname"] = $address;

$values["ishost"] = "yes";

} else {

if(s10_validate_ip($address)) {

$ipaddress = $address;
 $values["hostname"] = "";
} else {
$values["validity"] = "no";
}

}

 if(!isset($values["validity"])) {
   if(s10_rfc_1918_ip($ipaddress)) { 
     $private = "yes";
   } else {
     $private = "no";
   }
 } else {
   $private = "no";
 }

 if(!isset($ipaddress)) { $ipaddress = ""; }

$values["ipaddress"] = $ipaddress;

if(!isset($values["validity"]) && ($private == "no")) {

$stuff = getlatdata($ipaddress);

list(

     $values["city"],
     $values["state"],
     $values["country"],
     $values["lat"],
     $values["lon"]

) = stuffanalyze($stuff);

$desc = "";

// check if it is the user's ip address
if($local == 1) { $desc .= t("You at"); }

// add the ip address and hostname
$desc .= "<b>$values[hostname]</b> (<b>$values[ipaddress]</b>) ";

// use "are" if it is the user's ip address
if($local == 1) { $desc .= t("are"); } else { $desc .= t("is"); }

$desc .= " ".t("located in")." ";

// add the city if it's there
if($values["city"]) {
$desc .= "$values[city], ";
}

// add the state if its there
if($values["state"]) {
$desc .= "$values[state], ";
}

if($values["country"]) {
// make the country code capital so its ready for lookup
$values["country"] = strtoupper($values["country"]);
// convert the country code to a country name
$countries = file("countries.txt") or die("Could not open countries file");
$precountry = array_values(preg_grep("/$values[country]  /", $countries));
$values["country"] = trim(substr($precountry[0], 4));
$desc .= "$values[country].";
}

 $desc .= " <font color=\"#aaaaaa\">($values[lat], $values[lon])</font>";

 $state = $values["state"];
 $city = $values["city"];

// decide if address can't resolve, be located, or if it's fine
if($values["lat"] == "0.00" && $values["lon"] == "0.00" && (@($state != "bad" && $city != "bad"))) {
  $values["desc"] = "<b>$values[address]</b> ".t("cannot be located.");
$values["lat"] = "";
$values["lon"] = "";
} elseif($values["lat"] == "" && $values["lon"] == "") {
  $values["desc"] = "<b>$values[address]</b> ".t("cannot be located.");
} else {
$values["desc"] = $desc;
}

} else {

// check if it was a host before and decide on an error message

if($private == "yes") {
  $values["desc"] = "<b>$address</b> ".t("is a host in the private IP address range.");
} elseif(@($values["state"] == "bad" && $values["city"] == "bad")) {
$values["desc"] = t("Temporary lookup failure.");
} elseif(isset($values["ishost"])) {
$values["desc"] = "<b>$address</b> ".t("does not resolve.");
} else {
$values["desc"] = "<b>$address</b> ".t("is not a valid IP address.");
}

// some blank lat/lon for the image script or it will plot us in the center
$values["lon"] = "";
$values["lat"] = "";

}

return($values);
}

?>
