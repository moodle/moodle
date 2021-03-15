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

use \DOMDocument;
use \Iterator;
use \ArrayAccess;

/**
 * Date Type of CFPropertyList
 * Note: CFDate uses Unix timestamp (epoch) to store dates internally
 */
class CFDate extends CFType
{
    const TIMESTAMP_APPLE = 0;
    const TIMESTAMP_UNIX  = 1;
    const DATE_DIFF_APPLE_UNIX = 978307200;

   /**
    * Create new Date CFType.
    * @param integer $value timestamp to set
    * @param integer $format format the timestamp is specified in, use {@link TIMESTAMP_APPLE} or {@link TIMESTAMP_UNIX}, defaults to {@link TIMESTAMP_APPLE}
    * @uses setValue() to convert the timestamp
    */
    public function __construct($value, $format = CFDate::TIMESTAMP_UNIX)
    {
        $this->setValue($value, $format);
    }

   /**
    * Set the Date CFType's value.
    * @param integer $value timestamp to set
    * @param integer $format format the timestamp is specified in, use {@link TIMESTAMP_APPLE} or {@link TIMESTAMP_UNIX}, defaults to {@link TIMESTAMP_UNIX}
    * @return void
    * @uses TIMESTAMP_APPLE to determine timestamp type
    * @uses TIMESTAMP_UNIX to determine timestamp type
    * @uses DATE_DIFF_APPLE_UNIX to convert Apple-timestamp to Unix-timestamp
    */
    public function setValue($value, $format = CFDate::TIMESTAMP_UNIX)
    {
        if ($format == CFDate::TIMESTAMP_UNIX) {
            $this->value = $value;
        } else {
            $this->value = $value + CFDate::DATE_DIFF_APPLE_UNIX;
        }
    }

  /**
   * Get the Date CFType's value.
   * @param integer $format format the timestamp is specified in, use {@link TIMESTAMP_APPLE} or {@link TIMESTAMP_UNIX}, defaults to {@link TIMESTAMP_UNIX}
   * @return integer Unix timestamp
   * @uses TIMESTAMP_APPLE to determine timestamp type
   * @uses TIMESTAMP_UNIX to determine timestamp type
   * @uses DATE_DIFF_APPLE_UNIX to convert Unix-timestamp to Apple-timestamp
   */
    public function getValue($format = CFDate::TIMESTAMP_UNIX)
    {
        if ($format == CFDate::TIMESTAMP_UNIX) {
            return $this->value;
        } else {
            return $this->value - CFDate::DATE_DIFF_APPLE_UNIX;
        }
    }

 /**
  * Get XML-Node.
  * @param DOMDocument $doc DOMDocument to create DOMNode in
  * @param string $nodeName For compatibility reasons; just ignore it
  * @return DOMNode &lt;date&gt;-Element
  */
    public function toXML(DOMDocument $doc, $nodeName = "")
    {
        $text = $doc->createTextNode(gmdate("Y-m-d\TH:i:s\Z", $this->getValue()));
        $node = $doc->createElement("date");
        $node->appendChild($text);
        return $node;
    }

 /**
  * convert value to binary representation
  * @param CFBinaryPropertyList The binary property list object
  * @return The offset in the object table
  */
    public function toBinary(CFBinaryPropertyList &$bplist)
    {
        return $bplist->dateToBinary($this->value);
    }

 /**
  * Create a UNIX timestamp from a PList date string
  * @param string $val The date string (e.g. "2009-05-13T20:23:43Z")
  * @return integer The UNIX timestamp
  * @throws PListException when encountering an unknown date string format
  */
    public static function dateValue($val)
    {
      //2009-05-13T20:23:43Z
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z/', $val, $matches)) {
            throw new PListException("Unknown date format: $val");
        }
        return gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
    }
}
