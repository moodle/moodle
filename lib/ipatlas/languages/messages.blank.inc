<?

/*

Blank Language File

copy this language file from messages.blank.inc to
messages.<country code>.inc.

please email new language files to ivan@xpenguin.com
copy a language file to messages.<cc>.inc and \
edit what $p is on encountering an english phrase.

please notice the punctuation and capitilization.

change the $language variable in plotconf.inc after
creating a language file.

*/

function lookup($phrase) {
  switch($phrase) {

    # start translating the $p here

     case "Plotting" :
     $p = ""; break;

     case "preferences" :
     $p = ""; break;

     case "locate me" :
     $p = ""; break;

     case "IP/Hostname:" :
     $p = ""; break;

     case "You at" :
     $p = ""; break;

     case "Submit" :
     $p = ""; break;

     case "main" :
     $p = ""; break;

# Used in context "(host) is located in"
     case "is" :
     $p = ""; break;

# Used in context "You at (host) are located in"
     case "are" :
     $p = ""; break;

     case "located in" :
     $p = ""; break;

     case "cannot be located." :
     $p = ""; break;

     case "cannot be resolved." :
     $p = ""; break;

     case "is a host in the private IP address range." :
     $p = ""; break;

     case "does not resolve." :
     $p = ""; break;

     case "is not a valid IP address." :
     $p = ""; break;

     case "Temporary lookup failure." :
     $p = ""; break;

     case "IP-Atlas Preferences" :
     $p = ""; break;

# make the 'p' lowercase if possible in your language

     case "IP-Atlas preferences" :
     $p = ""; break;

     case "(cookie based)" :
     $p = ""; break;

     case "Draw mode (defaults guessed for your browser):" :
     $p = ""; break;

     // next two phrases are combined
     case "Your settings have been saved. You can now try" :
     $p = ""; break;

     case "plotting something." :
     $p = ""; break;

     case "Pointer Preferences (the dot that marks lat/lon):" :
     $p = ""; break;

     case "Shape:" :
     $p = ""; break;

     case "Size:" :
     $p = ""; break;

     case "Color:" :
     $p = ""; break;

     case "Pointer:" :
     $p = ""; break;

     case "Other Preferences:" :
     $p = ""; break;

     case "Earth Image:" :
     $p = ""; break;

     case "Color" :
     $p = ""; break;

     case "Grayscale" :
     $p = ""; break;

     case "Save" :
     $p = ""; break;

# shape names:
     case "Diamond" :
     $p = ""; break;

     case "Diamond Outline" :
     $p = ""; break;

     case "Square" :
     $p = ""; break;

     case "Square Outline" :
     $p = ""; break;

     case "Cross" :
     $p = ""; break;

# colors:

     case "red" :
     $p = ""; break;

     case "white" :
     $p = ""; break;

     case "yellow" :
     $p = ""; break;

     case "magenta" :
     $p = ""; break;

     case "cyan" :
     $p = ""; break;

     case "green" :
     $p = ""; break;

     case "violet" :
     $p = ""; break;

     # stop here

     // return in english if the phrase couldn't be found
     default:
     $p = $phrase; break;


  }

  // return english if match was blank
  if($p == "") { $p = $phrase; }

  return $p;

}

?>
