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
 * Excel writer abstraction layer.
 *
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\Fill;
use \PhpOffice\PhpSpreadsheet\Style\Font;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Define and operate over one Moodle Workbook.
 *
 * This class acts as a wrapper around another library
 * maintaining Moodle functions isolated from underlying code.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class MoodleExcelWorkbook {
    /** @var PhpSpreadsheet */
    protected $objspreadsheet;

    /** @var string */
    protected $filename;

    /** @var string format type */
    protected $type;

    /**
     * Constructs one Moodle Workbook.
     *
     * @param string $filename The name of the file
     * @param string $type file format type used to be 'Xls or Xlsx' but now only 'Xlsx'
     */
    public function __construct($filename, $type = 'Xlsx') {
        global $CFG;

        $this->objspreadsheet = new Spreadsheet();
        $this->objspreadsheet->removeSheetByIndex(0);

        $this->filename = $filename;

        if (strtolower($type) === 'Xls') {
            debugging('Xls is no longer supported, using Xlsx instead');
            $this->type = 'Xlsx';
        } else {
            $this->type = 'Xlsx';
        }
    }

    /**
     * Create one Moodle Worksheet
     *
     * @param string $name Name of the sheet
     * @return MoodleExcelWorksheet
     */
    public function add_worksheet($name = '') {
        return new MoodleExcelWorksheet($name, $this->objspreadsheet);
    }

    /**
     * Create one cell Format.
     *
     * @param array $properties array of properties [name]=value;
     *                          valid names are set_XXXX existing
     *                          functions without the set_ part
     *                          i.e: [bold]=1 for set_bold(1)...Optional!
     * @return MoodleExcelFormat
     */
    public function add_format($properties = array()) {
        return new MoodleExcelFormat($properties);
    }

    /**
     * Close the Moodle Workbook
     */
    public function close() {
        global $CFG;

        foreach ($this->objspreadsheet->getAllSheets() as $sheet) {
            $sheet->setSelectedCells('A1');
        }
        $this->objspreadsheet->setActiveSheetIndex(0);

        $filename = preg_replace('/\.xlsx?$/i', '', $this->filename);

        $mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $filename = $filename.'.xlsx';

        if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: max-age=10');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: no-cache');
        }

        if (core_useragent::is_ie() || core_useragent::is_edge()) {
            $filename = rawurlencode($filename);
        } else {
            $filename = s($filename);
        }

        header('Content-Type: '.$mimetype);
        header('Content-Disposition: attachment;filename="'.$filename.'"');

        $objwriter = IOFactory::createWriter($this->objspreadsheet, $this->type);
        $objwriter->save('php://output');
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
 * Define and operate over one Worksheet.
 *
 * This class acts as a wrapper around another library
 * maintaining Moodle functions isolated from underlying code.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core
 */
class MoodleExcelWorksheet {
    /** @var Worksheet */
    protected $worksheet;

