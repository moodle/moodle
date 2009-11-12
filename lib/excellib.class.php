<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

//setup.php icludes our hacked pear libs first
require_once 'Spreadsheet/Excel/Writer.php';

/**
* Define and operate over one Moodle Workbook.
*
* A big part of this class acts as a wrapper over the PEAR
* Spreadsheet_Excel_Writer_Workbook and OLE libraries
* maintaining Moodle functions isolated from underlying code.
*/
class MoodleExcelWorkbook {

    var $pear_excel_workbook;
    var $latin_output;

    /**
     * Constructs one Moodle Workbook.
     *
     * @param string $filename The name of the file
     */
    function MoodleExcelWorkbook($filename) {
        global $CFG;
    /// Internally, create one PEAR Spreadsheet_Excel_Writer_Workbook class
        $this->pear_excel_workbook = new Spreadsheet_Excel_Writer($filename);
    /// Prepare it to accept UTF-16LE data and to encode it properly
        if (empty($CFG->latinexcelexport)) { /// Only if don't want to use latin (win1252) stronger output
            $this->pear_excel_workbook->setVersion(8);
            $this->latin_output = false;
        } else { /// We want latin (win1252) output
            $this->latin_output = true;
        }
    /// Choose our temporary directory - see MDL-7176, found by paulo.matos
        make_upload_directory('temp/excel', false);
        $this->pear_excel_workbook->setTempDir($CFG->dataroot.'/temp/excel');
    }

    /**
     * Create one Moodle Worksheet
     *
     * @param string $name Name of the sheet
     */
    function &add_worksheet($name = '') {
    /// Create the Moodle Worksheet. Returns one pointer to it
        $ws =& new MoodleExcelWorksheet ($name, $this->pear_excel_workbook, $this->latin_output);
        return $ws;
    }

    /**
     * Create one Moodle Format
     *
     * @param array $properties array of properties [name]=value;
     *                          valid names are set_XXXX existing
     *                          functions without the set_ part
     *                          i.e: [bold]=1 for set_bold(1)...Optional!
     */
    function &add_format($properties = array()) {
    /// Create the Moodle Format. Returns one pointer to it
        $ft =& new MoodleExcelFormat ($this->pear_excel_workbook, $properties);
        return $ft;
    }

    /**
     * Close the Moodle Workbook
     */
    function close() {
        $this->pear_excel_workbook->close();
    }

    /**
     * Write the correct HTTP headers
     *
     * @param string $name Name of the downloaded file
     */
    function send($filename) {
        $this->pear_excel_workbook->send($filename);
    }
}

/**
* Define and operate over one Worksheet.
*
* A big part of this class acts as a wrapper over the PEAR
* Spreadsheet_Excel_Writer_Workbook and OLE libraries
* maintaining Moodle functions isolated from underlying code.
*/
class MoodleExcelWorksheet {

    var $pear_excel_worksheet;
    var $latin_output;

    /**
     * Constructs one Moodle Worksheet.
     *
     * @param string $filename The name of the file
     * @param object $workbook The internal PEAR Workbook onject we are creating
     */
    function MoodleExcelWorksheet($name, &$workbook, $latin_output=false) {

    /// Internally, add one sheet to the workbook    
        $this->pear_excel_worksheet =& $workbook->addWorksheet($name);
        $this->latin_output = $latin_output;
    /// Set encoding to UTF-16LE 
        if (!$this->latin_output) { /// Only if don't want to use latin (win1252) stronger output
            $this->pear_excel_worksheet->setInputEncoding('UTF-16LE');
        }
    }

    /**
     * Write one string somewhere in the worksheet
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $str    The string to write
     * @param mixed   $format The XF format for the cell
     */
    function write_string($row, $col, $str, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Loading the textlib singleton instance. We are going to need it.
        $textlib = textlib_get_instance();
    /// Convert the text from its original encoding to UTF-16LE
        if (!$this->latin_output) { /// Only if don't want to use latin (win1252) stronger output
            $str = $textlib->convert($str, 'utf-8', 'utf-16le');
        } else { /// else, convert to latin (win1252)
            $str = $textlib->convert($str, 'utf-8', 'windows-1252');
        }
    /// Add the string safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeString($row, $col, $str, $format);
    }

