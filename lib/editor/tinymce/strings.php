<?php

require_once('../../../config.php');

$contexturl = optional_param('context', null, PARAM_URL);
$isdialog = optional_param('dlg', false, PARAM_BOOL);

error_log($contexturl, 0);

$lang = substr(current_language(), 0, 2);
$output = '';

// get the keys from the reference english translations
$string = array();
include_once($CFG->dirroot .'/lang/en_utf8/tinymce.php');
$keys = array_keys($string);

if (!is_null($contexturl)) {
    $context = array_pop(explode('/tinymce/jscripts/tiny_mce/', $contexturl));
    $contexts = explode('/', $context);
    $moduletype = $contexts[0];
    $modulename = $contexts[1];

    $dialogpostfix = '';
    if ($modulename && $isdialog) {
        $dialogpostfix = '_dlg';
    }

    $selectedkeys = preg_grep('/^'. $moduletype .'\/'. $modulename . $dialogpostfix .':/', $keys);
   
    $output = "tinyMCE.addI18n('$lang". ($modulename ? '.'.$modulename:'') ."$dialogpostfix',{\n";
    foreach($selectedkeys as $key) {
        $output .= substr($key, strpos($key, ':')+1) .':"'. addslashes_js(get_string($key, 'tinymce')) ."\",\n";
    }
    $output .= "});";

} else {
    $output = "tinyMCE.addI18n({". $lang .":{";
    $selectedkeys = preg_grep('/^main\//', $keys);
    $currentsection = '';
    $firstiteration = true;
    foreach($selectedkeys as $key) {
        $subkey = explode(':', array_pop(explode('/', $key)));
        $section = $subkey[0];
        $string = $subkey[1];
        if ($section != $currentsection) {
            if ($firstiteration) {
                $firstiteration = false;
                $output .= "\n";
            } else {
                $output .= "},\n"; 
            }
            $currentsection = $section;
            $output .= $currentsection .":{\n";
        }
        $output .= $string .':"'. addslashes_js(get_string($key, 'tinymce')) ."\",\n";
    } 
    $output .= "}}});";
}

$lifetime = '86400';
@header('Content-type: text/javascript; charset=utf-8');
@header('Content-length: '.strlen($output));
@header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
@header('Cache-control: max-age='.$lifetime);
@header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
@header('Pragma: ');

echo $output;

?>
