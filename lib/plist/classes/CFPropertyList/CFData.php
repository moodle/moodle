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

class CFData extends CFType
{
   /**
    * Create new Data CFType
    * @param string $value data to be contained by new object
    * @param boolean $already_coded if true $value will not be base64-encoded, defaults to false
    */
    public function __construct($value = null, $already_coded = false)
    {
        if ($already_coded) {
            $this->value = $value;
        } else {
            $this->setValue($value);
        }
    }

   /**
    * Set the CFType's value and base64-encode it.
    * <b>Note:</b> looks like base64_encode has troubles with UTF-8 encoded strings
    * @return void
    */
    public function setValue($value)
    {
        // if(function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) $value = utf8_decode($value);
        $this->value = base64_encode($value);
    }

   /**
    * Get base64 encoded data
    * @return string The base64 encoded data value
    */
    public function getCodedValue()
    {
        return $this->value;
    }

   /**
    * Get the base64-decoded CFType's value.
    * @return mixed CFType's value
    */
    public function getValue()
    {
        return base64_decode($this->value);
    }

   /**
    * Get XML-Node.
    * @param DOMDocument $doc DOMDocument to create DOMNode in
    * @param string $nodeName For compatibility reasons; just ignore it
    * @return DOMNode &lt;data&gt;-Element
    */
    public function toXML(DOMDocument $doc, $nodeName = "")
    {
        return parent::toXML($doc, 'data');
    }

   /**
    * convert value to binary representation
    * @param CFBinaryPropertyList The binary property list object
    * @return The offset in the object table
    */
    public function toBinary(CFBinaryPropertyList &$bplist)
    {
        return $bplist->dataToBinary($this->getValue());
    }
}
