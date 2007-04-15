<?php // $Id$
    include_once("../../../config.php");
    require_login();

if (empty($CFG->aspellpath)) {
    error('Spellchecker not configured');
}

header('Content-type: text/html; charset=utf-8');

// Speller pages script http://spellerpages.sourceforge.net/
// Modified by Marc Alier on August 2004 for the integration with moodle

$aspell_prog = escapeshellarg($CFG->aspellpath);
$spellercss = $CFG->wwwroot .'/lib/speller/spellerStyle.css';
$word_win_src = $CFG->wwwroot .'/lib/speller/wordWindow.js';


$textinputs = $_POST['textinputs']; // array

if(!($lang = check_language($aspell_prog))) {
    error_handler("No suitable dictionary found installed on your server!");
    exit;
}

$aspell_opts = '-a -H --lang='. $lang .' --encoding=utf-8';
if (!empty($CFG->aspellextradicts)) {   // eg /usr/bin/.aspell.en.pws
    $aspell_opts .= ' --add-extra-dicts='.$CFG->aspellextradicts;
}
$tempfiledir = $CFG->dataroot; // Use dataroot since it must be writable
$input_separator = 'A';

function check_language($cmd) {
/// return users current language if its
/// dictionary is found installed in system
/// and always return english if user's own
/// language is not in the list. If english dictionary
/// isn't found, then false is returned.

    global $CFG;

    clearstatcache();
    $current_lang = str_replace('_utf8', '', current_language());
    $output = '';

    if(!($handle = popen($cmd .' dump dicts', 'r'))) {
        error_handler("Couldn't create handle!");
        exit;
    }

    while(!feof($handle)) {
        $output .= fread($handle, 1024);
    }
    @pclose($handle);

    $dicts = explode(chr(10), strtolower($output));

    if(is_array($dicts)) {
        if(in_array($current_lang,$dicts)) {
            return $current_lang;
        }
    }

    if (!empty($CFG->editordictionary)) {
        return $CFG->editordictionary;
    }

    return false;

}

// set the JavaScript variable to the submitted text.
// textinputs is an array, each element corresponding to the (url-encoded)
// value of the text control submitted for spell-checking
function print_textinputs_var() {
    global $textinputs;
    foreach( $textinputs as $key=>$val ) {
        // $val = str_replace( "'", "%27", $val );
        echo "textinputs[$key] = decodeURIComponent(\"" . $val . "\");\n";
    }
}

// make declarations for the text input index
function print_textindex_decl( $text_input_idx ) {
    echo "words[$text_input_idx] = [];\n";
    echo "suggs[$text_input_idx] = [];\n";
}

// set an element of the JavaScript 'words' array to a misspelled word
function print_words_elem( $word, $index, $text_input_idx ) {
    echo "words[$text_input_idx][$index] = '" . escape_quote( $word ) . "';\n";
}


// set an element of the JavaScript 'suggs' array to a list of suggestions
function print_suggs_elem( $suggs, $index, $text_input_idx ) {
    echo "suggs[$text_input_idx][$index] = [";
    foreach( $suggs as $key=>$val ) {
        if( $val ) {
            echo "'" . escape_quote( $val ) . "'";
            if ( $key+1 < count( $suggs )) {
                echo ", ";
            }
        }
    }
    echo "];\n";
}

// escape single quote
function escape_quote( $str ) {
    return preg_replace ( "/'/", "\\'", $str );
}


// handle a server-side error.
function error_handler( $err ) {
    echo "error = '" . escape_quote( $err ) . "';\n";
}

// get the list of misspelled words. Put the results in the javascript words array
// for each misspelled word, get suggestions and put in the javascript suggs array
function print_checker_results() {

    global $aspell_prog;
    global $aspell_opts;
    global $tempfiledir;
    global $textinputs;
    global $input_separator;
    $aspell_err = "";
    // create temp file
    $tempfile = tempnam( $tempfiledir, 'aspell_data_' );

    // open temp file, add the submitted text.
    if( $fh = fopen( $tempfile, 'w' )) {
        for( $i = 0; $i < count( $textinputs ); $i++ ) {
            $text = urldecode( $textinputs[$i] );
            $lines = explode( "\n", $text );
            fwrite ( $fh, "%\n" ); // exit terse mode
            fwrite ( $fh, "^$input_separator\n" );
            fwrite ( $fh, "!\n" ); // enter terse mode
            foreach( $lines as $key=>$value ) {
                // use carat on each line to escape possible aspell commands
                fwrite( $fh, "^$value\n" );
            }
        }
        fclose( $fh );

        // exec aspell command - redirect STDERR to STDOUT
        $cmd = "$aspell_prog $aspell_opts < $tempfile 2>&1";
        if( $aspellret = shell_exec( $cmd )) {
            $linesout = explode( "\n", $aspellret );
            $index = 0;
            $text_input_index = -1;
            // parse each line of aspell return
            foreach( $linesout as $key=>$val ) {
                $chardesc = substr( $val, 0, 1 );
                // if '&', then not in dictionary but has suggestions
                // if '#', then not in dictionary and no suggestions
                // if '*', then it is a delimiter between text inputs
                // if '@' then version info
                if( $chardesc == '&' || $chardesc == '#' ) {
                    $line = explode( " ", $val, 5 );
                    print_words_elem( $line[1], $index, $text_input_index );
                    if( isset( $line[4] )) {
                        $suggs = explode( ", ", $line[4] );
                    } else {
                        $suggs = array();
                    }
                    print_suggs_elem( $suggs, $index, $text_input_index );
                    $index++;
                } elseif( $chardesc == '*' ) {
                    $text_input_index++;
                    print_textindex_decl( $text_input_index );
                    $index = 0;
                } elseif( $chardesc != '@' && $chardesc != "" ) {
                    // assume this is error output
                    $aspell_err .= $val;
                }
            }
            if( $aspell_err ) {
                $aspell_err = "Error executing `$cmd`\\n$aspell_err";
                error_handler( $aspell_err );
            }
        } else {
            error_handler( "System error: Aspell program execution failed (`$cmd`)" );
        }
    } else {
        error_handler( "System error: Could not open file '$tempfile' for writing" );
    }

    // close temp file, delete file
    unlink( $tempfile );
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $spellercss ?>" />
<script type="text/javascript" src="<?php echo $word_win_src ?>"></script>
<script type="text/javascript">
//<![CDATA[
var suggs = new Array();
var words = new Array();
var textinputs = new Array();
var error;
<?php

print_textinputs_var();

print_checker_results();

?>

var wordWindowObj = new wordWindow();
wordWindowObj.originalSpellings = words;
wordWindowObj.suggestions = suggs;
wordWindowObj.textInputs = textinputs;

function init_spell() {
    // check if any error occured during server-side processing
    if( error ) {
        alert( error );
    } else {
        // call the init_spell() function in the parent frameset
        if (parent.frames.length) {
            parent.init_spell( wordWindowObj );
        } else {
            alert('This page was loaded outside of a frameset. It might not display properly');
        }
    }
}

//]]>
</script>

</head>
<body onLoad="init_spell();">

<script type="text/javascript">
//<![CDATA[
wordWindowObj.writeBody();
//]]>
</script>

</body>
</html>
