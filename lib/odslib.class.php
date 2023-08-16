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
 * ODS file writer.
 * The xml used here is derived from output of LibreOffice 3.6.4
 *
 * The design is based on Excel writer abstraction by Eloy Lafuente and others.
 *
 * @package   core
 * @copyright 2006 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * ODS workbook abstraction.
 *
 * @package   core
 * @copyright 2006 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSWorkbook {
    protected $worksheets = array();
    protected $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    /**
     * Create one Moodle Worksheet.
     *
     * @param string $name Name of the sheet
     * @return MoodleODSWorksheet
     */
    public function add_worksheet($name = '') {
        $ws = new MoodleODSWorksheet($name, $this->worksheets);
        $this->worksheets[] = $ws;
        return $ws;
    }

    /**
     * Create one Moodle Format.
     *
     * @param array $properties array of properties [name]=value;
     *                          valid names are set_XXXX existing
     *                          functions without the set_ part
     *                          i.e: [bold]=1 for set_bold(1)...Optional!
     * @return MoodleODSFormat
     */
    public function add_format($properties = array()) {
        return new MoodleODSFormat($properties);
    }

    /**
     * Close the Moodle Workbook.
     */
    public function close() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $writer = new MoodleODSWriter($this->worksheets);
        $contents = $writer->get_file_content();

        send_file($contents, $this->filename, 0, 0, true, true, $writer->get_ods_mimetype());
    }

    /**
     * Not required to use.
     * @param string $filename Name of the downloaded file
     */
    public function send($filename) {
        $this->filename = $filename;
    }

}