    /**
     * Constructs one Moodle Worksheet.
     *
     * @param string $name The name of the file
     * @param Spreadsheet $workbook The internal Workbook object we are creating.
     */
    public function __construct($name, Spreadsheet $workbook) {
        // Replace any characters in the name that Excel cannot cope with.
        $name = strtr(trim($name, "'"), '[]*/\?:', '       ');
        // Shorten the title if necessary.
        $name = core_text::substr($name, 0, 31);
        // After the substr, we might now have a single quote on the end.
        $name = trim($name, "'");

        if ($name === '') {
            // Name is required!
            $name = 'Sheet'.($workbook->getSheetCount()+1);
        }

        $this->worksheet = new Worksheet($workbook, $name);
        $this->worksheet->setPrintGridlines(false);

        $workbook->addSheet($this->worksheet);
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
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $this->worksheet->getStyleByColumnAndRow($col, $row + 1)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $this->worksheet->setCellValueExplicitByColumnAndRow($col, $row + 1, $str, DataType::TYPE_STRING);
        $this->apply_format($row, $col, $format);
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
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $this->worksheet->getStyleByColumnAndRow($col, $row + 1)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_GENERAL);
        $this->worksheet->setCellValueExplicitByColumnAndRow($col, $row + 1, $num, DataType::TYPE_NUMERIC);
        $this->apply_format($row, $col, $format);
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
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $this->worksheet->setCellValueByColumnAndRow($col, $row + 1, $url);
        $this->worksheet->getCellByColumnAndRow($col, $row + 1)->getHyperlink()->setUrl($url);
        $this->apply_format($row, $col, $format);
    }

    /**
     * Write one date somewhere in the worksheet.
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param int     $date   The date to write in UNIX timestamp format
     * @param mixed   $format The XF format for the cell
     */
    public function write_date($row, $col, $date, $format = null) {
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $getdate = usergetdate($date);
        $exceldate = Date::FormattedPHPToExcel(
            $getdate['year'],
            $getdate['mon'],
            $getdate['mday'],
            $getdate['hours'],
            $getdate['minutes'],
            $getdate['seconds']
        );

        $this->worksheet->setCellValueByColumnAndRow($col, $row + 1, $exceldate);
        $style = $this->worksheet->getStyleByColumnAndRow($col, $row + 1);
        $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_XLSX22);
        $this->apply_format($row, $col, $format);
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
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $this->worksheet->setCellValueExplicitByColumnAndRow($col, $row + 1, $formula, DataType::TYPE_FORMULA);
        $this->apply_format($row, $col, $format);
    }

    /**
     * Write one blank somewhere in the worksheet.
     *
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $format The XF format for the cell
     */
    public function write_blank($row, $col, $format = null) {
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $this->worksheet->setCellValueByColumnAndRow($col, $row + 1, '');
        $this->apply_format($row, $col, $format);
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
        if ($level < 0) {
            $level = 0;
        } else if ($level > 7) {
            $level = 7;
        }
        if (isset($height)) {
            $this->worksheet->getRowDimension($row + 1)->setRowHeight($height);
        }
        $this->worksheet->getRowDimension($row + 1)->setVisible(!$hidden);
        $this->worksheet->getRowDimension($row + 1)->setOutlineLevel($level);
        $this->apply_row_format($row, $format);
    }

    /**
     * Sets the width (and other settings) of one column.
     *
     * @param integer $firstcol first column on the range
     * @param integer $lastcol  last column on the range
     * @param integer $width    width to set  (null to set just format without setting the width)
     * @param mixed   $format   The optional format to apply to the columns
     * @param bool    $hidden   The optional hidden attribute
     * @param integer $level    The optional outline level (0-7)
     */
    public function set_column($firstcol, $lastcol, $width, $format = null, $hidden = false, $level = 0) {
        if ($level < 0) {
            $level = 0;
        } else if ($level > 7) {
            $level = 7;
        }
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $i = $firstcol + 1;
        while ($i <= $lastcol + 1) {
            if (isset($width)) {
                $this->worksheet->getColumnDimensionByColumn($i)->setWidth($width);
            }
            $this->worksheet->getColumnDimensionByColumn($i)->setVisible(!$hidden);
            $this->worksheet->getColumnDimensionByColumn($i)->setOutlineLevel($level);
            $this->apply_column_format($i, $format);
            $i++;
        }
    }

   /**
    * Set the option to hide grid lines on the printed page.
    */
    public function hide_gridlines() {
        // Not implemented - always off.
    }

   /**
    * Set the option to hide gridlines on the worksheet (as seen on the screen).
    */
    public function hide_screen_gridlines() {
        $this->worksheet->setShowGridlines(false);
    }

   /**
    * Insert an image in a worksheet.
    *
    * @param integer $row     The row we are going to insert the bitmap into
    * @param integer $col     The column we are going to insert the bitmap into
    * @param string  $bitmap  The bitmap filename
    * @param integer $x       The horizontal position (offset) of the image inside the cell.
    * @param integer $y       The vertical position (offset) of the image inside the cell.
    * @param integer $scalex The horizontal scale
    * @param integer $scaley The vertical scale
    */
    public function insert_bitmap($row, $col, $bitmap, $x = 0, $y = 0, $scalex = 1, $scaley = 1) {
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $col += 1;

        $objdrawing = new Drawing();
        $objdrawing->setPath($bitmap);
        $objdrawing->setCoordinates(Coordinate::stringFromColumnIndex($col) . ($row + 1));
        $objdrawing->setOffsetX($x);
        $objdrawing->setOffsetY($y);
        $objdrawing->setWorksheet($this->worksheet);
        if ($scalex != 1) {
            $objdrawing->setResizeProportional(false);
            $objdrawing->getWidth($objdrawing->getWidth() * $scalex);
        }
        if ($scaley != 1) {
            $objdrawing->setResizeProportional(false);
            $objdrawing->setHeight($objdrawing->getHeight() * $scaley);
        }
    }

   /**
    * Merges the area given by its arguments.
    *
    * @param integer $firstrow First row of the area to merge
    * @param integer $firstcol First column of the area to merge
    * @param integer $lastrow  Last row of the area to merge
    * @param integer $lastcol  Last column of the area to merge
    */
    public function merge_cells($firstrow, $firstcol, $lastrow, $lastcol) {
        // For PhpSpreadsheet library, the column indexes start on 1 (instead of 0 as before).
        $this->worksheet->mergeCellsByColumnAndRow($firstcol + 1, $firstrow + 1, $lastcol + 1, $lastrow + 1);
    }

    protected function apply_format($row, $col, $format = null) {
        if (!$format) {
            $format = new MoodleExcelFormat();
        } else if (is_array($format)) {
            $format = new MoodleExcelFormat($format);
        }
        $this->worksheet->getStyleByColumnAndRow($col, $row + 1)->applyFromArray($format->get_format_array());
    }

    protected function apply_column_format($col, $format = null) {
        if (!$format) {
            $format = new MoodleExcelFormat();
        } else if (is_array($format)) {
            $format = new MoodleExcelFormat($format);
        }
        $this->worksheet->getStyle(Coordinate::stringFromColumnIndex($col))->applyFromArray($format->get_format_array());
    }

    protected function apply_row_format($row, $format = null) {
        if (!$format) {
            $format = new MoodleExcelFormat();
        } else if (is_array($format)) {
            $format = new MoodleExcelFormat($format);
        }
        $this->worksheet->getStyle($row + 1)->applyFromArray($format->get_format_array());
    }
}


