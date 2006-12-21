<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//           (C) 2001-3001 Petr Skoda (skodak)                           //
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

// This code uses parts of the Minimalistic creator of OASIS OpenDocument
// from phpMyAdmin (www.phpmyadmin.net)
//   files: libraries/opendocument.lib.php and libraries/export/ods.php
// and also parts of the original MoodleExcelWorkbook class.

class MoodleODSWorkbook {
    var $worksheets = array();
    var $filename;

    function MoodleODSWorkbook($filename) {
        $this->filename = $filename;
    }

    /* Create one Moodle Worksheet
     * @param string $name Name of the sheet
     */
    function &add_worksheet($name = '') {
    /// Create the Moodle Worksheet. Returns one pointer to it
        $ws =& new MoodleODSWorksheet($name);
        $this->worksheets[] =& $ws;
        return $ws;
    }

    /* Create one Moodle Format
     * @param array $properties array of properties [name]=value;
     *                          valid names are set_XXXX existing
     *                          functions without the set_ part
     *                          i.e: [bold]=1 for set_bold(1)...Optional!
     */
    function &add_format($properties = array()) {
        $format = new MoodleODSFormat($this);
        return $format;;
    }

    /* Close the Moodle Workbook
     */
    function close() {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $dir = 'temp/ods/'.time();
        make_upload_directory($dir, false);
        make_upload_directory($dir.'/META-INF', false);
        $dir = "$CFG->dataroot/$dir";
        $files = array();

        $handle = fopen("$dir/mimetype", 'w');
        fwrite($handle, get_ods_mimetype());
        $files[] = "$dir/mimetype";

        $handle = fopen("$dir/content.xml", 'w');
        fwrite($handle, get_ods_content($this->worksheets));
        $files[] = "$dir/content.xml";

        $handle = fopen("$dir/meta.xml", 'w');
        fwrite($handle, get_ods_meta());
        $files[] = "$dir/meta.xml";

        $handle = fopen("$dir/styles.xml", 'w');
        fwrite($handle, get_ods_styles());
        $files[] = "$dir/styles.xml";

        $handle = fopen("$dir/META-INF/manifest.xml", 'w');
        fwrite($handle, get_ods_manifest());
        $files[] = "$dir/META-INF";

        $filename = "$dir/result.ods";
        zip_files($files, $filename);

        $handle = fopen($filename, 'rb');
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        remove_dir($dir); // cleanup the temp directory

        send_file($contents, $this->filename, 0, 0, true, true, 'application/vnd.oasis.opendocument.spreadsheet');
    }

    /* Not required to use
     * @param string $name Name of the downloaded file
     */
    function send($filename) {
        $this->filename = $filename;
    }

}

class MoodleODSWorksheet {
    var $data = array();
    var $name;


    /* Constructs one Moodle Worksheet.
     * @param string $filename The name of the file
     */
    function ODSWorksheet($name) {
        $this->name = $name;
    }

    /* Write one string somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $str    The string to write
     * @param mixed   $format The XF format for the cell
     */
    function write_string($row, $col, $str, $format=0) {
        if (!array_key_exists($row, $this->data)) {
            $this->data[$row] = array();
        }
        $this->data[$row][$col] = new object();
        $this->data[$row][$col]->value = $str;
        $this->data[$row][$col]->type = 'string';
        $this->data[$row][$col]->format = $format;
    }

    /* Write one number somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param float   $num    The number to write
     * @param mixed   $format The XF format for the cell
     */
    function write_number($row, $col, $num, $format=0) {
        if (!array_key_exists($row, $this->data)) {
            $this->data[$row] = array();
        }
        $this->data[$row][$col] = new object();
        $this->data[$row][$col]->value = $num;
        $this->data[$row][$col]->type = 'float';
        $this->data[$row][$col]->format = $format;
    }

    /* Write one url somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $url    The url to write
     * @param mixed   $format The XF format for the cell
     */
    function write_url($row, $col, $url, $format=0) {
        if (!array_key_exists($row, $this->data)) {
            $this->data[$row] = array();
        }
        $this->data[$row][$col] = new object();
        $this->data[$row][$col]->value = $url;
        $this->data[$row][$col]->type = 'string';
        $this->data[$row][$col]->format = $format;
    }

