<?php  // $Id$

require_once($CFG->libdir.'/lexer.php');

// Constants for the various types of tokens

define("T_USER","0");
define("T_META","1");
define("T_EXACT","2");
define("T_NEGATE","3");
define("T_STRING","4");

// Class to hold token/value pairs after they're parsed.

class search_token {
  var $value;
  var $type;
  function search_token($type,$value){
    $this->type = $type;
    $this->value = $this->sanitize($value);
  
  }

  // Try to clean up user input to avoid potential security issues.
  // Need to think about this some more. 

  function sanitize($userstring){
    return htmlentities(addslashes($userstring));
  }
  function getValue(){  
    return $this->value;
  }
  function getType(){
    return $this->type;
  }
}



// This class does the heavy lifting of lexing the search string into tokens.
// Using a full-blown lexer is probably overkill for this application, but 
// might be useful for other tasks.

class search_lexer extends Lexer{

  function search_lexer($parser){

    // Call parent constructor.
    $this->Lexer($parser);

    //Set up the state machine and pattern matches for transitions.

    // Patterns to handle strings  of the form user:foo

    // If we see the string user: while in the base accept state, start
    // parsing a username and go to the inusername state.
    $this->addEntryPattern("user:\S+","accept","inusername");

    // Snarf everything into the username until we see whitespace, then exit
    // back to the base accept state.
    $this->addExitPattern("\s","inusername");

    // Patterns to handle strings  of the form meta:foo
 
   // If we see the string meta: while in the base accept state, start
    // parsing a username and go to the inmeta state.
    $this->addEntryPattern("subject:\S+","accept","inmeta");

    // Snarf everything into the meta token until we see whitespace, then exit
    // back to the base accept state.
    $this->addExitPattern("\s","inmeta");

   
    // Patterns to handle required exact match strings (+foo) .

    // If we see a + sign  while in the base accept state, start
    // parsing an exact match string and enter the inrequired state
    $this->addEntryPattern("\+\S+","accept","inrequired");
    // When we see white space, exit back to accept state.
    $this->addExitPattern("\s","inrequired");

    // Handle excluded strings (-foo)

   // If we see a - sign  while in the base accept state, start
    // parsing an excluded string and enter the inexcluded state
    $this->addEntryPattern("\-\S+","accept","inexcluded");
    // When we see white space, exit back to accept state.
    $this->addExitPattern("\s","inexcluded");


    // Patterns to handle quoted strings.

    // If we see a quote  while in the base accept state, start
    // parsing a quoted string and enter the inquotedstring state.
    // Grab everything until we see the closing quote.
  
    $this->addEntryPattern("\"[^\"]+","accept","inquotedstring");

    // When we see a closing quote, reenter the base accept state.
    $this->addExitPattern("\"","inquotedstring");
 
    // Patterns to handle ordinary, nonquoted words.
  
    // When we see non-whitespace, snarf everything into the nonquoted word
    // until we see whitespace again.
    $this->addEntryPattern("\S+","accept","plainstring");

    // Once we see whitespace, reenter the base accept state.
    $this->addExitPattern("\s","plainstring");
  
  }
} 




// This class takes care of sticking the proper token type/value pairs into
// the parsed token  array.
// Most functions in this class should only be called by the lexer, the
// one exception being getParseArray() which returns the result.

class search_parser {
  var $tokens;

 
 // This function is called by the code that's interested in the result of the parse operation.
  function get_parsed_array(){
    return $this->tokens;
  }

  /*
   * Functions below this are part of the state machine for the parse
   * operation and should not be called directly.
   */

  // Base state. No output emitted.
  function accept() {
    return true;
  }
  
 
  // State for handling user:foo constructs. Potentially emits a token.
  function inusername($content){
    if(strlen($content) < 6) // State exit or missing parameter.
      return true;
    // Strip off the user: part and add the reminder to the parsed token array
    $param = trim(substr($content,5));
    $this->tokens[] = new search_token(T_USER,$param);
    return true;
  }


  // State for handling meta:foo constructs. Potentially emits a token.
  function inmeta($content){   
    if(strlen($content) < 9) // Missing parameter.
      return true;
    // Strip off the meta: part and add the reminder to the parsed token array.
    $param = trim(substr($content,8));
    $this->tokens[] = new search_token(T_META,$param);
    return true;
  }

  
  // State entered when we've seen a required string (+foo). Potentially
  // emits a token.
  function inrequired($content){
    if(strlen($content) < 2) // State exit or missing parameter, don't emit.
      return true;
    // Strip off the + sign and add the reminder to the parsed token array.
    $this->tokens[] = new search_token(T_EXACT,substr($content,1));
    return true;
    } 
 
  // State entered when we've seen an excluded string (-foo). Potentially 
  // emits a token.
  function inexcluded($content){
    if(strlen($content) < 2) // State exit or missing parameter.
      return true;
    // Strip off the -sign and add the reminder to the parsed token array.
    $this->tokens[] = new search_token(T_NEGATE,substr($content,1));
    return true;
  } 


  // State entered when we've seen a quoted string. Potentially emits a token.
  function inquotedstring($content){
    if(strlen($content) < 2) // State exit or missing parameter.
      return true;
    // Strip off the opening quote and add the reminder to the parsed token array.
    $this->tokens[] = new search_token(T_STRING,substr($content,1));
    return true;
  } 

  // State entered when we've seen an ordinary, non-quoted word. Potentially
  // emits a token.
  function plainstring($content){
    if(ctype_space($content)) // State exit
      return true;
    // Add the string to the parsed token array.
    $this->tokens[] = new search_token(T_STRING,$content);
    return true;
  } 



}


// Primitive function to generate a SQL string from a parse tree. 
// Parameters: 
//
// $parsetree should be a parse tree generated by a 
// search_lexer/search_parser combination. 
// Other fields are database table names to search.

function search_generate_SQL($parsetree,$datafield,$metafield,$mainidfield,$useridfield,$userfirstnamefield,$userlastnamefield){
  global $CFG;
 if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

  $ntokens = count($parsetree);
  if($ntokens == 0)
    return "";

  for($i = 0; $i < $ntokens; $i++){
    if($i > 0) // We have more than one clause, need to tack on AND
      $SQLString .= " AND ";
     
    $type = $parsetree[$i]->getType();
    $value = $parsetree[$i]->getValue();
    switch($type){
    case T_STRING  : $SQLString .= "(($datafield $LIKE '%$value%') OR ($metafield $LIKE '%$value%') )";
      break;
    case T_EXACT: 
      $SQLString .= "(($datafield $REGEXP '[[:<:]]".$value."[[:>:]]') OR ($metafield $REGEXP '[[:<:]]".$value."[[:>:]]'))";
      break; 
    case T_META  : if($metafield != "")
      $SQLString .= "($metafield $LIKE '%$value%')";
      break;
    case T_USER  : $SQLString .= "(($mainidfield = $useridfield) AND (($userfirstnamefield $LIKE '%$value%') OR ($userlastnamefield $LIKE '%$value%')))";
      break; 
    case T_NEGATE: $SQLString .= "(NOT (($datafield  $LIKE '%$value%') OR ($metafield  $LIKE '%$value%')))";
      break; 
    default:
      return "";
	
    } 
  } 
  return $SQLString;
}


?>
