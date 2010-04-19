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
* Class for generating Excel XF records (formats)
*
* @author Xavier Noguer <xnoguer@rezebra.com>
* @package Spreadsheet_WriteExcel
*/

class Format
{
  /**
  * Constructor
  *
  * @access public
  * @param integer $index the XF index for the format.
  * @param array   $properties array with properties to be set on initialization.
  */
    function Format($index = 0,$properties =  array())
    {
        $this->xf_index       = $index;
    
        $this->font_index     = 0;
        $this->font           = 'Arial';
        $this->size           = 10;
        $this->bold           = 0x0190;
        $this->_italic        = 0;
        $this->color          = 0x7FFF;
        $this->_underline     = 0;
        $this->font_strikeout = 0;
        $this->font_outline   = 0;
        $this->font_shadow    = 0;
        $this->font_script    = 0;
        $this->font_family    = 0;
        $this->font_charset   = 0;
    
        $this->_num_format    = 0;
    
        $this->hidden         = 0;
        $this->locked         = 1;
    
        $this->_text_h_align  = 0;
        $this->_text_wrap     = 0;
        $this->text_v_align   = 2;
        $this->text_justlast  = 0;
        $this->rotation       = 0;
    
        $this->fg_color       = 0x40;
        $this->bg_color       = 0x41;
    
        $this->pattern        = 0;
    
        $this->bottom         = 0;
        $this->top            = 0;
        $this->left           = 0;
        $this->right          = 0;
    
        $this->bottom_color   = 0x40;
        $this->top_color      = 0x40;
        $this->left_color     = 0x40;
        $this->right_color    = 0x40;
    
        // Set properties passed to Workbook::add_format()
        foreach($properties as $property => $value)
        {
            if(method_exists($this,"set_$property"))
            {
                $aux = 'set_'.$property;
                $this->$aux($value);
            }
        }
    }
    
    /**
    * Generate an Excel BIFF XF record (style or cell).
    *
    * @param string $style The type of the XF record ('style' or 'cell').
    * @return string The XF record
    */
    function get_xf($style)
    {
        // Set the type of the XF record and some of the attributes.
        if ($style == "style") {
            $style = 0xFFF5;
        }
        else {
            $style   = $this->locked;
            $style  |= $this->hidden << 1;
        }
    
        // Flags to indicate if attributes have been set.
        $atr_num     = ($this->_num_format != 0)?1:0;
        $atr_fnt     = ($this->font_index != 0)?1:0;
        $atr_alc     = ($this->_text_wrap)?1:0;
        $atr_bdr     = ($this->bottom   ||
                        $this->top      ||
                        $this->left     ||
                        $this->right)?1:0;
        $atr_pat     = (($this->fg_color != 0x40) ||
                        ($this->bg_color != 0x41) ||
                        $this->pattern)?1:0;
        $atr_prot    = 0;
    
        // Zero the default border colour if the border has not been set.
        if ($this->bottom == 0) {
            $this->bottom_color = 0;
            }
        if ($this->top  == 0) {
            $this->top_color = 0;
            }
        if ($this->right == 0) {
            $this->right_color = 0;
            }
        if ($this->left == 0) {
            $this->left_color = 0;
            }
    
        $record         = 0x00E0;              // Record identifier
        $length         = 0x0010;              // Number of bytes to follow
                                               
        $ifnt           = $this->font_index;   // Index to FONT record
        $ifmt           = $this->_num_format;  // Index to FORMAT record
    
        $align          = $this->_text_h_align;       // Alignment
        $align         |= $this->_text_wrap    << 3;
        $align         |= $this->text_v_align  << 4;
        $align         |= $this->text_justlast << 7;
        $align         |= $this->rotation      << 8;
        $align         |= $atr_num                << 10;
        $align         |= $atr_fnt                << 11;
        $align         |= $atr_alc                << 12;
        $align         |= $atr_bdr                << 13;
        $align         |= $atr_pat                << 14;
        $align         |= $atr_prot               << 15;
    
        $icv            = $this->fg_color;           // fg and bg pattern colors
        $icv           |= $this->bg_color      << 7;
    
        $fill           = $this->pattern;            // Fill and border line style
        $fill          |= $this->bottom        << 6;
        $fill          |= $this->bottom_color  << 9;
    
        $border1        = $this->top;                // Border line style and color
        $border1       |= $this->left          << 3;
        $border1       |= $this->right         << 6;
        $border1       |= $this->top_color     << 9;

        $border2        = $this->left_color;         // Border color
        $border2       |= $this->right_color   << 7;
    
        $header      = pack("vv",       $record, $length);
        $data        = pack("vvvvvvvv", $ifnt, $ifmt, $style, $align,
                                        $icv, $fill,
                                        $border1, $border2);
        return($header.$data);
    }
    