    /* Write one date somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $url    The url to write
     * @param mixed   $format The XF format for the cell
     */
    function write_date($row, $col, $date, $format=0) {
        if (!array_key_exists($row, $this->data)) {
            $this->data[$row] = array();
        }
        $this->data[$row][$col] = new object();
        $this->data[$row][$col]->value = $date;
        $this->data[$row][$col]->type = 'date';
        $this->data[$row][$col]->format = $format;
    }

    /* Write one blanck somewhere in the worksheet
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $format The XF format for the cell
     */
    function write_blank($row, $col, $format=0) {
        if (array_key_exists($row, $this->data)) {
            unset($this->data[$row][$col]);
        }
    }

    /* Write anything somewhere in the worksheet
     * Type will be automatically detected
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $token  What we are writing
     * @param mixed   $format The XF format for the cell
     */
    function write($row, $col, $token, $format=0) {

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

    /* Sets the height (and other settings) of one row
     * @param integer $row    The row to set
     * @param integer $height Height we are giving to the row (null to set just format withouth setting the height)
     * @param mixed   $format The optional XF format we are giving to the row
     * @param bool    $hidden The optional hidden attribute
     * @param integer $level  The optional outline level (0-7)
     */
    function set_row ($row, $height, $format = 0, $hidden = false, $level = 0) {
        //not defined yet
    }

    /* Sets the width (and other settings) of one column
     * @param integer $firstcol first column on the range
     * @param integer $lastcol  last column on the range
     * @param integer $width    width to set
     * @param mixed   $format   The optional XF format to apply to the columns
     * @param integer $hidden   The optional hidden atribute
     * @param integer $level    The optional outline level (0-7)
     */
    function set_column ($firstcol, $lastcol, $width, $format = 0, $hidden = false, $level = 0) {
        //not defined yet
    }

}

/**
* Define and operate over one Format.
*
* A big part of this class acts as a wrapper over the PEAR
* Spreadsheet_Excel_Writer_Workbook and OLE libraries
* maintaining Moodle functions isolated from underlying code.
*/
class MoodleODSFormat {

    /* Constructs one Moodle Format.
     * @param object $workbook The internal PEAR Workbook onject we are creating
     */
    function MoodleODSFormat(&$workbook, $properties = array()) {
    }

    /* Set weight of the format
     * @param integer $weight Weight for the text, 0 maps to 400 (normal text),
     *                        1 maps to 700 (bold text). Valid range is: 100-1000.
     *                        It's Optional, default is 1 (bold).
     */
    function set_bold($weight = 1) {
    }

    /* Set underline of the format
     * @param integer $underline The value for underline. Possible values are:
     *                           1 => underline, 2 => double underline
     */
    function set_underline($underline) {
    }

    /* Set italic of the format
     */
    function set_italic() {
    }

    /* Set strikeout of the format
     */
    function set_strikeout() {
    }

    /* Set outlining of the format
     */
    function set_outline() {
    }

    /* Set shadow of the format
     */
    function set_shadow() {
    }

    /* Set the script of the text
     * @param integer $script The value for script type. Possible values are:
     *                        1 => superscript, 2 => subscript
     */
    function set_script($script) {
    }

    /* Set color of the format
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_color($color) {
    }

    /* Set foreground color of the format
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_fg_color($color) {
    }

    /* Set background color of the format
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    function set_bg_color($color) {
    }

    /* Set the fill pattern of the format
     * @param integer Optional. Defaults to 1. Meaningful values are: 0-18
     *                0 meaning no background.
     */
    function set_pattern($pattern=1) {
    }

    /* Set text wrap of the format
     */
    function set_text_wrap() {
    }

    /* Set the cell alignment of the format
     * @param string $location alignment for the cell ('left', 'right', etc...)
     */
    function set_align($location) {
    }

    /* Set the cell horizontal alignment of the format
     * @param string $location alignment for the cell ('left', 'right', etc...)
     */
    function set_h_align($location) {
    }

    /* Set the cell vertical alignment of the format
     * @param string $location alignment for the cell ('top', 'vleft', etc...)
     */
    function set_v_align($location) {
    }

    /* Set the top border of the format
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_top($style) {
    }

    /* Set the bottom border of the format
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_bottom($style) {
    }

    /* Set the left border of the format
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_left($style) {
    }

    /* Set the right border of the format
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    function set_right($style) {
    }

    /**
     * Set cells borders to the same style
     * @param integer $style style to apply for all cell borders. 1 => thin, 2 => thick.
     */
    function set_border($style) {
    }

