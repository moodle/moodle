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
 * Function expands all relative parts of supplied path string thus
 * removing things like ../../ or ./../.
 *
 * @param string $path
 * @param string $dirsep Character that represents directory separator should be
 *                       specified here. Default is DIRECTORY_SEPARATOR.
 * @return string
 */
function fullPath($path,$dirsep=DIRECTORY_SEPARATOR) {
    $token = '$IMS-CC-FILEBASE$';
    $path = str_replace($token,'',$path);
    if ( is_string($path) && ($path != '') ) {
        $sep   = $dirsep;
        $dotDir= '.';
        $upDir = '..';
        $length= strlen($path);
        $rtemp= trim($path);
        $start = strrpos($path, $sep);
        $canContinue = ($start !== false);
        $result= $canContinue ? '': $path;
        $rcount=0;
        while ($canContinue) {
            $dirPart = ($start !== false) ? substr($rtemp,$start+1,$length-$start) : $rtemp;
            $canContinue = ($dirPart !== false);
            if ($canContinue) {
                if ($dirPart != $dotDir) {
                    if ($dirPart == $upDir) {
                        $rcount++;
                    } else {
                        if ($rcount > 0) {
                            $rcount--;
                        } else {
                            $result = ($result == '') ? $dirPart : $dirPart.$sep.$result;
                        }
                    }
                }
                $rtemp = substr($path,0,$start);
                $start = strrpos($rtemp, $sep);
                $canContinue = (($start !== false) || (strlen($rtemp) > 0));
            }
        } //end while
    }
    return $result;
}



/**
 * Function strips url part from css link
 *
 * @param string $path
 * @param string $rootDir
 * @return string
 */
function stripUrl($path, $rootDir='') {
    $result = $path;
    if ( is_string($path) && ($path != '') ) {
        $start=strpos($path,'(')+1;
        $length=strpos($path,')')-$start;
        $rut = $rootDir.substr($path,$start,$length);
        $result=fullPath($rut,'/');
    }
    return $result;
}

/**
 * Converts direcotry separator in given path to / to validate in CC
 * Value is passed byref hence variable itself is changed
 *
 * @param string $path
 */
function toNativePath(&$path) {
    for ($count = 0 ; $count < strlen($path); ++$count) {
        $chr = $path{$count};
        if (($chr == '\\') || ($chr == '/')) {
            $path{$count} = '/';
        }
    }
}


/**
 * Converts direcotry separator in given path to the one on the server platform
 * Value is passed byref hence variable itself is changed
 *
 * @param string $path
 */
function toNativePath2(&$path) {
    for ($count = 0 ; $count < strlen($path); ++$count) {
        $chr = $path{$count};
        if (($chr == '\\') || ($chr == '/')) {
            $path{$count} = DIRECTORY_SEPARATOR;
        }
    }
}

/**
 * Converts \ Directory separator to the / more suitable for URL
 *
 * @param string $path
 */
function toUrlPath(&$path) {
    for ($count = 0 ; $count < strlen($path); ++$count) {
        $chr = $path{$count};
        if (($chr == '\\')) {
            $path{$count} = '/';
        }
    }
}

/**
 * Returns relative path from two directories with full path
 *
 * @param string $path1
 * @param string $path2
 * @return string
 */
function pathDiff($path1, $path2) {
    toUrlPath($path1);
    toUrlPath($path2);
    $result = "";
    $bl2 = strlen($path2);
    $a = strpos($path1,$path2);
    if ($a !== false) {
        $result = trim(substr($path1,$bl2+$a),'/');
    }
    return $result;
}

 /**
  * Copy a file, or recursively copy a folder and its contents
  *
  * @author      Aidan Lister <aidan@php.net>
  * @version     1.0.1
  * @link        http://aidanlister.com/repos/v/function.copyr.php
  * @param       string   $source    Source path
  * @param       string   $dest      Destination path
  * @return      bool     Returns TRUE on success, FALSE on failure
  */
 function copyr($source, $dest)
 {
     global $CFG;
     // Simple copy for a file
     if (is_file($source)) {
         return copy($source, $dest);
     }

     // Make destination directory
     if (!is_dir($dest)) {
         mkdir($dest, $CFG->directorypermissions, true);
     }

     // Loop through the folder
     $dir = dir($source);
     while (false !== $entry = $dir->read()) {
         // Skip pointers
         if ($entry == '.' || $entry == '..') {
             continue;
         }

         // Deep copy directories
         if ($dest !== "$source/$entry") {
             copyr("$source/$entry", "$dest/$entry");
         }
     }

     // Clean up
     $dir->close();
     return true;
 }

/**
 * Function returns array with directories contained in folder (only first level)
 *
 * @param  string $rootDir  directory to look into
 * @param  string $contains which string to look for
 * @param  array  $excludeitems array of names to be excluded
 * @param  bool   $startswith should the $contains value be searched only from
 *                             beginning
 * @return array  Returns array of sub-directories. In case $rootDir path is
 *                invalid it returns FALSE.
 */
function getDirectories($rootDir, $contains, $excludeitems = null, $startswith = true) {
    $result = is_dir($rootDir);
    if ($result) {
        $dirlist = dir($rootDir);
        $entry = null;
        $result = array();
        while(false !== ($entry = $dirlist->read())) {
            $currdir = $rootDir.$entry;
            if (is_dir($currdir)) {
                $bret = strpos($entry,$contains);
                if (($bret !== false)) {
                    if (($startswith && ($bret == 0)) || !$startswith) {
                        if (!( is_array($excludeitems) && in_array($entry,$excludeitems) )) {
                            $result[] = $entry;
                        }
                    }
                }
            }
        }
    }
    return $result;
}

