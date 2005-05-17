<?php
//
// +----------------------------------------------------------------------+
// | Base32 Library                                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 The PHP Group                                     |
// +----------------------------------------------------------------------+
// | This source file is dual-licensed. It is available under the terms   | 
// | of the GNU GPL v2.0 and under the terms of the PHP license version   |
// | 2.02,  available at through the world-wide-web at                    |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// +----------------------------------------------------------------------+
// |  Minor fixes and additional functions by Allan Hansen.               |
// |  Moodle porting work by Martin Langhoff                              |
// +----------------------------------------------------------------------+
// | base32.php - based on race.php  - RACE encode and decode strings.    |
// +----------------------------------------------------------------------+
// | Authors: Allan Hansen  <All@nHansen.dk>                              |
// |          Arjan Wekking <a.wekking@synantics.nl>                      |
// |          Martin Langhoff <martin@catalyst.net.nz>                    |
// +----------------------------------------------------------------------+
//

/**
 * Base32 encode a binary string
 *
 * @param    $inString   Binary string to base32 encode
 *
 * @return   $outString  Base32 encoded $inString
 *
 * @access   private
 *
 */

function base32_encode ($inString) 
{ 
    $outString = ""; 
    $compBits = ""; 
    $BASE32_TABLE = array( 
                          '00000' => 0x61, 
                          '00001' => 0x62, 
                          '00010' => 0x63, 
                          '00011' => 0x64, 
                          '00100' => 0x65, 
                          '00101' => 0x66, 
                          '00110' => 0x67, 
                          '00111' => 0x68, 
                          '01000' => 0x69, 
                          '01001' => 0x6a, 
                          '01010' => 0x6b, 
                          '01011' => 0x6c, 
                          '01100' => 0x6d, 
                          '01101' => 0x6e, 
                          '01110' => 0x6f, 
                          '01111' => 0x70, 
                          '10000' => 0x71, 
                          '10001' => 0x72, 
                          '10010' => 0x73, 
                          '10011' => 0x74, 
                          '10100' => 0x75, 
                          '10101' => 0x76, 
                          '10110' => 0x77, 
                          '10111' => 0x78, 
                          '11000' => 0x79, 
                          '11001' => 0x7a, 
                          '11010' => 0x32, 
                          '11011' => 0x33, 
                          '11100' => 0x34, 
                          '11101' => 0x35, 
                          '11110' => 0x36, 
                          '11111' => 0x37, 
                          ); 
    
    /* Turn the compressed string into a string that represents the bits as 0 and 1. */
    for ($i = 0; $i < strlen($inString); $i++) {
        $compBits .= str_pad(decbin(ord(substr($inString,$i,1))), 8, '0', STR_PAD_LEFT);
    }
    
    /* Pad the value with enough 0's to make it a multiple of 5 */
    if((strlen($compBits) % 5) != 0) {
        $compBits = str_pad($compBits, strlen($compBits)+(5-(strlen($compBits)%5)), '0', STR_PAD_RIGHT);
    }
    
    /* Create an array by chunking it every 5 chars */
    $fiveBitsArray = split("\n",rtrim(chunk_split($compBits, 5, "\n"))); 
    
    /* Look-up each chunk and add it to $outstring */
    foreach($fiveBitsArray as $fiveBitsString) { 
        $outString .= chr($BASE32_TABLE[$fiveBitsString]); 
    } 
    
    return $outString; 
} 



/**
 * Base32 decode to a binary string
 *
 * @param    $inString   String to base32 decode
 *
 * @return   $outString  Base32 decoded $inString
 *
 * @access   private
 *
 */

function Base32_decode($inString) {
    /* declaration */
    $inputCheck = null;
    $deCompBits = null;
    
    $BASE32_TABLE = array( 
                          0x61 => '00000', 
                          0x62 => '00001', 
                          0x63 => '00010', 
                          0x64 => '00011', 
                          0x65 => '00100', 
                          0x66 => '00101', 
                          0x67 => '00110', 
                          0x68 => '00111', 
                          0x69 => '01000', 
                          0x6a => '01001', 
                          0x6b => '01010', 
                          0x6c => '01011', 
                          0x6d => '01100', 
                          0x6e => '01101', 
                          0x6f => '01110', 
                          0x70 => '01111', 
                          0x71 => '10000', 
                          0x72 => '10001', 
                          0x73 => '10010', 
                          0x74 => '10011', 
                          0x75 => '10100', 
                          0x76 => '10101', 
                          0x77 => '10110', 
                          0x78 => '10111', 
                          0x79 => '11000', 
                          0x7a => '11001', 
                          0x32 => '11010', 
                          0x33 => '11011', 
                          0x34 => '11100', 
                          0x35 => '11101', 
                          0x36 => '11110', 
                          0x37 => '11111', 
                          ); 
    
    /* Step 1 */
    $inputCheck = strlen($inString) % 8;
    if(($inputCheck == 1)||($inputCheck == 3)||($inputCheck == 6)) { 
        trigger_error('input to Base32Decode was a bad mod length: '.$inputCheck);
        return false; 
        //return $this->raiseError('input to Base32Decode was a bad mod length: '.$inputCheck, null, 
        // PEAR_ERROR_DIE, null, null, 'Net_RACE_Error', false );
    }
    
    /* $deCompBits is a string that represents the bits as 0 and 1.*/
    for ($i = 0; $i < strlen($inString); $i++) {
        $inChar = ord(substr($inString,$i,1));
        if(isset($BASE32_TABLE[$inChar])) {
            $deCompBits .= $BASE32_TABLE[$inChar];
        } else {
            trigger_error('input to Base32Decode had a bad character: '.$inChar);
            return false;
            //return $this->raiseError('input to Base32Decode had a bad character: '.$inChar, null, 
            //    PEAR_ERROR_DIE, null, null, 'Net_RACE_Error', false );
        }
    }
    
    /* Step 5 */
    $padding = strlen($deCompBits) % 8;
    $paddingContent = substr($deCompBits, (strlen($deCompBits) - $padding));
    if(substr_count($paddingContent, '1')>0) { 
        trigger_error('found non-zero padding in Base32Decode');
        return false;
        //return $this->raiseError('found non-zero padding in Base32Decode', null, 
        //    PEAR_ERROR_DIE, null, null, 'Net_RACE_Error', false );
    }
    
    /* Break the decompressed string into octets for returning */
    $deArr = array();
    for($i = 0; $i < (int)(strlen($deCompBits) / 8); $i++) {
        $deArr[$i] = chr(bindec(substr($deCompBits, $i*8, 8)));
    }
    
    $outString = join('',$deArr);
    
    return $outString;
}

?>