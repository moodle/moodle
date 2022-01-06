<?php
/**
 * Data-Types for CFPropertyList as defined by Apple.
 * {@link http://developer.apple.com/documentation/Darwin/Reference/ManPages/man5/plist.5.html Property Lists}
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 * @version $Id$
 */
namespace CFPropertyList;
use \DOMDocument, \Iterator, \ArrayAccess;

/**
 * Base-Class of all CFTypes used by CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 * @version $Id$
 * @example example-create-01.php Using the CFPropertyList API
 * @example example-create-02.php Using CFPropertyList::guess()
 * @example example-create-03.php Using CFPropertyList::guess() with {@link CFDate} and {@link CFData}
 */
abstract class CFType {
  /**
   * CFType nodes
   * @var array
   */
  protected $value = null;

  /**
   * Create new CFType.
   * @param mixed $value Value of CFType
   */
  public function __construct($value=null) {
    $this->setValue($value);
  }

  /************************************************************************************************
   *    M A G I C   P R O P E R T I E S
   ************************************************************************************************/

  /**
   * Get the CFType's value
   * @return mixed CFType's value
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Set the CFType's value
   * @return void
   */
  public function setValue($value) {
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
  public function toXML(DOMDocument $doc, $nodeName) {
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
  public abstract function toBinary(CFBinaryPropertyList &$bplist);

  /**
   * Get CFType's value.
   * @return mixed primitive value
   * @uses $value for retrieving primitive of CFType
   */
  public function toArray() {
    return $this->getValue();
  }

}

/**
 * String Type of CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFString extends CFType {
  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;string&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    return parent::toXML($doc, 'string');
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->stringToBinary($this->value);
  }
}

class CFUid extends CFType {
  public
  function toXML(DOMDocument $doc,$nodeName="") {
    $obj = new CFDictionary(array('CF$UID' => new CFNumber($this->value)));
    return $obj->toXml($doc);
  }

  public
  function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->uidToBinary($this->value);
  }
}

/**
 * Number Type of CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFNumber extends CFType {
  /**
   * Get XML-Node.
   * Returns &lt;real&gt; if $value is a float, &lt;integer&gt; if $value is an integer.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;real&gt; or &lt;integer&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    $ret = 'real';
    if(intval($this->value) == $this->value && !is_float($this->value) && strpos($this->value,'.') === false) {
      $this->value = intval($this->value);
      $ret = 'integer';
    }
    return parent::toXML($doc, $ret);
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->numToBinary($this->value);
  }
}

/**
 * Date Type of CFPropertyList
 * Note: CFDate uses Unix timestamp (epoch) to store dates internally
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFDate extends CFType {
  const TIMESTAMP_APPLE = 0;
  const TIMESTAMP_UNIX  = 1;
  const DATE_DIFF_APPLE_UNIX = 978307200;

  /**
   * Create new Date CFType.
   * @param integer $value timestamp to set
   * @param integer $format format the timestamp is specified in, use {@link TIMESTAMP_APPLE} or {@link TIMESTAMP_UNIX}, defaults to {@link TIMESTAMP_APPLE}
   * @uses setValue() to convert the timestamp
   */
  function __construct($value,$format=CFDate::TIMESTAMP_UNIX) {
    $this->setValue($value,$format);
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
  function setValue($value,$format=CFDate::TIMESTAMP_UNIX) {
    if($format == CFDate::TIMESTAMP_UNIX) $this->value = $value;
    else $this->value = $value + CFDate::DATE_DIFF_APPLE_UNIX;
  }

  /**
   * Get the Date CFType's value.
   * @param integer $format format the timestamp is specified in, use {@link TIMESTAMP_APPLE} or {@link TIMESTAMP_UNIX}, defaults to {@link TIMESTAMP_UNIX}
   * @return integer Unix timestamp
   * @uses TIMESTAMP_APPLE to determine timestamp type
   * @uses TIMESTAMP_UNIX to determine timestamp type
   * @uses DATE_DIFF_APPLE_UNIX to convert Unix-timestamp to Apple-timestamp
   */
  function getValue($format=CFDate::TIMESTAMP_UNIX) {
    if($format == CFDate::TIMESTAMP_UNIX) return $this->value;
    else return $this->value - CFDate::DATE_DIFF_APPLE_UNIX;
  }

  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;date&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    $text = $doc->createTextNode(gmdate("Y-m-d\TH:i:s\Z",$this->getValue()));
    $node = $doc->createElement("date");
    $node->appendChild($text);
    return $node;
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->dateToBinary($this->value);
  }

  /**
   * Create a UNIX timestamp from a PList date string
   * @param string $val The date string (e.g. "2009-05-13T20:23:43Z")
   * @return integer The UNIX timestamp
   * @throws PListException when encountering an unknown date string format
   */
  public static function dateValue($val) {
    //2009-05-13T20:23:43Z
    if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z/',$val,$matches)) throw new PListException("Unknown date format: $val");
    return gmmktime($matches[4],$matches[5],$matches[6],$matches[2],$matches[3],$matches[1]);
  }
}

/**
 * Boolean Type of CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFBoolean extends CFType {
  /**
   * Get XML-Node.
   * Returns &lt;true&gt; if $value is a true, &lt;false&gt; if $value is false.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;true&gt; or &lt;false&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    return $doc->createElement($this->value ? 'true' : 'false');
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->boolToBinary($this->value);
  }

}

/**
 * Data Type of CFPropertyList
 * Note: Binary data is base64-encoded.
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFData extends CFType {
  /**
   * Create new Data CFType
   * @param string $value data to be contained by new object
   * @param boolean $already_coded if true $value will not be base64-encoded, defaults to false
   */
  public function __construct($value=null,$already_coded=false) {
    if($already_coded) $this->value = $value;
    else $this->setValue($value);
  }

