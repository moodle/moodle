<?php
/**
 * LICENSE
 *
 * This file is part of CFPropertyList.
 *
 * The PHP implementation of Apple's PropertyList can handle XML PropertyLists
 * as well as binary PropertyLists. It offers functionality to easily convert
 * data between worlds, e.g. recalculating timestamps from unix epoch to apple
 * epoch and vice versa. A feature to automagically create (guess) the plist
 * structure from a normal PHP data structure will help you dump your data to
 * plist in no time.
 *
 * Copyright (c) 2018 Teclib'
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * ------------------------------------------------------------------------------
 * @author    Rodney Rehm <rodney.rehm@medialize.de>
 * @author    Christian Kruse <cjk@wwwtech.de>
 * @copyright Copyright © 2018 Teclib
 * @package   plist
 * @license   MIT
 * @link      https://github.com/TECLIB/CFPropertyList/
 * @link      http://developer.apple.com/documentation/Darwin/Reference/ManPages/man5/plist.5.html Property Lists
 * ------------------------------------------------------------------------------
 */

namespace CFPropertyList;

/**
 * Facility for reading and writing binary PropertyLists. Ported from {@link http://www.opensource.apple.com/source/CF/CF-476.15/CFBinaryPList.c CFBinaryPList.c}.
 * @example example-read-02.php Read a Binary PropertyList
 * @example example-read-03.php Read a PropertyList without knowing the type
 */
abstract class CFBinaryPropertyList
{
  /**
   * Content of the plist (unparsed string)
   * @var string
   */
    protected $content = null;

  /**
   * position in the (unparsed) string
   * @var integer
   */
    protected $pos = 0;

  /**
   * Table containing uniqued objects
   * @var array
   */
    protected $uniqueTable = array();

  /**
   * Number of objects in file
   * @var integer
   */
    protected $countObjects = 0;

  /**
   * The length of all strings in the file (byte length, not character length)
   * @var integer
   */
    protected $stringSize = 0;

  /**
   * The length of all ints in file (byte length)
   * @var integer
   */
    protected $intSize = 0;

  /**
   * The length of misc objects (i.e. not integer and not string) in file
   * @var integer
   */
    protected $miscSize = 0;

  /**
   * Number of object references in file (needed to calculate reference byte length)
   * @var integer
   */
    protected $objectRefs = 0;

  /**
   * Number of objects written during save phase; needed to calculate the size of the object table
   * @var integer
   */
    protected $writtenObjectCount = 0;

  /**
   * Table containing all objects in the file
   */
    protected $objectTable = array();

  /**
   * The size of object references
   */
    protected $objectRefSize = 0;

  /**
   * The „offsets” (i.e. the different entries) in the file
   */
    protected $offsets = array();

  /**
   * Read a „null type” (filler byte, true, false, 0 byte)
   * @param $length The byte itself
   * @return the byte value (e.g. CFBoolean(true), CFBoolean(false), 0 or 15)
   * @throws PListException on encountering an unknown null type
   */
    protected function readBinaryNullType($length)
    {
        switch ($length) {
            case 0:
                return 0; // null type
            case 8:
                return new CFBoolean(false);
            case 9:
                return new CFBoolean(true);
            case 15:
                return 15; // fill type
        }

        throw new PListException("unknown null type: $length");
    }

  /**
   * Create an 64 bit integer using bcmath or gmp
   * @param int $hi The higher word
   * @param int $lo The lower word
   * @return mixed The integer (as int if possible, as string if not possible)
   * @throws PListException if neither gmp nor bc available
   */
    protected static function make64Int($hi, $lo)
    {
      // on x64, we can just use int
        if (PHP_INT_SIZE > 4) {
            return (((int)$hi)<<32) | ((int)$lo);
        }

      // lower word has to be unsigned since we don't use bitwise or, we use bcadd/gmp_add
        $lo = sprintf("%u", $lo);

      // use GMP or bcmath if possible
        if (function_exists("gmp_mul")) {
            return gmp_strval(gmp_add(gmp_mul($hi, "4294967296"), $lo));
        }

        if (function_exists("bcmul")) {
            return bcadd(bcmul($hi, "4294967296"), $lo);
        }

        if (class_exists('Math_BigInteger')) {
            $bi = new \Math_BigInteger($hi);
            return $bi->multiply(new \Math_BigInteger("4294967296"))->add(new \Math_BigInteger($lo))->toString();
        }

        throw new PListException("either gmp or bc has to be installed, or the Math_BigInteger has to be available!");
    }

