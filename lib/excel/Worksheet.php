<?php
/*
*  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
*
*  The majority of this is _NOT_ my code.  I simply ported it from the
*  PERL Spreadsheet::WriteExcel module.
*
*  The author of the Spreadsheet::WriteExcel module is John McNamara 
*  <jmcnamara@cpan.org>
*
*  I _DO_ maintain this code, and John McNamara has nothing to do with the
*  porting of this code to PHP.  Any questions directly related to this
*  class library should be directed to me.
*
*  License Information:
*
*    Spreadsheet::WriteExcel:  A library for generating Excel Spreadsheets
*    Copyright (C) 2002 Xavier Noguer xnoguer@rezebra.com
*
*    This library is free software; you can redistribute it and/or
*    modify it under the terms of the GNU Lesser General Public
*    License as published by the Free Software Foundation; either
*    version 2.1 of the License, or (at your option) any later version.
*
*    This library is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
*    Lesser General Public License for more details.
*
*    You should have received a copy of the GNU Lesser General Public
*    License along with this library; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once('Parser.php');
require_once('BIFFwriter.php');

/**
* Class for generating Excel Spreadsheets
*
* @author Xavier Noguer <xnoguer@rezebra.com>
* @package Spreadsheet_WriteExcel
*/

class Worksheet extends BIFFwriter
{

    /**
    * Constructor
    *
    * @param string  $name         The name of the new worksheet
    * @param integer $index        The index of the new worksheet
    * @param mixed   &$activesheet The current activesheet of the workbook we belong to
    * @param mixed   &$firstsheet  The first worksheet in the workbook we belong to 
    * @param mixed   &$url_format  The default format for hyperlinks
    * @param mixed   &$parser      The formula parser created for the Workbook
    */
    function Worksheet($name,$index,&$activesheet,&$firstsheet,&$url_format,&$parser)
    {
        $this->BIFFwriter();     // It needs to call its parent's constructor explicitly
        $rowmax                = 65536; // 16384 in Excel 5
        $colmax                = 256;
        $strmax                = 255;
    
        $this->name            = $name;
        $this->index           = $index;
        $this->activesheet     = &$activesheet;
        $this->firstsheet      = &$firstsheet;
        $this->_url_format     = $url_format;
        $this->_parser         = &$parser;
    
        $this->ext_sheets      = array();
        $this->_using_tmpfile  = 1;
        $this->_filehandle     = "";
        $this->fileclosed      = 0;
        $this->offset          = 0;
        $this->xls_rowmax      = $rowmax;
        $this->xls_colmax      = $colmax;
        $this->xls_strmax      = $strmax;
        $this->dim_rowmin      = $rowmax +1;
        $this->dim_rowmax      = 0;
        $this->dim_colmin      = $colmax +1;
        $this->dim_colmax      = 0;
        $this->colinfo         = array();
        $this->_selection      = array(0,0,0,0);
        $this->_panes          = array();
        $this->_active_pane    = 3;
        $this->_frozen         = 0;
        $this->selected        = 0;
    
        $this->_paper_size      = 0x0;
        $this->_orientation     = 0x1;
        $this->_header          = '';
        $this->_footer          = '';
        $this->_hcenter         = 0;
        $this->_vcenter         = 0;
        $this->_margin_head     = 0.50;
        $this->_margin_foot     = 0.50;
        $this->_margin_left     = 0.75;
        $this->_margin_right    = 0.75;
        $this->_margin_top      = 1.00;
        $this->_margin_bottom   = 1.00;
    
        $this->_title_rowmin    = NULL;
        $this->_title_rowmax    = NULL;
        $this->_title_colmin    = NULL;
        $this->_title_colmax    = NULL;
        $this->_print_rowmin    = NULL;
        $this->_print_rowmax    = NULL;
        $this->_print_colmin    = NULL;
        $this->_print_colmax    = NULL;
    
        $this->_print_gridlines = 1;
        $this->_print_headers   = 0;
    
        $this->_fit_page        = 0;
        $this->_fit_width       = 0;
        $this->_fit_height      = 0;
    
        $this->_hbreaks         = array();
        $this->_vbreaks         = array();
    
        $this->_protect         = 0;
        $this->_password        = NULL;
    
        $this->col_sizes        = array();
        $this->row_sizes        = array();
    
        $this->_zoom            = 100;
        $this->_print_scale     = 100;
    
        $this->_initialize();
    }
    
    /**
    * Open a tmp file to store the majority of the Worksheet data. If this fails,
    * for example due to write permissions, store the data in memory. This can be
    * slow for large files.
    */
    function _initialize()
    {
        // Open tmp file for storing Worksheet data
        $fh = tmpfile();
        if ( $fh) {
            // Store filehandle
            $this->_filehandle = $fh;
        }
        else {
            // If tmpfile() fails store data in memory
            $this->_using_tmpfile = 0;
        }
    }
    
    /**
    * Add data to the beginning of the workbook (note the reverse order)
    * and to the end of the workbook.
    *
    * @access public 
    * @see Workbook::store_workbook()
    * @param array $sheetnames The array of sheetnames from the Workbook this 
    *                          worksheet belongs to
    */
    function close($sheetnames)
    {
        $num_sheets = count($sheetnames);

        /***********************************************
        * Prepend in reverse order!!
        */
    
        // Prepend the sheet dimensions
        $this->_store_dimensions();
    
        // Prepend the sheet password
        $this->_store_password();
    
        // Prepend the sheet protection
        $this->_store_protect();
    
        // Prepend the page setup
        $this->_store_setup();
    
        // Prepend the bottom margin
        $this->_store_margin_bottom();
    
        // Prepend the top margin
        $this->_store_margin_top();
    
        // Prepend the right margin
        $this->_store_margin_right();
    
        // Prepend the left margin
        $this->_store_margin_left();
    
        // Prepend the page vertical centering
        $this->store_vcenter();
    
        // Prepend the page horizontal centering
        $this->store_hcenter();
    
        // Prepend the page footer
        $this->store_footer();
    
        // Prepend the page header
        $this->store_header();
    
        // Prepend the vertical page breaks
        $this->_store_vbreak();
    
        // Prepend the horizontal page breaks
        $this->_store_hbreak();
    
        // Prepend WSBOOL
        $this->_store_wsbool();
    
        // Prepend GRIDSET
        $this->_store_gridset();
    
        // Prepend PRINTGRIDLINES
        $this->_store_print_gridlines();
    
        // Prepend PRINTHEADERS
        $this->_store_print_headers();
    
        // Prepend EXTERNSHEET references
        for ($i = $num_sheets; $i > 0; $i--) {
            $sheetname = $sheetnames[$i-1];
            $this->_store_externsheet($sheetname);
        }
    
        // Prepend the EXTERNCOUNT of external references.
        $this->_store_externcount($num_sheets);
    
        // Prepend the COLINFO records if they exist
        if (!empty($this->colinfo)){
            for($i=0; $i < count($this->colinfo); $i++)
            {
                $this->_store_colinfo($this->colinfo[$i]);
            }
            $this->_store_defcol();
        }
    
        // Prepend the BOF record
        $this->_store_bof(0x0010);
    
        /*
        * End of prepend. Read upwards from here.
        ***********************************************/
    
        // Append
        $this->_store_window2();
        $this->_store_zoom();
        if(!empty($this->_panes))
          $this->_store_panes($this->_panes);
        $this->_store_selection($this->_selection);
        $this->_store_eof();
    }
    
    /**
    * Retrieve the worksheet name. This is usefull when creating worksheets
    * without a name.
    *
    * @access public
    * @return string The worksheet's name
    */
    function get_name()
    {
        return($this->name);
    }
    
    /**
    * Retrieves data from memory in one chunk, or from disk in $buffer
    * sized chunks.
    *
    * @return string The data
    */
    function get_data()
    {
        $buffer = 4096;
    
        // Return data stored in memory
        if (isset($this->_data)) {
            $tmp   = $this->_data;
            unset($this->_data);
            $fh    = $this->_filehandle;
            if ($this->_using_tmpfile) {
                fseek($fh, 0);
            }
            return($tmp);
        }
        // Return data stored on disk
        if ($this->_using_tmpfile) {
            if ($tmp = fread($this->_filehandle, $buffer)) {
                return($tmp);
            }
        }
    
        // No data to return
        return('');
    }
    
    /**
    * Set this worksheet as a selected worksheet, i.e. the worksheet has its tab
    * highlighted.
    *
    * @access public
    */
    function select()
    {
        $this->selected = 1;
    }
    
    /**
    * Set this worksheet as the active worksheet, i.e. the worksheet that is
    * displayed when the workbook is opened. Also set it as selected.
    *
    * @access public
    */
    function activate()
    {
        $this->selected = 1;
        $this->activesheet =& $this->index;
    }
    
    /**
    * Set this worksheet as the first visible sheet. This is necessary
    * when there are a large number of worksheets and the activated
    * worksheet is not visible on the screen.
    *
    * @access public
    */
    function set_first_sheet()
    {
        $this->firstsheet = $this->index;
    }
    
    /**
    * Set the worksheet protection flag to prevent accidental modification and to
    * hide formulas if the locked and hidden format properties have been set.
    *
    * @access public
    * @param string $password The password to use for protecting the sheet.
    */
    function protect($password)
    {
        $this->_protect   = 1;
        $this->_password  = $this->_encode_password($password);
    }
    
    /**
    * Set the width of a single column or a range of columns.
    *
    * @access public
    * @see _store_colinfo()
    * @param integer $firstcol first column on the range
    * @param integer $lastcol  last column on the range
    * @param integer $width    width to set
    * @param mixed   $format   The optional XF format to apply to the columns
    * @param integer $hidden   The optional hidden atribute
    */
    function set_column($firstcol, $lastcol, $width, $format = 0, $hidden = 0)
    {
        $this->colinfo[] = array($firstcol, $lastcol, $width, $format, $hidden);

        // Set width to zero if column is hidden
        $width = ($hidden) ? 0 : $width;
    
        for($col = $firstcol; $col <= $lastcol; $col++) {
            $this->col_sizes[$col] = $width;
        }
    }
    
    /**
    * Set which cell or cells are selected in a worksheet
    *
    * @access public
    * @param integer $first_row    first row in the selected quadrant
    * @param integer $first_column first column in the selected quadrant
    * @param integer $last_row     last row in the selected quadrant
    * @param integer $last_column  last column in the selected quadrant
    * @see _store_selection()
    */
    function set_selection($first_row,$first_column,$last_row,$last_column)
    {
        $this->_selection = array($first_row,$first_column,$last_row,$last_column);
    }
    