function getFilesOnly($rootDir, $contains, $excludeitems = null, $startswith = true,$extension=null) {
    $result = is_dir($rootDir);
    if ($result) {
        $filelist = dir($rootDir);
        $entry = null;
        $result = array();
        while(false !== ($entry = $filelist->read())) {
            $curritem = $rootDir.$entry;
            $pinfo = pathinfo($entry);
            $ext = array_key_exists('extension',$pinfo) ? $pinfo['extension'] : null;
            if (is_file($curritem) && (is_null($extension) || ($ext == $extension) )) {
                $bret = strpos($entry,$contains);
                if (($bret !== false)) {
                    if (($startswith && ($bret == 0)) || !$startswith) {
                        if (!( is_array($excludeitems) && in_array($entry,$excludeitems) )) {
                            $result[] = $entry;
                        }
                    }
                }
            }
        }
    }
    natcasesort($result);
    return $result;
}



/**
 * Search an identifier in array
 *
 * @param array $array
 * @param string $name
 *
 */

function search_ident_by_name($array,$name){
    if (empty($array)){
        throw new Exception('The array given is null');
    }
    $ident = null;
    foreach ($array as $k => $v){
        ($k);
        if ($v[1] == $name){
            $ident = $v[0];
            break;
        }
    }
    return $ident;
}





/**
 * Function returns files recursivly with appeneded relative path
 *
 * @param string $startDir
 * @param string $rootDir
 * @param array $excludedirs
 * @param array $excludefileext
 * @return array
 */
function getRawFiles($startDir, &$fhandle, $rootDir='', $excludedirs = null, $excludefileext = null) {
    $result = is_dir($startDir);
    if ($result) {
        $dirlist = dir($startDir);
        $entry = null;
        while(false !== ($entry = $dirlist->read())) {
            $curritem = $startDir.$entry;
            if (($entry=='.') || ($entry =='..')) {
                continue;
            }
            if (is_dir($curritem)) {
                if (!( is_array($excludedirs) && in_array($entry,$excludedirs) )) {
                    getRawFiles($startDir.$entry."/",$fhandle,$rootDir.$entry."/",$excludedirs,$excludefileext);
                }
                continue;
            }
            if (is_file($curritem)){
                $pinfo = pathinfo($entry);
                $ext = array_key_exists('extension',$pinfo) ? $pinfo['extension'] : '';
                if (!is_array($excludefileext) ||
                (is_array($excludefileext) && !in_array($ext,$excludefileext))) {
                    fwrite($fhandle,$rootDir.$entry."\n");
                }
            }
        }
    }
    return $result;
}


function getRawFiles2($startDir,&$arr, $rootDir='', $excludedirs = null, $excludefileext = null) {

    $result = is_dir($startDir);
    if ($result) {
        $dirlist = dir($startDir);
        $entry = null;
        while(false !== ($entry = $dirlist->read())) {
            $curritem = $startDir.$entry;
            if (($entry=='.') || ($entry =='..')) {
                continue;
            }
            if (is_dir($curritem)) {
                if (!( is_array($excludedirs) && in_array($entry,$excludedirs) )) {
                    getRawFiles2($startDir.$entry."/",$arr,$rootDir.$entry."/",$excludedirs,$excludefileext);
                }
                continue;
            }
            if (is_file($curritem)){
                $pinfo = pathinfo($entry);
                $ext = array_key_exists('extension',$pinfo) ? $pinfo['extension'] : '';
                if (!is_array($excludefileext) ||
                (is_array($excludefileext) && !in_array($ext,$excludefileext))) {
                    array_push($arr,$rootDir.$entry);
                   // fwrite($fhandle,$rootDir.$entry."\n");
                }
            }
        }
    }
    return $result;
}


function GetFiles($startDir, $outfile, $rootDir='', $excludedirs = null, $excludefileext = null) {
    $fh = @fopen($outfile,"w+");
    if ($fh !== FALSE) {
        getRawFiles($startDir,$fh,$rootDir,$excludedirs,$excludefileext);
        @fclose($fh);
        @chmod($outfile,0777);
    }
}


/**
 * Function to get an array with all files in a directory and subdirectories
 *
 * @param string $startDir
 * @param string $rootDir
 * @param string $excludedirs
 * @param string $excludefileext
 * @return array
 */

function GetFilesArray($startDir, $rootDir='', $excludedirs = null, $excludefileext = null) {
    $arr = array();
    getRawFiles2($startDir,$arr,$rootDir,$excludedirs,$excludefileext);
    return $arr;
}



/**
 * Function returns array with directories contained in folder (only first level)
 * simmilar to getDirectories but returned items are naturally sorted.
 *
 * @param string $rootDir
 * @param string $contains
 * @param array $excludeitems
 * @param bool $startswith
 * @return array
 */
function getCourseDirs ($rootDir, $contains, $excludeitems=null, $startswith=true) {
    $result = getDirectories($rootDir,$contains,$excludeitems,$startswith);
    if ($result !== false) {
        natcasesort($result);
        $result = array_values($result);
    }
    return $result;
}


/**
 * Delete a directory recursive with files inside
 *
 * @param string $dirname
 * @return bool
 */
function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
    }
    $dir->close();
    return rmdir($dirname);
}
