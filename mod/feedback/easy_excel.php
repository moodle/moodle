<?php

/**
* makes it easier to use the spreadsheet-functions from pear
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

require_once("../../config.php");
require_once("lib.php");

ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));
require_once 'Spreadsheet/Excel/Writer.php';

class EasyWorkbook extends Spreadsheet_Excel_Writer {
    function &addWorksheet($name = ''){
        global $CFG;

        $index      = count($this->_worksheets);
        $sheetname = $this->_sheetname;

        if ($name == '') {
            $name = $sheetname.($index+1);
        }

        // Check that sheetname is <= 31 chars (Excel limit before BIFF8).
        if ($this->_BIFF_version != 0x0600)
        {
            if (strlen($name) > 31) {
                return $this->raiseError("Sheetname $name must be <= 31 chars");
            }
        }

        // Check that the worksheet name doesn't already exist: a fatal Excel error.
        $total_worksheets = count($this->_worksheets);
        for ($i = 0; $i < $total_worksheets; $i++) {
            if ($this->_worksheets[$i]->getName() == $name) {
                 return $this->raiseError("Worksheet '$name' already exists");
            }
        }

        $worksheet = new EasyWorksheet($this->_BIFF_version,
                                          $name, $index,
                                          $this->_activesheet, $this->_firstsheet,
                                          $this->_str_total, $this->_str_unique,
                                          $this->_str_table, $this->_url_format,
                                          $this->_parser);

        $this->_worksheets[$index] = &$worksheet;     // Store ref for iterator
        $this->_sheetnames[$index] = $name;             // Store EXTERNSHEET names
        $this->_parser->setExtSheet($name, $index);  // Register worksheet name with parser

        if(!isset($CFG->latinexcelexport) || !$CFG->latinexcelexport) {
            $worksheet->setInputEncoding('UTF-16LE');
            // $worksheet->setInputEncoding('utf-8');
        }
        return $worksheet;
    }
}


class EasyWorksheet extends Spreadsheet_Excel_Writer_Worksheet {
    var $m_format;
    var $m_formatbox = array();
    var $m_workbook;

    function set_workbook(&$workbook) {
        $this->m_workbook =& $workbook;
    }

    function write($row, $col, $token)
    {
        parent::write($row, $col, $token, $this->m_format);
    }

    function write_number($row, $col, $num)
    {
        parent::writeNumber($row, $col, $num, $this->m_format);
    }

    function write_string($row, $col, $str)
    {
        parent::writeString($row, $col, feedback_convert_to_win($str), $this->m_format);
    }

    function write_formula($row, $col, $formula)
    {
        parent::writeFormula($row, $col, $formula, $this->m_format);
    }

    function write_url($row, $col, $url, $string = '')
    {
        parent::writeUrl($row, $col, $url, $string, $this->m_format);
    }

    /**
     *  Setz das aktuelle Format, dass zum Schreiben verwendet wird
     *  Der Formatstring setzt sich aus den folgenden Buchstaben mit folgender Bedeutung zusammen.
     *  <f> = Fett
     *  <k> = kursiv
     *  <z> = zentriert
     *  <l> = linksb�ndig
     *  <r> = rechtsb�ndig
     *  <vo> = vertikal oben
     *  <vz> = vertikal zentriert
     *  <vu> = vertikal unten
     *  <uX> = unterstrichen X=1-einfach, X=2-doppelt
     *  <w> = w�hrungsformat
     *  <pr> = prozentformat
     *  <ruX> = Rahmen unten X=St�rke
     *  <roX> = rahmen oben X=St�rke
     *  <rrX> = rahmen rechts X=St�rke
     *  <rlX> = rahmen links X=St�rke
     *  <c:XXX> = Schriftfarbe, XXX kann einen der folgenden Farbwerte enthalten:
     *      aqua,cyan,black,blue,brown,magenta,fuchsia,gray,
     *      grey,green,lime,navy,orange,purple,red,silver,white,yellow
     *      Wichtig: alle Werte m�ssen klein geschrieben werden.
     *  @param string $formatString
     *  @param int $size the size of font in pt
     *  @param boolean $textWrap
     *  @return void
     */
    function setFormat($formatString,$size = 10,$textWrap = true)
    {
        //looking for an existing format-signature at the $m_formatbox
        //this prevents the overflow of formats
        $signature = $formatString.'_'.$size.'_'.$textWrap;
        if((count($this->m_formatbox) > 0) AND array_key_exists($signature, $this->m_formatbox)) {
            $this->m_format = $this->m_formatbox[$signature];
        }else {

            $this->m_format = &$this->m_workbook->addFormat();
            if($textWrap)
            {
                $this->m_format->setTextWrap();
            }

            if(preg_match("/<f>/i",$formatString) > 0)
            {
                $this->m_format->setBold();
            }

            if(preg_match("/<k>/i",$formatString) > 0)
            {
                $this->m_format->setItalic();
            }

            if(preg_match("/<z>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("center");
            }

            if(preg_match("/<l>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("left");
            }

            if(preg_match("/<r>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("right");
            }

            if(preg_match("/<vo>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("top");
            }

            if(preg_match("/<vz>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("vcenter");
            }

            if(preg_match("/<vu>/i",$formatString) > 0)
            {
                $this->m_format->setAlign("bottom");
            }

            if(preg_match("/<u\d>/i",$formatString,$treffer) > 0)
            {
                $this->m_format->setUnderline(substr($treffer[0],2,1));
            }

            if(preg_match("/<w>/i",$formatString) > 0)
            {
                $this->m_format->setNumFormat("#,##0.00_)�;[Red]-#,##0.00_)�");
            }

            if(preg_match("/<pr>/i",$formatString) > 0)
            {
                $this->m_format->setNumFormat("#,##0.00%");
            }

            if(preg_match("/<ru\d>/i",$formatString,$treffer) > 0)
            {
                $this->m_format->setBottom(substr($treffer[0],3,1));
            }

            if(preg_match("/<ro\d>/i",$formatString,$treffer) > 0)
            {
                $this->m_format->setTop(substr($treffer[0],3,1));
            }

            if(preg_match("/<rr\d>/i",$formatString,$treffer) > 0)
            {
                $this->m_format->setRight(substr($treffer[0],3,1));
            }

            if(preg_match("/<rl\d>/i",$formatString,$treffer) > 0)
            {
                $this->m_format->setLeft(substr($treffer[0],3,1));
            }

            if(preg_match("/<c\:[^>]+>/",$formatString,$treffer) > 0)
            {
                $len = strlen($treffer[0]) - 4; //abzueglich der Zeichen <c:>
                $this->m_format->setColor(substr($treffer[0],3,$len));
            }

            $this->m_format->setSize($size);

            //save the format with its signature
            $this->m_formatbox[$signature] = $this->m_format;
        }
    }
}

function feedback_convert_to_win($text) {
    global $CFG;
    static $textlib;
    static $newwincharset;

    if(!isset($textlib)) {
        $textlib = textlib_get_instance();
    }

    if(!isset($newwincharset)) {
        if(!isset($CFG->latinexcelexport) || !$CFG->latinexcelexport) {
            $newwincharset = 'UTF-16LE';
        }else {
            $newwincharset = get_string('localewincharset', 'langconfig');
            if($newwincharset == '') {
                $newwincharset = 'windows-1252';
            }
        }
    }

    //converting <br /> into newline
    $newtext = str_ireplace('<br />', "\n", $text);
    $newtext = str_ireplace('<br>', "\n", $newtext);

    return $textlib->convert($newtext, 'UTF-8', $newwincharset);
}