/**
 * ODS Cell abstraction.
 *
 * @package   core
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSCell {
    public $value;
    public $type;
    public $format;
    public $formula;
}


/**
 * ODS Worksheet abstraction.
 *
 * @package   core
 * @copyright 2006 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSWorksheet {
    public $data = array();
    public $columns = array();
    public $rows = array();
    public $showgrid = true;
    public $name;
    /** @var int Max number of rows in the sheet. */
    public $maxr = 0;
    /** @var int Max number of cols in the sheet. */
    public $maxc = 0;

    /**
     * Constructs one Moodle Worksheet.
     *
     * @param string $name The name of the file
     * @param array $worksheets existing worksheets
     */
    public function __construct($name, array $worksheets) {
        // Replace any characters in the name that Excel cannot cope with.
        $name = strtr($name, '[]*/\?:', '       ');

        if ($name === '') {
            // Name is required!
            $name = 'Sheet'.(count($worksheets)+1);
        }

        $this->name = $name;
    }

    /**
     * Write one string somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $str    The string to write
     * @param mixed   $format The XF format for the cell
     */
    public function write_string($row, $col, $str, $format = null) {
        if (!isset($this->data[$row][$col])) {
            $this->data[$row][$col] = new MoodleODSCell();
        }
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->data[$row][$col]->value = $str;
        $this->data[$row][$col]->type = 'string';
        $this->data[$row][$col]->format = $format;
        $this->data[$row][$col]->formula = null;
    }

    /**
     * Write one number somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param float   $num    The number to write
     * @param mixed   $format The XF format for the cell
     */
    public function write_number($row, $col, $num, $format = null) {
        if (!isset($this->data[$row][$col])) {
            $this->data[$row][$col] = new MoodleODSCell();
        }
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->data[$row][$col]->value = $num;
        $this->data[$row][$col]->type = 'float';
        $this->data[$row][$col]->format = $format;
        $this->data[$row][$col]->formula = null;
    }

    /**
     * Write one url somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $url    The url to write
     * @param mixed   $format The XF format for the cell
     */
    public function write_url($row, $col, $url, $format = null) {
        if (!isset($this->data[$row][$col])) {
            $this->data[$row][$col] = new MoodleODSCell();
        }
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->data[$row][$col]->value = $url;
        $this->data[$row][$col]->type = 'string';
        $this->data[$row][$col]->format = $format;
        $this->data[$row][$col]->formula = null;
    }

    /**
     * Write one date somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $date    The url to write
     * @param mixed   $format The XF format for the cell
     */
    public function write_date($row, $col, $date, $format = null) {
        if (!isset($this->data[$row][$col])) {
            $this->data[$row][$col] = new MoodleODSCell();
        }
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->data[$row][$col]->value = $date;
        $this->data[$row][$col]->type = 'date';
        $this->data[$row][$col]->format = $format;
        $this->data[$row][$col]->formula = null;
    }

    /**
     * Write one formula somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $formula The formula to write
     * @param mixed   $format The XF format for the cell
     */
    public function write_formula($row, $col, $formula, $format = null) {
        if (!isset($this->data[$row][$col])) {
            $this->data[$row][$col] = new MoodleODSCell();
        }
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->data[$row][$col]->formula = $formula;
        $this->data[$row][$col]->format = $format;
        $this->data[$row][$col]->value = null;
        $this->data[$row][$col]->format = null;
    }

    /**
     * Write one blank somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $format The XF format for the cell
     */
    public function write_blank($row, $col, $format = null) {
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        $this->write_string($row, $col, '', $format);
    }

    /**
     * Write anything somewhere in the worksheet,
     * type will be automatically detected.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $token  What we are writing
     * @param mixed   $format The XF format for the cell
     */
    public function write($row, $col, $token, $format = null) {
        // Analyse what are we trying to send.
        if (preg_match("/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $token)) {
            // Match number
            return $this->write_number($row, $col, $token, $format);
        } elseif (preg_match("/^[fh]tt?p:\/\//", $token)) {
            // Match http or ftp URL
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^mailto:/", $token)) {
            // Match mailto:
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^(?:in|ex)ternal:/", $token)) {
            // Match internal or external sheet link
            return $this->write_url($row, $col, $token, '', $format);
        } elseif (preg_match("/^=/", $token)) {
            // Match formula
            return $this->write_formula($row, $col, $token, $format);
        } elseif (preg_match("/^@/", $token)) {
            // Match formula
            return $this->write_formula($row, $col, $token, $format);
        } elseif ($token == '') {
            // Match blank
            return $this->write_blank($row, $col, $format);
        } else {
            // Default: match string
            return $this->write_string($row, $col, $token, $format);
        }
    }

    /**
     * Sets the height (and other settings) of one row.
     *
     * @param integer $row    The row to set
     * @param integer $height Height we are giving to the row (null to set just format without setting the height)
     * @param mixed   $format The optional format we are giving to the row
     * @param bool    $hidden The optional hidden attribute
     * @param integer $level  The optional outline level (0-7)
     */
    public function set_row($row, $height, $format = null, $hidden = false, $level = 0) {
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        if ($level < 0) {
            $level = 0;
        } else if ($level > 7) {
            $level = 7;
        }
        if (!isset($this->rows[$row])) {
            $this->rows[$row] = new stdClass();
        }
        if (isset($height)) {
            $this->rows[$row]->height = $height;
        }
        $this->rows[$row]->format = $format;
        $this->rows[$row]->hidden = $hidden;
        $this->rows[$row]->level  = $level;
    }

    /**
     * Sets the width (and other settings) of one column.
     *
     * @param integer $firstcol first column on the range
     * @param integer $lastcol  last column on the range
     * @param integer $width    width to set (null to set just format without setting the width)
     * @param mixed   $format   The optional format to apply to the columns
     * @param bool    $hidden   The optional hidden attribute
     * @param integer $level    The optional outline level (0-7)
     */
    public function set_column($firstcol, $lastcol, $width, $format = null, $hidden = false, $level = 0) {
        if (is_array($format)) {
            $format = new MoodleODSFormat($format);
        }
        if ($level < 0) {
            $level = 0;
        } else if ($level > 7) {
            $level = 7;
        }
        for($i=$firstcol; $i<=$lastcol; $i++) {
            if (!isset($this->columns[$i])) {
                $this->columns[$i] = new stdClass();
            }
            if (isset($width)) {
                $this->columns[$i]->width = $width*6.15; // 6.15 is a magic constant here!
            }
            $this->columns[$i]->format = $format;
            $this->columns[$i]->hidden = $hidden;
            $this->columns[$i]->level  = $level;
        }
    }

    /**
     * Set the option to hide gridlines on the printed page.
     */
    public function hide_gridlines() {
        // Not implemented - always off.
    }

    /**
     * Set the option to hide gridlines on the worksheet (as seen on the screen).
     */
    public function hide_screen_gridlines() {
        $this->showgrid = false;
    }

    /**
     * Insert a 24bit bitmap image in a worksheet.
     *
     * @param integer $row     The row we are going to insert the bitmap into
     * @param integer $col     The column we are going to insert the bitmap into
     * @param string  $bitmap  The bitmap filename
     * @param integer $x       The horizontal position (offset) of the image inside the cell.
     * @param integer $y       The vertical position (offset) of the image inside the cell.
     * @param integer $scale_x The horizontal scale
     * @param integer $scale_y The vertical scale
     */
    public function insert_bitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1) {
        // Not implemented.
    }

    /**
     * Merges the area given by its arguments.
     *
     * @param integer $first_row First row of the area to merge
     * @param integer $first_col First column of the area to merge
     * @param integer $last_row  Last row of the area to merge
     * @param integer $last_col  Last column of the area to merge
     */
    public function merge_cells($first_row, $first_col, $last_row, $last_col) {
        if ($first_row > $last_row or $first_col > $last_col) {
            return;
        }

        if (!isset($this->data[$first_row][$first_col])) {
            $this->data[$first_row][$first_col] = new MoodleODSCell();
        }

        $this->data[$first_row][$first_col]->merge = array('rows'=>($last_row-$first_row+1), 'columns'=>($last_col-$first_col+1));
    }
}