    /**
    * Set panes and mark them as frozen.
    *
    * @access public
    * @param array $panes This is the only parameter received and is composed of the following:
    *                     0 => Vertical split position,
    *                     1 => Horizontal split position
    *                     2 => Top row visible
    *                     3 => Leftmost column visible
    *                     4 => Active pane
    */
    function freeze_panes($panes)
    {
        $this->_frozen = 1;
        $this->_panes  = $panes;
    }
    
    /**
    * Set panes and mark them as unfrozen.
    *
    * @access public
    * @param array $panes This is the only parameter received and is composed of the following:
    *                     0 => Vertical split position,
    *                     1 => Horizontal split position
    *                     2 => Top row visible
    *                     3 => Leftmost column visible
    *                     4 => Active pane
    */
    function thaw_panes($panes)
    {
        $this->_frozen = 0;
        $this->_panes  = $panes;
    }
    
    /**
    * Set the page orientation as portrait.
    *
    * @access public
    */
    function set_portrait()
    {
        $this->_orientation = 1;
    }
    
    /**
    * Set the page orientation as landscape.
    *
    * @access public
    */
    function set_landscape()
    {
        $this->_orientation = 0;
    }
    
    /**
    * Set the paper type. Ex. 1 = US Letter, 9 = A4
    *
    * @access public
    * @param integer $size The type of paper size to use
    */
    function set_paper($size = 0)
    {
        $this->_paper_size = $size;
    }
    
    
    /**
    * Set the page header caption and optional margin.
    *
    * @access public
    * @param string $string The header text
    * @param float  $margin optional head margin in inches.
    */
    function set_header($string,$margin = 0.50)
    {
        if (strlen($string) >= 255) {
            //carp 'Header string must be less than 255 characters';
            return;
        }
        $this->_header      = $string;
        $this->_margin_head = $margin;
    }
    
    /**
    * Set the page footer caption and optional margin.
    *
    * @access public
    * @param string $string The footer text
    * @param float  $margin optional foot margin in inches.
    */
    function set_footer($string,$margin = 0.50)
    {
        if (strlen($string) >= 255) {
            //carp 'Footer string must be less than 255 characters';
            return;
        }
        $this->_footer      = $string;
        $this->_margin_foot = $margin;
    }
    
    /**
    * Center the page horinzontally.
    *
    * @access public
    * @param integer $center the optional value for centering. Defaults to 1 (center).
    */
    function center_horizontally($center = 1)
    {
        $this->_hcenter = $center;
    }
    
    /**
    * Center the page horinzontally.
    *
    * @access public
    * @param integer $center the optional value for centering. Defaults to 1 (center).
    */
    function center_vertically($center = 1)
    {
        $this->_vcenter = $center;
    }
    
    /**
    * Set all the page margins to the same value in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margins($margin)
    {
        $this->set_margin_left($margin);
        $this->set_margin_right($margin);
        $this->set_margin_top($margin);
        $this->set_margin_bottom($margin);
    }
    
    /**
    * Set the left and right margins to the same value in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margins_LR($margin)
    {
        $this->set_margin_left($margin);
        $this->set_margin_right($margin);
    }
    
    /**
    * Set the top and bottom margins to the same value in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margins_TB($margin)
    {
        $this->set_margin_top($margin);
        $this->set_margin_bottom($margin);
    }
    
    /**
    * Set the left margin in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margin_left($margin = 0.75)
    {
        $this->_margin_left = $margin;
    }
    
    /**
    * Set the right margin in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margin_right($margin = 0.75)
    {
        $this->_margin_right = $margin;
    }
    
    /**
    * Set the top margin in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margin_top($margin = 1.00)
    {
        $this->_margin_top = $margin;
    }
    
    /**
    * Set the bottom margin in inches.
    *
    * @access public
    * @param float $margin The margin to set in inches
    */
    function set_margin_bottom($margin = 1.00)
    {
        $this->_margin_bottom = $margin;
    }
    
    /**
    * Set the rows to repeat at the top of each printed page. See also the
    * _store_name_xxxx() methods in Workbook.php
    *
    * @access public
    * @param integer $first_row First row to repeat
    * @param integer $last_row  Last row to repeat. Optional.
    */
    function repeat_rows($first_row, $last_row = NULL)
    {
        $this->_title_rowmin  = $first_row;
        if(isset($last_row)) { //Second row is optional
            $this->_title_rowmax  = $last_row;
        }
        else {
            $this->_title_rowmax  = $first_row;
        }
    }
    
    /**
    * Set the columns to repeat at the left hand side of each printed page.
    * See also the _store_names() methods in Workbook.php
    *
    * @access public
    * @param integer $first_col First column to repeat
    * @param integer $last_col  Last column to repeat. Optional.
    */
    function repeat_columns($first_col, $last_col = NULL)
    {
        $this->_title_colmin  = $first_col;
        if(isset($last_col)) { // Second col is optional
            $this->_title_colmax  = $last_col;
        }
        else {
            $this->_title_colmax  = $first_col;
        }
    }
    
    /**
    * Set the area of each worksheet that will be printed.
    *
    * @access public
    * @see Workbook::_store_names()
    * @param integer $first_row First row of the area to print
    * @param integer $first_col First column of the area to print
    * @param integer $last_row  Last row of the area to print
    * @param integer $last_col  Last column of the area to print
    */
    function print_area($first_row, $first_col, $last_row, $last_col)
    {
        $this->_print_rowmin  = $first_row;
        $this->_print_colmin  = $first_col;
        $this->_print_rowmax  = $last_row;
        $this->_print_colmax  = $last_col;
    }
    
    
    /**
    * Set the option to hide gridlines on the printed page. 
    *
    * @access public
    * @see _store_print_gridlines(), _store_gridset()
    */
    function hide_gridlines()
    {
        $this->_print_gridlines = 0;
    }
    
    /**
    * Set the option to print the row and column headers on the printed page.
    * See also the _store_print_headers() method below.
    *
    * @access public
    * @see _store_print_headers()
    * @param integer $print Whether to print the headers or not. Defaults to 1 (print).
    */
    function print_row_col_headers($print = 1)
    {
        $this->_print_headers = $print;
    }
    
    /**
    * Store the vertical and horizontal number of pages that will define the
    * maximum area printed. It doesn't seem to work with OpenOffice.
    *
    * @access public
    * @param  integer $width  Maximun width of printed area in pages
    * @param  integer $heigth Maximun heigth of printed area in pages
    * @see set_print_scale()
    */
    function fit_to_pages($width, $height)
    {
        $this->_fit_page      = 1;
        $this->_fit_width     = $width;
        $this->_fit_height    = $height;
    }
    
    /**
    * Store the horizontal page breaks on a worksheet (for printing).
    * The breaks represent the row after which the break is inserted.
    *
    * @access public
    * @param array $breaks Array containing the horizontal page breaks
    */
    function set_h_pagebreaks($breaks)
    {
        foreach($breaks as $break) {
            array_push($this->_hbreaks,$break);
        }
    }
    
    /**
    * Store the vertical page breaks on a worksheet (for printing).
    * The breaks represent the column after which the break is inserted.
    *
    * @access public
    * @param array $breaks Array containing the vertical page breaks
    */
    function set_v_pagebreaks($breaks)
    {
        foreach($breaks as $break) {
            array_push($this->_vbreaks,$break);
        }
    }
    
    
    /**
    * Set the worksheet zoom factor.
    *
    * @access public
    * @param integer $scale The zoom factor
    */
    function set_zoom($scale = 100)
    {
        // Confine the scale to Excel's range
        if ($scale < 10 or $scale > 400) {
            //carp "Zoom factor $scale outside range: 10 <= zoom <= 400";
            $scale = 100;
        }
    
        $this->_zoom = floor($scale);
    }
    
    /**
    * Set the scale factor for the printed page. 
    * It turns off the "fit to page" option
    *
    * @access public
    * @param integer $scale The optional scale factor. Defaults to 100
    */
    function set_print_scale($scale = 100)
    {
        // Confine the scale to Excel's range
        if ($scale < 10 or $scale > 400)
        {
            // REPLACE THIS FOR A WARNING
            die("Print scale $scale outside range: 10 <= zoom <= 400");
            $scale = 100;
        }
    
        // Turn off "fit to page" option
        $this->_fit_page    = 0;
    
        $this->_print_scale = floor($scale);
    }
    
    /**
    * Map to the appropriate write method acording to the token recieved.
    *
    * @access public
    * @param integer $row    The row of the cell we are writing to
    * @param integer $col    The column of the cell we are writing to
    * @param mixed   $token  What we are writing
    * @param mixed   $format The optional format to apply to the cell
    */
    function write($row, $col, $token, $format = 0)
    {
        // Check for a cell reference in A1 notation and substitute row and column
        /*if ($_[0] =~ /^\D/) {
            @_ = $this->_substitute_cellref(@_);
    }*/
    
        /*
        # Match an array ref.
        if (ref $token eq "ARRAY") {
            return $this->write_row(@_);
    }*/
    
        // Match number
        if (preg_match("/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/",$token)) {
            return $this->write_number($row,$col,$token,$format);
        }
        // Match http or ftp URL
        elseif (preg_match("/^[fh]tt?p:\/\//",$token)) {
            return $this->write_url($row, $col, $token, $format);
        }
        // Match mailto:
        elseif (preg_match("/^mailto:/",$token)) {
            return $this->write_url($row, $col, $token, $format);
        }
        // Match internal or external sheet link
        elseif (preg_match("/^(?:in|ex)ternal:/",$token)) {
            return $this->write_url($row, $col, $token, $format);
        }
        // Match formula
        elseif (preg_match("/^=/",$token)) {
            return $this->write_formula($row, $col, $token, $format);
        }
        // Match formula
        elseif (preg_match("/^@/",$token)) {
            return $this->write_formula($row, $col, $token, $format);
        }
        // Match blank
        elseif ($token == '') {
            return $this->write_blank($row,$col,$format);
        }
        // Default: match string
        else {
            return $this->write_string($row,$col,$token,$format);
        }
    }
 
