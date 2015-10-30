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

/**
 * Librería de funciones básicas V1.0 (June, 16th 2009)
 *
 *
 * @author Daniel Mühlrad
 * @link daniel.muhlrad@uvcms.com
 * @version 1.0
 * @copyright 2009
 *
 */




/**
 * Make a Handler error with an exception msg error
 *
 * @param integer $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 */
function errorHandler($errno, $errstr, $errfile, $errline) {
    // si deseas podes guardarlos en un archivo
    ($errfile);($errline);
    throw new Exception($errstr, $errno);
}



/**
 * Return de mime-type of a file
 *
 * @param string $file
 * @param string $default_type
 *
 */
function file_mime_type ($file, $default_type = 'application/octet-stream'){
    $ftype = $default_type;
    $magic_path =   dirname(__FILE__)
                  . DIRECTORY_SEPARATOR
                  . '..'
                  . DIRECTORY_SEPARATOR
                  . 'magic'
                  . DIRECTORY_SEPARATOR
                  . 'magic';
    $finfo = @finfo_open(FILEINFO_MIME , $magic_path);
    if ($finfo !== false) {

        $fres = @finfo_file($finfo, $file);

        if ( is_string($fres) && !empty($fres) ) {
            $ftype = $fres;
        }
        @finfo_close($finfo);
    }
    return $ftype;
}




function array_remove_by_value($arr,$value) {
    return array_values(array_diff($arr,array($value)));

}


function array_remove_by_key($arr,$key) {
    return array_values(array_diff_key($arr,array($key)));

}


function cc_print_object($object) {
    echo '<pre>' . htmlspecialchars(print_r($object,true)) . '</pre>';
}



/**
 * IndexOf - first version of find an element in the Array given
 * returns the index of the *first* occurance
 * @param mixed $needle
 * @param array $haystack
 * @return mixed The element or false if the function didnt find it
 */

function indexOf($needle, $haystack) {
    for ($i = 0; $i < count($haystack) ; $i++) {
            if ($haystack[$i] == $needle) {
                return $i;
            }
    }
    return false;
}


/**
 * IndexOf2 - second version of find an element in the Array given
 *
 * @param mixed $needle
 * @param array $haystack
 * @return mixed The index of the element or false if the function didnt find it
 */

function indexOf2($needle, $haystack) {
    for($i = 0,$z = count($haystack); $i < $z; $i++){
        if ($haystack[$i] == $needle) {  //finds the needle
            return $i;
        }
    }
    return false;
}