  /**
   * Read an integer value
   * @param integer $length The length (in bytes) of the integer value, coded as „set bit $length to 1”
   * @return CFNumber The integer value
   * @throws PListException if integer val is invalid
   * @throws IOException if read error occurs
   * @uses make64Int() to overcome PHP's big integer problems
   */
    protected function readBinaryInt($length)
    {
        if ($length > 3) {
            throw new PListException("Integer greater than 8 bytes: $length");
        }

        $nbytes = 1 << $length;

        $val = null;
        if (strlen($buff = substr($this->content, $this->pos, $nbytes)) != $nbytes) {
            throw IOException::readError("");
        }
        $this->pos += $nbytes;

        switch ($length) {
            case 0:
                $val = unpack("C", $buff);
                $val = $val[1];
                break;
            case 1:
                $val = unpack("n", $buff);
                $val = $val[1];
                break;
            case 2:
                $val = unpack("N", $buff);
                $val = $val[1];
                break;
            case 3:
                $words = unpack("Nhighword/Nlowword", $buff);
              //$val = $words['highword'] << 32 | $words['lowword'];
                $val = self::make64Int($words['highword'], $words['lowword']);
                break;
        }

        return new CFNumber($val);
    }

  /**
   * Read a real value
   * @param integer $length The length (in bytes) of the integer value, coded as „set bit $length to 1”
   * @return CFNumber The real value
   * @throws PListException if real val is invalid
   * @throws IOException if read error occurs
   */
    protected function readBinaryReal($length)
    {
        if ($length > 3) {
            throw new PListException("Real greater than 8 bytes: $length");
        }

        $nbytes = 1 << $length;
        $val = null;
        if (strlen($buff = substr($this->content, $this->pos, $nbytes)) != $nbytes) {
            throw IOException::readError("");
        }
        $this->pos += $nbytes;

        switch ($length) {
            case 0: // 1 byte float? must be an error
            case 1: // 2 byte float? must be an error
                $x = $length + 1;
                throw new PListException("got {$x} byte float, must be an error!");
            case 2:
                $val = unpack("f", strrev($buff));
                $val = $val[1];
                break;
            case 3:
                $val = unpack("d", strrev($buff));
                $val = $val[1];
                break;
        }

        return new CFNumber($val);
    }

  /**
   * Read a date value
   * @param integer $length The length (in bytes) of the integer value, coded as „set bit $length to 1”
   * @return CFDate The date value
   * @throws PListException if date val is invalid
   * @throws IOException if read error occurs
   */
    protected function readBinaryDate($length)
    {
        if ($length > 3) {
            throw new PListException("Date greater than 8 bytes: $length");
        }

        $nbytes = 1 << $length;
        $val = null;
        if (strlen($buff = substr($this->content, $this->pos, $nbytes)) != $nbytes) {
            throw IOException::readError("");
        }
        $this->pos += $nbytes;

        switch ($length) {
            case 0: // 1 byte CFDate is an error
            case 1: // 2 byte CFDate is an error
                $x = $length + 1;
                throw new PListException("{$x} byte CFdate, error");

            case 2:
                $val = unpack("f", strrev($buff));
                $val = $val[1];
                break;
            case 3:
                $val = unpack("d", strrev($buff));
                $val = $val[1];
                break;
        }

        return new CFDate($val, CFDate::TIMESTAMP_APPLE);
    }

  /**
   * Read a data value
   * @param integer $length The length (in bytes) of the integer value, coded as „set bit $length to 1”
   * @return CFData The data value
   * @throws IOException if read error occurs
   */
    protected function readBinaryData($length)
    {
        if ($length == 0) {
            $buff = "";
        } else {
            $buff = substr($this->content, $this->pos, $length);
            if (strlen($buff) != $length) {
                throw IOException::readError("");
            }
            $this->pos += $length;
        }

        return new CFData($buff, false);
    }

  /**
   * Read a string value, usually coded as utf8
   * @param integer $length The length (in bytes) of the string value
   * @return CFString The string value, utf8 encoded
   * @throws IOException if read error occurs
   */
    protected function readBinaryString($length)
    {
        if ($length == 0) {
            $buff = "";
        } else {
            if (strlen($buff = substr($this->content, $this->pos, $length)) != $length) {
                throw IOException::readError("");
            }
            $this->pos += $length;
        }

        if (!isset($this->uniqueTable[$buff])) {
            $this->uniqueTable[$buff] = true;
        }
        return new CFString($buff);
    }

