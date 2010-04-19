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

/**
* Class for creating OLE streams for Excel Spreadsheets
*
* @author Xavier Noguer <xnoguer@rezebra.com>
* @package Spreadsheet_WriteExcel
*/
class OLEwriter
{
    /**
    * Filename for the OLE stream
    * @var string
    * @see _initialize()
    */
    var $_OLEfilename;

    /**
    * Filehandle for the OLE stream
    * @var resource
    */
    var $_filehandle;

    /**
    * Name of the temporal file in case OLE stream goes to stdout
    * @var string
    */
    var $_tmp_filename;

    /**
    * Variable for preventing closing two times
    * @var integer
    */
    var $_fileclosed;

    /**
    * Size of the data to be written to the OLE stream
    * @var integer
    */
    var $_biffsize;

    /**
    * Real data size to be written to the OLE stream
    * @var integer
    */
    var $_booksize;

    /**
    * Number of big blocks in the OLE stream
    * @var integer
    */
    var $_big_blocks;

    /**
    * Number of list blocks in the OLE stream
    * @var integer
    */
    var $_list_blocks;

    /**
    * Number of big blocks in the OLE stream
    * @var integer
    */
    var $_root_start;

    /**
    * Class for creating an OLEwriter
    *
    * @param string $OLEfilename the name of the file for the OLE stream
    */
    function OLEwriter($OLEfilename)
    {
        $this->_OLEfilename  = $OLEfilename;
        $this->_filehandle   = "";
        $this->_tmp_filename = "";
        $this->_fileclosed   = 0;
        //$this->_size_allowed = 0;
        $this->_biffsize     = 0;
        $this->_booksize     = 0;
        $this->_big_blocks   = 0;
        $this->_list_blocks  = 0;
        $this->_root_start   = 0;
        //$this->_block_count  = 4;
        $this->_initialize();
    }

/**
* Check for a valid filename and store the filehandle.
* Filehandle "-" writes to STDOUT
*/
    function _initialize()
    {
        $OLEfile = $this->_OLEfilename;
 
        if(($OLEfile == '-') or ($OLEfile == ''))
        {
            $this->_tmp_filename = tempnam("/tmp", "OLEwriter");
            $fh = fopen($this->_tmp_filename,"wb");
            if ($fh == false) {
                die("Can't create temporary file.");
            }
        }
        else
        {
            // Create a new file, open for writing (in binmode)
            $fh = fopen($OLEfile,"wb");
            if ($fh == false) {
                die("Can't open $OLEfile. It may be in use or protected.");
            }
        }

        // Store filehandle
        $this->_filehandle = $fh;
    }


    /**
    * Set the size of the data to be written to the OLE stream.
    * The maximun size comes from this:
    *   $big_blocks = (109 depot block x (128 -1 marker word)
    *                 - (1 x end words)) = 13842
    *   $maxsize    = $big_blocks * 512 bytes = 7087104
    *
    * @access public
    * @see Workbook::store_OLE_file()
    * @param integer $biffsize The size of the data to be written to the OLE stream
    * @return integer 1 for success
    */
    function set_size($biffsize)
    {
        $maxsize = 7087104; // TODO: extend max size
 
        if ($biffsize > $maxsize) {
            die("Maximum file size, $maxsize, exceeded.");
        }
 
        $this->_biffsize = $biffsize;
        // Set the min file size to 4k to avoid having to use small blocks
        if ($biffsize > 4096) {
            $this->_booksize = $biffsize;
        }
        else {
            $this->_booksize = 4096;
        }
        //$this->_size_allowed = 1;
        return(1);
    }


    /**
    * Calculate various sizes needed for the OLE stream
    */
    function _calculate_sizes()
    {
        $datasize = $this->_booksize;
        if ($datasize % 512 == 0) {
            $this->_big_blocks = $datasize/512;
        }
        else {
            $this->_big_blocks = floor($datasize/512) + 1;
        }
        // There are 127 list blocks and 1 marker blocks for each big block
        // depot + 1 end of chain block
        $this->_list_blocks = floor(($this->_big_blocks)/127) + 1;
        $this->_root_start  = $this->_big_blocks;
    }

    /**
    * Write root entry, big block list and close the filehandle.
    * This routine is used to explicitly close the open filehandle without
    * having to wait for DESTROY.
    *
    * @access public
    * @see Workbook::store_OLE_file()
    */
    function close() 
    {
        //return if not $this->{_size_allowed};
        $this->_write_padding();
        $this->_write_property_storage();
        $this->_write_big_block_depot();
        // Close the filehandle 
        fclose($this->_filehandle);
        if(($this->_OLEfilename == '-') or ($this->_OLEfilename == ''))
        {
            $fh = fopen($this->_tmp_filename, "rb");
            if ($fh == false) {
                die("Can't read temporary file.");
            }
            fpassthru($fh);
            // Delete the temporary file.
            @unlink($this->_tmp_filename);
        }
        $this->_fileclosed = 1;
    }


    /**
    * Write BIFF data to OLE file.
    *
    * @param string $data string of bytes to be written
    */
    function write($data) //por ahora s�lo a STDOUT
    {
        fwrite($this->_filehandle,$data,strlen($data));
    }