    /**
     * Write one number somewhere in the worksheet
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param float   $num    The number to write
     * @param mixed   $format The XF format for the cell
     */
    function write_number($row, $col, $num, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Add  the number safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeNumber($row, $col, $num, $format);
    }

    /**
     * Write one url somewhere in the worksheet
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $url    The url to write
     * @param mixed   $format The XF format for the cell
     */
    function write_url($row, $col, $url, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Add  the url safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeUrl($row, $col, $url, $format);
    }

    /**
     * Write one date somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $date   The date to write in UNIX timestamp format
     * @param mixed   $format The XF format for the cell
     */
    function write_date($row, $col, $date, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Convert the date to Excel format
        $timezone = get_user_timezone_offset();
        $value =  ((usertime($date) + (int)($timezone * HOURSECS * 2)) / 86400) + 25569;
    /// Add  the date safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeNumber($row, $col, $value, $format);
    }

    /**
     * Write one formula somewhere in the worksheet
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $formula The formula to write
     * @param mixed   $format The XF format for the cell
     */
    function write_formula($row, $col, $formula, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Add  the formula safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeFormula($row, $col, $formula, $format);
    }

    /**
     * Write one blanck somewhere in the worksheet
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $format The XF format for the cell
     */
    function write_blank($row, $col, $format=null) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Add  the blank safely to the PEAR Worksheet
        $this->pear_excel_worksheet->writeBlank($row, $col, $format);
    }

    /**
     * Write anything somewhere in the worksheet
     * Type will be automatically detected
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $token  What we are writing
     * @param mixed   $format The XF format for the cell
     */
    function write($row, $col, $token, $format=null) {

    /// Analyse what are we trying to send
        if (preg_match("/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $token)) {
        /// Match number
            return $this->write_number($row, $col, $token, $format);
        } elseif (preg_match("/^[fh]tt?p:\/\//", $token)) {
        /// Match http or ftp URL
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^mailto:/", $token)) {
        /// Match mailto:
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^(?:in|ex)ternal:/", $token)) {
        /// Match internal or external sheet link
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^=/", $token)) {
        /// Match formula
            return $this->write_formula($row, $col, $token, $format);
        } elseif (preg_match("/^@/", $token)) {
        /// Match formula
            return $this->write_formula($row, $col, $token, $format);
        } elseif ($token == '') {
        /// Match blank
            return $this->write_blank($row, $col, $format);
        } else {
        /// Default: match string
            return $this->write_string($row, $col, $token, $format);
        }
    }

    /**
     * Sets the height (and other settings) of one row
     *
     * @param integer $row    The row to set
     * @param integer $height Height we are giving to the row (null to set just format withouth setting the height)
     * @param mixed   $format The optional XF format we are giving to the row
     * @param bool    $hidden The optional hidden attribute
     * @param integer $level  The optional outline level (0-7)
     */
    function set_row ($row, $height, $format = null, $hidden = false, $level = 0) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Set the row safely to the PEAR Worksheet
        $this->pear_excel_worksheet->setRow($row, $height, $format, $hidden, $level);
    }

    /**
     * Sets the width (and other settings) of one column
     *
     * @param integer $firstcol first column on the range
     * @param integer $lastcol  last column on the range
     * @param integer $width    width to set
     * @param mixed   $format   The optional XF format to apply to the columns
     * @param integer $hidden   The optional hidden atribute
     * @param integer $level    The optional outline level (0-7)
     */
    function set_column ($firstcol, $lastcol, $width, $format = null, $hidden = false, $level = 0) {
    /// Calculate the internal PEAR format
        $format = $this->MoodleExcelFormat2PearExcelFormat($format);
    /// Set the column safely to the PEAR Worksheet
        $this->pear_excel_worksheet->setColumn($firstcol, $lastcol, $width, $format, $hidden, $level);
    }

    /**
    * Set the option to hide gridlines on the printed page.
    *
    * @access public
    */
    function hide_gridlines() {
        $this->pear_excel_worksheet->hideGridLines();
    }

    /**
    * Set the option to hide gridlines on the worksheet (as seen on the screen).
    *
    * @access public
    */
    function hide_screen_gridlines() {
        $this->pear_excel_worksheet->hideScreenGridlines();
    }
    
    /**
    * Insert a 24bit bitmap image in a worksheet.
    *
    * @access public
    * @param integer $row     The row we are going to insert the bitmap into
    * @param integer $col     The column we are going to insert the bitmap into
    * @param string  $bitmap  The bitmap filename
    * @param integer $x       The horizontal position (offset) of the image inside the cell.
    * @param integer $y       The vertical position (offset) of the image inside the cell.
    * @param integer $scale_x The horizontal scale
    * @param integer $scale_y The vertical scale
    */
    function insert_bitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1) {
    /// Add the bitmap safely to the PEAR Worksheet
        $this->pear_excel_worksheet->insertBitmap($row, $col, $bitmap, $x, $y, $scale_x, $scale_y);
    }

    /**
    * Merges the area given by its arguments.
    * This is an Excel97/2000 method. It is required to perform more complicated
    * merging than the normal setAlign('merge').
    *
    * @access public
    * @param integer $first_row First row of the area to merge
    * @param integer $first_col First column of the area to merge
    * @param integer $last_row  Last row of the area to merge
    * @param integer $last_col  Last column of the area to merge
    */
    function merge_cells($first_row, $first_col, $last_row, $last_col) {
        /// Merge cells safely to the PEAR Worksheet
        $this->pear_excel_worksheet->mergeCells($first_row, $first_col, $last_row, $last_col);
    }

    /**
     * Returns the PEAR Excel Format for one Moodle Excel Format
     *
     * @param mixed MoodleExcelFormat object
     * @return mixed PEAR Excel Format object
     */
    function MoodleExcelFormat2PearExcelFormat($format) {
        if ($format) {
            return $format->pear_excel_format;
        } else {
            return null;
        }
    }
}


