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
 * Array Type of CFPropertyList
 */
class CFArray extends CFType implements Iterator, ArrayAccess
{
  /**
   * Position of iterator {@link http://php.net/manual/en/class.iterator.php}
   * @var integer
   */
    protected $iteratorPosition = 0;


  /**
   * Create new CFType.
   * @param array $value Value of CFType
   */
    public function __construct($value = array())
    {
        $this->value = $value;
    }

  /**
   * Set the CFType's value
   * <b>Note:</b> this dummy does nothing
   * @return void
   */
    public function setValue($value)
    {
    }

  /**
   * Add CFType to collection.
   * @param CFType $value CFType to add to collection, defaults to null which results in an empty {@link CFString}
   * @return void
   * @uses $value for adding $value
   */
    public function add(CFType $value = null)
    {
      // anything but CFType is null, null is an empty string - sad but true
        if (!$value) {
            $value = new CFString();
        }

        $this->value[] = $value;
    }

  /**
   * Get CFType from collection.
   * @param integer $key Key of CFType to retrieve from collection
   * @return CFType CFType found at $key, null else
   * @uses $value for retrieving CFType of $key
   */
    public function get($key)
    {
        if (isset($this->value[$key])) {
            return $this->value[$key];
        }
        return null;
    }

  /**
   * Remove CFType from collection.
   * @param integer $key Key of CFType to removes from collection
   * @return CFType removed CFType, null else
   * @uses $value for removing CFType of $key
   */
    public function del($key)
    {
        if (isset($this->value[$key])) {
            unset($this->value[$key]);
        }
    }


  /************************************************************************************************
   *    S E R I A L I Z I N G
   ************************************************************************************************/

  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;array&gt;-Element
   */
    public function toXML(DOMDocument $doc, $nodeName = "")
    {
        $node = $doc->createElement('array');

        foreach ($this->value as $value) {
            $node->appendChild($value->toXML($doc));
        }
        return $node;
    }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
    public function toBinary(CFBinaryPropertyList &$bplist)
    {
        return $bplist->arrayToBinary($this);
    }

  /**
   * Get CFType's value.
   * @return array primitive value
   * @uses $value for retrieving primitive of CFType
   */
    public function toArray()
    {
        $a = array();
        foreach ($this->value as $value) {
            $a[] = $value->toArray();
        }
        return $a;
    }


  /************************************************************************************************
   *    I T E R A T O R   I N T E R F A C E
   ************************************************************************************************/

  /**
   * Rewind {@link $iteratorPosition} to first position (being 0)
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void
   * @uses $iteratorPosition set to 0
   */
    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

  /**
   * Get Iterator's current {@link CFType} identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed current Item
   * @uses $iteratorPosition identify current key
   */
   #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->value[$this->iteratorPosition];
    }

  /**
   * Get Iterator's current key identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed key of the current Item: mixed
   * @uses $iteratorPosition identify current key
   */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->iteratorPosition;
    }

  /**
   * Increment {@link $iteratorPosition} to address next {@see CFType}
   * @link http://php.net/manual/en/iterator.next.php
   * @return void
   * @uses $iteratorPosition increment by 1
   */
    public function next(): void
    {
        $this->iteratorPosition++;
    }

  /**
   * Test if {@link $iteratorPosition} addresses a valid element of {@link $value}
   * @link http://php.net/manual/en/iterator.valid.php
   * @return bool true if current position is valid, false else
   * @uses $iteratorPosition test if within {@link $iteratorKeys}
   * @uses $iteratorPosition test if within {@link $value}
   */
    public function valid(): bool
    {
        return isset($this->value[$this->iteratorPosition]);
    }

  /************************************************************************************************
   *    ArrayAccess   I N T E R F A C E
   ************************************************************************************************/

  /**
   * Determine if the array's key exists
   * @param string $offset the key to check
   * @return bool true if the offset exists, false if not
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @uses $value to check if $key exists
   * @author Sean Coates <sean@php.net>
   */
    public function offsetExists($offset): bool
    {
        return isset($this->value[$offset]);
    }

  /**
   * Fetch a specific key from the CFArray
   * @param mixed $offset the key to check
   * @return mixed the value associated with the key; null if the key is not found
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @uses get() to get the key's value
   * @author Sean Coates <sean@php.net>
   */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

  /**
   * Set a value in the array
   * @param mixed $offset the key to set
   * @param mixed $value the value to set
   * @return void
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @uses setValue() to set the key's new value
   * @author Sean Coates <sean@php.net>
   */
    public function offsetSet($offset, $value): void
    {
        $this->setValue($value);
    }

  /**
   * Unsets a value in the array
   * <b>Note:</b> this dummy does nothing
   * @param mixed $offset the key to set
   * @return void
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @author Sean Coates <sean@php.net>
   */
    public function offsetUnset($offset): void
    {
    }
}