  /**
   * Set the CFType's value and base64-encode it.
   * <b>Note:</b> looks like base64_encode has troubles with UTF-8 encoded strings
   * @return void
   */
  public function setValue($value) {
    //if(function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) $value = utf8_decode($value);
    $this->value = base64_encode($value);
  }

  /**
   * Get base64 encoded data
   * @return string The base64 encoded data value
   */
  public function getCodedValue() {
    return $this->value;
  }

  /**
   * Get the base64-decoded CFType's value.
   * @return mixed CFType's value
   */
  public function getValue() {
    return base64_decode($this->value);
  }

  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;data&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    return parent::toXML($doc, 'data');
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->dataToBinary($this->getValue());
  }
}

/**
 * Array Type of CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFArray extends CFType implements Iterator, ArrayAccess {
  /**
   * Position of iterator {@link http://php.net/manual/en/class.iterator.php}
   * @var integer
   */
  protected $iteratorPosition = 0;


  /**
   * Create new CFType.
   * @param array $value Value of CFType
   */
  public function __construct($value=array()) {
    $this->value = $value;
  }

  /**
   * Set the CFType's value
   * <b>Note:</b> this dummy does nothing
   * @return void
   */
  public function setValue($value) {
  }

  /**
   * Add CFType to collection.
   * @param CFType $value CFType to add to collection, defaults to null which results in an empty {@link CFString}
   * @return void
   * @uses $value for adding $value
   */
  public function add(CFType $value=null) {
    // anything but CFType is null, null is an empty string - sad but true
    if( !$value )
      $value = new CFString();

    $this->value[] = $value;
  }

  /**
   * Get CFType from collection.
   * @param integer $key Key of CFType to retrieve from collection
   * @return CFType CFType found at $key, null else
   * @uses $value for retrieving CFType of $key
   */
  public function get($key) {
    if(isset($this->value[$key])) return $this->value[$key];
    return null;
  }

  /**
   * Remove CFType from collection.
   * @param integer $key Key of CFType to removes from collection
   * @return CFType removed CFType, null else
   * @uses $value for removing CFType of $key
   */
  public function del($key) {
    if(isset($this->value[$key])) unset($this->value[$key]);
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
  public function toXML(DOMDocument $doc,$nodeName="") {
    $node = $doc->createElement('array');

    foreach($this->value as $value) $node->appendChild($value->toXML($doc));
    return $node;
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->arrayToBinary($this);
  }

  /**
   * Get CFType's value.
   * @return array primitive value
   * @uses $value for retrieving primitive of CFType
   */
  public function toArray() {
    $a = array();
    foreach($this->value as $value) $a[] = $value->toArray();
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
  public function rewind() {
    $this->iteratorPosition = 0;
  }

  /**
   * Get Iterator's current {@link CFType} identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.current.php
   * @return CFType current Item
   * @uses $iteratorPosition identify current key
   */
  public function current() {
    return $this->value[$this->iteratorPosition];
  }

  /**
   * Get Iterator's current key identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.key.php
   * @return string key of the current Item
   * @uses $iteratorPosition identify current key
   */
  public function key() {
    return $this->iteratorPosition;
  }

  /**
   * Increment {@link $iteratorPosition} to address next {@see CFType}
   * @link http://php.net/manual/en/iterator.next.php
   * @return void
   * @uses $iteratorPosition increment by 1
   */
  public function next() {
    $this->iteratorPosition++;
  }

  /**
   * Test if {@link $iteratorPosition} addresses a valid element of {@link $value}
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean true if current position is valid, false else
   * @uses $iteratorPosition test if within {@link $iteratorKeys}
   * @uses $iteratorPosition test if within {@link $value}
   */
  public function valid() {
    return isset($this->value[$this->iteratorPosition]);
  }

  /************************************************************************************************
   *    ArrayAccess   I N T E R F A C E
   ************************************************************************************************/

  /**
   * Determine if the array's key exists
   * @param string $key the key to check
   * @return bool true if the offset exists, false if not
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @uses $value to check if $key exists
   * @author Sean Coates <sean@php.net>
   */
  public function offsetExists($key) {
    return isset($this->value[$key]);
  }

  /**
   * Fetch a specific key from the CFArray
   * @param string $key the key to check
   * @return mixed the value associated with the key; null if the key is not found
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @uses get() to get the key's value
   * @author Sean Coates <sean@php.net>
   */
  public function offsetGet($key) {
    return $this->get($key);
  }

  /**
   * Set a value in the array
   * @param string $key the key to set
   * @param string $value the value to set
   * @return void
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @uses setValue() to set the key's new value
   * @author Sean Coates <sean@php.net>
   */
  public function offsetSet($key, $value) {
    return $this->setValue($value);
  }

  /**
   * Unsets a value in the array
   * <b>Note:</b> this dummy does nothing
   * @param string $key the key to set
   * @return void
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @author Sean Coates <sean@php.net>
   */
  public function offsetUnset($key) {

  }


}

/**
 * Array Type of CFPropertyList
 * @author Rodney Rehm <rodney.rehm@medialize.de>
 * @author Christian Kruse <cjk@wwwtech.de>
 * @package plist
 * @subpackage plist.types
 */
class CFDictionary extends CFType implements Iterator {
  /**
   * Position of iterator {@link http://php.net/manual/en/class.iterator.php}
   * @var integer
   */
  protected $iteratorPosition = 0;

  /**
   * List of Keys for numerical iterator access {@link http://php.net/manual/en/class.iterator.php}
   * @var array
   */
  protected $iteratorKeys = null;


  /**
   * Create new CFType.
   * @param array $value Value of CFType
   */
  public function __construct($value=array()) {
    $this->value = $value;
  }

  /**
   * Set the CFType's value
   * <b>Note:</b> this dummy does nothing
   * @return void
   */
  public function setValue($value) {
  }

  /**
   * Add CFType to collection.
   * @param string $key Key to add to collection
   * @param CFType $value CFType to add to collection, defaults to null which results in an empty {@link CFString}
   * @return void
   * @uses $value for adding $key $value pair
   */
  public function add($key, CFType $value=null) {
    // anything but CFType is null, null is an empty string - sad but true
    if( !$value )
      $value = new CFString();

    $this->value[$key] = $value;
  }

  /**
   * Get CFType from collection.
   * @param string $key Key of CFType to retrieve from collection
   * @return CFType CFType found at $key, null else
   * @uses $value for retrieving CFType of $key
   */
  public function get($key) {
    if(isset($this->value[$key])) return $this->value[$key];
    return null;
  }

  /**
   * Generic getter (magic)
   * @param integer $key Key of CFType to retrieve from collection
   * @return CFType CFType found at $key, null else
   * @link http://php.net/oop5.overloading
   * @uses get() to retrieve the key's value
   * @author Sean Coates <sean@php.net>
   */
  public function __get($key) {
    return $this->get($key);
  }

  /**
   * Remove CFType from collection.
   * @param string $key Key of CFType to removes from collection
   * @return CFType removed CFType, null else
   * @uses $value for removing CFType of $key
   */
  public function del($key) {
    if(isset($this->value[$key])) unset($this->value[$key]);
  }


  /************************************************************************************************
   *    S E R I A L I Z I N G
   ************************************************************************************************/

  /**
   * Get XML-Node.
   * @param DOMDocument $doc DOMDocument to create DOMNode in
   * @param string $nodeName For compatibility reasons; just ignore it
   * @return DOMNode &lt;dict&gt;-Element
   */
  public function toXML(DOMDocument $doc,$nodeName="") {
    $node = $doc->createElement('dict');

    foreach($this->value as $key => $value) {
      $node->appendChild($doc->createElement('key', $key));
      $node->appendChild($value->toXML($doc));
    }

    return $node;
  }

  /**
   * convert value to binary representation
   * @param CFBinaryPropertyList The binary property list object
   * @return The offset in the object table
   */
  public function toBinary(CFBinaryPropertyList &$bplist) {
    return $bplist->dictToBinary($this);
  }

  /**
   * Get CFType's value.
   * @return array primitive value
   * @uses $value for retrieving primitive of CFType
   */
  public function toArray() {
    $a = array();

    foreach($this->value as $key => $value) $a[$key] = $value->toArray();
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
   * @uses $iteratorKeys store keys of {@link $value}
   */
  public function rewind() {
    $this->iteratorPosition = 0;
    $this->iteratorKeys = array_keys($this->value);
  }

  /**
   * Get Iterator's current {@link CFType} identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.current.php
   * @return CFType current Item
   * @uses $iteratorPosition identify current key
   * @uses $iteratorKeys identify current value
   */
  public function current() {
    return $this->value[$this->iteratorKeys[$this->iteratorPosition]];
  }

  /**
   * Get Iterator's current key identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.key.php
   * @return string key of the current Item
   * @uses $iteratorPosition identify current key
   * @uses $iteratorKeys identify current value
   */
  public function key() {
    return $this->iteratorKeys[$this->iteratorPosition];
  }

  /**
   * Increment {@link $iteratorPosition} to address next {@see CFType}
   * @link http://php.net/manual/en/iterator.next.php
   * @return void
   * @uses $iteratorPosition increment by 1
   */
  public function next() {
    $this->iteratorPosition++;
  }

  /**
   * Test if {@link $iteratorPosition} addresses a valid element of {@link $value}
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean true if current position is valid, false else
   * @uses $iteratorPosition test if within {@link $iteratorKeys}
   * @uses $iteratorPosition test if within {@link $value}
   */
  public function valid() {
    return isset($this->iteratorKeys[$this->iteratorPosition]) && isset($this->value[$this->iteratorKeys[$this->iteratorPosition]]);
  }

}

# eof