    /**
    * Returns an index to the XF record in the workbook
    *
    * @param mixed $format The optional XF format
    * @return integer The XF record index
    */
    function _XF(&$format)
    {
        if($format != 0)
        {
            return($format->get_xf_index());
        }
        else
        {
            return(0x0F);
        }
    }
    
    
    /******************************************************************************
    *******************************************************************************
    *
    * Internal methods
    */
    
    
    /**
    * Store Worksheet data in memory using the parent's class append() or to a
    * temporary file, the default.
    *
    * @param string $data The binary data to append
    */
    function _append($data)
    {
        if ($this->_using_tmpfile)
        {
            // Add CONTINUE records if necessary
            if (strlen($data) > $this->_limit) {
                $data = $this->_add_continue($data);
            }
            fwrite($this->_filehandle,$data);
            $this->_datasize += strlen($data);
        }
        else {
            parent::_append($data);
        }
    }
    
    /**
    * Substitute an Excel cell reference in A1 notation for  zero based row and
    * column values in an argument list.
    *
    * Ex: ("A4", "Hello") is converted to (3, 0, "Hello").
    *
    * @param string $cell The cell reference. Or range of cells.
    * @return array
    */
    function _substitute_cellref($cell)
    {
        $cell = strtoupper($cell);
    
        // Convert a column range: 'A:A' or 'B:G'
        if (preg_match("/([A-I]?[A-Z]):([A-I]?[A-Z])/",$cell,$match)) {
            list($no_use, $col1) =  $this->_cell_to_rowcol($match[1] .'1'); // Add a dummy row
            list($no_use, $col2) =  $this->_cell_to_rowcol($match[2] .'1'); // Add a dummy row
            return(array($col1, $col2));
        }
    
        // Convert a cell range: 'A1:B7'
        if (preg_match("/\$?([A-I]?[A-Z]\$?\d+):\$?([A-I]?[A-Z]\$?\d+)/",$cell,$match)) {
            list($row1, $col1) =  $this->_cell_to_rowcol($match[1]);
            list($row2, $col2) =  $this->_cell_to_rowcol($match[2]);
            return(array($row1, $col1, $row2, $col2));
        }
    
        // Convert a cell reference: 'A1' or 'AD2000'
        if (preg_match("/\$?([A-I]?[A-Z]\$?\d+)/",$cell)) {
            list($row1, $col1) =  $this->_cell_to_rowcol($match[1]);
            return(array($row1, $col1));
        }
    
        die("Unknown cell reference $cell ");
    }
    
    /**
    * Convert an Excel cell reference in A1 notation to a zero based row and column
    * reference; converts C1 to (0, 2).
    *
    * @param string $cell The cell reference.
    * @return array containing (row, column)
    */
    function _cell_to_rowcol($cell)
    {
        preg_match("/\$?([A-I]?[A-Z])\$?(\d+)/",$cell,$match);
        $col     = $match[1];
        $row     = $match[2];
    
        // Convert base26 column string to number
        $chars = str_split($col);
        $expn  = 0;
        $col   = 0;
    
        while ($chars) {
            $char = array_pop($chars);        // LS char first
            $col += (ord($char) -ord('A') +1) * pow(26,$expn);
            $expn++;
        }
    
        // Convert 1-index to zero-index
        $row--;
        $col--;
    
        return(array($row, $col));
    }
    
    /**
    * Based on the algorithm provided by Daniel Rentz of OpenOffice.
    *
    * @param string $plaintext The password to be encoded in plaintext.
    * @return string The encoded password
    */
    function _encode_password($plaintext)
    {
        $password = 0x0000;
        $i        = 1;       // char position
 
        // split the plain text password in its component characters
        $chars = preg_split('//', $plaintext, -1, PREG_SPLIT_NO_EMPTY);
        foreach($chars as $char)
        {
            $value     = ord($char) << $i;   // shifted ASCII value 
            $bit_16    = $value & 0x8000;    // the bit 16
            $bit_16  >>= 15;                 // 0x0000 or 0x0001
            //$bit_17    = $value & 0x00010000;
            //$bit_17  >>= 15;
            $value    &= 0x7fff;             // first 15 bits
            $password ^= ($value | $bit_16);
            //$password ^= ($value | $bit_16 | $bit_17);
            $i++;
        }
    
        $password ^= strlen($plaintext);
        $password ^= 0xCE4B;

        return($password);
    }
    
    /******************************************************************************
    *******************************************************************************
    *
    * BIFF RECORDS
    */
    
    
    /**
    * Write a double to the specified row and column (zero indexed).
    * An integer can be written as a double. Excel will display an
    * integer. $format is optional.
    *
    * Returns  0 : normal termination
    *         -2 : row or column out of range
    *
    * @access public
    * @param integer $row    Zero indexed row
    * @param integer $col    Zero indexed column
    * @param float   $num    The number to write
    * @param mixed   $format The optional XF format
    */
    function write_number($row, $col, $num, $format = 0)
    {
        $record    = 0x0203;                 // Record identifier
        $length    = 0x000E;                 // Number of bytes to follow
        $xf        = $this->_XF($format);    // The cell format
    
        // Check that row and col are valid and store max and min values
        if ($row >= $this->xls_rowmax)
        {
            return(-2);
        }
        if ($col >= $this->xls_colmax)
        {
            return(-2);
        }
        if ($row <  $this->dim_rowmin) 
        {
            $this->dim_rowmin = $row;
        }
        if ($row >  $this->dim_rowmax) 
        {
            $this->dim_rowmax = $row;
        }
        if ($col <  $this->dim_colmin) 
        {
            $this->dim_colmin = $col;
        }
        if ($col >  $this->dim_colmax) 
        {
            $this->dim_colmax = $col;
        }
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("vvv", $row, $col, $xf);
        $xl_double = pack("d",   $num);
        if ($this->_byte_order) // if it's Big Endian
        {
            $xl_double = strrev($xl_double);
        }
    
        $this->_append($header.$data.$xl_double);
        return(0);
    }
    
    /**
    * Write a string to the specified row and column (zero indexed).
    * NOTE: there is an Excel 5 defined limit of 255 characters.
    * $format is optional.
    * Returns  0 : normal termination
    *         -1 : insufficient number of arguments
    *         -2 : row or column out of range
    *         -3 : long string truncated to 255 chars
    *
    * @access public
    * @param integer $row    Zero indexed row
    * @param integer $col    Zero indexed column
    * @param string  $str    The string to write
    * @param mixed   $format The XF format for the cell
    */
    function write_string($row, $col, $str, $format = 0)
    {
        $strlen    = strlen($str);
        $record    = 0x0204;                   // Record identifier
        $length    = 0x0008 + $strlen;         // Bytes to follow
        $xf        = $this->_XF($format);      // The cell format
        
        $str_error = 0;
    
        // Check that row and col are valid and store max and min values
        if ($row >= $this->xls_rowmax) 
        {
            return(-2);
        }
        if ($col >= $this->xls_colmax) 
        {
            return(-2);
        }
        if ($row <  $this->dim_rowmin) 
        {
            $this->dim_rowmin = $row;
        }
        if ($row >  $this->dim_rowmax) 
        {
            $this->dim_rowmax = $row;
        }
        if ($col <  $this->dim_colmin) 
        {
            $this->dim_colmin = $col;
        }
        if ($col >  $this->dim_colmax) 
        {
            $this->dim_colmax = $col;
        }
    
        if ($strlen > $this->xls_strmax)  // LABEL must be < 255 chars
        {
            $str       = substr($str, 0, $this->xls_strmax);
            $length    = 0x0008 + $this->xls_strmax;
            $strlen    = $this->xls_strmax;
            $str_error = -3;
        }
    
        $header    = pack("vv",   $record, $length);
        $data      = pack("vvvv", $row, $col, $xf, $strlen);
        $this->_append($header.$data.$str);
        return($str_error);
    }
 
    /**
    * Writes a note associated with the cell given by the row and column.
    * NOTE records don't have a length limit.
    *
    * @access public
    * @param integer $row    Zero indexed row
    * @param integer $col    Zero indexed column
    * @param string  $note   The note to write
    */
    function write_note($row, $col, $note)
    {
        $note_length    = strlen($note);
        $record         = 0x001C;                // Record identifier
        $max_length     = 2048;                  // Maximun length for a NOTE record
        //$length      = 0x0006 + $note_length;    // Bytes to follow

        // Check that row and col are valid and store max and min values
        if ($row >= $this->xls_rowmax) 
        {
            return(-2);
        }
        if ($col >= $this->xls_colmax) 
        {
            return(-2);
        }
        if ($row <  $this->dim_rowmin) 
        {
            $this->dim_rowmin = $row;
        }
        if ($row >  $this->dim_rowmax) 
        {
            $this->dim_rowmax = $row;
        }
        if ($col <  $this->dim_colmin) 
        {
            $this->dim_colmin = $col;
        }
        if ($col >  $this->dim_colmax) 
        {
            $this->dim_colmax = $col;
        }
 
        // Length for this record is no more than 2048 + 6
        $length    = 0x0006 + min($note_length, 2048);
        $header    = pack("vv",   $record, $length);
        $data      = pack("vvv", $row, $col, $note_length);
        $this->_append($header.$data.substr($note, 0, 2048));

        for($i = $max_length; $i < $note_length; $i += $max_length)
        {
            $chunk  = substr($note, $i, $max_length);
            $length = 0x0006 + strlen($chunk);
            $header = pack("vv",   $record, $length);
            $data   = pack("vvv", -1, 0, strlen($chunk));
            $this->_append($header.$data.$chunk);
        }
        return(0);
    }
 
    /**
    * Write a blank cell to the specified row and column (zero indexed).
    * A blank cell is used to specify formatting without adding a string
    * or a number.
    *
    * A blank cell without a format serves no purpose. Therefore, we don't write
    * a BLANK record unless a format is specified. This is mainly an optimisation
    * for the write_row() and write_col() methods.
    *
    * Returns  0 : normal termination (including no format)
    *         -1 : insufficient number of arguments
    *         -2 : row or column out of range
    *
    * @access public
    * @param integer $row    Zero indexed row
    * @param integer $col    Zero indexed column
    * @param mixed   $format The XF format
    */
    function write_blank($row, $col, $format = 0)
    {
        // Don't write a blank cell unless it has a format
        if ($format == 0)
        {
            return(0);
        }
    
        $record    = 0x0201;                 // Record identifier
        $length    = 0x0006;                 // Number of bytes to follow
        $xf        = $this->_XF($format);    // The cell format
    
        // Check that row and col are valid and store max and min values
        if ($row >= $this->xls_rowmax) 
        {
            return(-2);
        }
        if ($col >= $this->xls_colmax) 
        {
            return(-2);
        }
        if ($row <  $this->dim_rowmin) 
        {
            $this->dim_rowmin = $row;
        }
        if ($row >  $this->dim_rowmax) 
        {
            $this->dim_rowmax = $row;
        }
        if ($col <  $this->dim_colmin) 
        {
            $this->dim_colmin = $col;
        }
        if ($col >  $this->dim_colmax) 
        {
            $this->dim_colmax = $col;
        }
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("vvv", $row, $col, $xf);
        $this->_append($header.$data);
        return 0;
    }
 