    /**
    * Generate an Excel BIFF FONT record.
    *
    * @see Workbook::_store_all_fonts()
    * @return string The FONT record
    */
    function get_font()
    {
        $dyHeight   = $this->size * 20;    // Height of font (1/20 of a point)
        $icv        = $this->color;        // Index to color palette
        $bls        = $this->bold;         // Bold style
        $sss        = $this->font_script;  // Superscript/subscript
        $uls        = $this->_underline;   // Underline
        $bFamily    = $this->font_family;  // Font family
        $bCharSet   = $this->font_charset; // Character set
        $rgch       = $this->font;         // Font name
    
        $cch        = strlen($rgch);       // Length of font name
        $record     = 0x31;                // Record identifier
        $length     = 0x0F + $cch;         // Record length
        $reserved   = 0x00;                // Reserved
        $grbit      = 0x00;                // Font attributes
        if ($this->_italic) {
            $grbit     |= 0x02;
        }
        if ($this->font_strikeout) {
            $grbit     |= 0x08;
        }
        if ($this->font_outline) {
            $grbit     |= 0x10;
        }
        if ($this->font_shadow) {
            $grbit     |= 0x20;
        }
    
        $header  = pack("vv",         $record, $length);
        $data    = pack("vvvvvCCCCC", $dyHeight, $grbit, $icv, $bls,
                                      $sss, $uls, $bFamily,
                                      $bCharSet, $reserved, $cch);
        return($header . $data. $this->font);
    }
    
    /**
    * Returns a unique hash key for a font. Used by Workbook->_store_all_fonts()
    *
    * The elements that form the key are arranged to increase the probability of
    * generating a unique key. Elements that hold a large range of numbers
    * (eg. _color) are placed between two binary elements such as _italic
    *
    * @return string A key for this font
    */
    function get_font_key()
    {
        $key  = "$this->font$this->size";
        $key .= "$this->font_script$this->_underline";
        $key .= "$this->font_strikeout$this->bold$this->font_outline";
        $key .= "$this->font_family$this->font_charset";
        $key .= "$this->font_shadow$this->color$this->_italic";
        $key  = str_replace(" ","_",$key);
        return ($key);
    }
    
    /**
    * Returns the index used by Worksheet->_XF()
    *
    * @return integer The index for the XF record
    */
    function get_xf_index()
    {
        return($this->xf_index);
    }
    