  /**
   * Convert the given string from one charset to another.
   * Trying to use MBString, Iconv, Recode - in that particular order.
   * @param string $string the string to convert
   * @param string $fromCharset the charset the given string is currently encoded in
   * @param string $toCharset the charset to convert to, defaults to UTF-8
   * @return string the converted string
   * @throws PListException on neither MBString, Iconv, Recode being available
   */
    public static function convertCharset($string, $fromCharset, $toCharset = 'UTF-8')
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, $toCharset, $fromCharset);
        }
        if (function_exists('iconv')) {
            return iconv($fromCharset, $toCharset, $string);
        }
        if (function_exists('recode_string')) {
            return recode_string($fromCharset .'..'. $toCharset, $string);
        }

        throw new PListException('neither iconv nor mbstring supported. how are we supposed to work on strings here?');
    }

  /**
   * Count characters considering character set
   * Trying to use MBString, Iconv - in that particular order.
   * @param string $string the string to convert
   * @param string $charset the charset the given string is currently encoded in
   * @return integer The number of characters in that string
   * @throws PListException on neither MBString, Iconv being available
   */
    public static function charsetStrlen($string, $charset = "UTF-8")
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, $charset);
        }
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($string, $charset);
        }

        throw new PListException('neither iconv nor mbstring supported. how are we supposed to work on strings here?');
    }

  /**
   * Read a unicode string value, coded as UTF-16BE
   * @param integer $length The length (in bytes) of the string value
   * @return CFString The string value, utf8 encoded
   * @throws IOException if read error occurs
   */
    protected function readBinaryUnicodeString($length)
    {
      /* The problem is: we get the length of the string IN CHARACTERS;
         since a char in UTF-16 can be 16 or 32 bit long, we don't really know
         how long the string is in bytes */
        if (strlen($buff = substr($this->content, $this->pos, 2*$length)) != 2*$length) {
            throw IOException::readError("");
        }
        $this->pos += 2 * $length;

        if (!isset($this->uniqueTable[$buff])) {
            $this->uniqueTable[$buff] = true;
        }
        return new CFString(self::convertCharset($buff, "UTF-16BE", "UTF-8"));
    }

  /**
   * Read an array value, including contained objects
   * @param integer $length The number of contained objects
   * @return CFArray The array value, including the objects
   * @throws IOException if read error occurs
   */
    protected function readBinaryArray($length)
    {
        $ary = new CFArray();

      // first: read object refs
        if ($length != 0) {
            if (strlen($buff = substr($this->content, $this->pos, $length * $this->objectRefSize)) != $length * $this->objectRefSize) {
                throw IOException::readError("");
            }
            $this->pos += $length * $this->objectRefSize;

            $objects = self::unpackWithSize($this->objectRefSize, $buff);

          // now: read objects
            for ($i=0; $i<$length; ++$i) {
                $object = $this->readBinaryObjectAt($objects[$i+1]+1);
                $ary->add($object);
            }
        }

        return $ary;
    }

  /**
   * Read a dictionary value, including contained objects
   * @param integer $length The number of contained objects
   * @return CFDictionary The dictionary value, including the objects
   * @throws IOException if read error occurs
   */
    protected function readBinaryDict($length)
    {
        $dict = new CFDictionary();

        // first: read keys
        if ($length != 0) {
            if (strlen($buff = substr($this->content, $this->pos, $length * $this->objectRefSize)) != $length * $this->objectRefSize) {
                throw IOException::readError("");
            }
            $this->pos += $length * $this->objectRefSize;
            $keys = self::unpackWithSize($this->objectRefSize, $buff);

            // second: read object refs
            if (strlen($buff = substr($this->content, $this->pos, $length * $this->objectRefSize)) != $length * $this->objectRefSize) {
                throw IOException::readError("");
            }
            $this->pos += $length * $this->objectRefSize;
            $objects = self::unpackWithSize($this->objectRefSize, $buff);

            // read real keys and objects
            for ($i=0; $i<$length; ++$i) {
                $key = $this->readBinaryObjectAt($keys[$i+1]+1);
                $object = $this->readBinaryObjectAt($objects[$i+1]+1);
                $dict->add($key->getValue(), $object);
            }
        }

        return $dict;
    }

  /**
   * Read an object type byte, decode it and delegate to the correct reader function
   * @return mixed The value of the delegate reader, so any of the CFType subclasses
   * @throws IOException if read error occurs
   */
    public function readBinaryObject()
    {
        // first: read the marker byte
        if (strlen($buff = substr($this->content, $this->pos, 1)) != 1) {
            throw IOException::readError("");
        }
        $this->pos++;

        $object_length = unpack("C*", $buff);
        $object_length = $object_length[1]  & 0xF;
        $buff = unpack("H*", $buff);
        $buff = $buff[1];

        $object_type = substr($buff, 0, 1);
        if ($object_type != "0" && $object_length == 15) {
            $object_length = $this->readBinaryObject();
            $object_length = $object_length->getValue();
        }

        $retval = null;
        switch ($object_type) {
            case '0': // null, false, true, fillbyte
                $retval = $this->readBinaryNullType($object_length);
                break;
            case '1': // integer
                $retval = $this->readBinaryInt($object_length);
                break;
            case '2': // real
                $retval = $this->readBinaryReal($object_length);
                break;
            case '3': // date
                $retval = $this->readBinaryDate($object_length);
                break;
            case '4': // data
                $retval = $this->readBinaryData($object_length);
                break;
            case '5': // byte string, usually utf8 encoded
                $retval = $this->readBinaryString($object_length);
                break;
            case '6': // unicode string (utf16be)
                $retval = $this->readBinaryUnicodeString($object_length);
                break;
            case '8':
                $num = $this->readBinaryInt($object_length);
                $retval = new CFUid($num->getValue());
                break;
            case 'a': // array
                $retval = $this->readBinaryArray($object_length);
                break;
            case 'd': // dictionary
                $retval = $this->readBinaryDict($object_length);
                break;
        }

        return $retval;
    }

  /**
   * Read an object type byte at position $pos, decode it and delegate to the correct reader function
   * @param integer $pos The table position in the offsets table
   * @return mixed The value of the delegate reader, so any of the CFType subclasses
   */
    public function readBinaryObjectAt($pos)
    {
        $this->pos = $this->offsets[$pos];
        return $this->readBinaryObject();
    }

  /**
   * Parse a binary plist string
   * @return void
   * @throws IOException if read error occurs
   */
    public function parseBinaryString()
    {
        $this->uniqueTable = array();
        $this->countObjects = 0;
        $this->stringSize = 0;
        $this->intSize = 0;
        $this->miscSize = 0;
        $this->objectRefs = 0;

        $this->writtenObjectCount = 0;
        $this->objectTable = array();
        $this->objectRefSize = 0;

        $this->offsets = array();

        // first, we read the trailer: 32 byte from the end
        $buff = substr($this->content, -32);

        if (strlen($buff) < 32) {
            throw new PListException('Error in PList format: content is less than at least necessary 32 bytes!');
        }

        $infos = unpack("x6/Coffset_size/Cobject_ref_size/x4/Nnumber_of_objects/x4/Ntop_object/x4/Ntable_offset", $buff);

        // after that, get the offset table
        $coded_offset_table = substr($this->content, $infos['table_offset'], $infos['number_of_objects'] * $infos['offset_size']);
        if (strlen($coded_offset_table) != $infos['number_of_objects'] * $infos['offset_size']) {
            throw IOException::readError("");
        }
        $this->countObjects = $infos['number_of_objects'];

        // decode offset table
        $formats = array("","C*","n*",null,"N*");
        if ($infos['offset_size'] == 3) {
            /* since PHP does not support parenthesis in pack/unpack expressions,
               "(H6)*" does not work and we have to work round this by repeating the
               expression as often as it fits in the string
             */
            $this->offsets = array(null);
            while ($coded_offset_table) {
                $str = unpack("H6", $coded_offset_table);
                $this->offsets[] = hexdec($str[1]);
                $coded_offset_table = substr($coded_offset_table, 3);
            }
        } else {
            $this->offsets = unpack($formats[$infos['offset_size']], $coded_offset_table);
        }

        $this->uniqueTable = array();
        $this->objectRefSize = $infos['object_ref_size'];

        $top = $this->readBinaryObjectAt($infos['top_object']+1);
        $this->add($top);
    }

  /**
   * Read a binary plist stream
   * @param resource $stream The stream to read
   * @return void
   * @throws IOException if read error occurs
   */
    public function readBinaryStream($stream)
    {
        if (($str = stream_get_contents($stream)) === false || empty($str)) {
            throw new PListException("Error reading stream!");
        }

        $this->parseBinary($str);
    }

  /**
   * parse a binary plist string
   * @param string $content The stream to read, defaults to {@link $this->content}
   * @return void
   * @throws IOException if read error occurs
   */
    public function parseBinary($content = null)
    {
        if ($content !== null) {
            $this->content = $content;
        }

        if (empty($this->content)) {
            throw new PListException("Content may not be empty!");
        }

        if (substr($this->content, 0, 8) != 'bplist00') {
            throw new PListException("Invalid binary string!");
        }

        $this->pos = 0;

        $this->parseBinaryString();
    }

  /**
   * Read a binary plist file
   * @param string $file The file to read
   * @return void
   * @throws IOException if read error occurs
   */
    public function readBinary($file)
    {
        if (!($fd = fopen($file, "rb"))) {
            throw new IOException("Could not open file {$file}!");
        }

        $this->readBinaryStream($fd);
        fclose($fd);
    }

  /**
   * calculate the bytes needed for a size integer value
   * @param integer $int The integer value to calculate
   * @return integer The number of bytes needed
   */
    public static function bytesSizeInt($int)
    {
        $nbytes = 0;

        if ($int > 0xE) {
            $nbytes += 2; // 2 size-bytes
        }
        if ($int > 0xFF) {
            $nbytes += 1; // 3 size-bytes
        }
        if ($int > 0xFFFF) {
            $nbytes += 2; // 5 size-bytes
        }

        return $nbytes;
    }

  /**
   * Calculate the byte needed for a „normal” integer value
   * @param integer $int The integer value
   * @return integer The number of bytes needed + 1 (because of the „marker byte”)
   */
    public static function bytesInt($int)
    {
        $nbytes = 1;

        if ($int > 0xFF) {
            $nbytes += 1; // 2 byte integer
        }
        if ($int > 0xFFFF) {
            $nbytes += 2; // 4 byte integer
        }
        if ($int > 0xFFFFFFFF) {
            $nbytes += 4; // 8 byte integer
        }
        if ($int < 0) {
            $nbytes += 7; // 8 byte integer (since it is signed)
        }

        return $nbytes + 1; // one „marker” byte
    }

  /**
   * „pack” a value (i.e. write the binary representation as big endian to a string) with the specified size
   * @param integer $nbytes The number of bytes to pack
   * @param integer $int The integer value to pack
   * @return string The packed value as string
   */
    public static function packItWithSize($nbytes, $int)
    {
        $formats = array("C", "n", "N", "N");
        $format = $formats[$nbytes-1];

        if ($nbytes == 3) {
            return substr(pack($format, $int), -3);
        }
        return pack($format, $int);
    }

  /**
   * „unpack” multiple values of the specified size (i.e. get the integers from their binary representation) from a string
   * @param integer $nbytes The number of bytes of each value to unpack
   * @param integer $buff The string packed with integer values
   * @return array The unpacked integers
   */
    public static function unpackWithSize($nbytes, $buff)
    {
        $formats = array("C*", "n*", "N*", "N*");
        $format = $formats[$nbytes-1];

        if ($nbytes == 3) {
            $buff = "\0" . implode("\0", mb_str_split($buff, 3));
        }
        return unpack($format, $buff);
    }

  /**
   * Calculate the bytes needed to save the number of objects
   * @param integer $count_objects The number of objects
   * @return integer The number of bytes
   */
    public static function bytesNeeded($count_objects)
    {
        $nbytes = 0;

        while ($count_objects >= 1) {
            $nbytes++;
            $count_objects /= 256;
        }

        return $nbytes;
    }

  /**
   * Code an integer to byte representation
   * @param integer $int The integer value
   * @return string The packed byte value
   */
    public static function intBytes($int)
    {
        $intbytes = "";

        if ($int > 0xFFFF) {
            $intbytes = "\x12".pack("N", $int); // 4 byte integer
        } elseif ($int > 0xFF) {
            $intbytes = "\x11".pack("n", $int); // 2 byte integer
        } else {
            $intbytes = "\x10".pack("C", $int); // 8 byte integer
        }

        return $intbytes;
    }

  /**
   * Code an type byte, consisting of the type marker and the length of the type
   * @param string $type The type byte value (i.e. "d" for dictionaries)
   * @param integer $type_len The length of the type
   * @return string The packed type byte value
   */
    public static function typeBytes($type, $type_len)
    {
        $optional_int = "";

        if ($type_len < 15) {
            $type .= sprintf("%x", $type_len);
        } else {
            $type .= "f";
            $optional_int = self::intBytes($type_len);
        }

        return pack("H*", $type).$optional_int;
    }

  /**
   * Count number of objects and create a unique table for strings
   * @param $value The value to count and unique
   * @return void
   */
    protected function uniqueAndCountValues($value)
    {
      // no uniquing for other types than CFString and CFData
        if ($value instanceof CFNumber) {
            $val = $value->getValue();
            if (intval($val) == $val && !is_float($val) && strpos($val, '.') === false) {
                $this->intSize += self::bytesInt($val);
            } else {
                $this->miscSize += 9; // 9 bytes (8 + marker byte) for real
            }
            $this->countObjects++;
            return;
        } elseif ($value instanceof CFDate) {
            $this->miscSize += 9; // since date in plist is real, we need 9 byte (8 + marker byte)
            $this->countObjects++;
            return;
        } elseif ($value instanceof CFBoolean) {
            $this->countObjects++;
            $this->miscSize += 1;
            return;
        } elseif ($value instanceof CFArray) {
            $cnt = 0;
            foreach ($value as $v) {
                ++$cnt;
                $this->uniqueAndCountValues($v);
                $this->objectRefs++; // each array member is a ref
            }

            $this->countObjects++;
            $this->intSize += self::bytesSizeInt($cnt);
            $this->miscSize++; // marker byte for array
            return;
        } elseif ($value instanceof CFDictionary) {
            $cnt = 0;
            foreach ($value as $k => $v) {
                ++$cnt;
                if (!isset($this->uniqueTable[$k])) {
                    $this->uniqueTable[$k] = 0;
                    $len = self::binaryStrlen($k);
                    $this->stringSize += $len + 1;
                    $this->intSize += self::bytesSizeInt(self::charsetStrlen($k, 'UTF-8'));
                }

                $this->objectRefs += 2; // both, key and value, are refs
                $this->uniqueTable[$k]++;
                $this->uniqueAndCountValues($v);
            }

            $this->countObjects++;
            $this->miscSize++; // marker byte for dict
            $this->intSize += self::bytesSizeInt($cnt);
            return;
        } elseif ($value instanceof CFData) {
            $val = $value->getValue();
            $len = strlen($val);
            $this->intSize += self::bytesSizeInt($len);
            $this->miscSize += $len + 1;
            $this->countObjects++;
            return;
        } else {
            $val = $value->getValue();
        }

        if (!isset($this->uniqueTable[$val])) {
            $this->uniqueTable[$val] = 0;
            $len = self::binaryStrlen($val);
            $this->stringSize += $len + 1;
            $this->intSize += self::bytesSizeInt(self::charsetStrlen($val, 'UTF-8'));
        }
        $this->uniqueTable[$val]++;
    }

  /**
   * Convert CFPropertyList to binary format; since we have to count our objects we simply unique CFDictionary and CFArray
   * @return string The binary plist content
   */
    public function toBinary()
    {
        $this->uniqueTable = array();
        $this->countObjects = 0;
        $this->stringSize = 0;
        $this->intSize = 0;
        $this->miscSize = 0;
        $this->objectRefs = 0;

        $this->writtenObjectCount = 0;
        $this->objectTable = array();
        $this->objectRefSize = 0;

        $this->offsets = array();

        $binary_str = "bplist00";
        $value = $this->getValue(true);
        $this->uniqueAndCountValues($value);

        $this->countObjects += count($this->uniqueTable);
        $this->objectRefSize = self::bytesNeeded($this->countObjects);
        $file_size = $this->stringSize + $this->intSize + $this->miscSize + $this->objectRefs * $this->objectRefSize + 40;
        $offset_size = self::bytesNeeded($file_size);
        $table_offset = $file_size - 32;

        $this->objectTable = array();
        $this->writtenObjectCount = 0;
        $this->uniqueTable = array(); // we needed it to calculate several values
        $value->toBinary($this);

        $object_offset = 8;
        $offsets = array();

        for ($i=0; $i<count($this->objectTable); ++$i) {
            $binary_str .= $this->objectTable[$i];
            $offsets[$i] = $object_offset;
            $object_offset += strlen($this->objectTable[$i]);
        }

        for ($i=0; $i<count($offsets); ++$i) {
            $binary_str .= self::packItWithSize($offset_size, $offsets[$i]);
        }


        $binary_str .= pack("x6CC", $offset_size, $this->objectRefSize);
        $binary_str .= pack("x4N", $this->countObjects);
        $binary_str .= pack("x4N", 0);
        $binary_str .= pack("x4N", $table_offset);

        return $binary_str;
    }

  /**
   * Counts the number of bytes the string will have when coded; utf-16be if non-ascii characters are present.
   * @param string $val The string value
   * @return integer The length of the coded string in bytes
   */
    protected static function binaryStrlen($val)
    {
        $val = (string) $val;

        for ($i=0; $i<strlen($val); ++$i) {
            if (ord($val[$i]) >= 128) {
                $val = self::convertCharset($val, 'UTF-8', 'UTF-16BE');
                return strlen($val);
            }
        }

        return strlen($val);
    }

  /**
   * Uniques and transforms a string value to binary format and adds it to the object table
   * @param string $val The string value
   * @return integer The position in the object table
   */
    public function stringToBinary($val)
    {
        $saved_object_count = -1;

        if (!isset($this->uniqueTable[$val])) {
            $saved_object_count = $this->writtenObjectCount++;
            $this->uniqueTable[$val] = $saved_object_count;
            $utf16 = false;

            for ($i=0; $i<strlen($val); ++$i) {
                if (ord($val[$i]) >= 128) {
                    $utf16 = true;
                    break;
                }
            }

            if ($utf16) {
                $bdata = self::typeBytes("6", mb_strlen($val, 'UTF-8')); // 6 is 0110, unicode string (utf16be)
                $val = self::convertCharset($val, 'UTF-8', 'UTF-16BE');
                $this->objectTable[$saved_object_count] = $bdata.$val;
            } else {
                $bdata = self::typeBytes("5", strlen($val)); // 5 is 0101 which is an ASCII string (seems to be ASCII encoded)
                $this->objectTable[$saved_object_count] = $bdata.$val;
            }
        } else {
            $saved_object_count = $this->uniqueTable[$val];
        }

        return $saved_object_count;
    }

  /**
   * Codes an integer to binary format
   * @param integer $value The integer value
   * @return string the coded integer
   */
    protected function intToBinary($value)
    {
        $nbytes = 0;
        if ($value > 0xFF) {
            $nbytes = 1; // 1 byte integer
        }
        if ($value > 0xFFFF) {
            $nbytes += 1; // 4 byte integer
        }
        if ($value > 0xFFFFFFFF) {
            $nbytes += 1; // 8 byte integer
        }
        if ($value < 0) {
            $nbytes = 3; // 8 byte integer, since signed
        }

        $bdata = self::typeBytes("1", $nbytes); // 1 is 0001, type indicator for integer
        $buff = "";

        if ($nbytes < 3) {
            if ($nbytes == 0) {
                $fmt = "C";
            } elseif ($nbytes == 1) {
                $fmt = "n";
            } else {
                $fmt = "N";
            }

            $buff = pack($fmt, $value);
        } else {
            if (PHP_INT_SIZE > 4) {
              // 64 bit signed integer; we need the higher and the lower 32 bit of the value
                $high_word = $value >> 32;
                $low_word = $value & 0xFFFFFFFF;
            } else {
                // since PHP can only handle 32bit signed, we can only get 32bit signed values at this point - values above 0x7FFFFFFF are
                // floats. So we ignore the existance of 64bit on non-64bit-machines
                if ($value < 0) {
                    $high_word = 0xFFFFFFFF;
                } else {
                    $high_word = 0;
                }
                $low_word = $value;
            }
            $buff = pack("N", $high_word).pack("N", $low_word);
        }

        return $bdata.$buff;
    }

  /**
   * Codes a real value to binary format
   * @param float $val The real value
   * @return string The coded real
   */
    protected function realToBinary($val)
    {
        $bdata = self::typeBytes("2", 3); // 2 is 0010, type indicator for reals
        return $bdata.strrev(pack("d", (float)$val));
    }

    public function uidToBinary($value)
    {
        $saved_object_count = $this->writtenObjectCount++;

        $val = "";

        $nbytes = 0;
        if ($value > 0xFF) {
            $nbytes = 1; // 1 byte integer
        }
        if ($value > 0xFFFF) {
            $nbytes += 1; // 4 byte integer
        }
        if ($value > 0xFFFFFFFF) {
            $nbytes += 1; // 8 byte integer
        }
        if ($value < 0) {
            $nbytes = 3; // 8 byte integer, since signed
        }

        $bdata = self::typeBytes("1000", $nbytes); // 1 is 0001, type indicator for integer
        $buff = "";

        if ($nbytes < 3) {
            if ($nbytes == 0) {
                $fmt = "C";
            } elseif ($nbytes == 1) {
                $fmt = "n";
            } else {
                $fmt = "N";
            }

            $buff = pack($fmt, $value);
        } else {
            if (PHP_INT_SIZE > 4) {
                // 64 bit signed integer; we need the higher and the lower 32 bit of the value
                $high_word = $value >> 32;
                $low_word = $value & 0xFFFFFFFF;
            } else {
                // since PHP can only handle 32bit signed, we can only get 32bit signed values at this point - values above 0x7FFFFFFF are
                // floats. So we ignore the existance of 64bit on non-64bit-machines
                if ($value < 0) {
                    $high_word = 0xFFFFFFFF;
                } else {
                    $high_word = 0;
                }
                $low_word = $value;
            }
            $buff = pack("N", $high_word).pack("N", $low_word);
        }

        $val = $bdata.$buff;

        $this->objectTable[$saved_object_count] = $val;
        return $saved_object_count;
    }

  /**
   * Converts a numeric value to binary and adds it to the object table
   * @param numeric $value The numeric value
   * @return integer The position in the object table
   */
    public function numToBinary($value)
    {
        $saved_object_count = $this->writtenObjectCount++;

        $val = "";
        if (intval($value) == $value && !is_float($value) && strpos($value, '.') === false) {
            $val = $this->intToBinary($value);
        } else {
            $val = $this->realToBinary($value);
        }

        $this->objectTable[$saved_object_count] = $val;
        return $saved_object_count;
    }

  /**
   * Convert date value (apple format) to binary and adds it to the object table
   * @param integer $value The date value
   * @return integer The position of the coded value in the object table
   */
    public function dateToBinary($val)
    {
        $saved_object_count = $this->writtenObjectCount++;

        $hour = gmdate("H", $val);
        $min = gmdate("i", $val);
        $sec = gmdate("s", $val);
        $mday = gmdate("j", $val);
        $mon = gmdate("n", $val);
        $year = gmdate("Y", $val);

        $val = gmmktime($hour, $min, $sec, $mon, $mday, $year) - CFDate::DATE_DIFF_APPLE_UNIX; // CFDate is a real, number of seconds since 01/01/2001 00:00:00 GMT

        $bdata = self::typeBytes("3", 3); // 3 is 0011, type indicator for date
        $this->objectTable[$saved_object_count] = $bdata.strrev(pack("d", $val));

        return $saved_object_count;
    }

  /**
   * Convert a bool value to binary and add it to the object table
   * @param bool $val The boolean value
   * @return integer The position in the object table
   */
    public function boolToBinary($val)
    {
        $saved_object_count = $this->writtenObjectCount++;
        $this->objectTable[$saved_object_count] = $val ? "\x9" : "\x8"; // 0x9 is 1001, type indicator for true; 0x8 is 1000, type indicator for false
        return $saved_object_count;
    }

  /**
   * Convert data value to binary format and add it to the object table
   * @param string $val The data value
   * @return integer The position in the object table
   */
    public function dataToBinary($val)
    {
        $saved_object_count = $this->writtenObjectCount++;

        $bdata = self::typeBytes("4", strlen($val)); // a is 1000, type indicator for data
        $this->objectTable[$saved_object_count] = $bdata.$val;

        return $saved_object_count;
    }

  /**
   * Convert array to binary format and add it to the object table
   * @param CFArray $val The array to convert
   * @return integer The position in the object table
   */
    public function arrayToBinary($val)
    {
        $saved_object_count = $this->writtenObjectCount++;

        $bdata = self::typeBytes("a", count($val->getValue())); // a is 1010, type indicator for arrays

        foreach ($val as $v) {
            $bval = $v->toBinary($this);
            $bdata .= self::packItWithSize($this->objectRefSize, $bval);
        }

        $this->objectTable[$saved_object_count] = $bdata;
        return $saved_object_count;
    }

  /**
   * Convert dictionary to binary format and add it to the object table
   * @param CFDictionary $val The dict to convert
   * @return integer The position in the object table
   */
    public function dictToBinary($val)
    {
        $saved_object_count = $this->writtenObjectCount++;
        $bdata = self::typeBytes("d", count($val->getValue())); // d=1101, type indicator for dictionary

        foreach ($val as $k => $v) {
            $str = new CFString($k);
            $key = $str->toBinary($this);
            $bdata .= self::packItWithSize($this->objectRefSize, $key);
        }

        foreach ($val as $k => $v) {
            $bval = $v->toBinary($this);
            $bdata .= self::packItWithSize($this->objectRefSize, $bval);
        }

        $this->objectTable[$saved_object_count] = $bdata;
        return $saved_object_count;
    }
}

# eof