/**
 * ODS cell format abstraction.
 *
 * @package   core
 * @copyright 2006 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSFormat {
    public $id;
    public $properties = array();

    /**
     * Constructs one Moodle Format.
     *
     * @param array $properties
     */
    public function __construct($properties = array()) {
        static $fid = 1;

        $this->id = $fid++;

        foreach($properties as $property => $value) {
            if (method_exists($this, "set_$property")) {
                $aux = 'set_'.$property;
                $this->$aux($value);
            }
        }
    }

    /**
     * Set the size of the text in the format (in pixels).
     * By default all texts in generated sheets are 10pt.
     *
     * @param integer $size Size of the text (in points)
     */
    public function set_size($size) {
        $this->properties['size'] = $size;
    }

    /**
     * Set weight of the format.
     *
     * @param integer $weight Weight for the text, 0 maps to 400 (normal text),
     *                        1 maps to 700 (bold text). Valid range is: 100-1000.
     *                        It's Optional, default is 1 (bold).
     */
    public function set_bold($weight = 1) {
        if ($weight == 1) {
            $weight = 700;
        }
        $this->properties['bold'] = ($weight > 400);
    }

    /**
     * Set underline of the format.
     *
     * @param integer $underline The value for underline. Possible values are:
     *                           1 => underline, 2 => double underline
     */
    public function set_underline($underline = 1) {
        if ($underline == 1) {
            $this->properties['underline'] = 1;
        } else if ($underline == 2) {
            $this->properties['underline'] = 2;
        } else {
            unset($this->properties['underline']);
        }
    }

    /**
     * Set italic of the format.
     */
    public function set_italic() {
        $this->properties['italic'] = true;
    }

    /**
     * Set strikeout of the format
     */
    public function set_strikeout() {
        $this->properties['strikeout'] = true;
    }

    /**
     * Set outlining of the format.
     */
    public function set_outline() {
        // Not implemented.
    }

    /**
     * Set shadow of the format.
     */
    public function set_shadow() {
        // Not implemented.
    }

    /**
     * Set the script of the text.
     *
     * @param integer $script The value for script type. Possible values are:
     *                        1 => superscript, 2 => subscript
     */
    public function set_script($script) {
        if ($script == 1) {
            $this->properties['super_script'] = true;
            unset($this->properties['sub_script']);

        } else if ($script == 2) {
            $this->properties['sub_script'] = true;
            unset($this->properties['super_script']);

        } else {
            unset($this->properties['sub_script']);
            unset($this->properties['super_script']);
        }
    }

    /**
     * Set color of the format.
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    public function set_color($color) {
        $this->properties['color'] = $this->parse_color($color);
    }

    /**
     * Not used.
     *
     * @param mixed $color
     */
    public function set_fg_color($color) {
        // Not implemented.
    }

    /**
     * Set background color of the cell.
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    public function set_bg_color($color) {
        $this->properties['bg_color'] = $this->parse_color($color);
    }

    /**
     * Set the cell fill pattern.
     *
     * @deprecated use set_bg_color() instead.
     * @param integer
     */
    public function set_pattern($pattern=1) {
        if ($pattern > 0) {
            if (!isset($this->properties['bg_color'])) {
                $this->properties['bg_color'] = $this->parse_color('black');
            }
        } else {
            unset($this->properties['bg_color']);
        }

    }

    /**
     * Set text wrap of the format
     */
    public function set_text_wrap() {
        $this->properties['wrap'] = true;
    }

    /**
     * Set the cell alignment of the format.
     *
     * @param string $location alignment for the cell ('left', 'right', 'justify', etc...)
     */
    public function set_align($location) {
        if (in_array($location, array('left', 'centre', 'center', 'right', 'fill', 'merge', 'justify', 'equal_space'))) {
            $this->set_h_align($location);

        } else if (in_array($location, array('top', 'vcentre', 'vcenter', 'bottom', 'vjustify', 'vequal_space'))) {
            $this->set_v_align($location);
        }
    }

    /**
     * Set the cell horizontal alignment of the format.
     *
     * @param string $location alignment for the cell ('left', 'right', 'justify', etc...)
     */
    public function set_h_align($location) {
        switch ($location) {
            case 'left':
                $this->properties['align'] = 'start';
                break;
            case 'center':
            case 'centre':
            $this->properties['align'] = 'center';
                break;
            case 'right':
                $this->properties['align'] = 'end';
                break;
        }
    }

    /**
     * Set the cell vertical alignment of the format.
     *
     * @param string $location alignment for the cell ('top', 'bottom', 'center', 'justify')
     */
    public function set_v_align($location) {
        switch ($location) {
            case 'top':
                $this->properties['v_align'] = 'top';
                break;
            case 'vcentre':
            case 'vcenter':
            case 'centre':
            case 'center':
                $this->properties['v_align'] = 'middle';
                break;
            default:
                $this->properties['v_align'] = 'bottom';
        }
    }

    /**
     * Set the top border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_top($style) {
        if ($style == 1) {
            $style = 0.2;
        } else if ($style == 2) {
            $style = 0.5;
        } else {
            return;
        }
        $this->properties['border_top'] = $style;
    }

    /**
     * Set the bottom border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_bottom($style) {
        if ($style == 1) {
            $style = 0.2;
        } else if ($style == 2) {
            $style = 0.5;
        } else {
            return;
        }
        $this->properties['border_bottom'] = $style;
    }

    /**
     * Set the left border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_left($style) {
        if ($style == 1) {
            $style = 0.2;
        } else if ($style == 2) {
            $style = 0.5;
        } else {
            return;
        }
        $this->properties['border_left'] = $style;
    }

    /**
     * Set the right border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_right($style) {
        if ($style == 1) {
            $style = 0.2;
        } else if ($style == 2) {
            $style = 0.5;
        } else {
            return;
        }
        $this->properties['border_right'] = $style;
    }

    /**
     * Set cells borders to the same style
     * @param integer $style style to apply for all cell borders. 1 => thin, 2 => thick.
     */
    public function set_border($style) {
        $this->set_top($style);
        $this->set_bottom($style);
        $this->set_left($style);
        $this->set_right($style);
    }

    /**
     * Set the numerical format of the format.
     * It can be date, time, currency, etc...
     *
     * @param mixed $num_format The numeric format
     */
    public function set_num_format($num_format) {

        $numbers = array();

        $numbers[1] = '0';
        $numbers[2] = '0.00';
        $numbers[3] = '#,##0';
        $numbers[4] = '#,##0.00';
        $numbers[11] = '0.00E+00';
        $numbers[12] = '# ?/?';
        $numbers[13] = '# ??/??';
        $numbers[14] = 'mm-dd-yy';
        $numbers[15] = 'd-mmm-yy';
        $numbers[16] = 'd-mmm';
        $numbers[17] = 'mmm-yy';
        $numbers[22] = 'm/d/yy h:mm';
        $numbers[49] = '@';

        if ($num_format !== 0 and in_array($num_format, $numbers)) {
            $flipped = array_flip($numbers);
            $this->properties['num_format'] = $flipped[$num_format];
        }
        if (!isset($numbers[$num_format])) {
            return;
        }

        $this->properties['num_format'] = $num_format;
    }

    /**
     * Standardise colour name.
     *
     * @param mixed $color name of the color (i.e.: 'blue', 'red', etc..), or an integer (range is [8...63]).
     * @return string the RGB color value
     */
    protected function parse_color($color) {
        if (strpos($color, '#') === 0) {
            // No conversion should be needed.
            return $color;
        }

        if ($color > 7 and $color < 53) {
            $numbers = array(
                8  => 'black',
                12 => 'blue',
                16 => 'brown',
                15 => 'cyan',
                23 => 'gray',
                17 => 'green',
                11 => 'lime',
                14 => 'magenta',
                18 => 'navy',
                53 => 'orange',
                33 => 'pink',
                20 => 'purple',
                10 => 'red',
                22 => 'silver',
                9  => 'white',
                13 => 'yellow',
            );
            if (isset($numbers[$color])) {
                $color = $numbers[$color];
            } else {
                $color = 'black';
            }
        }

        $colors = array(
            'aqua'    => '00FFFF',
            'black'   => '000000',
            'blue'    => '0000FF',
            'brown'   => 'A52A2A',
            'cyan'    => '00FFFF',
            'fuchsia' => 'FF00FF',
            'gray'    => '808080',
            'grey'    => '808080',
            'green'   => '00FF00',
            'lime'    => '00FF00',
            'magenta' => 'FF00FF',
            'maroon'  => '800000',
            'navy'    => '000080',
            'orange'  => 'FFA500',
            'olive'   => '808000',
            'pink'    => 'FAAFBE',
            'purple'  => '800080',
            'red'     => 'FF0000',
            'silver'  => 'C0C0C0',
            'teal'    => '008080',
            'white'   => 'FFFFFF',
            'yellow'  => 'FFFF00',
        );

        if (isset($colors[$color])) {
            return('#'.$colors[$color]);
        }

        return('#'.$colors['black']);
    }
}