    /**
    * Write a formula to the specified row and column (zero indexed).
    * The textual representation of the formula is passed to the parser in
    * Parser.php which returns a packed binary string.
    *
    * Returns  0 : normal termination
    *         -2 : row or column out of range
    *
    * @access public
    * @param integer $row     Zero indexed row
    * @param integer $col     Zero indexed column
    * @param string  $formula The formula text string
    * @param mixed   $format  The optional XF format
    */
    function write_formula($row, $col, $formula, $format = 0)
    {
        $record    = 0x0006;     // Record identifier
    
        // Excel normally stores the last calculated value of the formula in $num.
        // Clearly we are not in a position to calculate this a priori. Instead
        // we set $num to zero and set the option flags in $grbit to ensure
        // automatic calculation of the formula when the file is opened.
        //
        $xf        = $this->_XF($format); // The cell format
        $num       = 0x00;                // Current value of formula
        $grbit     = 0x03;                // Option flags
        $chn       = 0x0000;              // Must be zero
    
    
        // Check that row and col are valid and store max and min values
        if ($row >= $this->xls_rowmax)
        {
            return(-2);
        }
        if ($col >= $this->xls_colmax)
        {
            return(-2);
        }
        if ($row <  $this->dim_rowmin) 
        {
            $this->dim_rowmin = $row;
        }
        if ($row >  $this->dim_rowmax) 
        {
            $this->dim_rowmax = $row;
        }
        if ($col <  $this->dim_colmin) 
        {
            $this->dim_colmin = $col;
        }
        if ($col >  $this->dim_colmax) 
        {
            $this->dim_colmax = $col;
        }
    
        // Strip the '=' or '@' sign at the beginning of the formula string
        if (preg_match("/^=/",$formula)) {
            $formula = preg_replace("/(^=)/","",$formula);
        }
        elseif(preg_match("/^@/",$formula)) {
            $formula = preg_replace("/(^@)/","",$formula);
        }
        else {
            die("Unrecognised character for formula");
        }
    
        // Parse the formula using the parser in Parser.php
        //$tree      = new Parser($this->_byte_order);
        $this->_parser->parse($formula);
        //$tree->parse($formula);
        $formula = $this->_parser->to_reverse_polish();
    
        $formlen    = strlen($formula);    // Length of the binary string
        $length     = 0x16 + $formlen;     // Length of the record data
    
        $header    = pack("vv",      $record, $length);
        $data      = pack("vvvdvVv", $row, $col, $xf, $num,
                                     $grbit, $chn, $formlen);
    
        $this->_append($header.$data.$formula);
        return 0;
    }
    
    /**
    * Write a hyperlink. This is comprised of two elements: the visible label and
    * the invisible link. The visible label is the same as the link unless an
    * alternative string is specified. The label is written using the
    * write_string() method. Therefore the 255 characters string limit applies.
    * $string and $format are optional and their order is interchangeable.
    *
    * The hyperlink can be to a http, ftp, mail, internal sheet, or external
    * directory url.
    *
    * Returns  0 : normal termination
    *         -1 : insufficient number of arguments
    *         -2 : row or column out of range
    *         -3 : long string truncated to 255 chars
    *
    * @access public
    * @param integer $row    Row
    * @param integer $col    Column
    * @param string  $url    URL string
    * @param string  $string Alternative label
    * @param mixed   $format The cell format
    */
    function write_url($row, $col, $url, $string = '', $format = 0)
    {
        // Add start row and col to arg list
        return($this->_write_url_range($row, $col, $row, $col, $url, $string, $format));
    }
    
    /**
    * This is the more general form of write_url(). It allows a hyperlink to be
    * written to a range of cells. This function also decides the type of hyperlink
    * to be written. These are either, Web (http, ftp, mailto), Internal
    * (Sheet1!A1) or external ('c:\temp\foo.xls#Sheet1!A1').
    *
    * See also write_url() above for a general description and return values.
    *
    * @param integer $row1   Start row
    * @param integer $col1   Start column
    * @param integer $row2   End row
    * @param integer $col2   End column
    * @param string  $url    URL string
    * @param string  $string Alternative label
    * @param mixed   $format The cell format
    */
    
