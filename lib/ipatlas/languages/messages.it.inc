<?

/*

Italian Language File

I forgot who started translating this file, email me so i can put your 
email here.

please email new language files to ivan@xpenguin.com
copy a language file to messages.<cc>.inc and \
edit what $p is on encountering an english phrase.

you can start off with messages.blank.inc

please notice the punctuation and capitilization.

change the $language variable in plotconf.inc after
creating a language file.

*/

function lookup($phrase) {
  switch($phrase) {

    # start translating the $p here

     case "Plotting" :
     $p = "Mappa di"; break;

     case "preferences" :
     $p = "preferenze"; break;

     case "locate me" :
     $p = "localizzami"; break;

     case "IP/Hostname:" :
     $p = "IP/Nome host:"; break;

     case "You at" :
     $p = "Tu"; break;

     case "Submit" :
     $p = ""; break;

     case "main" :
     $p = ""; break;

# Used in context "(host) is located in"
     case "is" :
     $p = "è"; break;

# Used in context "You at (host) are located in"
     case "are" :
     $p = "sei"; break;

     case "located in" :
     $p = "stato localizzato in"; break;

     case "cannot be located." :
     $p = "non può essere localizzato."; break;

     case "cannot be resolved." :
     $p = "non può essere risolto."; break;

     case "is a host in the private IP address range." :
     $p = "è un host in un range di IP privati."; break;

     case "does not resolve." :
     $p = "irrisolvibile."; break;

     case "is not a valid IP address." :
     $p = "non è un indirizzo IP valido."; break;

     case "Temporary lookup failure." :
     $p = "Errore temporaneo di lookup."; break;

     case "IP-Atlas Preferences" :
     $p = "Preferenze IP-Atlas"; break;

# make the 'p' lowercase if possible in your language

     case "IP-Atlas preferences" :
     $p = "Preferenze IP-Atlas"; break;

     case "(cookie based)" :
     $p = "(basato su cookie)"; break;

     case "Draw mode (defaults guessed for your browser):" :
     $p = ""; break;

     // next two phrases are combined
     case "Your settings have been saved. You can now try" :
     $p = "Le tue impostazioni sono state salvate "; break;

     case "plotting something." :
     $p = "torna indietro."; break;

     case "Pointer Preferences (the dot that marks lat/lon):" :
     $p = "Preferenze puntatore (il simbolo che indica la lat/lon):"; break;

     case "Shape:" :
     $p = "Forma:"; break;

     case "Size:" :
     $p = "Dimensione:"; break;

     case "Color:" :
     $p = "Colore:"; break;

     case "Pointer:" :
     $p = "Puntatore:"; break;

# any help here?

     case "Other Preferences:" :
     $p = ""; break;

     case "Earth Image:" :
     $p = ""; break;

     case "Color" :
     $p = "Colore"; break;

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
  
  if($p == "") { $p = $phrase; }

  return $p;

}

?>
