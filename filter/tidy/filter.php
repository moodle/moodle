<?php
      
// This function looks for text including markup and
// applies tidy's repair function to it.
// Tidy is a HTML clean and
// repair utility, which is currently available for PHP 4.3.x and PHP 5 as a
// PECL extension from http://pecl.php.net/package/tidy, in PHP 5 you need only
// to compile using the --with-tidy option.
// If you don't have the tidy extension installed or don't know, you can enable  
// or disable this filter, it just won't have any effect.
// If you want to know what you can set in $tidyoptions and what their default
// values are, see http://php.net/manual/en/function.tidy-get-config.php.
      
/**
* @author Hannes Gassert <hannes at mediagonal dot ch>
* @param        int            course id
* @param        string         text to be filtered
*/
function tidy_filter($courseid, $text) {
       
/// Configuration for tidy. Feel free to tune for your needs, e.g. to allow
/// proprietary markup.
    $tidyoptions = array(             
             'output-xhtml' => true,
             'show-body-only' => true,
             'tidy-mark' => false,
             'drop-proprietary-attributes' => true,
             'drop-font-tags' => true,
             'drop-empty-paras' => true,
             'indent' => true,
             'quiet' => true,
    );
    
/// Do a quick check using strpos to avoid unnecessary work
    if (strpos($text, '<') === false) {
        return $text;
    }

    
/// If enabled: run tidy over the entire string
    if (function_exists('tidy_repair_string')){
        $text = tidy_repair_string($text, $tidyoptions, 'utf8');
    }

    return $text;
}
?>
