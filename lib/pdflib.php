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
 * pdflib.php - Moodle PDF library
 * 
 * We currently use the TCPDF library by Nicola Asuni.
 *
 * The default location for fonts that are included with TCPDF is
 * lib/tcpdf/fonts/. If $CFG->datadir.'/fonts/' exists, this directory
 * will be used instead of lib/tcpdf/fonts/. If there is only one font
 * present in $CFG->datadir.'/fonts/', the font is used as the default
 * font.
 * 
 * See lib/tcpdf/fonts/README for details on how to convert fonts for use
 * with TCPDF.
 * 
 * Example usage:
 * <code>
 *    $doc = new pdf;
 *    $doc->setPrintHeader(false);
 *    $doc->setPrintFooter(false);
 *    $doc->AddPage();
 *    $doc->Write(5, 'Hello World!');
 *    $doc->Output();
 * </code>
 *
 * @package moodlecore
 * @copyright Vy-Shane Sin Fat
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



/** Includes */
require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');



/// Constants
define('PDF_CUSTOM_FONT_PATH', $CFG->dataroot.'/fonts/');
define('PDF_DEFAULT_FONT', 'DejaVuSerif');



/**
 * Wrapper class that extends TCPDF (lib/tcpdf/tcpdf.php).
 * Moodle customisations are done here.
 *
 * @package moodlecore
 * @copyright Vy-Shane Sin Fat
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdf extends TCPDF {
        
    /**
     * Class constructor
     * 
     * See the parent class documentation for the parameters info.
     */
    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8') {
        
        parent::__construct($orientation, $unit, $format, $unicode, $encoding);
        
        if (is_dir(PDF_CUSTOM_FONT_PATH)) {
            $fontfiles = $this->_getfontfiles(PDF_CUSTOM_FONT_PATH);
            
            if (count($fontfiles) == 1) {
                $autofontname = substr($fontfile[0], 0, -4);
                $this->AddFont($autofontname, '', $autofontname.'.php');
                $this->SetFont($autofontname);
            } else if (count($fontfiles == 0)) {
                $this->SetFont(PDF_DEFAULT_FONT);
            }
        } else {
            $this->SetFont(PDF_DEFAULT_FONT);
        }
    }
    
    
    /**
     * Return fonts path
     * Overriding TCPDF::_getfontpath()
     *
     * @global object
     */
    protected function _getfontpath() {
        global $CFG;
        
        if (is_dir(PDF_CUSTOM_FONT_PATH)
                    && count($this->_getfontfiles(PDF_CUSTOM_FONT_PATH)) > 0) {
            $fontpath = PDF_CUSTOM_FONT_PATH;
        } else {
            $fontpath = $CFG->dirroot.'/lib/tcpdf/fonts/';
        }
        return $fontpath;
    }
    
    
    /**
     * Get the .php files for the fonts
     */
    protected function _getfontfiles($fontdir) {
        $dirlist = get_directory_list($fontdir);
        $fontfiles = array();
        
        foreach ($dirlist as $file) {
            if (substr($file, -4) == '.php') {
                array_push($fontfiles, $file);
            }
        }
        return $fontfiles;
    }
    
    
} // End class pdf


?>
