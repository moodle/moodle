<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Xavier Noguer <xnoguer@php.net>                              |
// | Based on OLE::Storage_Lite by Kawai, Takanori                        |
// +----------------------------------------------------------------------+
//
// $Id$


/**
* Constants for OLE package
*/
define('OLE_PPS_TYPE_ROOT',        5);
define('OLE_PPS_TYPE_DIR',         1);
define('OLE_PPS_TYPE_FILE',        2);
define('OLE_DATA_SIZE_SMALL', 0x1000);
define('OLE_LONG_INT_SIZE',        4);
define('OLE_PPS_SIZE',          0x80);

require_once('PEAR.php');
require_once 'OLE/PPS.php';

/**
* OLE package base class.
*
* @author   Xavier Noguer <xnoguer@php.net>
* @category Structures
* @package  OLE
*/
class OLE extends PEAR
{
    /**
    * The file handle for reading an OLE container
    * @var resource
    */
    var $_file_handle;

    /**
    * Array of PPS's found on the OLE container
    * @var array
    */
    var $_list;

    /**
    * Creates a new OLE object
    * Remember to use ampersand when creating an OLE object ($my_ole =& new OLE();)
    * @access public
    */
    function OLE()
    {
        $this->_list = array();
    }

    /**
    * Reads an OLE container from the contents of the file given.
    *
    * @acces public
    * @param string $file
    * @return mixed true on success, PEAR_Error on failure
    */
    function read($file)
    {
        /* consider storing offsets as constants */
        $big_block_size_offset = 30;
        $iBdbCnt_offset        = 44;
        $bd_start_offset       = 68;

        $fh = @fopen($file, "r");
        if ($fh == false) {
            return $this->raiseError("Can't open file $file");
        }
        $this->_file_handle = $fh;

        /* begin reading OLE attributes */
        fseek($fh, 0);
        $signature = fread($fh, 8);
        if ("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" != $signature) {
            return $this->raiseError("File doesn't seem to be an OLE container.");
        }
        fseek($fh, $big_block_size_offset);
        $packed_array = unpack("v", fread($fh, 2));
        $big_block_size   = pow(2, $packed_array['']);

        $packed_array = unpack("v", fread($fh, 2));
        $small_block_size = pow(2, $packed_array['']);
        $i1stBdL = ($big_block_size - 0x4C) / OLE_LONG_INT_SIZE;

        fseek($fh, $iBdbCnt_offset);
        $packed_array = unpack("V", fread($fh, 4));
        $iBdbCnt = $packed_array[''];

        $packed_array = unpack("V", fread($fh, 4));
        $pps_wk_start = $packed_array[''];

        fseek($fh, $bd_start_offset);
        $packed_array = unpack("V", fread($fh, 4));
        $bd_start = $packed_array[''];
        $packed_array = unpack("V", fread($fh, 4));
        $bd_count = $packed_array[''];
        $packed_array = unpack("V", fread($fh, 4));
        $iAll = $packed_array[''];  // this may be wrong
        /* create OLE_PPS objects from */
        $ret = $this->_readPpsWks($pps_wk_start, $big_block_size);
        if (PEAR::isError($ret)) {
            return $ret;
        }
        return true;
    }

    /**
    * Destructor (using PEAR)
    * Just closes the file handle on the OLE file.
    *
    * @access private
    */
    function _OLE()
    {
        fclose($this->_file_handle);
    }

