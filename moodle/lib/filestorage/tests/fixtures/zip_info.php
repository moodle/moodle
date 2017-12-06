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
 * This debug script is used during zip support development ONLY.
 *
 * @package    core_files
 * @copyright  2012 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->libdir/filestorage/zip_packer.php");

if (count($_SERVER['argv']) != 2 or !file_exists($_SERVER['argv'][1])) {
    cli_error("This script expects zip file name as the only parameter");
}

$archive = $_SERVER['argv'][1];

// Note: the ZIP structure is described at http://www.pkware.com/documents/casestudies/APPNOTE.TXT
if (!$filesize = filesize($archive) or !$fp = fopen($archive, 'rb+')) {
    cli_error("Can not open ZIP archive: $archive");
}

if ($filesize == 22) {
    $info = unpack('Vsig', fread($fp, 4));
    fclose($fp);
    if ($info['sig'] == 0x6054b50) {
        cli_error("This ZIP archive is empty: $archive");
    } else {
        cli_error("This is not a ZIP archive: $archive");
    }
}

fseek($fp, 0);
$info = unpack('Vsig', fread($fp, 4));
if ($info['sig'] !== 0x04034b50) {
    fclose($fp);
    cli_error("This is not a ZIP archive: $archive");
}

// Find end of central directory record.
$centralend = zip_archive::zip_get_central_end($fp, $filesize);
if ($centralend === false) {
    cli_error("This is not a ZIP archive: $archive");
}

if ($centralend['disk'] !== 0 or $centralend['disk_start'] !== 0) {
    cli_error("Multi-disk archives are not supported: $archive");
}

if ($centralend['offset'] === 0xFFFFFFFF) {
    cli_error("ZIP64 archives are not supported: $archive");
}

fseek($fp, $centralend['offset']);
$data = fread($fp, $centralend['size']);
$pos = 0;
$files = array();
for($i=0; $i<$centralend['entries']; $i++) {
    $file = zip_archive::zip_parse_file_header($data, $centralend, $pos);
    if ($file === false) {
        cli_error('Invalid Zip file header structure: '.$archive);
    }

    // Read local file header.
    fseek($fp, $file['local_offset']);
    $localfile = unpack('Vsig/vversion_req/vgeneral/vmethod/Vmodified/Vcrc/Vsize_compressed/Vsize/vname_length/vextra_length', fread($fp, 30));
    if ($localfile['sig'] !== 0x04034b50) {
        // Borked file!
        $file['error'] = 'Invalid local file signature';
        $files[] = $file;
        continue;
    }
    if ($localfile['name_length']) {
        $localfile['name'] = fread($fp, $localfile['name_length']);
    } else {
        $localfile['name'] = '';
    }
    $localfile['extra'] = array();
    $localfile['extra_data'] = '';
    if ($localfile['extra_length']) {
        $extradata = fread($fp, $localfile['extra_length']);
        $localfile['extra_data'] = $extradata;
        while (strlen($extradata) > 4) {
            $extra = unpack('vid/vsize', substr($extradata, 0, 4));
            $extra['data'] = substr($extradata, 4, $extra['size']);
            $extradata = substr($extradata, 4+$extra['size']);
            $localfile['extra'][] = $extra;
        }
    }

    $file['local'] = $localfile;
    $files[] = $file;
}

echo "Archive:         $archive\n";
echo "Number of files: {$centralend['entries']}\n";
echo "Archive comment: \"{$centralend['comment']}\" ({$centralend['comment_length']} bytes)\n";
foreach ($files as $i=>$file) {
    echo "======== File ".($i+1)." ==============================================\n";
    echo "  Name:           ".zip_print_name($file['name'])."\n";
    if ($file['comment'] !== '') {
        echo "  Comment:        \"{$file['comment']}\" ({$file['comment_length']} bytes)\n";
    }
    echo "  Version:        0x".str_pad(dechex($file['version']), 4, '0', STR_PAD_LEFT)."\n";
    echo "  Required:       0x".str_pad(dechex($file['version_req']), 4, '0', STR_PAD_LEFT)."\n";
    echo "  Method:         ".zip_print_method($file['method'])."\n";
    echo "  General:        ".zip_print_general($file['general'])."\n";
    echo "  Modified:       ".userdate(zip_dos2unixtime($file['modified']))."\n";
    echo "  Size:           ".zip_print_sizes($file['size'], $file['size_compressed'])."\n";
    echo "  CRC-32:         {$file['crc']}\n";
    foreach($file['extra'] as $j=>$extra) {
        echo "  Extra ".($j+1).":        ".zip_print_extra($extra)."\n";
    }
    if (!empty($file['local']['error'])) {
        echo "  Local ERROR:    {$file['local']['error']}\n";
        continue;
    }
    $localfile = $file['local'];
    if ($localfile['name'] !== $file['name']) {
        echo "  Local name:     ".zip_print_name($localfile['name'])."\n";
    }
    if ($localfile['version_req'] !== $file['version_req']) {
        echo "  Local required: 0x".str_pad(dechex($localfile['version_req']), 4, '0', STR_PAD_LEFT)."\n";
    }
    if ($localfile['method'] !== $file['method']) {
        echo "  Local method:   ".zip_print_method($localfile['method'])."\n";
    }
    if ($localfile['general'] !== $file['general']) {
        echo "  Local general:  ".zip_print_general($localfile['general'])."\n";
    }
    if ($localfile['modified'] !== $file['modified']) {
        echo "  Local modified: ".userdate(zip_dos2unixtime($localfile['modified']))."\n";
    }
    if ($localfile['size'] !== $file['size']) {
        echo "  Local size:     ".zip_print_sizes($localfile['size'], $localfile['size_compressed'])."\n";
    }
    if ($localfile['crc'] !== $file['crc']) {
        echo "  Local CRC-32:   {$localfile['crc']}\n";
    }
    foreach($localfile['extra'] as $j=>$extra) {
        echo "  Local extra ".($j+1).":  ".zip_print_extra($extra)."\n";
    }
}

