<?php
// This file adds xslt_xxx emulation functions.
// It is intended for systems, e.g. those running PHP 5, where:
// 1) The XSLT library is not installed.
// 2) The XSL library is installed.
//
// Note that not everything is implemented.
// In particular, only the bare minimum to support the BB conversion is here.

// This silliness is required to prevent PHP from evaluating the function() blocks before processing the return;s
if(true) {


if(function_exists('xslt_create')) return;     // xslt_create() already exists, so emulation isn't needed.
if(!class_exists('XSLTProcessor')) return;     // There is no XSLTProcessor class, so emulation isn't possible.
if(!class_exists('DOMDocument')) return;       // There is no DOMDocument class, so emulation isn't possible.



function xslt_create() {
       return new XSLTProcessor();
}

// We don't support arguments or parameters because the Bb import doesn't use them
function xslt_process($proc, $xmlfile, $xslfile, $resultfile = null, $unsupported_args = null, $unsupported_params = null) {
       $doc = new DOMDocument;
       $doc->load($xmlfile);
       $xsl = new DOMDocument;
       $xsl->load($xslfile);
       $proc->importStylesheet($xsl);

       // Squash warnings here because xsl complains about COURSE_ACCESS tags which really are invalid XML (multiple root elements)
       if($resultfile !== null) {
               $fp = fopen($resultfile, 'w');
               fwrite($fp, @$proc->transformToXML($doc));
               fclose($fp);
               return true;
       } else {
               return @$proc->transformToXML($doc);
       }
}

} // end if(true)