/**
 * Define and operate over one Format.
 *
 * A big part of this class acts as a wrapper over other libraries
 * maintaining Moodle functions isolated from underlying code.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class MoodleExcelFormat {
    /** @var array */
    protected $format = array();

    /**
     * Constructs one Moodle Format.
     *
     * @param array $properties
     */
    public function __construct($properties = array()) {
        // If we have something in the array of properties, compute them
        foreach($properties as $property => $value) {
            if(method_exists($this,"set_$property")) {
                $aux = 'set_'.$property;
                $this->$aux($value);
            }
        }
    }

    /**
     * Returns standardised Excel format array.
     * @private
     *
     * @return array
     */
    public function get_format_array() {
        return $this->format;
    }
    /**
     * Set the size of the text in the format (in pixels).
     * By default all texts in generated sheets are 10pt.
     *
     * @param integer $size Size of the text (in points)
     */
    public function set_size($size) {
        $this->format['font']['size'] = $size;
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
        $this->format['font']['bold'] = ($weight > 400);
    }

    /**
     * Set underline of the format.
     *
     * @param integer $underline The value for underline. Possible values are:
     *                           1 => underline, 2 => double underline
     */
    public function set_underline($underline) {
        if ($underline == 1) {
            $this->format['font']['underline'] = Font::UNDERLINE_SINGLE;
        } else if ($underline == 2) {
            $this->format['font']['underline'] = Font::UNDERLINE_DOUBLE;
        } else {
            $this->format['font']['underline'] = Font::UNDERLINE_NONE;
        }
    }

    /**
     * Set italic of the format.
     */
    public function set_italic() {
        $this->format['font']['italic'] = true;
    }

    /**
     * Set strikeout of the format.
     */
    public function set_strikeout() {
        $this->format['font']['strikethrough'] = true;
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
            $this->format['font']['superscript'] = true;
        } else if ($script == 2) {
            $this->format['font']['subscript'] = true;
        } else {
            $this->format['font']['superscript'] = false;
            $this->format['font']['subscript'] = false;
        }
    }

    /**
     * Set color of the format. Used to specify the color of the text to be formatted.
     *
     * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63])
     */
    public function set_color($color) {
        $this->format['font']['color']['rgb'] = $this->parse_color($color);
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
            return substr($color, 1);
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
            return($colors[$color]);
        }

        return($colors['black']);
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
        if (!isset($this->format['fill']['fillType'])) {
            $this->format['fill']['fillType'] = Fill::FILL_SOLID;
        }
        $this->format['fill']['color']['rgb'] = $this->parse_color($color);
    }

    /**
     * Set the cell fill pattern.
     *
     * @deprecated use set_bg_color() instead.
     * @param integer
     */
    public function set_pattern($pattern=1) {
        if ($pattern > 0) {
            if (!isset($this->format['fill']['color']['rgb'])) {
                $this->set_bg_color('black');
            }
        } else {
            unset($this->format['fill']['color']['rgb']);
            unset($this->format['fill']['fillType']);
        }
    }

    /**
     * Set text wrap of the format.
     */
    public function set_text_wrap() {
        $this->format['alignment']['wrapText'] = true;
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
                $this->format['alignment']['horizontal'] = Alignment::HORIZONTAL_LEFT;
                break;
            case 'center':
            case 'centre':
                $this->format['alignment']['horizontal'] = Alignment::HORIZONTAL_CENTER;
                break;
            case 'right':
                $this->format['alignment']['horizontal'] = Alignment::HORIZONTAL_RIGHT;
                break;
            case 'justify':
                $this->format['alignment']['horizontal'] = Alignment::HORIZONTAL_JUSTIFY;
                break;
            default:
                $this->format['alignment']['horizontal'] = Alignment::HORIZONTAL_GENERAL;
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
                $this->format['alignment']['vertical'] = Alignment::VERTICAL_TOP;
                break;
            case 'vcentre':
            case 'vcenter':
            case 'centre':
            case 'center':
                $this->format['alignment']['vertical'] = Alignment::VERTICAL_CENTER;
                break;
            case 'vjustify':
            case 'justify':
                $this->format['alignment']['vertical'] = Alignment::VERTICAL_JUSTIFY;
                break;
            default:
                $this->format['alignment']['vertical'] = Alignment::VERTICAL_BOTTOM;
        }
    }

    /**
     * Set the top border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_top($style) {
        if ($style == 1) {
            $this->format['borders']['top']['borderStyle'] = Border::BORDER_THIN;
        } else if ($style == 2) {
            $this->format['borders']['top']['borderStyle'] = Border::BORDER_THICK;
        } else {
            $this->format['borders']['top']['borderStyle'] = Border::BORDER_NONE;
        }
    }

    /**
     * Set the bottom border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_bottom($style) {
        if ($style == 1) {
            $this->format['borders']['bottom']['borderStyle'] = Border::BORDER_THIN;
        } else if ($style == 2) {
            $this->format['borders']['bottom']['borderStyle'] = Border::BORDER_THICK;
        } else {
            $this->format['borders']['bottom']['borderStyle'] = Border::BORDER_NONE;
        }
    }

    /**
     * Set the left border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_left($style) {
        if ($style == 1) {
            $this->format['borders']['left']['borderStyle'] = Border::BORDER_THIN;
        } else if ($style == 2) {
            $this->format['borders']['left']['borderStyle'] = Border::BORDER_THICK;
        } else {
            $this->format['borders']['left']['borderStyle'] = Border::BORDER_NONE;
        }
    }

    /**
     * Set the right border of the format.
     *
     * @param integer $style style for the cell. 1 => thin, 2 => thick
     */
    public function set_right($style) {
        if ($style == 1) {
            $this->format['borders']['right']['borderStyle'] = Border::BORDER_THIN;
        } else if ($style == 2) {
            $this->format['borders']['right']['borderStyle'] = Border::BORDER_THICK;
        } else {
            $this->format['borders']['right']['borderStyle'] = Border::BORDER_NONE;
        }
    }

    /**
     * Set cells borders to the same style.
     *
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
     * @param mixed $numformat The numeric format
     */
    public function set_num_format($numformat) {
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

        if ($numformat !== 0 and in_array($numformat, $numbers)) {
            $this->format['numberFormat']['formatCode'] = $numformat;
        }

        if (!isset($numbers[$numformat])) {
            return;
        }

        $this->format['numberFormat']['formatCode'] = $numbers[$numformat];
    }
}
