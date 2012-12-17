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
 * Major Contributors:
 *     - Eloy Lafuente (stronk7) {@link  http://contiento.com}
 *     - Petr Skoda (skodak)
 *
 * @package    core
 * @subpackage lib
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The xml used here is derived from output of KSpread 1.6.1
 *
 * Known problems:
 *  - missing formatting
 *  - write_date() works fine in OOo, but it does not work in KOffice - it knows only date or time but not both :-(
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    function add_worksheet($name = '') {
    /// Create the Moodle Worksheet. Returns one pointer to it
        $ws = new MoodleODSWorksheet($name);
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

        $dir = 'ods/'.time();
        make_temp_directory($dir);
        make_temp_directory($dir.'/META-INF');
        $dir = "$CFG->tempdir/$dir";
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

/**
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
        $this->data[$row][$col] = new stdClass();
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
        $this->data[$row][$col] = new stdClass();
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
        $this->data[$row][$col] = new stdClass();
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
        $this->data[$row][$col] = new stdClass();
        $this->data[$row][$col]->value = $date;
        $this->data[$row][$col]->type = 'date';
        $this->data[$row][$col]->format = $format;
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
        // not implement
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
        $this->rows[$row] = new stdClass();
        $this->rows[$row]->height = $height;
        //$this->rows[$row]->format = $format; // TODO: fix and enable
        $this->rows[$row]->hidden = $hidden;
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
            $this->columns[$i] = new stdClass();
            $this->columns[$i]->width = $width;
            //$this->columns[$i]->format = $format; // TODO: fix and enable
            $this->columns[$i]->hidden = $hidden;

        }
    }

    /**
    * Set the option to hide gridlines on the printed page.
    *
    * @access public
    */
    function hide_gridlines() {
        // not implement
    }

    /**
    * Set the option to hide gridlines on the worksheet (as seen on the screen).
    *
    * @access public
    */
    function hide_screen_gridlines() {
        // not implement
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
        // not implement
    }
    /**
    * Merges the area given by its arguments.
    * merging than the normal setAlign('merge').
    *
    * @access public
    * @param integer $first_row First row of the area to merge
    * @param integer $first_col First column of the area to merge
    * @param integer $last_row  Last row of the area to merge
    * @param integer $last_col  Last column of the area to merge
    */
    function merge_cells($first_row, $first_col, $last_row, $last_col) {
        // not implement
    }
}
/**
 * Define and operate over one Format.
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSFormat {
    var $id;
    var $properties;

    /* Constructs one Moodle Format.
     * @param object $workbook The internal PEAR Workbook onject we are creating
     */
    function MoodleODSFormat($properties = array()) {
        static $fid = 1;

        $this->id = $fid++;

        foreach($properties as $property => $value) {
            if(method_exists($this,"set_$property")) {
                $aux = 'set_'.$property;
                $this->$aux($value);
            }
        }
    }

    /* Set weight of the format
     * @param integer $weight Weight for the text, 0 maps to 400 (normal text),
     *                        1 maps to 700 (bold text). Valid range is: 100-1000.
     *                        It's Optional, default is 1 (bold).
     */
    function set_bold($weight = 1) {
        $this->properties['bold'] = $weight;
    }

    /* Set underline of the format
     * @param integer $underline The value for underline. Possible values are:
     *                           1 => underline, 2 => double underline
     */
    function set_underline($underline = 1) {
        $this->properties['underline'] = $underline;
    }

    /* Set italic of the format
     */
    function set_italic() {
        $this->properties['italic'] = true;
    }

    /* Set strikeout of the format
     */
    function set_strikeout() {
        $this->properties['strikeout'] = true;
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
        $this->properties['color'] = $this->_get_color($color);
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
        $this->properties['bg_color'] = $this->_get_color($color);
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
        switch ($location) {
            case 'start':
            case 'left':
                $this->properties['align'] = 'start';
                break;
            case 'center':
                $this->properties['align'] = 'center';
                break;
            case 'end':
            case 'right':
                $this->properties['align'] = 'end';
                break;
            default:
                //ignore the rest == start
        }
    }

    /* Set the cell horizontal alignment of the format
     * @param string $location alignment for the cell ('left', 'right', etc...)
     */
    function set_h_align($location) {
        $this->set_align($location);
    }

    /* Set the cell vertical alignment of the format
     * @param string $location alignment for the cell ('top', 'vleft', etc...)
     */
    function set_v_align($location) {
        switch ($location) {
            case 'top':
                $this->properties['v_align'] = 'top';
                break;
            case 'bottom':
                $this->properties['v_align'] = 'bottom';
                break;
            default:
                //ignore the rest == middle
        }
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

    function _get_color($name_color = '') {
        if (strpos($name_color, '#') === 0) {
            return $name_color; // no conversion needed
        }

        $colors = array('aqua'    => '#00FFFF',
                        'cyan'    => '#00FFFF',
                        'black'   => '#FFFFFF',
                        'blue'    => '#0000FF',
                        'brown'   => '#A52A2A',
                        'magenta' => '#FF00FF',
                        'fuchsia' => '#FF00FF',
                        'gray'    => '#A0A0A0',
                        'grey'    => '#A0A0A0',
                        'green'   => '#00FF00',
                        'lime'    => '#00FF00',
                        'navy'    => '#000080',
                        'orange'  => '#FF8000',
                        'purple'  => '#800080',
                        'red'     => '#FF0000',
                        'silver'  => '#DCDCDC',
                        'white'   => '#FFFFFF',
                        'yellow'  => '#FFFF00');

        if(array_key_exists($name_color, $colors)) {
            return $colors[$name_color];
        } else {
            return false;
        }
    }
}


//=============================
// OpenDocument XML functions
//=============================
function get_ods_content(&$worksheets) {


    // find out the size of worksheets and used styles
    $formats = array();
    $formatstyles = '';
    $rowstyles = '';
    $colstyles = '';

    foreach($worksheets as $wsnum=>$ws) {
        $worksheets[$wsnum]->maxr = 0;
        $worksheets[$wsnum]->maxc = 0;
        foreach($ws->data as $rnum=>$row) {
            if ($rnum > $worksheets[$wsnum]->maxr) {
                $worksheets[$wsnum]->maxr = $rnum;
            }
            foreach($row as $cnum=>$cell) {
                if ($cnum > $worksheets[$wsnum]->maxc) {
                    $worksheets[$wsnum]->maxc = $cnum;
                }
                if (!empty($cell->format)) {
                    if (!array_key_exists($cell->format->id, $formats)) {
                        $formats[$cell->format->id] = $cell->format;
                    }
                }
            }
        }

        foreach($ws->rows as $rnum=>$row) {
            if (!empty($row->format)) {
                if (!array_key_exists($row->format->id, $formats)) {
                    $formats[$row->format->id] = $row->format;
                }
            }
            if ($rnum > $worksheets[$wsnum]->maxr) {
                $worksheets[$wsnum]->maxr = $rnum;
            }
            //define all column styles
            if (!empty($ws->rows[$rnum])) {
                $rowstyles .= '
  <style:style style:name="ws'.$wsnum.'ro'.$rnum.'" style:family="table-row">
   <style:table-row-properties style:row-height="'.$row->height.'pt"/>
  </style:style>';
            }
        }

        foreach($ws->columns as $cnum=>$col) {
            if (!empty($col->format)) {
                if (!array_key_exists($col->format->id, $formats)) {
                    $formats[$col->format->id] = $col->format;
                }
            }
            if ($cnum > $worksheets[$wsnum]->maxc) {
                $worksheets[$wsnum]->maxc = $cnum;
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

    foreach($formats as $format) {
        $textprop = '';
        $cellprop = '';
        $parprop  = '';
        foreach($format->properties as $pname=>$pvalue) {
            switch ($pname) {
                case 'bold':
                    if (!empty($pvalue)) {
                        $textprop .= ' fo:font-weight="bold"';
                    }
                    break;
                case 'italic':
                    if (!empty($pvalue)) {
                        $textprop .= ' fo:font-style="italic"';
                    }
                    break;
                case 'underline':
                    if (!empty($pvalue)) {
                        $textprop .= ' style:text-underline-color="font-color" style:text-underline-style="solid" style:text-underline-width="auto"';
                    }
                    break;
                case 'strikeout':
                    if (!empty($pvalue)) {
                        $textprop .= ' style:text-line-through-style="solid"';
                    }
                    break;
                case 'color':
                    if ($pvalue !== false) {
                        $textprop .= ' fo:color="'.$pvalue.'"';
                    }
                    break;
                case 'bg_color':
                    if ($pvalue !== false) {
                        $cellprop .= ' fo:background-color="'.$pvalue.'"';
                    }
                    break;
                case 'align':
                    $parprop .= ' fo:text-align="'.$pvalue.'"';
                    break;
                case 'v_align':
                    $cellprop .= ' style:vertical-align="'.$pvalue.'"';
                    break;
            }
        }
        if (!empty($textprop)) {
            $textprop = '
   <style:text-properties'.$textprop.'/>';
        }

        if (!empty($cellprop)) {
            $cellprop = '
   <style:table-cell-properties'.$cellprop.'/>';
        }

        if (!empty($parprop)) {
            $parprop = '
   <style:paragraph-properties'.$parprop.'/>';
        }

        $formatstyles .= '
  <style:style style:name="format'.$format->id.'" style:family="table-cell">'.$textprop.$cellprop.$parprop.'
  </style:style>';
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
$buffer .= $rowstyles;
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
            if (array_key_exists($c, $ws->columns)) {
                $extra = '';
                if (!empty($ws->columns[$c]->format)) {
                    $extra .= ' table:default-cell-style-name="format'.$ws->columns[$c]->format->id.'"';
                }
                if ($ws->columns[$c]->hidden) {
                    $extra .= ' table:visibility="collapse"';
                }
                $buffer .= '<table:table-column table:style-name="ws'.$wsnum.'co'.$c.'"'.$extra.'/>'."\n";
            } else {
                $buffer .= '<table:table-column/>'."\n";
            }
        }

        // print all rows
        for($r=0; $r<=$ws->maxr; $r++) {
            if (array_key_exists($r, $ws->rows)) {
                $extra = '';
                if (!empty($ws->rows[$r]->format)) {
                    $extra .= ' table:default-cell-style-name="format'.$ws->rows[$r]->format->id.'"';
                }
                if ($ws->rows[$r]->hidden) {
                    $extra .= ' table:visibility="collapse"';
                }
                $buffer .= '<table:table-row table:style-name="ws'.$wsnum.'ro'.$r.'"'.$extra.'>'."\n";
            } else {
                $buffer .= '<table:table-row>'."\n";
            }
            for($c=0; $c<=$ws->maxc; $c++) {
                if (isset($ws->data[$r][$c])) {
                    $cell = $ws->data[$r][$c];
                    $extra = ' ';
                    if (!empty($cell->format)) {
                        $extra = ' table:style-name="format'.$cell->format->id.'"';
                    }
                    if ($cell->type == 'date') {
                        $buffer .= '<table:table-cell office:value-type="date" table:style-name="date0" office:date-value="' . strftime('%Y-%m-%dT%H:%M:%S', $cell->value) . '"'.$extra.'>'
                                 . '<text:p>' . strftime('%Y-%m-%dT%H:%M:%S', $cell->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else if ($cell->type == 'float') {
                        $buffer .= '<table:table-cell office:value-type="float" office:value="' . htmlspecialchars($cell->value) . '"'.$extra.'>'
                                 . '<text:p>' . htmlspecialchars($cell->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else if ($cell->type == 'string') {
                        $buffer .= '<table:table-cell office:value-type="string" office:string-value="' . htmlspecialchars($cell->value) . '"'.$extra.'>'
                                 . '<text:p>' . htmlspecialchars($cell->value) . '</text:p>'
                                 . '</table:table-cell>'."\n";
                    } else {
                        $buffer .= '<table:table-cell office:value-type="string"'.$extra.'>'
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
