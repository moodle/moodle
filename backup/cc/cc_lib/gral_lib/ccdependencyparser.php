<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once dirname(__FILE__) .'/../xmlbase.php';
require_once 'cssparser.php';
require_once 'pathutils.php';



/**
 *
 * Older version better suited for PHP < 5.2
 * @deprecated
 * @param unknown_type $url
 * @return boolean
 */
function is_url_deprecated($url) {
    if (
         !preg_match('#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url) &&
         !preg_match('#^https\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url) &&
         !preg_match('#^ftp\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url)
        ) {
        $status = false;
    } else {
        $status = true;
    }
    return $status;
}

/**
 *
 * validates URL
 * @param string $url
 * @return boolean
 */
function is_url($url) {
    $result = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== false;
    return $result;
}

function GetDepFiles($manifestroot, $fname,$folder,&$filenames) {
    $extension      = pathinfo($fname, PATHINFO_EXTENSION);
    $filenames      = array();
    $dcx            = new XMLGenericDocument();
    $result         = true;

    switch ($extension){
        case 'xml':
                 $result = @$dcx->loadXMLFile($manifestroot.$folder.$fname);
                 if (!$result) {
                    $result = @$dcx->loadXMLFile($manifestroot.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$fname);
                 }
                 GetDepFilesXML($manifestroot, $fname,$filenames,$dcx, $folder);
            break;
        case 'html':
        case 'htm':
                 $result = @$dcx->loadHTMLFile($manifestroot.$folder.$fname);
                 if (!$result) {
                    $result = @$dcx->loadHTMLFile($manifestroot.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$fname);
                 }
                 GetDepFilesHTML($manifestroot, $fname,$filenames,$dcx, $folder);
            break;
    }
    return $result;
}



function GetDepFilesXML ($manifestroot, $fname,&$filenames,&$dcx, $folder){
        $nlist = $dcx->nodeList("//img/@src | //attachments/attachment/@href  | //link/@href | //script/@src");
        $css_obj_array = array();
        foreach ($nlist as $nl) {
            $item = $folder.$nl->nodeValue;
            $path_parts = pathinfo($item);
            $fname = $path_parts['basename'];
            $ext   = array_key_exists('extension',$path_parts) ? $path_parts['extension'] : '';
            if (!is_url($nl->nodeValue)) {
              //$file =   $folder.$nl->nodeValue; // DEPENDERA SI SE QUIERE Q SEA RELATIVO O ABSOLUTO
              $file =   $nl->nodeValue;
              toNativePath($file);
              $filenames[]=$file;
            }
        }
        $dcx->registerNS('qti','http://www.imsglobal.org/xsd/imscc/ims_qtiasiv1p2.xsd');
        $dcx->resetXpath();
        $nlist = $dcx->nodeList("//qti:mattext | //text");
        $dcx2 = new XMLGenericDocument();
        foreach ($nlist as $nl) {
            if ($dcx2->loadString($nl->nodeValue)){
                GetDepFilesHTML($manifestroot,$fname,$filenames,$dcx2,$folder);
            }
        }
}



function GetDepFilesHTML ($manifestroot, $fname, &$filenames, &$dcx, $folder){
        $dcx->resetXpath();
        $nlist = $dcx->nodeList("//img/@src | //link/@href | //script/@src | //a[not(starts-with(@href,'#'))]/@href");
        $css_obj_array=array();
        foreach ($nlist as $nl) {
            $item = $folder.$nl->nodeValue;
            $path_parts = pathinfo($item);
            $fname = $path_parts['basename'];
            $ext   = array_key_exists('extension',$path_parts) ? $path_parts['extension'] : '';
            if (!is_url($folder.$nl->nodeValue) && !is_url($nl->nodeValue)) {
              $path = $folder.$nl->nodeValue;
              $file = fullPath($path,"/");
              toNativePath($file);
              if (file_exists($manifestroot.DIRECTORY_SEPARATOR.$file)) {
                  $filenames[]= $file;
              }
            }
            if ($ext == 'css') {
                $css = new cssparser();
                $css->Parse($dcx->filePath().$nl->nodeValue);
                $css_obj_array[$item]=$css;
            }
        }
        $nlist = $dcx->nodeList("//*/@class");
        foreach ($nlist as $nl) {
            $item = $folder.$nl->nodeValue;
            foreach ($css_obj_array as $csskey => $cssobj) {
                $bimg = $cssobj->Get($item,"background-image");
                $limg = $cssobj->Get($item,"list-style-image");
                $npath = pathinfo($csskey);
                if ((!empty($bimg))&& ($bimg != 'none')) {
                    $filenames[] = stripUrl($bimg,$npath['dirname'].'/');
                } else
                if ((!empty($limg))&& ($limg != 'none')) {
                    $filenames[] = stripUrl($limg,$npath['dirname'].'/');
                }
            }
        }
        $elems_to_check = array("body","p","ul","h4","a","th");
        $do_we_have_it = array();
        foreach ($elems_to_check as $elem) {
            $do_we_have_it[$elem]=($dcx->nodeList("//".$elem)->length > 0);
        }
        foreach ($elems_to_check as $elem) {
            if ($do_we_have_it[$elem]) {
                foreach ($css_obj_array as $csskey => $cssobj) {
                    $sb = $cssobj->Get($elem, "background-image");
                    $sbl = $cssobj->Get($elem,"list-style-image");
                    $npath = pathinfo($csskey);
                    if ((!empty($sb)) && ($sb != 'none')) {
                        $filenames[] = stripUrl($sb,$npath['dirname'].'/');
                    } else
                    if ((!empty($sbl)) && ($sbl != 'none')) {
                        $filenames[] = stripUrl($sbl,$npath['dirname'].'/');
                    }
                }
            }
        }
}