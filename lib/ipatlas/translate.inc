<?

function t($phrase) {
  global $language;
  if($language == "en") {
    return $phrase;
  } else {
  include_once("languages/messages.$language.inc");
  $phrase = lookup($phrase, $language);
  return $phrase;
  }
}

?>