    /**
    * Used in conjunction with the set_xxx_color methods to convert a color
    * string into a number. Color range is 0..63 but we will restrict it
    * to 8..63 to comply with Gnumeric. Colors 0..7 are repeated in 8..15.
    *
    * @param string $name_color name of the color (i.e.: 'blue', 'red', etc..). Optional.
    * @return integer The color index
    */
    function _get_color($name_color = '')
    {
        $colors = array(
                        'aqua'    => 0x0F,
                        'cyan'    => 0x0F,
                        'black'   => 0x08,
                        'blue'    => 0x0C,
                        'brown'   => 0x10,
                        'magenta' => 0x0E,
                        'fuchsia' => 0x0E,
                        'gray'    => 0x17,
                        'grey'    => 0x17,
                        'green'   => 0x11,
                        'lime'    => 0x0B,
                        'navy'    => 0x12,
                        'orange'  => 0x35,
                        'purple'  => 0x14,
                        'red'     => 0x0A,
                        'silver'  => 0x16,
                        'white'   => 0x09,
                        'yellow'  => 0x0D
                       );
    
        // Return the default color, 0x7FFF, if undef,
        if($name_color == '') {
            return(0x7FFF);
        }
    
        // or the color string converted to an integer,
        if(isset($colors[$name_color])) {
            return($colors[$name_color]);
        }
    
        // or the default color if string is unrecognised,
        if(preg_match("/\D/",$name_color)) {
            return(0x7FFF);
        }
    
        // or an index < 8 mapped into the correct range,
        if($name_color < 8) {
            return($name_color + 8);
        }
    
        // or the default color if arg is outside range,
        if($name_color > 63) {
            return(0x7FFF);
        }
    
        // or an integer in the valid range
        return($name_color);
    }
    
    /**
    * Set cell alignment.
    *
    * @access public
    * @param string $location alignment for the cell ('left', 'right', etc...).
    */
    function set_align($location)
    {
        if (preg_match("/\d/",$location)) {
            return;                      // Ignore numbers
        }
    
        $location = strtolower($location);
    
        if ($location == 'left')
            $this->_text_h_align = 1; 
        if ($location == 'centre')
            $this->_text_h_align = 2; 
        if ($location == 'center')
            $this->_text_h_align = 2; 
        if ($location == 'right')
            $this->_text_h_align = 3; 
        if ($location == 'fill')
            $this->_text_h_align = 4; 
        if ($location == 'justify')
            $this->_text_h_align = 5;
        if ($location == 'merge')
            $this->_text_h_align = 6;
        if ($location == 'equal_space') // For T.K.
            $this->_text_h_align = 7; 
        if ($location == 'top')
            $this->text_v_align = 0; 
        if ($location == 'vcentre')
            $this->text_v_align = 1; 
        if ($location == 'vcenter')
            $this->text_v_align = 1; 
        if ($location == 'bottom')
            $this->text_v_align = 2; 
        if ($location == 'vjustify')
            $this->text_v_align = 3; 
        if ($location == 'vequal_space') // For T.K.
            $this->text_v_align = 4; 
    }
    
    /**
    * This is an alias for the unintuitive set_align('merge')
    *
    * @access public
    */
    function set_merge()
    {
        $this->set_align('merge');
    }
    
    /**
    * Bold has a range 0x64..0x3E8.
    * 0x190 is normal. 0x2BC is bold.
    *
    * @access public
    * @param integer $weight Weight for the text, 0 maps to 0x190, 1 maps to 0x2BC. 
                             It's Optional, default is 1 (bold).
    */
    function set_bold($weight = 1)
    {
        if($weight == 1) {
            $weight = 0x2BC;  // Bold text
        }
        if($weight == 0) {
            $weight = 0x190;  // Normal text
        }
        if($weight <  0x064) {
            $weight = 0x190;  // Lower bound
        }
        if($weight >  0x3E8) {
            $weight = 0x190;  // Upper bound
        }
        $this->bold = $weight;
    }
    
    
    /************************************
    * FUNCTIONS FOR SETTING CELLS BORDERS
    */
    
    /**
    * Sets the bottom border of the cell
    *
    * @access public
    * @param integer $style style of the cell border. 1 => thin, 2 => thick.
    */
    function set_bottom($style)
    {
        $this->bottom = $style;
    }
    
    /**
    * Sets the top border of the cell
    *
    * @access public
    * @param integer $style style of the cell top border. 1 => thin, 2 => thick.
    */
    function set_top($style)
    {
        $this->top = $style;
    }
    