    /**
    * Write OLE header block.
    */
    function write_header()
    {
        $this->_calculate_sizes();
        $root_start      = $this->_root_start;
        $num_lists       = $this->_list_blocks;
        $id              = pack("nnnn", 0xD0CF, 0x11E0, 0xA1B1, 0x1AE1);
        $unknown1        = pack("VVVV", 0x00, 0x00, 0x00, 0x00);
        $unknown2        = pack("vv",   0x3E, 0x03);
        $unknown3        = pack("v",    -2);
        $unknown4        = pack("v",    0x09);
        $unknown5        = pack("VVV",  0x06, 0x00, 0x00);
        $num_bbd_blocks  = pack("V",    $num_lists);
        $root_startblock = pack("V",    $root_start);
        $unknown6        = pack("VV",   0x00, 0x1000);
        $sbd_startblock  = pack("V",    -2);
        $unknown7        = pack("VVV",  0x00, -2 ,0x00);
        $unused          = pack("V",    -1);
 
        fwrite($this->_filehandle,$id);
        fwrite($this->_filehandle,$unknown1);
        fwrite($this->_filehandle,$unknown2);
        fwrite($this->_filehandle,$unknown3);
        fwrite($this->_filehandle,$unknown4);
        fwrite($this->_filehandle,$unknown5);
        fwrite($this->_filehandle,$num_bbd_blocks);
        fwrite($this->_filehandle,$root_startblock);
        fwrite($this->_filehandle,$unknown6);
        fwrite($this->_filehandle,$sbd_startblock);
        fwrite($this->_filehandle,$unknown7);
 
        for($i=1; $i <= $num_lists; $i++)
        {
            $root_start++;
            fwrite($this->_filehandle,pack("V",$root_start));
        }
        for($i = $num_lists; $i <=108; $i++)
        {
            fwrite($this->_filehandle,$unused);
        }
    }


    /**
    * Write big block depot.
    */
    function _write_big_block_depot()
    {
        $num_blocks   = $this->_big_blocks;
        $num_lists    = $this->_list_blocks;
        $total_blocks = $num_lists *128;
        $used_blocks  = $num_blocks + $num_lists +2;
 
        $marker       = pack("V", -3);
        $end_of_chain = pack("V", -2);
        $unused       = pack("V", -1);
 
        for($i=1; $i < $num_blocks; $i++)
        {
            fwrite($this->_filehandle,pack("V",$i));
        }
        fwrite($this->_filehandle,$end_of_chain);
        fwrite($this->_filehandle,$end_of_chain);
        for($i=0; $i < $num_lists; $i++)
        {
            fwrite($this->_filehandle,$marker);
        }
        for($i=$used_blocks; $i <= $total_blocks; $i++)
        {
            fwrite($this->_filehandle,$unused);
        }
    }

/**
* Write property storage. TODO: add summary sheets
*/
    function _write_property_storage()
    {
        //$rootsize = -2;
        /***************  name         type   dir start size */
        $this->_write_pps("Root Entry", 0x05,   1,   -2, 0x00);
        $this->_write_pps("Book",       0x02,  -1, 0x00, $this->_booksize);
        $this->_write_pps('',           0x00,  -1, 0x00, 0x0000);
        $this->_write_pps('',           0x00,  -1, 0x00, 0x0000);
    }

/**
* Write property sheet in property storage
*
* @param string  $name  name of the property storage.
* @param integer $type  type of the property storage.
* @param integer $dir   dir of the property storage.
* @param integer $start start of the property storage.
* @param integer $size  size of the property storage.
* @access private
*/
    function _write_pps($name,$type,$dir,$start,$size)
    {
        $length  = 0;
        $rawname = '';
 
        if ($name != '')
        {
            $name = $name . "\0";
            for($i=0;$i<strlen($name);$i++)
            {
                // Simulate a Unicode string
                $rawname .= pack("H*",dechex(ord($name{$i}))).pack("C",0);
            }
            $length = strlen($name) * 2;
        }
       
        $zero            = pack("C",  0);
        $pps_sizeofname  = pack("v",  $length);    // 0x40
        $pps_type        = pack("v",  $type);      // 0x42
        $pps_prev        = pack("V",  -1);         // 0x44
        $pps_next        = pack("V",  -1);         // 0x48
        $pps_dir         = pack("V",  $dir);       // 0x4c
       
        $unknown1        = pack("V",  0);
       
        $pps_ts1s        = pack("V",  0);          // 0x64
        $pps_ts1d        = pack("V",  0);          // 0x68
        $pps_ts2s        = pack("V",  0);          // 0x6c
        $pps_ts2d        = pack("V",  0);          // 0x70
        $pps_sb          = pack("V",  $start);     // 0x74
        $pps_size        = pack("V",  $size);      // 0x78
       
       
        fwrite($this->_filehandle,$rawname);
        for($i=0; $i < (64 -$length); $i++) {
            fwrite($this->_filehandle,$zero);
        }
        fwrite($this->_filehandle,$pps_sizeofname);
        fwrite($this->_filehandle,$pps_type);
        fwrite($this->_filehandle,$pps_prev);
        fwrite($this->_filehandle,$pps_next);
        fwrite($this->_filehandle,$pps_dir);
        for($i=0; $i < 5; $i++) {
            fwrite($this->_filehandle,$unknown1);
        }
        fwrite($this->_filehandle,$pps_ts1s);
        fwrite($this->_filehandle,$pps_ts1d);
        fwrite($this->_filehandle,$pps_ts2d);
        fwrite($this->_filehandle,$pps_ts2d);
        fwrite($this->_filehandle,$pps_sb);
        fwrite($this->_filehandle,$pps_size);
        fwrite($this->_filehandle,$unknown1);
    }

    /**
    * Pad the end of the file
    */
    function _write_padding()
    {
        $biffsize = $this->_biffsize;
        if ($biffsize < 4096) {
	    $min_size = 4096;
        }
	else {    
            $min_size = 512;
        }
	if ($biffsize % $min_size != 0)
        {
            $padding  = $min_size - ($biffsize % $min_size);
            for($i=0; $i < $padding; $i++) {
                fwrite($this->_filehandle,"\0");
            }
        }
    }
}
?>