    function _write_url_range($row1, $col1, $row2, $col2, $url, $string = '', $format = 0)
    {
        // Check for internal/external sheet links or default to web link
        if (preg_match('[^internal:]', $url)) {
            return($this->_write_url_internal($row1, $col1, $row2, $col2, $url, $string, $format));
        }
        if (preg_match('[^external:]', $url)) {
            return($this->_write_url_external($row1, $col1, $row2, $col2, $url, $string, $format));
        }
        return($this->_write_url_web($row1, $col1, $row2, $col2, $url, $string, $format));
    }
    
    
    /**
    * Used to write http, ftp and mailto hyperlinks.
    * The link type ($options) is 0x03 is the same as absolute dir ref without
    * sheet. However it is differentiated by the $unknown2 data stream.
    *
    * @see write_url()
    * @param integer $row1   Start row
    * @param integer $col1   Start column
    * @param integer $row2   End row
    * @param integer $col2   End column
    * @param string  $url    URL string
    * @param string  $str    Alternative label
    * @param mixed   $format The cell format
    */
    function _write_url_web($row1, $col1, $row2, $col2, $url, $str, $format = 0)
    {
        $record      = 0x01B8;                       // Record identifier
        $length      = 0x00000;                      // Bytes to follow
    
        if($format == 0) {
            $format = $this->_url_format;
        }
    
        // Write the visible label using the write_string() method.
        if($str == '') {
            $str = $url;
        }
        $str_error = $this->write_string($row1, $col1, $str, $format);
        if ($str_error == -2) {
            return($str_error);
        }
    
        // Pack the undocumented parts of the hyperlink stream
        $unknown1    = pack("H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000");
        $unknown2    = pack("H*", "E0C9EA79F9BACE118C8200AA004BA90B");
    
        // Pack the option flags
        $options     = pack("V", 0x03);
    
        // Convert URL to a null terminated wchar string
        $url         = join("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url         = $url . "\0\0\0";
    
        // Pack the length of the URL
        $url_len     = pack("V", strlen($url));
    
        // Calculate the data length
        $length      = 0x34 + strlen($url);
    
        // Pack the header data
        $header      = pack("vv",   $record, $length);
        $data        = pack("vvvv", $row1, $row2, $col1, $col2);
    
        // Write the packed data
        $this->_append( $header. $data.
                        $unknown1. $options.
                        $unknown2. $url_len. $url);
        return($str_error);
    }
    
    /**
    * Used to write internal reference hyperlinks such as "Sheet1!A1".
    *
    * @see write_url()
    * @param integer $row1   Start row
    * @param integer $col1   Start column
    * @param integer $row2   End row
    * @param integer $col2   End column
    * @param string  $url    URL string
    * @param string  $str    Alternative label
    * @param mixed   $format The cell format
    */
    function _write_url_internal($row1, $col1, $row2, $col2, $url, $str, $format = 0)
    {
        $record      = 0x01B8;                       // Record identifier
        $length      = 0x00000;                      // Bytes to follow
    
        if ($format == 0) {
            $format = $this->_url_format;
        }
    
        // Strip URL type
        $url = preg_replace('s[^internal:]', '', $url);
    
        // Write the visible label
        if($str == '') {
            $str = $url;
        }
        $str_error = $this->write_string($row1, $col1, $str, $format);
        if ($str_error == -2) {
            return($str_error);
        }
    
        // Pack the undocumented parts of the hyperlink stream
        $unknown1    = pack("H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000");
    
        // Pack the option flags
        $options     = pack("V", 0x08);
    
        // Convert the URL type and to a null terminated wchar string
        $url         = join("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url         = $url . "\0\0\0";
    
        // Pack the length of the URL as chars (not wchars)
        $url_len     = pack("V", floor(strlen($url)/2));
    
        // Calculate the data length
        $length      = 0x24 + strlen($url);
    
        // Pack the header data
        $header      = pack("vv",   $record, $length);
        $data        = pack("vvvv", $row1, $row2, $col1, $col2);
    
        // Write the packed data
        $this->_append($header. $data.
                       $unknown1. $options.
                       $url_len. $url);
        return($str_error);
    }
    
    /**
    * Write links to external directory names such as 'c:\foo.xls',
    * c:\foo.xls#Sheet1!A1', '../../foo.xls'. and '../../foo.xls#Sheet1!A1'.
    *
    * Note: Excel writes some relative links with the $dir_long string. We ignore
    * these cases for the sake of simpler code.
    *
    * @see write_url()
    * @param integer $row1   Start row
    * @param integer $col1   Start column
    * @param integer $row2   End row
    * @param integer $col2   End column
    * @param string  $url    URL string
    * @param string  $str    Alternative label
    * @param mixed   $format The cell format
    */
    function _write_url_external($row1, $col1, $row2, $col2, $url, $str, $format = 0)
    {
        // Network drives are different. We will handle them separately
        // MS/Novell network drives and shares start with \\
        if (preg_match('[^external:\\\\]', $url)) {
            return($this->_write_url_external_net($row1, $col1, $row2, $col2, $url, $str, $format));
        }
    
        $record      = 0x01B8;                       // Record identifier
        $length      = 0x00000;                      // Bytes to follow
    
        if ($format == 0) {
            $format = $this->_url_format;
        }
    
        // Strip URL type and change Unix dir separator to Dos style (if needed)
        //
        $url = preg_replace('[^external:]', '', $url);
        $url = preg_replace('[/]', "\\", $url);
    
        // Write the visible label
        if ($str == '') {
            $str = preg_replace('[\#]', ' - ', $url);
        }
        $str_error = $this->write_string($row1, $col1, $str, $format);
        if ($str_error == -2) {
            return($str_error);
        }
    
        // Determine if the link is relative or absolute:
        //   relative if link contains no dir separator, "somefile.xls"
        //   relative if link starts with up-dir, "..\..\somefile.xls"
        //   otherwise, absolute
        
        $absolute    = 0x02; // Bit mask
        if (!preg_match('[\\]', $url)) {
            $absolute    = 0x00;
        }
        if (preg_match('[^\.\.\\]', $url)) {
            $absolute    = 0x00;
        }
    
        // Determine if the link contains a sheet reference and change some of the
        // parameters accordingly.
        // Split the dir name and sheet name (if it exists)
        list($dir_long , $sheet) = explode('/#/', $url);
        $link_type               = 0x01 | $absolute;
    
        if (isset($sheet)) {
            $link_type |= 0x08;
            $sheet_len  = pack("V", strlen($sheet) + 0x01);
            $sheet      = join("\0", str_split($sheet));
            $sheet     .= "\0\0\0";
        }
        else {
            $sheet_len   = '';
            $sheet       = '';
        }
    
        // Pack the link type
        $link_type   = pack("V", $link_type);
    
        // Calculate the up-level dir count e.g.. (..\..\..\ == 3)
        $up_count    = preg_match_all("/\.\.\\/", $dir_long, $useless);
        $up_count    = pack("v", $up_count);
    
        // Store the short dos dir name (null terminated)
        $dir_short   = preg_replace('/\.\.\\/', '', $dir_long) . "\0";
    
        // Store the long dir name as a wchar string (non-null terminated)
        $dir_long       = join("\0", str_split($dir_long));
        $dir_long       = $dir_long . "\0";
    
        // Pack the lengths of the dir strings
        $dir_short_len = pack("V", strlen($dir_short)      );
        $dir_long_len  = pack("V", strlen($dir_long)       );
        $stream_len    = pack("V", strlen($dir_long) + 0x06);
    
        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack("H*",'D0C9EA79F9BACE118C8200AA004BA90B02000000'       );
        $unknown2 = pack("H*",'0303000000000000C000000000000046'               );
        $unknown3 = pack("H*",'FFFFADDE000000000000000000000000000000000000000');
        $unknown4 = pack("v",  0x03                                            );
    
        // Pack the main data stream
        $data        = pack("vvvv", $row1, $row2, $col1, $col2) .
                          $unknown1     .
                          $link_type    .
                          $unknown2     .
                          $up_count     .
                          $dir_short_len.
                          $dir_short    .
                          $unknown3     .
                          $stream_len   .
                          $dir_long_len .
                          $unknown4     .
                          $dir_long     .
                          $sheet_len    .
                          $sheet        ;
    
        // Pack the header data
        $length   = strlen($data);
        $header   = pack("vv", $record, $length);
    
        // Write the packed data
        $this->_append($header. $data);
        return($str_error);
    }
    
    
    /*
    ###############################################################################
    #
    # write_url_xxx($row1, $col1, $row2, $col2, $url, $string, $format)
    #
    # Write links to external MS/Novell network drives and shares such as
    # '//NETWORK/share/foo.xls' and '//NETWORK/share/foo.xls#Sheet1!A1'.
    #
    # See also write_url() above for a general description and return values.
    #
    sub _write_url_external_net {
    
        my $this    = shift;
    
        my $record      = 0x01B8;                       # Record identifier
        my $length      = 0x00000;                      # Bytes to follow
    
        my $row1        = $_[0];                        # Start row
        my $col1        = $_[1];                        # Start column
        my $row2        = $_[2];                        # End row
        my $col2        = $_[3];                        # End column
        my $url         = $_[4];                        # URL string
        my $str         = $_[5];                        # Alternative label
        my $xf          = $_[6] || $this->{_url_format};# The cell format
    
    
        # Strip URL type and change Unix dir separator to Dos style (if needed)
        #
        $url            =~ s[^external:][];
        $url            =~ s[/][\\]g;
    
    
        # Write the visible label
        ($str = $url)   =~ s[\#][ - ] unless defined $str;
        my $str_error   = $this->write_string($row1, $col1, $str, $xf);
        return $str_error if $str_error == -2;
    
    
        # Determine if the link contains a sheet reference and change some of the
        # parameters accordingly.
        # Split the dir name and sheet name (if it exists)
        #
        my ($dir_long , $sheet) = split /\#/, $url;
        my $link_type           = 0x0103; # Always absolute
        my $sheet_len;
    
        if (defined $sheet) {
            $link_type |= 0x08;
            $sheet_len  = pack("V", length($sheet) + 0x01);
            $sheet      = join("\0", str_split($sheet));
            $sheet     .= "\0\0\0";
    }
        else {
            $sheet_len   = '';
            $sheet       = '';
    }
    
        # Pack the link type
        $link_type      = pack("V", $link_type);
    
    
        # Make the string null terminated
        $dir_long       = $dir_long . "\0";
    
    
        # Pack the lengths of the dir string
        my $dir_long_len  = pack("V", length $dir_long);
    
    
        # Store the long dir name as a wchar string (non-null terminated)
        $dir_long       = join("\0", str_split($dir_long));
        $dir_long       = $dir_long . "\0";
    
    
        # Pack the undocumented part of the hyperlink stream
        my $unknown1    = pack("H*",'D0C9EA79F9BACE118C8200AA004BA90B02000000');
    
    
        # Pack the main data stream
        my $data        = pack("vvvv", $row1, $row2, $col1, $col2) .
                          $unknown1     .
                          $link_type    .
                          $dir_long_len .
                          $dir_long     .
                          $sheet_len    .
                          $sheet        ;
    
    
        # Pack the header data
        $length         = length $data;
        my $header      = pack("vv",   $record, $length);
    
    
        # Write the packed data
        $this->_append( $header, $data);
    
        return $str_error;
}*/
    
    /**
    * This method is used to set the height and XF format for a row.
    * Writes the  BIFF record ROW.
    *
    * @access public
    * @param integer $row    The row to set
    * @param integer $height Height we are giving to the row. 
    *                        Use NULL to set XF without setting height
    * @param mixed   $format XF format we are giving to the row
    */
    function set_row($row, $height, $format = 0)
    {
        $record      = 0x0208;               // Record identifier
        $length      = 0x0010;               // Number of bytes to follow
    
        $colMic      = 0x0000;               // First defined column
        $colMac      = 0x0000;               // Last defined column
        $irwMac      = 0x0000;               // Used by Excel to optimise loading
        $reserved    = 0x0000;               // Reserved
        $grbit       = 0x01C0;               // Option flags. (monkey) see $1 do
        $ixfe        = $this->_XF($format); // XF index
    
        // Use set_row($row, NULL, $XF) to set XF without setting height
        if ($height != NULL) {
            $miyRw = $height * 20;  // row height
        }
        else {
            $miyRw = 0xff;          // default row height is 256
        }
    
        $header   = pack("vv",       $record, $length);
        $data     = pack("vvvvvvvv", $row, $colMic, $colMac, $miyRw,
                                     $irwMac,$reserved, $grbit, $ixfe);
        $this->_append($header.$data);
    }
    
    /**
    * Writes Excel DIMENSIONS to define the area in which there is data.
    */
    function _store_dimensions()
    {
        $record    = 0x0000;               // Record identifier
        $length    = 0x000A;               // Number of bytes to follow
        $row_min   = $this->dim_rowmin;    // First row
        $row_max   = $this->dim_rowmax;    // Last row plus 1
        $col_min   = $this->dim_colmin;    // First column
        $col_max   = $this->dim_colmax;    // Last column plus 1
        $reserved  = 0x0000;               // Reserved by Excel
    
        $header    = pack("vv",    $record, $length);
        $data      = pack("vvvvv", $row_min, $row_max,
                                   $col_min, $col_max, $reserved);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write BIFF record Window2.
    */
    function _store_window2()
    {
        $record         = 0x023E;     // Record identifier
        $length         = 0x000A;     // Number of bytes to follow
    
        $grbit          = 0x00B6;     // Option flags
        $rwTop          = 0x0000;     // Top row visible in window
        $colLeft        = 0x0000;     // Leftmost column visible in window
        $rgbHdr         = 0x00000000; // Row/column heading and gridline color
    
        // The options flags that comprise $grbit
        $fDspFmla       = 0;                     // 0 - bit
        $fDspGrid       = 1;                     // 1
        $fDspRwCol      = 1;                     // 2
        $fFrozen        = $this->_frozen;        // 3
        $fDspZeros      = 1;                     // 4
        $fDefaultHdr    = 1;                     // 5
        $fArabic        = 0;                     // 6
        $fDspGuts       = 1;                     // 7
        $fFrozenNoSplit = 0;                     // 0 - bit
        $fSelected      = $this->selected;       // 1
        $fPaged         = 1;                     // 2
    
        $grbit             = $fDspFmla;
        $grbit            |= $fDspGrid       << 1;
        $grbit            |= $fDspRwCol      << 2;
        $grbit            |= $fFrozen        << 3;
        $grbit            |= $fDspZeros      << 4;
        $grbit            |= $fDefaultHdr    << 5;
        $grbit            |= $fArabic        << 6;
        $grbit            |= $fDspGuts       << 7;
        $grbit            |= $fFrozenNoSplit << 8;
        $grbit            |= $fSelected      << 9;
        $grbit            |= $fPaged         << 10;
    
        $header  = pack("vv",   $record, $length);
        $data    = pack("vvvV", $grbit, $rwTop, $colLeft, $rgbHdr);
        $this->_append($header.$data);
    }
    
    /**
    * Write BIFF record DEFCOLWIDTH if COLINFO records are in use.
    */
    function _store_defcol()
    {
        $record   = 0x0055;      // Record identifier
        $length   = 0x0002;      // Number of bytes to follow
        $colwidth = 0x0008;      // Default column width
    
        $header   = pack("vv", $record, $length);
        $data     = pack("v",  $colwidth);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write BIFF record COLINFO to define column widths
    *
    * Note: The SDK says the record length is 0x0B but Excel writes a 0x0C
    * length record.
    *
    * @param array $col_array This is the only parameter received and is composed of the following:
    *                0 => First formatted column,
    *                1 => Last formatted column,
    *                2 => Col width (8.43 is Excel default),
    *                3 => The optional XF format of the column,
    *                4 => Option flags.
    */
    function _store_colinfo($col_array)
    {
        if(isset($col_array[0])) {
            $colFirst = $col_array[0];
        }
        if(isset($col_array[1])) {
            $colLast = $col_array[1];
        }
        if(isset($col_array[2])) {
            $coldx = $col_array[2];
        }
        else {
            $coldx = 8.43;
        }
        if(isset($col_array[3])) {
            $format = $col_array[3];
        }
        else {
            $format = 0;
        }
        if(isset($col_array[4])) {
            $grbit = $col_array[4];
        }
        else {
            $grbit = 0;
        }
        $record   = 0x007D;          // Record identifier
        $length   = 0x000B;          // Number of bytes to follow
    
        $coldx   += 0.72;            // Fudge. Excel subtracts 0.72 !?
        $coldx   *= 256;             // Convert to units of 1/256 of a char
    
        $ixfe     = $this->_XF($format);
        $reserved = 0x00;            // Reserved
    
        $header   = pack("vv",     $record, $length);
        $data     = pack("vvvvvC", $colFirst, $colLast, $coldx,
                                   $ixfe, $grbit, $reserved);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write BIFF record SELECTION.
    *
    * @param array $array array containing ($rwFirst,$colFirst,$rwLast,$colLast)
    * @see set_selection()
    */
    function _store_selection($array)
    {
        list($rwFirst,$colFirst,$rwLast,$colLast) = $array;
        $record   = 0x001D;                  // Record identifier
        $length   = 0x000F;                  // Number of bytes to follow
    
        $pnn      = $this->_active_pane;     // Pane position
        $rwAct    = $rwFirst;                // Active row
        $colAct   = $colFirst;               // Active column
        $irefAct  = 0;                       // Active cell ref
        $cref     = 1;                       // Number of refs
    
        if (!isset($rwLast)) {
            $rwLast   = $rwFirst;       // Last  row in reference
        }
        if (!isset($colLast)) {
            $colLast  = $colFirst;      // Last  col in reference
        }
    
        // Swap last row/col for first row/col as necessary
        if ($rwFirst > $rwLast)
        {
            list($rwFirst, $rwLast) = array($rwLast, $rwFirst);
        }
    
        if ($colFirst > $colLast)
        {
            list($colFirst, $colLast) = array($colLast, $colFirst);
        }
    
        $header   = pack("vv",         $record, $length);
        $data     = pack("CvvvvvvCC",  $pnn, $rwAct, $colAct,
                                       $irefAct, $cref,
                                       $rwFirst, $rwLast,
                                       $colFirst, $colLast);
        $this->_append($header.$data);
    }
    
    
    /**
    * Write BIFF record EXTERNCOUNT to indicate the number of external sheet
    * references in a worksheet.
    *
    * Excel only stores references to external sheets that are used in formulas.
    * For simplicity we store references to all the sheets in the workbook
    * regardless of whether they are used or not. This reduces the overall
    * complexity and eliminates the need for a two way dialogue between the formula
    * parser the worksheet objects.
    *
    * @param integer $count The number of external sheet references in this worksheet
    */
    function _store_externcount($count)
    {
        $record   = 0x0016;          // Record identifier
        $length   = 0x0002;          // Number of bytes to follow
    
        $header   = pack("vv", $record, $length);
        $data     = pack("v",  $count);
        $this->_prepend($header.$data);
    }
    
    /**
    * Writes the Excel BIFF EXTERNSHEET record. These references are used by
    * formulas. A formula references a sheet name via an index. Since we store a
    * reference to all of the external worksheets the EXTERNSHEET index is the same
    * as the worksheet index.
    *
    * @param string $sheetname The name of a external worksheet
    */
    function _store_externsheet($sheetname)
    {
        $record    = 0x0017;         // Record identifier
    
        // References to the current sheet are encoded differently to references to
        // external sheets.
        //
        if ($this->name == $sheetname) {
            $sheetname = '';
            $length    = 0x02;  // The following 2 bytes
            $cch       = 1;     // The following byte
            $rgch      = 0x02;  // Self reference
        }
        else {
            $length    = 0x02 + strlen($sheetname);
            $cch       = strlen($sheetname);
            $rgch      = 0x03;  // Reference to a sheet in the current workbook
        }
    
        $header     = pack("vv",  $record, $length);
        $data       = pack("CC", $cch, $rgch);
        $this->_prepend($header.$data.$sheetname);
    }
    
    /**
    * Writes the Excel BIFF PANE record.
    * The panes can either be frozen or thawed (unfrozen).
    * Frozen panes are specified in terms of an integer number of rows and columns.
    * Thawed panes are specified in terms of Excel's units for rows and columns.
    *
    * @param array $panes This is the only parameter received and is composed of the following:
    *                     0 => Vertical split position,
    *                     1 => Horizontal split position
    *                     2 => Top row visible
    *                     3 => Leftmost column visible
    *                     4 => Active pane
    */
    function _store_panes($panes)
    {
        $y       = $panes[0];
        $x       = $panes[1];
        $rwTop   = $panes[2];
        $colLeft = $panes[3];
        if(count($panes) > 4) { // if Active pane was received
            $pnnAct = $panes[4];
        }
        else {
            $pnnAct = NULL;
        }
        $record  = 0x0041;       // Record identifier
        $length  = 0x000A;       // Number of bytes to follow
    
        // Code specific to frozen or thawed panes.
        if ($this->_frozen) {
            // Set default values for $rwTop and $colLeft
            if(!isset($rwTop)) {
                $rwTop   = $y;
            }
            if(!isset($colLeft)) {
                $colLeft = $x;
            }
        }
        else {
            // Set default values for $rwTop and $colLeft
            if(!isset($rwTop)) {
                $rwTop   = 0;
            }
            if(!isset($colLeft)) {
                $colLeft = 0;
            }
    
            // Convert Excel's row and column units to the internal units.
            // The default row height is 12.75
            // The default column width is 8.43
            // The following slope and intersection values were interpolated.
            //
            $y = 20*$y      + 255;
            $x = 113.879*$x + 390;
        }
    
    
        // Determine which pane should be active. There is also the undocumented
        // option to override this should it be necessary: may be removed later.
        //
        if (!isset($pnnAct))
        {
            if ($x != 0 and $y != 0)
                $pnnAct = 0; // Bottom right
            if ($x != 0 and $y == 0)
                $pnnAct = 1; // Top right
            if ($x == 0 and $y != 0)
                $pnnAct = 2; // Bottom left
            if ($x == 0 and $y == 0)
                $pnnAct = 3; // Top left
        }
    
        $this->_active_pane = $pnnAct; // Used in _store_selection
    
        $header     = pack("vv",    $record, $length);
        $data       = pack("vvvvv", $x, $y, $rwTop, $colLeft, $pnnAct);
        $this->_append($header.$data);
    }
    
    /**
    * Store the page setup SETUP BIFF record.
    */
    function _store_setup()
    {
        $record       = 0x00A1;                  // Record identifier
        $length       = 0x0022;                  // Number of bytes to follow
    
        $iPaperSize   = $this->_paper_size;    // Paper size
        $iScale       = $this->_print_scale;   // Print scaling factor
        $iPageStart   = 0x01;                 // Starting page number
        $iFitWidth    = $this->_fit_width;    // Fit to number of pages wide
        $iFitHeight   = $this->_fit_height;   // Fit to number of pages high
        $grbit        = 0x00;                 // Option flags
        $iRes         = 0x0258;               // Print resolution
        $iVRes        = 0x0258;               // Vertical print resolution
        $numHdr       = $this->_margin_head;  // Header Margin
        $numFtr       = $this->_margin_foot;   // Footer Margin
        $iCopies      = 0x01;                 // Number of copies
    
        $fLeftToRight = 0x0;                     // Print over then down
        $fLandscape   = $this->_orientation;     // Page orientation
        $fNoPls       = 0x0;                     // Setup not read from printer
        $fNoColor     = 0x0;                     // Print black and white
        $fDraft       = 0x0;                     // Print draft quality
        $fNotes       = 0x0;                     // Print notes
        $fNoOrient    = 0x0;                     // Orientation not set
        $fUsePage     = 0x0;                     // Use custom starting page
    
        $grbit           = $fLeftToRight;
        $grbit          |= $fLandscape    << 1;
        $grbit          |= $fNoPls        << 2;
        $grbit          |= $fNoColor      << 3;
        $grbit          |= $fDraft        << 4;
        $grbit          |= $fNotes        << 5;
        $grbit          |= $fNoOrient     << 6;
        $grbit          |= $fUsePage      << 7;
    
        $numHdr = pack("d", $numHdr);
        $numFtr = pack("d", $numFtr);
        if ($this->_byte_order) // if it's Big Endian
        {
            $numHdr = strrev($numHdr);
            $numFtr = strrev($numFtr);
        }
    
        $header = pack("vv", $record, $length);
        $data1  = pack("vvvvvvvv", $iPaperSize,
                                   $iScale,
                                   $iPageStart,
                                   $iFitWidth,
                                   $iFitHeight,
                                   $grbit,
                                   $iRes,
                                   $iVRes);
        $data2  = $numHdr .$numFtr;
        $data3  = pack("v", $iCopies);
        $this->_prepend($header.$data1.$data2.$data3);
    }
    
    /**
    * Store the header caption BIFF record.
    */
    function store_header()
    {
        $record  = 0x0014;               // Record identifier
    
        $str     = $this->_header;        // header string
        $cch     = strlen($str);         // Length of header string
        $length  = 1 + $cch;             // Bytes to follow
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("C",   $cch);
    
        $this->_append($header.$data.$str);
    }
    
    /**
    * Store the footer caption BIFF record.
    */
    function store_footer()
    {
        $record  = 0x0015;               // Record identifier
    
        $str     = $this->_footer;       // Footer string
        $cch     = strlen($str);         // Length of footer string
        $length  = 1 + $cch;             // Bytes to follow
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("C",   $cch);
    
        $this->_append($header.$data.$str);
    }
    
    /**
    * Store the horizontal centering HCENTER BIFF record.
    */
    function store_hcenter()
    {
        $record   = 0x0083;              // Record identifier
        $length   = 0x0002;              // Bytes to follow
    
        $fHCenter = $this->_hcenter;      // Horizontal centering
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("v",   $fHCenter);
    
        $this->_append($header.$data);
    }
    
    /**
    * Store the vertical centering VCENTER BIFF record.
    */
    function store_vcenter()
    {
        $record   = 0x0084;              // Record identifier
        $length   = 0x0002;              // Bytes to follow
    
        $fVCenter = $this->_vcenter;      // Horizontal centering
    
        $header    = pack("vv", $record, $length);
        $data      = pack("v", $fVCenter);
        $this->_append($header.$data);
    }
    
    /**
    * Store the LEFTMARGIN BIFF record.
    */
    function _store_margin_left()
    {
        $record  = 0x0026;                   // Record identifier
        $length  = 0x0008;                   // Bytes to follow
    
        $margin  = $this->_margin_left;       // Margin in inches
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("d",   $margin);
        if ($this->_byte_order) // if it's Big Endian
        { 
            $data = strrev($data);
        }
    
        $this->_append($header.$data);
    }
    
    /**
    * Store the RIGHTMARGIN BIFF record.
    */
    function _store_margin_right()
    {
        $record  = 0x0027;                   // Record identifier
        $length  = 0x0008;                   // Bytes to follow
    
        $margin  = $this->_margin_right;      // Margin in inches
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("d",   $margin);
        if ($this->_byte_order) // if it's Big Endian
        { 
            $data = strrev($data);
        }
    
        $this->_append($header.$data);
    }
    
    /**
    * Store the TOPMARGIN BIFF record.
    */
    function _store_margin_top()
    {
        $record  = 0x0028;                   // Record identifier
        $length  = 0x0008;                   // Bytes to follow
    
        $margin  = $this->_margin_top;        // Margin in inches
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("d",   $margin);
        if ($this->_byte_order) // if it's Big Endian
        { 
            $data = strrev($data);
        }
    
        $this->_append($header.$data);
    }
    
    /**
    * Store the BOTTOMMARGIN BIFF record.
    */
    function _store_margin_bottom()
    {
        $record  = 0x0029;                   // Record identifier
        $length  = 0x0008;                   // Bytes to follow
    
        $margin  = $this->_margin_bottom;     // Margin in inches
    
        $header    = pack("vv",  $record, $length);
        $data      = pack("d",   $margin);
        if ($this->_byte_order) // if it's Big Endian
        { 
            $data = strrev($data);
        }
    
        $this->_append($header.$data);
    }

    /**
    * This is an Excel97/2000 method. It is required to perform more complicated
    * merging than the normal set_align('merge'). It merges the area given by 
    * its arguments.
    *
    * @access public
    * @param integer $first_row First row of the area to merge
    * @param integer $first_col First column of the area to merge
    * @param integer $last_row  Last row of the area to merge
    * @param integer $last_col  Last column of the area to merge
    */
    function merge_cells($first_row, $first_col, $last_row, $last_col)
    {
        $record  = 0x00E5;                   // Record identifier
        $length  = 0x000A;                   // Bytes to follow
        $cref     = 1;                       // Number of refs

        // Swap last row/col for first row/col as necessary
        if ($first_row > $last_row) {
            list($first_row, $last_row) = array($last_row, $first_row);
        }
    
        if ($first_col > $last_col) {
            list($first_col, $last_col) = array($last_col, $first_col);
        }
    
        $header   = pack("vv",    $record, $length);
        $data     = pack("vvvvv", $cref, $first_row, $last_row,
                                  $first_col, $last_col);
    
        $this->_append($header.$data);
    }
    
    /**
    * Write the PRINTHEADERS BIFF record.
    */
    function _store_print_headers()
    {
        $record      = 0x002a;                   // Record identifier
        $length      = 0x0002;                   // Bytes to follow
    
        $fPrintRwCol = $this->_print_headers;     // Boolean flag
    
        $header      = pack("vv", $record, $length);
        $data        = pack("v", $fPrintRwCol);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write the PRINTGRIDLINES BIFF record. Must be used in conjunction with the
    * GRIDSET record.
    */
    function _store_print_gridlines()
    {
        $record      = 0x002b;                    // Record identifier
        $length      = 0x0002;                    // Bytes to follow
    
        $fPrintGrid  = $this->_print_gridlines;    // Boolean flag
    
        $header      = pack("vv", $record, $length);
        $data        = pack("v", $fPrintGrid);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write the GRIDSET BIFF record. Must be used in conjunction with the
    * PRINTGRIDLINES record.
    */
    function _store_gridset()
    {
        $record      = 0x0082;                        // Record identifier
        $length      = 0x0002;                        // Bytes to follow
    
        $fGridSet    = !($this->_print_gridlines);     // Boolean flag
    
        $header      = pack("vv",  $record, $length);
        $data        = pack("v",   $fGridSet);
        $this->_prepend($header.$data);
    }
    
    /**
    * Write the WSBOOL BIFF record, mainly for fit-to-page. Used in conjunction
    * with the SETUP record.
    */
    function _store_wsbool()
    {
        $record      = 0x0081;   // Record identifier
        $length      = 0x0002;   // Bytes to follow
    
        // The only option that is of interest is the flag for fit to page. So we
        // set all the options in one go.
        //
        if ($this->_fit_page) {
            $grbit = 0x05c1;
        }
        else {
            $grbit = 0x04c1;
        }
    
        $header      = pack("vv", $record, $length);
        $data        = pack("v",  $grbit);
        $this->_prepend($header.$data);
    }
    
    
    /**
    * Write the HORIZONTALPAGEBREAKS BIFF record.
    */
    function _store_hbreak()
    {
        // Return if the user hasn't specified pagebreaks
        if(empty($this->_hbreaks)) {
            return;
        }
    
        // Sort and filter array of page breaks
        $breaks = $this->_hbreaks;
        sort($breaks,SORT_NUMERIC);
        if($breaks[0] == 0) { // don't use first break if it's 0
            array_shift($breaks);
        }
    
        $record  = 0x001b;               // Record identifier
        $cbrk    = count($breaks);       // Number of page breaks
        $length  = ($cbrk + 1) * 2;      // Bytes to follow
    
        $header  = pack("vv", $record, $length);
        $data    = pack("v",  $cbrk);
    
        // Append each page break
        foreach($breaks as $break) {
            $data .= pack("v", $break);
        }
    
        $this->_prepend($header.$data);
    }
    
    
    /**
    * Write the VERTICALPAGEBREAKS BIFF record.
    */
    function _store_vbreak()
    {
        // Return if the user hasn't specified pagebreaks
        if(empty($this->_vbreaks)) {
            return;
        }
    
        // 1000 vertical pagebreaks appears to be an internal Excel 5 limit.
        // It is slightly higher in Excel 97/200, approx. 1026
        $breaks = array_slice($this->_vbreaks,0,1000);
    
        // Sort and filter array of page breaks
        sort($breaks,SORT_NUMERIC);
        if($breaks[0] == 0) { // don't use first break if it's 0
            array_shift($breaks);
        }
    
        $record  = 0x001a;               // Record identifier
        $cbrk    = count($breaks);       // Number of page breaks
        $length  = ($cbrk + 1) * 2;      // Bytes to follow
    
        $header  = pack("vv",  $record, $length);
        $data    = pack("v",   $cbrk);
    
        // Append each page break
        foreach ($breaks as $break) {
            $data .= pack("v", $break);
        }
    
        $this->_prepend($header.$data);
    }
    
    /**
    * Set the Biff PROTECT record to indicate that the worksheet is protected.
    */
    function _store_protect()
    {
        // Exit unless sheet protection has been specified
        if($this->_protect == 0) {
            return;
        }
    
        $record      = 0x0012;             // Record identifier
        $length      = 0x0002;             // Bytes to follow
    
        $fLock       = $this->_protect;    // Worksheet is protected
    
        $header      = pack("vv", $record, $length);
        $data        = pack("v",  $fLock);
    
        $this->_prepend($header.$data);
    }
    
    /**
    * Write the worksheet PASSWORD record.
    */
    function _store_password()
    {
        // Exit unless sheet protection and password have been specified
        if(($this->_protect == 0) or (!isset($this->_password))) {
            return;
        }
    
        $record      = 0x0013;               // Record identifier
        $length      = 0x0002;               // Bytes to follow
    
        $wPassword   = $this->_password;     // Encoded password
    
        $header      = pack("vv", $record, $length);
        $data        = pack("v",  $wPassword);
    
        $this->_prepend($header.$data);
    }
    
    /**
    * Insert a 24bit bitmap image in a worksheet. The main record required is
    * IMDATA but it must be proceeded by a OBJ record to define its position.
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
    function insert_bitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1)
    {
        list($width, $height, $size, $data) = $this->_process_bitmap($bitmap);
    
        // Scale the frame of the image.
        $width  *= $scale_x;
        $height *= $scale_y;
    
        // Calculate the vertices of the image and write the OBJ record
        $this->_position_image($col, $row, $x, $y, $width, $height);
    
        // Write the IMDATA record to store the bitmap data
        $record      = 0x007f;
        $length      = 8 + $size;
        $cf          = 0x09;
        $env         = 0x01;
        $lcb         = $size;
    
        $header      = pack("vvvvV", $record, $length, $cf, $env, $lcb);
        $this->_append($header.$data);
    }
    
    /**
    * Calculate the vertices that define the position of the image as required by
    * the OBJ record.
    *
    *         +------------+------------+
    *         |     A      |      B     |
    *   +-----+------------+------------+
    *   |     |(x1,y1)     |            |
    *   |  1  |(A1)._______|______      |
    *   |     |    |              |     |
    *   |     |    |              |     |
    *   +-----+----|    BITMAP    |-----+
    *   |     |    |              |     |
    *   |  2  |    |______________.     |
    *   |     |            |        (B2)|
    *   |     |            |     (x2,y2)|
    *   +---- +------------+------------+
    *
    * Example of a bitmap that covers some of the area from cell A1 to cell B2.
    *
    * Based on the width and height of the bitmap we need to calculate 8 vars:
    *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
    * The width and height of the cells are also variable and have to be taken into
    * account.
    * The values of $col_start and $row_start are passed in from the calling
    * function. The values of $col_end and $row_end are calculated by subtracting
    * the width and height of the bitmap from the width and height of the
    * underlying cells.
    * The vertices are expressed as a percentage of the underlying cell width as
    * follows (rhs values are in pixels):
    *
    *       x1 = X / W *1024
    *       y1 = Y / H *256
    *       x2 = (X-1) / W *1024
    *       y2 = (Y-1) / H *256
    *
    *       Where:  X is distance from the left side of the underlying cell
    *               Y is distance from the top of the underlying cell
    *               W is the width of the cell
    *               H is the height of the cell
    *
    * @note  the SDK incorrectly states that the height should be expressed as a
    *        percentage of 1024.
    * @param integer $col_start Col containing upper left corner of object
    * @param integer $row_start Row containing top left corner of object
    * @param integer $x1        Distance to left side of object
    * @param integer $y1        Distance to top of object
    * @param integer $width     Width of image frame
    * @param integer $height    Height of image frame
    */
    function _position_image($col_start, $row_start, $x1, $y1, $width, $height)
    {
        // Initialise end cell to the same as the start cell
        $col_end    = $col_start;  // Col containing lower right corner of object
        $row_end    = $row_start;  // Row containing bottom right corner of object
    
        // Zero the specified offset if greater than the cell dimensions
        if ($x1 >= $this->size_col($col_start))
        {
            $x1 = 0;
        }
        if ($y1 >= $this->size_row($row_start))
        {
            $y1 = 0;
        }
    
        $width      = $width  + $x1 -1;
        $height     = $height + $y1 -1;
    
        // Subtract the underlying cell widths to find the end cell of the image
        while ($width >= $this->size_col($col_end)) {
            $width -= $this->size_col($col_end);
            $col_end++;
        }
    
        // Subtract the underlying cell heights to find the end cell of the image
        while ($height >= $this->size_row($row_end)) {
            $height -= $this->size_row($row_end);
            $row_end++;
        }
    
        // Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
        // with zero eight or width.
        //
        if ($this->size_col($col_start) == 0)
            return;
        if ($this->size_col($col_end)   == 0)
            return;
        if ($this->size_row($row_start) == 0)
            return;
        if ($this->size_row($row_end)   == 0)
            return;
    
        // Convert the pixel values to the percentage value expected by Excel
        $x1 = $x1     / $this->size_col($col_start)   * 1024;
        $y1 = $y1     / $this->size_row($row_start)   *  256;
        $x2 = $width  / $this->size_col($col_end)     * 1024; // Distance to right side of object
        $y2 = $height / $this->size_row($row_end)     *  256; // Distance to bottom of object
    
        $this->_store_obj_picture( $col_start, $x1,
                                  $row_start, $y1,
                                  $col_end, $x2,
                                  $row_end, $y2
                                );
    }
    
    /**
    * Convert the width of a cell from user's units to pixels. By interpolation
    * the relationship is: y = 7x +5. If the width hasn't been set by the user we
    * use the default value. If the col is hidden we use a value of zero.
    *
    * @param integer  $col The column 
    * @return integer The width in pixels
    */
    function size_col($col)
    {
        // Look up the cell value to see if it has been changed
        if (isset($this->col_sizes[$col])) {
            if ($this->col_sizes[$col] == 0) {
                return(0);
            }
            else {
                return(floor(7 * $this->col_sizes[$col] + 5));
            }
        }
        else {
            return(64);
        }
    }
    
    /**
    * Convert the height of a cell from user's units to pixels. By interpolation
    * the relationship is: y = 4/3x. If the height hasn't been set by the user we
    * use the default value. If the row is hidden we use a value of zero. (Not
    * possible to hide row yet).
    *
    * @param integer $row The row
    * @return integer The width in pixels
    */
    function size_row($row)
    {
        // Look up the cell value to see if it has been changed
        if (isset($this->row_sizes[$row])) {
            if ($this->row_sizes[$row] == 0) {
                return(0);
            }
            else {
                return(floor(4/3 * $this->row_sizes[$row]));
            }
        }
        else {
            return(17);
        }
    }
    
    /**
    * Store the OBJ record that precedes an IMDATA record. This could be generalise
    * to support other Excel objects.
    *
    * @param integer $colL Column containing upper left corner of object
    * @param integer $dxL  Distance from left side of cell
    * @param integer $rwT  Row containing top left corner of object
    * @param integer $dyT  Distance from top of cell
    * @param integer $colR Column containing lower right corner of object
    * @param integer $dxR  Distance from right of cell
    * @param integer $rwB  Row containing bottom right corner of object
    * @param integer $dyB  Distance from bottom of cell
    */
    function _store_obj_picture($colL,$dxL,$rwT,$dyT,$colR,$dxR,$rwB,$dyB)
    {
        $record      = 0x005d;   // Record identifier
        $length      = 0x003c;   // Bytes to follow
    
        $cObj        = 0x0001;   // Count of objects in file (set to 1)
        $OT          = 0x0008;   // Object type. 8 = Picture
        $id          = 0x0001;   // Object ID
        $grbit       = 0x0614;   // Option flags
    
        $cbMacro     = 0x0000;   // Length of FMLA structure
        $Reserved1   = 0x0000;   // Reserved
        $Reserved2   = 0x0000;   // Reserved
    
        $icvBack     = 0x09;     // Background colour
        $icvFore     = 0x09;     // Foreground colour
        $fls         = 0x00;     // Fill pattern
        $fAuto       = 0x00;     // Automatic fill
        $icv         = 0x08;     // Line colour
        $lns         = 0xff;     // Line style
        $lnw         = 0x01;     // Line weight
        $fAutoB      = 0x00;     // Automatic border
        $frs         = 0x0000;   // Frame style
        $cf          = 0x0009;   // Image format, 9 = bitmap
        $Reserved3   = 0x0000;   // Reserved
        $cbPictFmla  = 0x0000;   // Length of FMLA structure
        $Reserved4   = 0x0000;   // Reserved
        $grbit2      = 0x0001;   // Option flags
        $Reserved5   = 0x0000;   // Reserved
    
    
        $header      = pack("vv", $record, $length);
        $data        = pack("V", $cObj);
        $data       .= pack("v", $OT);
        $data       .= pack("v", $id);
        $data       .= pack("v", $grbit);
        $data       .= pack("v", $colL);
        $data       .= pack("v", $dxL);
        $data       .= pack("v", $rwT);
        $data       .= pack("v", $dyT);
        $data       .= pack("v", $colR);
        $data       .= pack("v", $dxR);
        $data       .= pack("v", $rwB);
        $data       .= pack("v", $dyB);
        $data       .= pack("v", $cbMacro);
        $data       .= pack("V", $Reserved1);
        $data       .= pack("v", $Reserved2);
        $data       .= pack("C", $icvBack);
        $data       .= pack("C", $icvFore);
        $data       .= pack("C", $fls);
        $data       .= pack("C", $fAuto);
        $data       .= pack("C", $icv);
        $data       .= pack("C", $lns);
        $data       .= pack("C", $lnw);
        $data       .= pack("C", $fAutoB);
        $data       .= pack("v", $frs);
        $data       .= pack("V", $cf);
        $data       .= pack("v", $Reserved3);
        $data       .= pack("v", $cbPictFmla);
        $data       .= pack("v", $Reserved4);
        $data       .= pack("v", $grbit2);
        $data       .= pack("V", $Reserved5);
    
        $this->_append($header.$data);
    }
    
    /**
    * Convert a 24 bit bitmap into the modified internal format used by Windows.
    * This is described in BITMAPCOREHEADER and BITMAPCOREINFO structures in the
    * MSDN library.
    *
    * @param string $bitmap The bitmap to process
    * @return array Array with data and properties of the bitmap
    */
    function _process_bitmap($bitmap)
    {
        // Open file.
        $bmp_fd = fopen($bitmap,"rb");
        if (!$bmp_fd) {
            die("Couldn't import $bitmap");
        }
            
        // Slurp the file into a string.
        $data = fread($bmp_fd, filesize($bitmap));
    
        // Check that the file is big enough to be a bitmap.
        if (strlen($data) <= 0x36) {
            die("$bitmap doesn't contain enough data.\n");
        }
    
        // The first 2 bytes are used to identify the bitmap.
        $identity = unpack("A2", $data);
        if ($identity[''] != "BM") {
            die("$bitmap doesn't appear to be a valid bitmap image.\n");
        }
    
        // Remove bitmap data: ID.
        $data = substr($data, 2);
    
        // Read and remove the bitmap size. This is more reliable than reading
        // the data size at offset 0x22.
        //
        $size_array   = unpack("V", substr($data, 0, 4));
        $size   = $size_array[''];
        $data   = substr($data, 4);
        $size  -= 0x36; // Subtract size of bitmap header.
        $size  += 0x0C; // Add size of BIFF header.
    
        // Remove bitmap data: reserved, offset, header length.
        $data = substr($data, 12);
    
        // Read and remove the bitmap width and height. Verify the sizes.
        $width_and_height = unpack("V2", substr($data, 0, 8));
        $width  = $width_and_height[1];
        $height = $width_and_height[2];
        $data   = substr($data, 8);
        if ($width > 0xFFFF) { 
            die("$bitmap: largest image width supported is 65k.\n");
        }
        if ($height > 0xFFFF) { 
            die("$bitmap: largest image height supported is 65k.\n");
        }
    
        // Read and remove the bitmap planes and bpp data. Verify them.
        $planes_and_bitcount = unpack("v2", substr($data, 0, 4));
        $data = substr($data, 4);
        if ($planes_and_bitcount[2] != 24) { // Bitcount
            die("$bitmap isn't a 24bit true color bitmap.\n");
        }
        if ($planes_and_bitcount[1] != 1) {
            die("$bitmap: only 1 plane supported in bitmap image.\n");
        }
    
        // Read and remove the bitmap compression. Verify compression.
        $compression = unpack("V", substr($data, 0, 4));
        $data = substr($data, 4);
      
        //$compression = 0;
        if ($compression[""] != 0) {
            die("$bitmap: compression not supported in bitmap image.\n");
        }
    
        // Remove bitmap data: data size, hres, vres, colours, imp. colours.
        $data = substr($data, 20);
    
        // Add the BITMAPCOREHEADER data
        $header  = pack("Vvvvv", 0x000c, $width, $height, 0x01, 0x18);
        $data    = $header . $data;
    
        return (array($width, $height, $size, $data));
    }
    
    /**
    * Store the window zoom factor. This should be a reduced fraction but for
    * simplicity we will store all fractions with a numerator of 100.
    */
    function _store_zoom()
    {
        // If scale is 100 we don't need to write a record
        if ($this->_zoom == 100) {
            return;
        }
    
        $record      = 0x00A0;               // Record identifier
        $length      = 0x0004;               // Bytes to follow
    
        $header      = pack("vv", $record, $length);
        $data        = pack("vv", $this->_zoom, 100);
        $this->_append($header.$data);
    }
}
?>