/**
 * ODS file writer.
 *
 * @package   core
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleODSWriter {
    protected $worksheets;

    public function __construct(array $worksheets) {
        $this->worksheets = $worksheets;
    }

    /**
     * Fetch the file ocntnet for the ODS.
     *
     * @return string
     */
    public function get_file_content() {
        $dir = make_request_directory();
        $filename = $dir . '/result.ods';

        $files = [
                'mimetype'              => [$this->get_ods_mimetype()],
                'content.xml'           => [$this->get_ods_content($this->worksheets)],
                'meta.xml'              => [$this->get_ods_meta()],
                'styles.xml'            => [$this->get_ods_styles()],
                'settings.xml'          => [$this->get_ods_settings()],
                'META-INF/manifest.xml' => [$this->get_ods_manifest()],
            ];

        $packer = get_file_packer('application/zip');
        $packer->archive_to_pathname($files, $filename);

        $contents = file_get_contents($filename);

        remove_dir($dir);
        return $contents;
    }

    protected function get_ods_content() {

        // Find out the size of worksheets and used styles.
        $formats = array();
        $formatstyles = '';
        $rowstyles = '';
        $colstyles = '';

        foreach($this->worksheets as $wsnum=>$ws) {
            foreach($ws->data as $rnum=>$row) {
                if ($rnum > $this->worksheets[$wsnum]->maxr) {
                    $this->worksheets[$wsnum]->maxr = $rnum;
                }
                foreach($row as $cnum=>$cell) {
                    if ($cnum > $this->worksheets[$wsnum]->maxc) {
                        $this->worksheets[$wsnum]->maxc = $cnum;
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
                if ($rnum > $this->worksheets[$wsnum]->maxr) {
                    $this->worksheets[$wsnum]->maxr = $rnum;
                }
                // Define all column styles.
                if (!empty($ws->rows[$rnum])) {
                    $rowstyles .= '<style:style style:name="ws'.$wsnum.'ro'.$rnum.'" style:family="table-row">';
                    if (isset($row->height)) {
                        $rowstyles .= '<style:table-row-properties style:row-height="'.$row->height.'pt"/>';
                    }
                    $rowstyles .= '</style:style>';
                }
            }

            foreach($ws->columns as $cnum=>$col) {
                if (!empty($col->format)) {
                    if (!array_key_exists($col->format->id, $formats)) {
                        $formats[$col->format->id] = $col->format;
                    }
                }
                if ($cnum > $this->worksheets[$wsnum]->maxc) {
                    $this->worksheets[$wsnum]->maxc = $cnum;
                }
                // Define all column styles.
                if (!empty($ws->columns[$cnum])) {
                    $colstyles .= '<style:style style:name="ws'.$wsnum.'co'.$cnum.'" style:family="table-column">';
                    if (isset($col->width)) {
                        $colstyles .= '<style:table-column-properties style:column-width="'.$col->width.'pt"/>';
                    }
                    $colstyles .= '</style:style>';
                }
            }
        }

        foreach($formats as $format) {
            $textprop = '';
            $cellprop = '';
            $parprop  = '';
            $dataformat = '';
            foreach($format->properties as $pname=>$pvalue) {
                switch ($pname) {
                    case 'size':
                        if (!empty($pvalue)) {
                            $textprop .= ' fo:font-size="'.$pvalue.'pt"';
                        }
                        break;
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
                            if ($pvalue == 2) {
                                $textprop .= ' style:text-underline-type="double"';
                            }
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
                    case 'wrap':
                        if ($pvalue) {
                            $cellprop .= ' fo:wrap-option="wrap"';
                        }
                        break;
                    case 'border_top':
                        $cellprop .= ' fo:border-top="'.$pvalue.'pt solid #000000"';
                        break;
                    case 'border_left':
                        $cellprop .= ' fo:border-left="'.$pvalue.'pt solid #000000"';
                        break;
                    case 'border_bottom':
                        $cellprop .= ' fo:border-bottom="'.$pvalue.'pt solid #000000"';
                        break;
                    case 'border_right':
                        $cellprop .= ' fo:border-right="'.$pvalue.'pt solid #000000"';
                        break;
                    case 'num_format':
                        $dataformat = ' style:data-style-name="NUM'.$pvalue.'"';
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
  <style:style style:name="format'.$format->id.'" style:family="table-cell"'.$dataformat.'>'.$textprop.$cellprop.$parprop.'
  </style:style>';
        }

        // The text styles may be breaking older ODF validators.
        $scriptstyles ='
  <style:style style:name="T1" style:family="text">
    <style:text-properties style:text-position="33% 58%"/>
  </style:style>
  <style:style style:name="T2" style:family="text">
    <style:text-properties style:text-position="-33% 58%"/>
  </style:style>
';
        // Header.
        $buffer =
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
                         xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
                         xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
                         xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
                         xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
                         xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
                         xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/"
                         xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
                         xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
                         xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"
                         xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
                         xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
                         xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
                         xmlns:math="http://www.w3.org/1998/Math/MathML"
                         xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
                         xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
                         xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer"
                         xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events"
                         xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                         xmlns:rpt="http://openoffice.org/2005/report"
                         xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
                         xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#"
                         xmlns:tableooo="http://openoffice.org/2009/table"
                         xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
                         xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
                         xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
                         xmlns:css3t="http://www.w3.org/TR/css3-text/" office:version="1.2">
  <office:scripts/>
  <office:font-face-decls>
    <style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss"
                     style:font-pitch="variable"/>
    <style:font-face style:name="Arial Unicode MS" svg:font-family="&apos;Arial Unicode MS&apos;"
                     style:font-family-generic="system" style:font-pitch="variable"/>
    <style:font-face style:name="Tahoma" svg:font-family="Tahoma" style:font-family-generic="system"
                     style:font-pitch="variable"/>
  </office:font-face-decls>
  <office:automatic-styles>';
        $buffer .= $this->get_num_styles();
        $buffer .= '
    <style:style style:name="ta1" style:family="table" style:master-page-name="Standard1">
      <style:table-properties table:display="true"/>
    </style:style>';

        $buffer .= $formatstyles;
        $buffer .= $rowstyles;
        $buffer .= $colstyles;
        $buffer .= $scriptstyles;

         $buffer .= '
  </office:automatic-styles>
  <office:body>
    <office:spreadsheet>
';

        foreach($this->worksheets as $wsnum=>$ws) {

            // Worksheet header.
            $buffer .= '<table:table table:name="' . htmlspecialchars($ws->name, ENT_QUOTES, 'utf-8') . '" table:style-name="ta1">'."\n";

            // Define column properties.
            $level = 0;
            for($c=0; $c<=$ws->maxc; $c++) {
                if (array_key_exists($c, $ws->columns)) {
                    $column = $ws->columns[$c];
                    if ($column->level > $level) {
                        while ($column->level > $level) {
                            $buffer .= '<table:table-column-group>';
                            $level++;
                        }
                    } else if ($column->level < $level) {
                        while ($column->level < $level) {
                            $buffer .= '</table:table-column-group>';
                            $level--;
                        }
                    }
                    $extra = '';
                    if (!empty($column->format)) {
                        $extra .= ' table:default-cell-style-name="format'.$column->format->id.'"';
                    }
                    if ($column->hidden) {
                        $extra .= ' table:visibility="collapse"';
                    }
                    $buffer .= '<table:table-column table:style-name="ws'.$wsnum.'co'.$c.'"'.$extra.'/>'."\n";
                } else {
                    while ($level > 0) {
                        $buffer .= '</table:table-column-group>';
                        $level--;
                    }
                    $buffer .= '<table:table-column/>'."\n";
                }
            }
            while ($level > 0) {
                $buffer .= '</table:table-column-group>';
                $level--;
            }

            // Print all rows.
            $level = 0;
            for($r=0; $r<=$ws->maxr; $r++) {
                if (array_key_exists($r, $ws->rows)) {
                    $row = $ws->rows[$r];
                    if ($row->level > $level) {
                        while ($row->level > $level) {
                            $buffer .= '<table:table-row-group>';
                            $level++;
                        }
                    } else if ($row->level < $level) {
                        while ($row->level < $level) {
                            $buffer .= '</table:table-row-group>';
                            $level--;
                        }
                    }
                    $extra = '';
                    if (!empty($row->format)) {
                        $extra .= ' table:default-cell-style-name="format'.$row->format->id.'"';
                    }
                    if ($row->hidden) {
                        $extra .= ' table:visibility="collapse"';
                    }
                    $buffer .= '<table:table-row table:style-name="ws'.$wsnum.'ro'.$r.'"'.$extra.'>'."\n";
                } else {
                    while ($level > 0) {
                        $buffer .= '</table:table-row-group>';
                        $level--;
                    }
                    $buffer .= '<table:table-row>'."\n";
                }
                for($c=0; $c<=$ws->maxc; $c++) {
                    if (isset($ws->data[$r][$c])) {
                        $cell = $ws->data[$r][$c];
                        $extra = '';
                        if (!empty($cell->format)) {
                            $extra .= ' table:style-name="format'.$cell->format->id.'"';
                        }
                        if (!empty($cell->merge)) {
                            $extra .= ' table:number-columns-spanned="'.$cell->merge['columns'].'" table:number-rows-spanned="'.$cell->merge['rows'].'"';
                        }
                        $pretext = '<text:p>';
                        $posttext = '</text:p>';
                        if (!empty($cell->format->properties['sub_script'])) {
                            $pretext = $pretext.'<text:span text:style-name="T2">';
                            $posttext = '</text:span>'.$posttext;
                        } else if (!empty($cell->format->properties['super_script'])) {
                            $pretext = $pretext.'<text:span text:style-name="T1">';
                            $posttext = '</text:span>'.$posttext;
                        }

                        if (isset($cell->formula)) {
                            $buffer .= '<table:table-cell table:formula="of:'.$cell->formula.'"'.$extra.'></table:table-cell>'."\n";
                        } else if ($cell->type == 'date') {
                            $buffer .= '<table:table-cell office:value-type="date" office:date-value="' . date("Y-m-d\\TH:i:s", $cell->value) . '"'.$extra.'>'
                                     . $pretext . date("Y-m-d\\TH:i:s", $cell->value) . $posttext
                                     . '</table:table-cell>'."\n";
                        } else if ($cell->type == 'float') {
                            $buffer .= '<table:table-cell office:value-type="float" office:value="' . htmlspecialchars($cell->value, ENT_QUOTES, 'utf-8') . '"'.$extra.'>'
                                     . $pretext . htmlspecialchars($cell->value, ENT_QUOTES, 'utf-8') . $posttext
                                     . '</table:table-cell>'."\n";
                        } else if ($cell->type == 'string') {
                            $buffer .= '<table:table-cell office:value-type="string"'.$extra.'>'
                                     . $pretext . htmlspecialchars($cell->value, ENT_QUOTES, 'utf-8') . $posttext
                                     . '</table:table-cell>'."\n";
                        } else {
                            $buffer .= '<table:table-cell office:value-type="string"'.$extra.'>'
                                     . $pretext . '!!Error - unknown type!!' . $posttext
                                     . '</table:table-cell>'."\n";
                        }
                    } else {
                        $buffer .= '<table:table-cell/>'."\n";
                    }
                }
                $buffer .= '</table:table-row>'."\n";
            }
            while ($level > 0) {
                $buffer .= '</table:table-row-group>';
                $level--;
            }
            $buffer .= '</table:table>'."\n";

        }

        // Footer.
        $buffer .= '
    </office:spreadsheet>
  </office:body>
</office:document-content>';

        return $buffer;
    }

    public function get_ods_mimetype() {
        return 'application/vnd.oasis.opendocument.spreadsheet';
    }

    protected function get_ods_settings() {
        $buffer =
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
                          xmlns:xlink="http://www.w3.org/1999/xlink"
                          xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
                          xmlns:ooo="http://openoffice.org/2004/office" office:version="1.2">
    <office:settings>
      <config:config-item-set config:name="ooo:view-settings">
        <config:config-item config:name="VisibleAreaTop" config:type="int">0</config:config-item>
        <config:config-item config:name="VisibleAreaLeft" config:type="int">0</config:config-item>
        <config:config-item-map-indexed config:name="Views">
          <config:config-item-map-entry>
            <config:config-item config:name="ViewId" config:type="string">view1</config:config-item>
            <config:config-item-map-named config:name="Tables">
';
        foreach ($this->worksheets as $ws) {
            $buffer .= '               <config:config-item-map-entry config:name="'.htmlspecialchars($ws->name, ENT_QUOTES, 'utf-8').'">'."\n";
            $buffer .= '                 <config:config-item config:name="ShowGrid" config:type="boolean">'.($ws->showgrid ? 'true' : 'false').'</config:config-item>'."\n";
            $buffer .= '               </config:config-item-map-entry>."\n"';
        }
            $buffer .=
'           </config:config-item-map-named>
          </config:config-item-map-entry>
        </config:config-item-map-indexed>
      </config:config-item-set>
      <config:config-item-set config:name="ooo:configuration-settings">
        <config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item>
      </config:config-item-set>
    </office:settings>
</office:document-settings>';

        return $buffer;
    }
    protected function get_ods_meta() {
        global $CFG, $USER;

        return
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
                      xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/"
                      xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
                      xmlns:ooo="http://openoffice.org/2004/office" xmlns:grddl="http://www.w3.org/2003/g/data-view#"
                      office:version="1.2">
    <office:meta>
        <meta:generator>Moodle '.$CFG->release.'</meta:generator>
        <meta:initial-creator>' . htmlspecialchars(fullname($USER, true), ENT_QUOTES, 'utf-8') . '</meta:initial-creator>
        <meta:creation-date>'.date("Y-m-d\\TH:i:s").'</meta:creation-date>
        <meta:document-statistic meta:table-count="1" meta:cell-count="0" meta:object-count="0"/>
    </office:meta>
</office:document-meta>';
    }

    protected function get_ods_styles() {
        return
'<?xml version="1.0" encoding="UTF-8"?>
<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
                        xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
                        xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
                        xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
                        xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
                        xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/"
                        xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
                        xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
                        xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"
                        xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
                        xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
                        xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
                        xmlns:math="http://www.w3.org/1998/Math/MathML"
                        xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
                        xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
                        xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer"
                        xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events"
                        xmlns:rpt="http://openoffice.org/2005/report"
                        xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
                        xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#"
                        xmlns:tableooo="http://openoffice.org/2009/table"
                        xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
                        xmlns:css3t="http://www.w3.org/TR/css3-text/" office:version="1.2">
    <office:font-face-decls>
        <style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss"
                         style:font-pitch="variable"/>
        <style:font-face style:name="Arial Unicode MS" svg:font-family="&apos;Arial Unicode MS&apos;"
                         style:font-family-generic="system" style:font-pitch="variable"/>
        <style:font-face style:name="Tahoma" svg:font-family="Tahoma" style:font-family-generic="system"
                         style:font-pitch="variable"/>
    </office:font-face-decls>
    <office:styles>
        <style:default-style style:family="table-cell">
            <style:paragraph-properties style:tab-stop-distance="1.25cm"/>
        </style:default-style>
        <number:number-style style:name="N0">
            <number:number number:min-integer-digits="1"/>
        </number:number-style>
        <style:style style:name="Default" style:family="table-cell">
            <style:text-properties style:font-name-asian="Arial Unicode MS" style:font-name-complex="Arial Unicode MS"/>
        </style:style>
        <style:style style:name="Result" style:family="table-cell" style:parent-style-name="Default">
            <style:text-properties fo:font-style="italic" style:text-underline-style="solid"
                                   style:text-underline-width="auto" style:text-underline-color="font-color"
                                   fo:font-weight="bold"/>
        </style:style>
        <style:style style:name="Result2" style:family="table-cell" style:parent-style-name="Result"
                     style:data-style-name="N104"/>
        <style:style style:name="Heading" style:family="table-cell" style:parent-style-name="Default">
            <style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/>
            <style:paragraph-properties fo:text-align="center"/>
            <style:text-properties fo:font-size="16pt" fo:font-style="italic" fo:font-weight="bold"/>
        </style:style>
        <style:style style:name="Heading1" style:family="table-cell" style:parent-style-name="Heading">
            <style:table-cell-properties style:rotation-angle="90"/>
        </style:style>
    </office:styles>
    <office:automatic-styles>
        <style:page-layout style:name="Mpm1">
            <style:page-layout-properties style:writing-mode="lr-tb"/>
            <style:header-style>
                <style:header-footer-properties fo:min-height="0.75cm" fo:margin-left="0cm" fo:margin-right="0cm"
                                                fo:margin-bottom="0.25cm"/>
            </style:header-style>
            <style:footer-style>
                <style:header-footer-properties fo:min-height="0.75cm" fo:margin-left="0cm" fo:margin-right="0cm"
                                                fo:margin-top="0.25cm"/>
            </style:footer-style>
        </style:page-layout>
        <style:page-layout style:name="Mpm2">
            <style:page-layout-properties style:writing-mode="lr-tb"/>
            <style:header-style>
                <style:header-footer-properties fo:min-height="0.75cm" fo:margin-left="0cm" fo:margin-right="0cm"
                                                fo:margin-bottom="0.25cm" fo:border="2.49pt solid #000000"
                                                fo:padding="0.018cm" fo:background-color="#c0c0c0">
                    <style:background-image/>
                </style:header-footer-properties>
            </style:header-style>
            <style:footer-style>
                <style:header-footer-properties fo:min-height="0.75cm" fo:margin-left="0cm" fo:margin-right="0cm"
                                                fo:margin-top="0.25cm" fo:border="2.49pt solid #000000"
                                                fo:padding="0.018cm" fo:background-color="#c0c0c0">
                    <style:background-image/>
                </style:header-footer-properties>
            </style:footer-style>
        </style:page-layout>
    </office:automatic-styles>
    <office:master-styles>
        <style:master-page style:name="Default" style:page-layout-name="Mpm1">
            <style:header>
                <text:p>
                    <text:sheet-name>???</text:sheet-name>
                </text:p>
            </style:header>
            <style:header-left style:display="false"/>
            <style:footer>
                <text:p>Page
                    <text:page-number>1</text:page-number>
                </text:p>
            </style:footer>
            <style:footer-left style:display="false"/>
        </style:master-page>
        <style:master-page style:name="Report" style:page-layout-name="Mpm2">
            <style:header>
                <style:region-left>
                    <text:p>
                        <text:sheet-name>???</text:sheet-name>
                        (<text:title>???</text:title>)
                    </text:p>
                </style:region-left>
                <style:region-right>
                    <text:p><text:date style:data-style-name="N2" text:date-value="2013-01-05">00.00.0000</text:date>,
                        <text:time>00:00:00</text:time>
                    </text:p>
                </style:region-right>
            </style:header>
            <style:header-left style:display="false"/>
            <style:footer>
                <text:p>Page
                    <text:page-number>1</text:page-number>
                    /
                    <text:page-count>99</text:page-count>
                </text:p>
            </style:footer>
            <style:footer-left style:display="false"/>
        </style:master-page>
    </office:master-styles>
</office:document-styles>';
    }

    protected function get_ods_manifest() {
        return
'<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">
 <manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>
 <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="settings.xml" manifest:media-type="text/xml"/>
</manifest:manifest>';
    }

    protected function get_num_styles() {
        return '
        <number:number-style style:name="NUM1">
            <number:number number:decimal-places="0" number:min-integer-digits="1"/>
        </number:number-style>
        <number:number-style style:name="NUM2">
            <number:number number:decimal-places="2" number:min-integer-digits="1"/>
        </number:number-style>
        <number:number-style style:name="NUM3">
            <number:number number:decimal-places="0" number:min-integer-digits="1" number:grouping="true"/>
        </number:number-style>
        <number:number-style style:name="NUM4">
            <number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true"/>
        </number:number-style>
        <number:number-style style:name="NUM11">
            <number:scientific-number number:decimal-places="2" number:min-integer-digits="1"
                                      number:min-exponent-digits="2"/>
        </number:number-style>
        <number:number-style style:name="NUM12">
            <number:fraction number:min-integer-digits="0" number:min-numerator-digits="1"
                             number:min-denominator-digits="1"/>
        </number:number-style>
        <number:number-style style:name="NUM13">
            <number:fraction number:min-integer-digits="0" number:min-numerator-digits="2"
                             number:min-denominator-digits="2"/>
        </number:number-style>
        <number:date-style style:name="NUM14" number:automatic-order="true">
            <number:month number:style="long"/>
            <number:text>/</number:text>
            <number:day number:style="long"/>
            <number:text>/</number:text>
            <number:year/>
        </number:date-style>
        <number:date-style style:name="NUM15">
            <number:day/>
            <number:text>.</number:text>
            <number:month number:textual="true"/>
            <number:text>.</number:text>
            <number:year number:style="long"/>
        </number:date-style>
        <number:date-style style:name="NUM16" number:automatic-order="true">
            <number:month number:textual="true"/>
            <number:text></number:text>
            <number:day number:style="long"/>
        </number:date-style>
        <number:date-style style:name="NUM17">
            <number:month number:style="long"/>
            <number:text>-</number:text>
            <number:day number:style="long"/>
        </number:date-style>
        <number:date-style style:name="NUM22" number:automatic-order="true"
                           number:format-source="language">
            <number:month/>
            <number:text>/</number:text>
            <number:day/>
            <number:text>/</number:text>
            <number:year/>
            <number:text></number:text>
            <number:hours number:style="long"/>
            <number:text>:</number:text>
            <number:minutes number:style="long"/>
            <number:text></number:text>
            <number:am-pm/>
        </number:date-style>
        <number:text-style style:name="NUM49">
            <number:text-content/>
        </number:text-style>
';
    }
}
