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
 *Base-Class of all CFTypes used by CFPropertyList.
 * @example example-create-01.php Using the CFPropertyList API
 * @example example-create-02.php Using CFPropertyList::guess()
 * @example example-create-03.php Using CFPropertyList::guess() with {@link CFDate} and {@link CFData}
 */
abstract class CFType
{
  /**
   * CFType nodes
   * @var array
   */
    protected $value = null;

  /**
   * Create new CFType.
   * @param mixed $value Value of CFType
   */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

  /************************************************************************************************
   *    M A G I C   P R O P E R T I E S
   ************************************************************************************************/

  /**
   * Get the CFType's value
   * @return mixed CFType's value
   */
    public function getValue()
    {
        return $this->value;
    }

  /**
   * Set the CFType's value
   * @return void
   */
    public function setValue($value)
    {
        $this->value = $value;
    }

  /************************************************************************************************
   *    S E R I A L I Z I N G
   ************************************************************************************************/

  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName Name of element to create
   * @return DOMNode Node created based on CType
   * @uses $value as nodeValue
   */
    public function toXML(DOMDocument $doc, $nodeName = "")
    {
        $node = $doc->createElement($nodeName);
        $text = $doc->createTextNode($this->value);
        $node->appendChild($text);
        return $node;
    }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
    abstract public function toBinary(CFBinaryPropertyList &$bplist);

  /**
   * Get CFType's value.
   * @return mixed primitive value
   * @uses $value for retrieving primitive of CFType
   */
    public function toArray()
    {
        return $this->getValue();
    }
}

# eof
