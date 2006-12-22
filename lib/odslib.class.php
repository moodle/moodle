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

/*
 * The xml used here is derived from output of KSpread 1.6.1
 *
 * Known problems:
 *  - missing formatting
 *  - write_date() works fine in OOo, but it does not work in KOffice - it knows only date or time but not both :-(
 */

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
        $format = new MoodleODSFormat($properties);
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
    var $columns = array();
    var $rows = array();
    var $name;


    /* Constructs one Moodle Worksheet.
     * @param string $filename The name of the file
     */
    function MoodleODSWorksheet($name) {
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
    function set_row($row, $height, $format = 0, $hidden = false, $level = 0) {
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
    function set_column($firstcol, $lastcol, $width, $format = 0, $hidden = false, $level = 0) {
        for($i=$firstcol; $i<=$lastcol; $i++) {
            $this->columns[$i] = new object();
            $this->columns[$i]->width = $width;
        }
    }

}

/**
* Define and operate over one Format.
*/
class MoodleODSFormat {
    var $formatid;
    var $properties;

    /* Constructs one Moodle Format.
     * @param object $workbook The internal PEAR Workbook onject we are creating
     */
    function MoodleODSFormat($properties = array()) {
        static $fid = 1;
       
        $this->properties = $properties; 
        $this->formatid = $fid++;
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
    

    // find out the size of worksheets and used formats
    $formats = array();
    $formatstyles = '';
    $colstyles = '';

    foreach($worksheets as $wsnum=>$ws) {
        $ws->maxr = 0;
        $ws->maxc = 0;
        foreach($ws->data as $rnum=>$row) {
            if ($rnum > $ws->maxr) {
                $ws->maxr = $rnum;
            }
            foreach($row as $cnum=>$col) {
                if ($cnum > $ws->maxc) {
                    $ws->maxc = $cnum;
                }
            }
        }

        foreach($ws->columns as $cnum=>$col) {
            if ($cnum > $ws->maxc) {
                $ws->maxc = $cnum;
            }
            //define all column styles
            if (!empty($ws->columns[$cnum])) {
                $colstyles .= '
  <style:style style:name="ws'.$wsnum.'co'.$cnum.'" style:family="table-column">
   <style:table-column-properties style:column-width="'.$col->width.'pt"/>
  </style:style>';
            }
        }
    }

/// header
    $buffer =
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xlink="http://www.w3.org/1999/xlink">
 <office:automatic-styles>
  <style:style style:name="ta1" style:family="table" style:master-page-name="Standard1">
   <style:table-properties table:display="true"/>
  </style:style>
  <style:style style:name="date0" style:family="table-cell"/>';

$buffer .= $formatstyles;
$buffer .= $colstyles;

 $buffer .= '
 </office:automatic-styles>
 <office:body>
  <office:spreadsheet>
';

    foreach($worksheets as $wsnum=>$ws) {

    /// worksheet header
        $buffer .= '<table:table table:name="' . htmlspecialchars($ws->name) . '" table:style-name="ta1">'."\n";

        // define column properties
        for($c=0; $c<=$ws->maxc; $c++) {
            if (!empty($ws->columns[$c])) {
                $buffer .= '<table:table-column table:style-name="ws'.$wsnum.'co'.$c.'"/>'."\n";
            } else {
                $buffer .= '<table:table-column/>'."\n";
            }
        }

        // print all rows
        for($r=0; $r<=$ws->maxr; $r++) {
            $buffer .= '<table:table-row>'."\n";
            for($c=0; $c<=$ws->maxc; $c++) {
                if (isset($ws->data[$r][$c])) {
                    if ($ws->data[$r][$c]->type == 'date') {
                        $buffer .= '<table:table-cell office:value-type="date" table:style-name="date0" office:date-value="' . strftime('%Y-%m-%dT%H:%M:%S', $ws->data[$r][$c]->value) . '">'
                                 . '<text:p>' . strftime('%Y-%m-%dT%H:%M:%S', $ws->data[$r][$c]->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else if ($ws->data[$r][$c]->type == 'float') {
                        $buffer .= '<table:table-cell office:value-type="float" office:value="' . htmlspecialchars($ws->data[$r][$c]->value) . '">'
                                 . '<text:p>' . htmlspecialchars($ws->data[$r][$c]->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else if ($ws->data[$r][$c]->type == 'string') {
                        $buffer .= '<table:table-cell office:value-type="string" office:string-value="' . htmlspecialchars($ws->data[$r][$c]->value) . '">'
                                 . '<text:p>' . htmlspecialchars($ws->data[$r][$c]->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else {
                        $buffer .= '<table:table-cell office:value-type="string">'
                                 . '<text:p>!!Error - unknown type!!</text:p>'
                                 . '</table:table-cell>'."\n";
                    }
                } else {
                    $buffer .= '<table:table-cell/>'."\n";
                }
            }
            $buffer .= '</table:table-row>'."\n";
        }
    /// worksheet footer
        $buffer .= '</table:table>'."\n";

    }

/// footer
    $buffer .=
'  </office:spreadsheet>
 </office:body>
</office:document-content>';

    return $buffer;
}

function get_ods_mimetype() {
    return 'application/vnd.oasis.opendocument.spreadsheet';
}

function get_ods_meta() {
    global $CFG, $USER;

    return
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xlink="http://www.w3.org/1999/xlink">
 <office:meta>
  <meta:generator>Moodle '.$CFG->version.'</meta:generator>
  <meta:initial-creator>'.fullname($USER, true).'</meta:initial-creator>
  <meta:editing-cycles>1</meta:editing-cycles>
  <meta:creation-date>'.strftime('%Y-%m-%dT%H:%M:%S').'</meta:creation-date>
  <dc:date>'.strftime('%Y-%m-%dT%H:%M:%S').'</dc:date>
  <dc:creator>'.fullname($USER, true).'</dc:creator>
 </office:meta>
</office:document-meta>';
}

function get_ods_styles() {
    return
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xlink="http://www.w3.org/1999/xlink">
 <office:styles>
  <style:default-style style:family="table-column">
   <style:table-column-properties style:column-width="75pt"/>
  </style:default-style>
  <style:default-style style:family="table-row">
   <style:table-row-properties style:row-height="15pt"/>
  </style:default-style>
  <style:default-style style:family="table-cell">
   <style:table-cell-properties fo:background-color="#ffffff" style:cell-protect="protected" style:vertical-align="middle"/>
   <style:text-properties fo:color="#000000" fo:font-family="Arial" fo:font-size="12pt"/>
  </style:default-style>
 </office:styles>
 <office:automatic-styles>
  <style:page-layout style:name="pm1">
   <style:page-layout-properties fo:margin-bottom="56.6930116pt" fo:margin-left="56.6930116pt" fo:margin-right="56.6930116pt" fo:margin-top="56.6930116pt" fo:page-height="841.89122226pt" fo:page-width="595.2766218pt" style:print="objects charts drawings zero-values" style:print-orientation="portrait"/>
  </style:page-layout>
 </office:automatic-styles>
 <office:master-styles>
  <style:master-page style:name="Standard1" style:page-layout-name="pm1">
   <style:header>
 <text:p>
  <text:sheet-name>???</text:sheet-name>
 </text:p>
</style:header><style:footer>
 <text:p>
  <text:sheet-name>Page </text:sheet-name>
  <text:page-number>1</text:page-number>
 </text:p>
</style:footer>
  </style:master-page>
 </office:master-styles>
</office:document-styles>
';
}

function get_ods_manifest() {
    return
'<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
 <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
</manifest:manifest>';
}
?>