    /**
    * Gets information about all PPS's on the OLE container from the PPS WK's
    * creates an OLE_PPS object for each one.
    *
    * @access private
    * @param integer $pps_wk_start   Position inside the OLE file where PPS WK's start
    * @param integer $big_block_size Size of big blobks in the OLE file
    * @return mixed true on success, PEAR_Error on failure
    */
    function _readPpsWks($pps_wk_start, $big_block_size)
    {
        $pointer = ($pps_wk_start + 1) * $big_block_size;
        while (1)
        {
            fseek($this->_file_handle, $pointer);
            $pps_wk = fread($this->_file_handle, OLE_PPS_SIZE);
            if (strlen($pps_wk) != OLE_PPS_SIZE) {
                break; // Excel likes to add a trailing byte sometimes 
                //return $this->raiseError("PPS at $pointer seems too short: ".strlen($pps_wk));
            }
            $name_length = unpack("c", substr($pps_wk, 64, 2)); // FIXME (2 bytes??)
            $name_length = $name_length[''] - 2;
            $name = substr($pps_wk, 0, $name_length);
            $type = unpack("c", substr($pps_wk, 66, 1));
            if (($type[''] != OLE_PPS_TYPE_ROOT) and
                ($type[''] != OLE_PPS_TYPE_DIR) and
                ($type[''] != OLE_PPS_TYPE_FILE))
            {
                return $this->raiseError("PPS at $pointer has unknown type: {$type['']}");
            }
            $prev = unpack("V", substr($pps_wk, 68, 4));
            $next = unpack("V", substr($pps_wk, 72, 4));
            $dir  = unpack("V", substr($pps_wk, 76, 4));
            // there is no magic number, it can take different values.
            //$magic = unpack("V", strrev(substr($pps_wk, 92, 4)));
            $time_1st = substr($pps_wk, 100, 8);
            $time_2nd = substr($pps_wk, 108, 8);
            $start_block = unpack("V", substr($pps_wk, 116, 4));
            $size = unpack("V", substr($pps_wk, 120, 4));
            // _data member will point to position in file!!
            // OLE_PPS object is created with an empty children array!!
            $this->_list[] = new OLE_PPS(null, '', $type[''], $prev[''], $next[''],
                                         $dir[''], OLE::OLE2LocalDate($time_1st),
                                         OLE::OLE2LocalDate($time_2nd),
                                         ($start_block[''] + 1) * $big_block_size, array());
            // give it a size
            $this->_list[count($this->_list) - 1]->Size = $size[''];
            // check if the PPS tree (starting from root) is complete
            if ($this->_ppsTreeComplete(0)) {
                break;
            }
            $pointer += OLE_PPS_SIZE;
        }
    }
 
    /**
    * It checks whether the PPS tree is complete (all PPS's read)
    * starting with the given PPS (not necessarily root)
    *
    * @access private
    * @param integer $index The index of the PPS from which we are checking
    * @return boolean Whether the PPS tree for the given PPS is complete
    */
    function _ppsTreeComplete($index)
    {
        if ($this->_list[$index]->NextPps != -1) {
            if (!isset($this->_list[$this->_list[$index]->NextPps])) {
                return false;
            }
            else {
                return $this->_ppsTreeComplete($this->_list[$index]->NextPps);
            }
        }
        if ($this->_list[$index]->DirPps != -1) {
            if (!isset($this->_list[$this->_list[$index]->DirPps])) {
                return false;
            }
            else {
                return $this->_ppsTreeComplete($this->_list[$index]->DirPps);
            }
        }
        return true;
    }

    /** 
    * Checks whether a PPS is a File PPS or not.
    * If there is no PPS for the index given, it will return false.
    *
    * @access public
    * @param integer $index The index for the PPS
    * @return bool true if it's a File PPS, false otherwise
    */
    function isFile($index)
    {
        if (isset($this->_list[$index])) {
            return ($this->_list[$index]->Type == OLE_PPS_TYPE_FILE);
        }
        return false;
    }

    /** 
    * Checks whether a PPS is a Root PPS or not.
    * If there is no PPS for the index given, it will return false.
    *
    * @access public
    * @param integer $index The index for the PPS.
    * @return bool true if it's a Root PPS, false otherwise
    */
    function isRoot($index)
    {
        if (isset($this->_list[$index])) {
            return ($this->_list[$index]->Type == OLE_PPS_TYPE_ROOT);
        }
        return false;
    }

    /** 
    * Gives the total number of PPS's found in the OLE container.
    *
    * @access public
    * @return integer The total number of PPS's found in the OLE container
    */
    function ppsTotal()
    {
        return count($this->_list);
    }