/**
* Define and operate over one Format.
*
* A big part of this class acts as a wrapper over the PEAR
* Spreadsheet_Excel_Writer_Workbook and OLE libraries
* maintaining Moodle functions isolated from underlying code.
*/
class MoodleExcelFormat {

    var $pear_excel_format;

    /**
     * Constructs one Moodle Format.
     *
     * @param object $workbook The internal PEAR Workbook onject we are creating
     */
    function MoodleExcelFormat(&$workbook, $properties = array()) {
    /// Internally, add one sheet to the workbook    
        $this->pear_excel_format =& $workbook->addFormat();
    /// If we have something in the array of properties, compute them
        foreach($properties as $property => $value) {
            if(method_exists($this,"set_$property")) {
                $aux = 'set_'.$property;
                $this->$aux($value);
            }
        }
    }

    /**
     * Set the size of the text in the format (in pixels).
     * By default all texts in generated sheets are 10px.
     *
     * @param integer $size Size of the text (in pixels)
     */
    function set_size($size) {
    /// Set the size safely to the PEAR Format
        $this->pear_excel_format->setSize($size);
    }

    /**
     * Set weight of the format
     *
     * @param integer $weight Weight for the text, 0 maps to 400 (normal text),
     *                        1 maps to 700 (bold text). Valid range is: 100-1000.
     *                        It's Optional, default is 1 (bold).
     */
    function set_bold($weight = 1) {
    /// Set the bold safely to the PEAR Format
        $this->pear_excel_format->setBold($weight);
    }

    /**
     * Set underline of the format
     *
     * @param integer $underline The value for underline. Possible values are:
     *                           1 => underline, 2 => double underline
     */
    function set_underline($underline) {
    /// Set the underline safely to the PEAR Format
        $this->pear_excel_format->setUnderline($underline);
    }

    /**
     * Set italic of the format
     */
    function set_italic() {
    /// Set the italic safely to the PEAR Format
        $this->pear_excel_format->setItalic();
    }

    /**
     * Set strikeout of the format
     */
    function set_strikeout() {
    /// Set the strikeout safely to the PEAR Format
        $this->pear_excel_format->setStrikeOut();
    }

    /**
     * Set outlining of the format
     */
    function set_outline() {
    /// Set the outlining safely to the PEAR Format
        $this->pear_excel_format->setOutLine();
    }

