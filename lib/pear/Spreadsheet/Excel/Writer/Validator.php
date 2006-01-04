<?php
/*
*  Module written by Herman Kuiper <herman@ozuzo.net>
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

//require_once('PEAR.php');

// Possible operator types

/*
FIXME: change prefixes
*/
define("OP_BETWEEN",    0x00);
define("OP_NOTBETWEEN", 0x01);
define("OP_EQUAL",      0x02);
define("OP_NOTEQUAL",   0x03);
define("OP_GT",         0x04);
define("OP_LT",         0x05);
define("OP_GTE",        0x06);
define("OP_LTE",        0x07);

/**
* Baseclass for generating Excel DV records (validations)
*
* @author   Herman Kuiper
* @category FileFormats
* @package  Spreadsheet_Excel_Writer
*/
class Spreadsheet_Excel_Writer_Validator
{
   var $_type;
   var $_style;
   var $_fixedList;
   var $_blank;
   var $_incell;
   var $_showprompt;
   var $_showerror;
   var $_title_prompt;
   var $_descr_prompt;
   var $_title_error;
   var $_descr_error;
   var $_operator;
   var $_formula1;
   var $_formula2;
    /**
    * The parser from the workbook. Used to parse validation formulas also
    * @var Spreadsheet_Excel_Writer_Parser
    */
    var $_parser;

    function Spreadsheet_Excel_Writer_Validator(&$parser)
    {
        $this->_parser       = $parser;
        $this->_type         = 0x01; // FIXME: add method for setting datatype
        $this->_style        = 0x00;
        $this->_fixedList    = false;
        $this->_blank        = false;
        $this->_incell       = false;
        $this->_showprompt   = false;
        $this->_showerror    = true;
        $this->_title_prompt = "\x00";
        $this->_descr_prompt = "\x00";
        $this->_title_error  = "\x00";
        $this->_descr_error  = "\x00";
        $this->_operator     = 0x00; // default is equal
        $this->_formula1    = '';
        $this->_formula2    = '';
    }

   function setPrompt($promptTitle = "\x00", $promptDescription = "\x00", $showPrompt = true)
   {
      $this->_showprompt = $showPrompt;
      $this->_title_prompt = $promptTitle;
      $this->_descr_prompt = $promptDescription;
   }

   function setError($errorTitle = "\x00", $errorDescription = "\x00", $showError = true)
   {
      $this->_showerror = $showError;
      $this->_title_error = $errorTitle;
      $this->_descr_error = $errorDescription;
   }

   function allowBlank()
   {
      $this->_blank = true;
   }

   function onInvalidStop()
   {
      $this->_style = 0x00;
   }

    function onInvalidWarn()
    {
        $this->_style = 0x01;
    }

    function onInvalidInfo()
    {
        $this->_style = 0x02;
    }

    function setFormula1($formula)
    {
        // Parse the formula using the parser in Parser.php
        $error = $this->_parser->parse($formula);
        if (PEAR::isError($error)) {
            return $this->_formula1;
        }

        $this->_formula1 = $this->_parser->toReversePolish();
        if (PEAR::isError($this->_formula1)) {
            return $this->_formula1;
        }
        return true;
    }

    function setFormula2($formula)
    {
        // Parse the formula using the parser in Parser.php
        $error = $this->_parser->parse($formula);
        if (PEAR::isError($error)) {
            return $this->_formula2;
        }

        $this->_formula2 = $this->_parser->toReversePolish();
        if (PEAR::isError($this->_formula2)) {
            return $this->_formula2;
        }
        return true;
    }

    function _getOptions()
    {
        $options = $this->_type;
        $options |= $this->_style << 3;
        if ($this->_fixedList) {
            $options |= 0x80;
        }
        if ($this->_blank) {
            $options |= 0x100;
        }
        if (!$this->_incell) {
            $options |= 0x200;
        }
        if ($this->_showprompt) {
            $options |= 0x40000;
        }
        if ($this->_showerror) {
            $options |= 0x80000;
        }
      $options |= $this->_operator << 20;

      return $options;
   }

   function _getData()
   {
      $title_prompt_len = strlen($this->_title_prompt);
      $descr_prompt_len = strlen($this->_descr_prompt);
      $title_error_len = strlen($this->_title_error);
      $descr_error_len = strlen($this->_descr_error);

      $formula1_size = strlen($this->_formula1);
      $formula2_size = strlen($this->_formula2);

      $data  = pack("V", $this->_getOptions());
      $data .= pack("vC", $title_prompt_len, 0x00) . $this->_title_prompt;
      $data .= pack("vC", $title_error_len, 0x00) . $this->_title_error;
      $data .= pack("vC", $descr_prompt_len, 0x00) . $this->_descr_prompt;
      $data .= pack("vC", $descr_error_len, 0x00) . $this->_descr_error;

      $data .= pack("vv", $formula1_size, 0x0000) . $this->_formula1;
      $data .= pack("vv", $formula2_size, 0x0000) . $this->_formula2;

      return $data;
   }
}

/*class Spreadsheet_Excel_Writer_Validation_List extends Spreadsheet_Excel_Writer_Validation
{
   function Spreadsheet_Excel_Writer_Validation_list()
   {
      parent::Spreadsheet_Excel_Writer_Validation();
      $this->_type = 0x03;
   }

   function setList($source, $incell = true)
   {
      $this->_incell = $incell;
      $this->_fixedList = true;

      $source = implode("\x00", $source);
      $this->_formula1 = pack("CCC", 0x17, strlen($source), 0x0c) . $source;
   }

   function setRow($row, $col1, $col2, $incell = true)
   {
      $this->_incell = $incell;
      //$this->_formula1 = ...;
   }

   function setCol($col, $row1, $row2, $incell = true)
   {
      $this->_incell = $incell;
      //$this->_formula1 = ...;
   }
}*/

?>