    /**
    * Sets the left border of the cell
    *
    * @access public
    * @param integer $style style of the cell left border. 1 => thin, 2 => thick.
    */
    function set_left($style)
    {
        $this->left = $style;
    }
    
    /**
    * Sets the right border of the cell
    *
    * @access public
    * @param integer $style style of the cell right border. 1 => thin, 2 => thick.
    */
    function set_right($style)
    {
        $this->right = $style;
    }
    
    
    /**
    * Set cells borders to the same style
    *
    * @access public
    * @param integer $style style to apply for all cell borders. 1 => thin, 2 => thick.
    */
    function set_border($style)
    {
        $this->set_bottom($style);
        $this->set_top($style);
        $this->set_left($style);
        $this->set_right($style);
    }
    
    
    /*******************************************
    * FUNCTIONS FOR SETTING CELLS BORDERS COLORS
    */
    
    /**
    * Sets all the cell's borders to the same color
    *
    * @access public
    * @param mixed $color The color we are setting. Either a string (like 'blue'), 
    *                     or an integer (like 0x41).
    */
    function set_border_color($color)
    {
        $this->set_bottom_color($color);
        $this->set_top_color($color);
        $this->set_left_color($color);
        $this->set_right_color($color);
    }
    
    /**
    * Sets the cell's bottom border color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63]).
    */
    function set_bottom_color($color)
    {
        $value = $this->_get_color($color);
        $this->bottom_color = $value;
    }
    
    /**
    * Sets the cell's top border color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (range is [8...63]).
    */
    function set_top_color($color)
    {
        $value = $this->_get_color($color);
        $this->top_color = $value;
    }
    
    /**
    * Sets the cell's left border color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (like 0x41).
    */
    function set_left_color($color)
    {
        $value = $this->_get_color($color);
        $this->left_color = $value;
    }
    
    /**
    * Sets the cell's right border color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (like 0x41).
    */
    function set_right_color($color)
    {
        $value = $this->_get_color($color);
        $this->right_color = $value;
    }
    
    
    /**
    * Sets the cell's foreground color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (like 0x41).
    */
    function set_fg_color($color)
    {
        $value = $this->_get_color($color);
        $this->fg_color = $value;
    }
      
    /**
    * Sets the cell's background color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (like 0x41).
    */
    function set_bg_color($color)
    {
        $value = $this->_get_color($color);
        $this->bg_color = $value;
    }
    
    /**
    * Sets the cell's color
    *
    * @access public
    * @param mixed $color either a string (like 'blue'), or an integer (like 0x41).
    */
    function set_color($color)
    {
        $value = $this->_get_color($color);
        $this->color = $value;
    }
    
    /**
    * Sets the pattern attribute of a cell
    *
    * @access public
    * @param integer $arg Optional. Defaults to 1.
    */
    function set_pattern($arg = 1)
    {
        $this->pattern = $arg;
    }
    
    /**
    * Sets the underline of the text
    *
    * @access public
    * @param integer $underline The value for underline. Possible values are:
    *                          1 => underline, 2 => double underline.
    */
    function set_underline($underline)
    {
        $this->_underline = $underline;
    }
 
    /**
    * Sets the font style as italic
    *
    * @access public
    */
    function set_italic()
    {
        $this->_italic = 1;
    }

    /**
    * Sets the font size 
    *
    * @access public
    * @param integer $size The font size (in pixels I think).
    */
    function set_size($size)
    {
        $this->size = $size;
    }
    
    /**
    * Sets the num format
    *
    * @access public
    * @param integer $num_format The num format.
    */
    function set_num_format($num_format)
    {
        $this->_num_format = $num_format;
    }
    
    /**
    * Sets text wrapping
    *
    * @access public
    * @param integer $text_wrap Optional. 0 => no text wrapping, 1 => text wrapping. 
    *                           Defaults to 1.
    */
    function set_text_wrap($text_wrap = 1)
    {
        $this->_text_wrap = $text_wrap;
    }
}
?>