    /**
     * Set shadow of the format
     */
    function set_shadow() {
    /// Set the shadow safely to the PEAR Format
        $this->pear_excel_format->setShadow();
    }

    /**
     * Set the script of the text
     *
     * @param integer $script The value for script type. Possible values are:
     *                        1 => superscript, 2 => subscript
     */
    function set_script($script) {
    /// Set the script safely to the PEAR Format
        $this->pear_excel_format->setScript($script);
    }

    /**
     * Set color of the format. Used to specify the color of the text to be formatted.
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_color($color) {
    /// Set the background color safely to the PEAR Format
        $this->pear_excel_format->setColor($color);
    }

    /**
     * Set foreground color (top layer) of the format. About formatting colors note that cells backgrounds
     * have TWO layers, in order to support patterns and paint them with two diferent colors.
     * This method set the color of the TOP layer of the background format. So, when filling
     * cells with plain colors (no patterns) this is the method to use.
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_fg_color($color) {
    /// Set the foreground color safely to the PEAR Format
        $this->pear_excel_format->setFgColor($color);
    }

    /**
     * Set background color (bottom layer) of the format. About formatting colors note that cells backgrounds
     * have TWO layers, in order to support patterns and paint them with two diferent colors.
     * This method set the color of the BOTTOM layer of the background format. So, the color
     * specified here only will be visible if using patterns. Use set_fg_color() to fill
     * cells with plain colors (no patterns).
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_bg_color($color) {
    /// Set the background color safely to the PEAR Format
        $this->pear_excel_format->setBgColor($color);
    }

    /**
     * Set the fill pattern of the format
     * @param integer Optional. Defaults to 1. Meaningful values are: 0-18
     *                0 meaning no background.
     */
    function set_pattern($pattern=1) {
    /// Set the fill pattern safely to the PEAR Format
        $this->pear_excel_format->setPattern($pattern);
    }

    /**
     * Set text wrap of the format
     */
    function set_text_wrap() {
    /// Set the shadow safely to the PEAR Format
        $this->pear_excel_format->setTextWrap();
    }

    /**
     * Set the cell alignment of the format
     *
     * @param string $location alignment for the cell ('left', 'right', etc...)
     */
    function set_align($location) {
    /// Set the alignment of the cell safely to the PEAR Format
        $this->pear_excel_format->setAlign($location);
    }

    /**
     * Set the cell horizontal alignment of the format
     *
     * @param string $location alignment for the cell ('left', 'right', etc...)
     */
    function set_h_align($location) {
    /// Set the alignment of the cell safely to the PEAR Format
        $this->pear_excel_format->setHAlign($location);
    }

    /**
     * Set the cell vertical alignment of the format
     *
     * @param string $location alignment for the cell ('top', 'vleft', etc...)
     */
    function set_v_align($location) {
    /// Set the alignment of the cell safely to the PEAR Format
        $this->pear_excel_format->setVAlign($location);
    }

    /**
     * Set the top border of the format
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_top($style) {
    /// Set the top border of the cell safely to the PEAR Format
        $this->pear_excel_format->setTop($style);
    }

    /**
     * Set the bottom border of the format
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_bottom($style) {
    /// Set the bottom border of the cell safely to the PEAR Format
        $this->pear_excel_format->setBottom($style);
    }

    /**
     * Set the left border of the format
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_left($style) {
    /// Set the left border of the cell safely to the PEAR Format
        $this->pear_excel_format->setLeft($style);
    }

    /**
     * Set the right border of the format
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_right($style) {
    /// Set the right border of the cell safely to the PEAR Format
        $this->pear_excel_format->setRight($style);
    }

    /**
     * Set cells borders to the same style
     *
     * @param integer $style style to apply for all cell borders. 1 => thin, 2 => thick.
     */
    function set_border($style) {
    /// Set all the borders of the cell safely to the PEAR Format
        $this->pear_excel_format->setBorder($style);
    }

    /**
     * Set the numerical format of the format
     * It can be date, time, currency, etc...
     *
     * @param integer $num_format The numeric format
     */
    function set_num_format($num_format) {
    /// Set the numerical format safely to the PEAR Format
        $this->pear_excel_format->setNumFormat($num_format);
    }

}

?>