fclose($fp);
exit(0);

// === Some useful functions ======================================

function zip_print_name($name) {
    $size = strlen($name);
    $crc = crc32($name);
    return "\"$name\" ($size bytes) - CRC $crc";
}

function zip_print_method($method) {
    $desc = '';
    switch($method) {
        case 0: $desc = 'Stored'; break;
        case 1: $desc = 'Shrunk'; break;
        case 2: $desc = 'Reduced factor 1'; break;
        case 3: $desc = 'Reduced factor 2'; break;
        case 4: $desc = 'Reduced factor 3'; break;
        case 5: $desc = 'Reduced factor 4'; break;
        case 6: $desc = 'Imploded'; break;
        case 8: $desc = 'Deflated'; break;
        case 9: $desc = 'Deflate64'; break;
        case 10: $desc = 'old IBM TERSE'; break;
        case 12: $desc = 'BZIP2'; break;
        case 14: $desc = 'LZMA'; break;
        case 18: $desc = 'IBM TERSE'; break;
        case 19: $desc = 'IBM LZ77'; break;
        case 97: $desc = 'WavPack'; break;
        case 98: $desc = 'PPMd v1'; break;
    }
    if ($desc) {
        $desc = " ($desc)";
    }
    return "0x".str_pad(dechex($method), 4, '0', STR_PAD_LEFT).$desc;
}

function zip_print_general($general) {
    $desc = array();
    if ($general & pow(2, 0)) {
        $desc[] = 'Encrypted';
    }
    if ($general & pow(2, 11)) {
        $desc[] = 'Unicode name';
    }
    if ($desc) {
        $desc = " (".implode(', ', $desc).")";
    } else {
        $desc = '';
    }
    return str_pad(decbin($general), 16, '0', STR_PAD_LEFT).$desc;
}

/**
 * Convert MS date+time format to unix timestamp:
 * http://msdn.microsoft.com/en-us/library/windows/desktop/ms724274(v=vs.85).aspx
 *
 * Copied from: http://plugins.svn.wordpress.org/wp2epub/trunk/zipcreate/functions.lib.php
 * author: redmonkey
 * license: GPL
 */
function zip_dos2unixtime($dostime) {
    $sec = 2 * ($dostime & 0x1f);
    $min = ($dostime >> 5) & 0x3f;
    $hrs = ($dostime >> 11) & 0x1f;
    $day = ($dostime >> 16) & 0x1f;
    $mon = ($dostime >> 21) & 0x0f;
    $year = (($dostime >> 25) & 0x7f) + 1980;

    return mktime($hrs, $min, $sec, $mon, $day, $year);
}

function zip_print_sizes($size, $compressed) {
    return "$size ==> $compressed bytes";
}

function zip_print_extra($extra) {
    $desc = '';
    $info = "- ".bin2hex($extra['data'])." ({$extra['size']} bytes)";
    switch($extra['id']) {
        case 0x0009: $desc = 'OS/2'; break;
        case 0x000a: $desc = 'NTFS'; break;
        case 0x000d: $desc = 'UNIX'; break;
        case 0x5455: $desc = 'Extended timestamp'; break;
        case 0x5855: $desc = 'Infor-ZIP (original)'; break;
        case 0x7075:
            $desc = 'Info-ZIP Unicode path';
            $data = unpack('cversion/Vcrc', substr($extra['data'], 0, 5));
            $name = substr($extra['data'], 5);
            $size = strlen($name);
            if ($data['version'] === 1) {
                $info = "- \"$name\" ($size bytes) - CRC {$data['crc']}";
            }
            break;
        case 0x7865: $desc = 'Info-ZIP UNIX (new)'; break;
        case 0x7875: $desc = 'Info-ZIP UNIX (3rd generation)'; break;
    }
    if ($desc) {
        $desc = " ($desc)";
    }
    return "0x".str_pad(dechex($extra['id']), 4, '0', STR_PAD_LEFT)."$desc $info";
}