    /* Set the numerical format of the format
     * It can be date, time, currency, etc...
    /* Set the numerical format of the format
     * It can be date, time, currency, etc...
     * @param integer $num_format The numeric format
     */
    function set_num_format($num_format) {
    }
}


//=============================
// OpenDocument XML functions
//=============================
function get_ods_content(&$worksheets) {

/// header
    $buffer =
        '<?xml version="1.0" encoding="UTF-8"?' . '>'
        . '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
                . 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" '
                . 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" '
                . 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" '
                . 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" office:version="1.0">'
                . 'xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"'
        . '<office:body>'
        . '<office:spreadsheet>';

    foreach($worksheets as $ws) {
    /// worksheet header
        $buffer .= '<table:table table:name="' . htmlspecialchars($ws->name) . '">';

        $nr = 0;
        $nc = 0;
        foreach($ws->data as $rkey=>$row) {
            if ($rkey > $nr) {
                $nr = $rkey;
            }
            foreach($row as $ckey=>$col) {
                if ($ckey > $nc) {
                    $nc = $ckey;
                }
            }
        }

        for($r=0; $r<=$nr; $r++) {
            $buffer .= '<table:table-row>';
            for($c=0; $c<=$nc; $c++) {
                if (isset($ws->data[$r][$c])) {
                    if ($ws->data[$r][$c]->type == 'date') {
                        $buffer .= '<table:table-cell office:value-type="date" table:style-name="Default" office:date-value="' . strftime('%Y-%m-%dT%H:%M:%S', $ws->data[$r][$c]->value) . '">'
                                 . '<text:p>' . htmlspecialchars($ws->data[$r][$c]->value) . '</text:p>'
                                 . '</table:table-cell>';
                    } else {
                        $buffer .= '<table:table-cell office:value-type="' . $ws->data[$r][$c]->type . '">'
                                 . '<text:p>' . htmlspecialchars($ws->data[$r][$c]->value) . '</text:p>'
                                 . '</table:table-cell>';
                    }
                } else {
                    $buffer .= '<table:table-cell/>';
                }
            }
            $buffer .= '</table:table-row>';
        }
    /// worksheet footer
        $buffer .= '</table:table>';

    }

/// footer
    $buffer .= '</office:spreadsheet></office:body></office:document-content>';

    return $buffer;
}

function get_ods_mimetype() {
    return 'application/vnd.oasis.opendocument.spreadsheet';
}

function get_ods_meta() {
    global $CFG;
    return
        '<?xml version="1.0" encoding="UTF-8"?'. '>'
        . '<office:document-meta '
            . 'xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
            . 'xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" '
            . 'office:version="1.0">'
            . '<office:meta>'
                . '<meta:generator>Moodle ' . $CFG->version. '</meta:generator>'
                . '<meta:initial-creator>Moodle ' . $CFG->version . '</meta:initial-creator>'
                . '<meta:creation-date>' . strftime('%Y-%m-%dT%H:%M:%S') . '</meta:creation-date>'
            . '</office:meta>'
        . '</office:document-meta>';
}

function get_ods_styles() {
    return
        '<?xml version="1.0" encoding="UTF-8"?' . '>'
        . '<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
                . 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" '
                . 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" '
                . 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" '
                . 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" office:version="1.0">'
                . 'xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"'
            . '<office:font-face-decls>'
                . '<style:font-face style:name="Arial Unicode MS" svg:font-family="\'Arial Unicode MS\'" style:font-pitch="variable"/>'
                . '<style:font-face style:name="DejaVu Sans1" svg:font-family="\'DejaVu Sans\'" style:font-pitch="variable"/>'
                . '<style:font-face style:name="HG Mincho Light J" svg:font-family="\'HG Mincho Light J\'" style:font-pitch="variable"/>'
                . '<style:font-face style:name="DejaVu Serif" svg:font-family="\'DejaVu Serif\'" style:font-family-generic="roman" style:font-pitch="variable"/>'
                . '<style:font-face style:name="Thorndale" svg:font-family="Thorndale" style:font-family-generic="roman" style:font-pitch="variable"/>'
                . '<style:font-face style:name="DejaVu Sans" svg:font-family="\'DejaVu Sans\'" style:font-family-generic="swiss" style:font-pitch="variable"/>'
            . '</office:font-face-decls>'
            . '<office:styles>'
                . '<style:style style:name="Default" style:family="table-cell" />'
                . '<style:default-style style:family="paragraph">'
                    . '<style:paragraph-properties fo:hyphenation-ladder-count="no-limit" style:text-autospace="ideograph-alpha" style:punctuation-wrap="hanging" style:line-break="strict" style:tab-stop-distance="0.4925in" style:writing-mode="page"/>'
                    . '<style:text-properties style:use-window-font-color="true" style:font-name="DejaVu Serif" fo:font-size="12pt" fo:language="en" fo:country="US" style:font-name-asian="DejaVu Sans1" style:font-size-asian="12pt" style:language-asian="none" style:country-asian="none" style:font-name-complex="DejaVu Sans1" style:font-size-complex="12pt" style:language-complex="none" style:country-complex="none" fo:hyphenate="false" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"/>'
                . '</style:default-style>'
                . '<style:style style:name="Standard" style:family="paragraph" style:class="text"/>'
                . '<style:style style:name="Text_body" style:display-name="Text body" style:family="paragraph" style:parent-style-name="Standard" style:class="text">'
                    . '<style:paragraph-properties fo:margin-top="0in" fo:margin-bottom="0.0835in"/>'
                . '</style:style>'
                . '<style:style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard" style:next-style-name="Text_body" style:class="text">'
                    . '<style:paragraph-properties fo:margin-top="0.1665in" fo:margin-bottom="0.0835in" fo:keep-with-next="always"/>'
                    . '<style:text-properties style:font-name="DejaVu Sans" fo:font-size="14pt" style:font-name-asian="DejaVu Sans1" style:font-size-asian="14pt" style:font-name-complex="DejaVu Sans1" style:font-size-complex="14pt"/>'
                    . '</style:style>'
                . '<style:style style:name="Heading_1" style:display-name="Heading 1" style:family="paragraph" style:parent-style-name="Heading" style:next-style-name="Text_body" style:class="text" style:default-outline-level="1">'
                    . '<style:text-properties style:font-name="Thorndale" fo:font-size="24pt" fo:font-weight="bold" style:font-name-asian="HG Mincho Light J" style:font-size-asian="24pt" style:font-weight-asian="bold" style:font-name-complex="Arial Unicode MS" style:font-size-complex="24pt" style:font-weight-complex="bold"/>'
                . '</style:style>'
                . '<style:style style:name="Heading_2" style:display-name="Heading 2" style:family="paragraph" style:parent-style-name="Heading" style:next-style-name="Text_body" style:class="text" style:default-outline-level="2">'
                    . '<style:text-properties style:font-name="DejaVu Serif" fo:font-size="18pt" fo:font-weight="bold" style:font-name-asian="DejaVu Sans1" style:font-size-asian="18pt" style:font-weight-asian="bold" style:font-name-complex="DejaVu Sans1" style:font-size-complex="18pt" style:font-weight-complex="bold"/>'
                . '</style:style>'
            . '</office:styles>'
            . '<office:automatic-styles>'
                . '<style:page-layout style:name="pm1">'
                    . '<style:page-layout-properties fo:page-width="8.2673in" fo:page-height="11.6925in" style:num-format="1" style:print-orientation="portrait" fo:margin-top="1in" fo:margin-bottom="1in" fo:margin-left="1.25in" fo:margin-right="1.25in" style:writing-mode="lr-tb" style:footnote-max-height="0in">'
                        . '<style:footnote-sep style:width="0.0071in" style:distance-before-sep="0.0398in" style:distance-after-sep="0.0398in" style:adjustment="left" style:rel-width="25%" style:color="#000000"/>'
                    . '</style:page-layout-properties>'
                    . '<style:header-style/>'
                    . '<style:footer-style/>'
                . '</style:page-layout>'
            . '</office:automatic-styles>'
            . '<office:master-styles>'
                . '<style:master-page style:name="Standard" style:page-layout-name="pm1"/>'
            . '</office:master-styles>'
        . '</office:document-styles>';
}

function get_ods_manifest() {
    return
        '<?xml version="1.0" encoding="UTF-8"?' . '>'
        . '<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">'
        . '<manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>'
        . '<manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>'
        . '<manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>'
        . '<manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>'
        . '</manifest:manifest>';
}
?>