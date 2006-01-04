<?php
/*
*  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
*
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
*    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
*    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
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

require_once 'PEAR.php';
require_once 'Spreadsheet/Excel/Writer/Workbook.php';

/**
* Class for writing Excel Spreadsheets. This class should change COMPLETELY.
*
* @author   Xavier Noguer <xnoguer@rezebra.com>
* @category FileFormats
* @package  Spreadsheet_Excel_Writer
*/

class Spreadsheet_Excel_Writer extends Spreadsheet_Excel_Writer_Workbook
{
    /**
    * The constructor. It just creates a Workbook
    *
    * @param string $filename The optional filename for the Workbook.
    * @return Spreadsheet_Excel_Writer_Workbook The Workbook created
    */
    function Spreadsheet_Excel_Writer($filename = '')
    {
        $this->_filename = $filename;
        $this->Spreadsheet_Excel_Writer_Workbook($filename);
    }

    /**
    * Send HTTP headers for the Excel file.
    *
    * @param string $filename The filename to use for HTTP headers
    * @access public
    */
    function send($filename)
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    /**
    * Utility function for writing formulas
    * Converts a cell's coordinates to the A1 format.
    *
    * @access public
    * @static
    * @param integer $row Row for the cell to convert (0-indexed).
    * @param integer $col Column for the cell to convert (0-indexed).
    * @return string The cell identifier in A1 format
    */
    function rowcolToCell($row, $col)
    {
        if ($col > 255) { //maximum column value exceeded
            return new PEAR_Error("Maximum column value exceeded: $col");
        }

        $int = (int)($col / 26);
        $frac = $col % 26;
        $chr1 = '';

        if ($int > 0) {
            $chr1 = chr(ord('A') + $int - 1);
        }

        $chr2 = chr(ord('A') + $frac);
        $row++;

        return $chr1 . $chr2 . $row;
    }
}
?>