    /**
    * Gets data from a PPS
    * If there is no PPS for the index given, it will return an empty string.
    *
    * @access public
    * @param integer $index    The index for the PPS
    * @param integer $position The position from which to start reading
    *                          (relative to the PPS)
    * @param integer $length   The amount of bytes to read (at most)
    * @return string The binary string containing the data requested
    */
    function getData($index, $position, $length)
    {
        // if position is not valid return empty string
        if (!isset($this->_list[$index]) or ($position >= $this->_list[$index]->Size) or ($position < 0)) {
            return '';
        }
        // Beware!!! _data member is actually a position
        fseek($this->_file_handle, $this->_list[$index]->_data + $position);
        return fread($this->_file_handle, $length);
    }
    
    /**
    * Gets the data length from a PPS
    * If there is no PPS for the index given, it will return 0.
    *
    * @access public
    * @param integer $index    The index for the PPS
    * @return integer The amount of bytes in data the PPS has
    */
    function getDataLength($index)
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Size;
        }
        return 0;
    }

    /**
    * Utility function to transform ASCII text to Unicode
    *
    * @access public
    * @static
    * @param string $ascii The ASCII string to transform
    * @return string The string in Unicode
    */
    function Asc2Ucs($ascii)
    {
        $rawname = '';
        for ($i = 0; $i < strlen($ascii); $i++) {
            $rawname .= $ascii{$i}."\x00";
        }
        return $rawname;
    }

    /**
    * Utility function
    * Returns a string for the OLE container with the date given
    *
    * @access public
    * @static
    * @param integer $date A timestamp 
    * @return string The string for the OLE container
    */
    function LocalDate2OLE($date = null)
    {
        if (!isset($date)) {
            return "\x00\x00\x00\x00\x00\x00\x00\x00";
        }

        // factor used for separating numbers into 4 bytes parts
        $factor = pow(2,32);

        // days from 1-1-1601 until the beggining of UNIX era
        $days = 134774;
        // calculate seconds
        $big_date = $days*24*3600 + gmmktime(date("H",$date),date("i",$date),date("s",$date),
                                             date("m",$date),date("d",$date),date("Y",$date));
        // multiply just to make MS happy
        $big_date *= 10000000;

        $high_part = floor($big_date/$factor);
        // lower 4 bytes
        $low_part = floor((($big_date/$factor) - $high_part)*$factor);

        // Make HEX string
        $res = '';

        for ($i=0; $i<4; $i++)
        {
            $hex = $low_part % 0x100;
            $res .= pack('c', $hex);
            $low_part /= 0x100;
        }
        for ($i=0; $i<4; $i++)
        {
            $hex = $high_part % 0x100;
            $res .= pack('c', $hex);
            $high_part /= 0x100;
        }
        return $res;
    }

    /**
    * Returns a timestamp from an OLE container's date
    *
    * @access public
    * @static
    * @param integer $string A binary string with the encoded date
    * @return string The timestamp corresponding to the string
    */
    function OLE2LocalDate($string)
    {
        if (strlen($string) != 8) {
            return new PEAR_Error("Expecting 8 byte string");
        }

        // factor used for separating numbers into 4 bytes parts
        $factor = pow(2,32);
        $high_part = 0;
        for ($i=0; $i<4; $i++)
        {
            $al = unpack('C', $string{(7 - $i)});
            $high_part += $al[''];
            if ($i < 3) {
                $high_part *= 0x100;
            }
        }
        $low_part = 0;
        for ($i=4; $i<8; $i++)
        {
            $al = unpack('C', $string{(7 - $i)});
            $low_part += $al[''];
            if ($i < 7) {
                $low_part *= 0x100;
            }
        }
        $big_date = ($high_part*$factor) + $low_part;
        // translate to seconds
        $big_date /= 10000000;
        
        // days from 1-1-1601 until the beggining of UNIX era
        $days = 134774;
        
        // translate to seconds from beggining of UNIX era
        $big_date -= $days*24*3600;
        return floor($big_date);
    }
}
?>
