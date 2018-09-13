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
 * Ases block
 *
 * @author     John Lourido
 * @package    block_ases
 * @copyright  2017 John Lourido <jhonkrave@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');

/**
 * Deletes files from a folder given its path
 * 
 * @see deleteFilesFromFolder($folderPath)
 * @param $folderPath --> folder path
 * @return void 
 */
function deleteFilesFromFolder($folderPath){
    $files = glob($folderPath.'/*'); // get all file names
    foreach($files as $file){ // iterate files
          if(is_file($file))  unlink($file); // delete file
    }
}

/**
 * Creates a zip given a folder and the zip path
 * 
 * @see createZip($patchFolder,$patchStorageZip)
 * @param $patchFolder --> folder
 * @param $patchStorageZip --> patch zip
 * @return void 
 */
function createZip($patchFolder,$patchStorageZip){
    // Get real path for our folder
    $rootPath = realpath($patchFolder);
    
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($patchStorageZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
    
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    // Zip archive will be created only after closing object
    $zip->close();
}
?>