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
 * Sub-class of lib/pear/PHP/CodeSniffer.php
 *
 * Modified to read thirdpartylibs.xml from a recursed directory and apply
 * its contents to the ignored list.
 *
 * @package   lib-pear-php-codesniffer
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('PHP/CodeSniffer/CLI.php');

if (class_exists('PHP_CodeSniffer', true) === false) {
    throw new Exception('Class PHP_CodeSniffer not found');
}

class moodle_codesniffer extends php_codesniffer {
    public function processFiles($dir, $local=false) {
        try {
            if ($local === true) {
                $di = new DirectoryIterator($dir);
            } else {
                $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            }

            // MOODLE CODE: If thirdpartylibs.xml is found, add these values to the ignored array
            // first iteration to find thirdpartylibs.xml
            foreach ($di as $file) {
                if ($file->getFileName() == 'thirdpartylibs.xml') {
                    $xml = simplexml_load_file($file->getPathName());
                    foreach ($xml->library as $libobject) {
                        $this->ignorePatterns[] = (string) $libobject->location;
                    }
                }
            }

            foreach ($di as $file) {
                $filePath = realpath($file->getPathname());

                if (is_dir($filePath) === true) {
                    continue;
                }

                // Check that the file's extension is one we are checking.
                // Note that because we are doing a whole directory, we
                // are strick about checking the extension and we don't
                // let files with no extension through.
                $fileParts = explode('.', $file);
                $extension = array_pop($fileParts);
                if ($extension === $file) {
                    continue;
                }

                if (isset($this->allowedFileExtensions[$extension]) === false) {
                    continue;
                }

                $this->processFile($filePath);
            }//end foreach
        } catch (Exception $e) {
            $trace    = $e->getTrace();
            $filename = $trace[0]['args'][0];
            $error    = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();

            $phpcsFile = new PHP_CodeSniffer_File($filename, $this->listeners, $this->allowedFileExtensions);
            $this->addFile($phpcsFile);
            $phpcsFile->addError($error, null);
            return;
        }
    }
}
