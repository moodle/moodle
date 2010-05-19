<?php 
/**
 * AJAX checks for zlib.output_compression
 * 
 * @package Minify
 */

$_oc = ini_get('zlib.output_compression');
 
// allow access only if builder is enabled
require dirname(__FILE__) . '/../config.php';
if (! $min_enableBuilder) {
    header('Location: /');
    exit();
}

if (isset($_GET['hello'])) {
    // echo 'World!'
    
    // try to prevent double encoding (may not have an effect)
    ini_set('zlib.output_compression', '0');
    
    require $min_libPath . '/HTTP/Encoder.php';
    HTTP_Encoder::$encodeToIe6  = true; // just in case
    $he = new HTTP_Encoder(array(
        'content' => 'World!'
        ,'method' => 'deflate'
    ));
    $he->encode();
    $he->sendAll();

} else {
    // echo status "0" or "1"
    header('Content-Type: text/plain');
    echo (int)$_oc;
}
