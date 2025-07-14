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
 */

namespace CFPropertyList;

use Iterator;
use DOMDocument;
use DOMException;
use DOMImplementation;
use DOMNode;

/**
 * Property List
 * Interface for handling reading, editing and saving Property Lists as defined by Apple.
 * @example   example-read-01.php Read an XML PropertyList
 * @example   example-read-02.php Read a Binary PropertyList
 * @example   example-read-03.php Read a PropertyList without knowing the type
 * @example   example-create-01.php Using the CFPropertyList API
 * @example   example-create-02.php Using CFTypeDetector
 * @example   example-create-03.php Using CFTypeDetector with CFDate and CFData
 * @example   example-modify-01.php Read, modify and save a PropertyList
 * ------------------------------------------------------------------------------
 */
class CFPropertyList extends CFBinaryPropertyList implements Iterator
{
  /**
   * Format constant for binary format
   * @var integer
   */
    const FORMAT_BINARY = 1;

  /**
   * Format constant for xml format
   * @var integer
   */
    const FORMAT_XML = 2;

  /**
   * Format constant for automatic format recognizing
   * @var integer
   */
    const FORMAT_AUTO = 0;

  /**
   * Path of PropertyList
   * @var string
   */
    protected $file = null;

  /**
   * Detected format of PropertyList
   * @var integer
   */
    protected $detectedFormat = null;

  /**
   * Path of PropertyList
   * @var integer
   */
    protected $format = null;

  /**
   * CFType nodes
   * @var array
   */
    protected $value = array();

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
   * List of NodeNames to ClassNames for resolving plist-files
   * @var array
   */
    protected static $types = array(
    'string'  => 'CFString',
    'real'    => 'CFNumber',
    'integer' => 'CFNumber',
    'date'    => 'CFDate',
    'true'    => 'CFBoolean',
    'false'   => 'CFBoolean',
    'data'    => 'CFData',
    'array'   => 'CFArray',
    'dict'    => 'CFDictionary'
    );


  /**
   * Create new CFPropertyList.
   * If a path to a PropertyList is specified, it is loaded automatically.
   * @param string $file Path of PropertyList
   * @param integer $format he format of the property list, see {@link FORMAT_XML}, {@link FORMAT_BINARY} and {@link FORMAT_AUTO}, defaults to {@link FORMAT_AUTO}
   * @throws IOException if file could not be read by {@link load()}
   * @uses $file for storing the current file, if specified
   * @uses load() for loading the plist-file
   */
    public function __construct($file = null, $format = self::FORMAT_AUTO)
    {
        $this->file = $file;
        $this->format = $format;
        $this->detectedFormat = $format;
        if ($this->file) {
            $this->load();
        }
    }

  /**
   * Load an XML PropertyList.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @return void
   * @throws IOException if file could not be read
   * @throws DOMException if XML-file could not be read properly
   * @uses load() to actually load the file
   */
    public function loadXML($file = null)
    {
        $this->load($file, CFPropertyList::FORMAT_XML);
    }

  /**
   * Load an XML PropertyList.
   * @param resource $stream A stream containing the xml document.
   * @return void
   * @throws IOException if stream could not be read
   * @throws DOMException if XML-stream could not be read properly
   */
    public function loadXMLStream($stream)
    {
        if (($contents = stream_get_contents($stream)) === false) {
            throw IOException::notReadable('<stream>');
        }
        $this->parse($contents, CFPropertyList::FORMAT_XML);
    }

  /**
   * Load an binary PropertyList.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @return void
   * @throws IOException if file could not be read
   * @throws PListException if binary plist-file could not be read properly
   * @uses load() to actually load the file
   */
    public function loadBinary($file = null)
    {
        $this->load($file, CFPropertyList::FORMAT_BINARY);
    }

  /**
   * Load an binary PropertyList.
   * @param stream $stream Stream containing the PropertyList
   * @return void
   * @throws IOException if file could not be read
   * @throws PListException if binary plist-file could not be read properly
   * @uses parse() to actually load the file
   */
    public function loadBinaryStream($stream)
    {
        if (($contents = stream_get_contents($stream)) === false) {
            throw IOException::notReadable('<stream>');
        }
        $this->parse($contents, CFPropertyList::FORMAT_BINARY);
    }

  /**
   * Load a plist file.
   * Load and import a plist file.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @param integer $format The format of the property list, see {@link FORMAT_XML}, {@link FORMAT_BINARY} and {@link FORMAT_AUTO}, defaults to {@link $format}
   * @return void
   * @throws PListException if file format version is not 00
   * @throws IOException if file could not be read
   * @throws DOMException if plist file could not be parsed properly
   * @uses $file if argument $file was not specified
   * @uses $value reset to empty array
   * @uses import() for importing the values
   */
    public function load($file = null, $format = null)
    {
        $file = $file ? $file : $this->file;
        $format = $format !== null ? $format : $this->format;
        $this->value = array();

        if (!is_readable($file)) {
            throw IOException::notReadable($file);
        }

        switch ($format) {
            case CFPropertyList::FORMAT_BINARY:
                $this->readBinary($file);
                break;
            case CFPropertyList::FORMAT_AUTO: // what we now do is ugly, but neccessary to recognize the file format
                $fd = fopen($file, "rb");
                if (($magic_number = fread($fd, 8)) === false) {
                    throw IOException::notReadable($file);
                }
                fclose($fd);

                $filetype = substr($magic_number, 0, 6);
                $version  = substr($magic_number, -2);

                if ($filetype == "bplist") {
                    if ($version != "00") {
                        throw new PListException("Wrong file format version! Expected 00, got $version!");
                    }
                    $this->detectedFormat = CFPropertyList::FORMAT_BINARY;
                    $this->readBinary($file);
                    break;
                }
                $this->detectedFormat = CFPropertyList::FORMAT_XML;
              // else: xml format, break not neccessary
            case CFPropertyList::FORMAT_XML:
                $doc = new DOMDocument();
                $prevXmlErrors = libxml_use_internal_errors(true);
                libxml_clear_errors();
                if (!$doc->load($file)) {
                    $message = $this->getLibxmlErrors();
                    libxml_clear_errors();
                    libxml_use_internal_errors($prevXmlErrors);
                    throw new DOMException($message);
                }
                libxml_use_internal_errors($prevXmlErrors);
                $this->import($doc->documentElement, $this);
                break;
        }
    }

  /**
   * Parse a plist string.
   * Parse and import a plist string.
   * @param string $str String containing the PropertyList, defaults to {@link $content}
   * @param integer $format The format of the property list, see {@link FORMAT_XML}, {@link FORMAT_BINARY} and {@link FORMAT_AUTO}, defaults to {@link $format}
   * @return void
   * @throws PListException if file format version is not 00
   * @throws IOException if file could not be read
   * @throws DOMException if plist file could not be parsed properly
   * @uses $content if argument $str was not specified
   * @uses $value reset to empty array
   * @uses import() for importing the values
   */
    public function parse($str = null, $format = null)
    {
        $format = $format !== null ? $format : $this->format;
        $str = $str !== null ? $str : $this->content;
        if ($str === null || strlen($str) === 0) {
            throw IOException::readError('');
        }
        $this->value = array();

        switch ($format) {
            case CFPropertyList::FORMAT_BINARY:
                $this->parseBinary($str);
                break;
            case CFPropertyList::FORMAT_AUTO: // what we now do is ugly, but neccessary to recognize the file format
                if (($magic_number = substr($str, 0, 8)) === false) {
                    throw IOException::notReadable("<string>");
                }

                $filetype = substr($magic_number, 0, 6);
                $version  = substr($magic_number, -2);

                if ($filetype == "bplist") {
                    if ($version != "00") {
                        throw new PListException("Wrong file format version! Expected 00, got $version!");
                    }
                    $this->detectedFormat = CFPropertyList::FORMAT_BINARY;
                    $this->parseBinary($str);
                    break;
                }
                $this->detectedFormat = CFPropertyList::FORMAT_XML;
              // else: xml format, break not neccessary
            case CFPropertyList::FORMAT_XML:
                $doc = new DOMDocument();
                $prevXmlErrors = libxml_use_internal_errors(true);
                libxml_clear_errors();
                if (!$doc->loadXML($str)) {
                    $message = $this->getLibxmlErrors();
                    libxml_clear_errors();
                    libxml_use_internal_errors($prevXmlErrors);
                    throw new DOMException($message);
                }
                libxml_use_internal_errors($prevXmlErrors);
                $this->import($doc->documentElement, $this);
                break;
        }
    }

    protected function getLibxmlErrors()
    {
        return implode(', ', array_map(function (\LibXMLError $error) {
            return trim("{$error->line}:{$error->column} [$error->code] $error->message");
        }, libxml_get_errors()));
    }

  /**
   * Convert a DOMNode into a CFType.
   * @param DOMNode $node Node to import children of
   * @param CFDictionary|CFArray|CFPropertyList $parent
   * @return void
   */
    protected function import(DOMNode $node, $parent)
    {
      // abort if there are no children
        if (!$node->childNodes->length) {
            return;
        }

        foreach ($node->childNodes as $n) {
          // skip if we can't handle the element
            if (!isset(self::$types[$n->nodeName])) {
                continue;
            }

            $class = __NAMESPACE__ . '\\'.self::$types[$n->nodeName];
            $key = null;

          // find previous <key> if possible
            $ps = $n->previousSibling;
            while ($ps && $ps->nodeName == '#text' && $ps->previousSibling) {
                $ps = $ps->previousSibling;
            }

          // read <key> if possible
            if ($ps && $ps->nodeName == 'key') {
                $key = $ps->firstChild->nodeValue;
            }

            switch ($n->nodeName) {
                case 'date':
                    $value = new $class(CFDate::dateValue($n->nodeValue));
                    break;
                case 'data':
                    $value = new $class($n->nodeValue, true);
                    break;
                case 'string':
                    $value = new $class($n->nodeValue);
                    break;

                case 'real':
                case 'integer':
                    $value = new $class($n->nodeName == 'real' ? floatval($n->nodeValue) : intval($n->nodeValue));
                    break;

                case 'true':
                case 'false':
                    $value = new $class($n->nodeName == 'true');
                    break;

                case 'array':
                case 'dict':
                    $value = new $class();
                    $this->import($n, $value);

                    if ($value instanceof CFDictionary) {
                        $hsh = $value->getValue();
                        if (isset($hsh['CF$UID']) && count($hsh) == 1) {
                            $value = new CFUid($hsh['CF$UID']->getValue());
                        }
                    }

                    break;
            }

            if ($parent instanceof CFDictionary) {
                // Dictionaries need a key
                $parent->add($key, $value);
            } else {
                // others don't
                $parent->add($value);
            }
        }
    }

  /**
   * Convert CFPropertyList to XML and save to file.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @param bool $formatted Print plist formatted (i.e. with newlines and whitespace indention) if true; defaults to false
   * @return void
   * @throws IOException if file could not be read
   * @uses $file if $file was not specified
   */
    public function saveXML($file, $formatted = false)
    {
        $this->save($file, CFPropertyList::FORMAT_XML, $formatted);
    }

  /**
   * Convert CFPropertyList to binary format (bplist00) and save to file.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @return void
   * @throws IOException if file could not be read
   * @uses $file if $file was not specified
   */
    public function saveBinary($file)
    {
        $this->save($file, CFPropertyList::FORMAT_BINARY);
    }

  /**
   * Convert CFPropertyList to XML or binary and save to file.
   * @param string $file Path of PropertyList, defaults to {@link $file}
   * @param string $format Format of PropertyList, defaults to {@link $format}
   * @param bool $formatted_xml Print XML plist formatted (i.e. with newlines and whitespace indention) if true; defaults to false
   * @return void
   * @throws IOException if file could not be read
   * @throws PListException if evaluated $format is neither {@link FORMAT_XML} nor {@link FORMAL_BINARY}
   * @uses $file if $file was not specified
   * @uses $format if $format was not specified
   */
    public function save($file = null, $format = null, $formatted_xml = false)
    {
        $file = $file ? $file : $this->file;
        $format = $format ? $format : $this->format;
        if ($format == self::FORMAT_AUTO) {
            $format = $this->detectedFormat;
        }

        if (!in_array($format, array( self::FORMAT_BINARY, self::FORMAT_XML ))) {
            throw new PListException("format {$format} is not supported, use CFPropertyList::FORMAT_BINARY or CFPropertyList::FORMAT_XML");
        }

        if (!file_exists($file)) {
          // dirname("file.xml") == "" and is treated as the current working directory
            if (!is_writable(dirname($file))) {
                throw IOException::notWritable($file);
            }
        } elseif (!is_writable($file)) {
            throw IOException::notWritable($file);
        }

        $content = $format == self::FORMAT_BINARY ? $this->toBinary() : $this->toXML($formatted_xml);

        $fh = fopen($file, 'wb');
        fwrite($fh, $content);
        fclose($fh);
    }

  /**
   * Convert CFPropertyList to XML
   * @param bool $formatted Print plist formatted (i.e. with newlines and whitespace indention) if true; defaults to false
   * @return string The XML content
   */
    public function toXML($formatted = false)
    {
        $domimpl = new DOMImplementation();
      // <!DOCTYPE plist PUBLIC "-//Apple Computer//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
        $dtd = $domimpl->createDocumentType('plist', '-//Apple//DTD PLIST 1.0//EN', 'http://www.apple.com/DTDs/PropertyList-1.0.dtd');
        $doc = $domimpl->createDocument(null, "plist", $dtd);
        $doc->encoding = "UTF-8";

      // format output
        if ($formatted) {
            $doc->formatOutput = true;
            $doc->preserveWhiteSpace = true;
        }

      // get documentElement and set attribs
        $plist = $doc->documentElement;
        $plist->setAttribute('version', '1.0');

      // add PropertyList's children
        $plist->appendChild($this->getValue(true)->toXML($doc));

        return $doc->saveXML();
    }


  /************************************************************************************************
   *    M A N I P U L A T I O N
   ************************************************************************************************/

  /**
   * Add CFType to collection.
   * @param CFType $value CFType to add to collection
   * @return void
   * @uses $value for adding $value
   */
    public function add(?CFType $value = null)
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
   * Generic getter (magic)
   *
   * @param integer $key Key of CFType to retrieve from collection
   * @return CFType CFType found at $key, null else
   * @author Sean Coates <sean@php.net>
   * @link http://php.net/oop5.overloading
   */
    public function __get($key)
    {
        return $this->get($key);
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
            $t = $this->value[$key];
            unset($this->value[$key]);
            return $t;
        }

        return null;
    }

  /**
   * Empty the collection
   * @return array the removed CFTypes
   * @uses $value for removing CFType of $key
   */
    public function purge()
    {
        $t = $this->value;
        $this->value = array();
        return $t;
    }

  /**
   * Get first (and only) child, or complete collection.
   * @param boolean $cftype if set to true returned value will be CFArray instead of an array in case of a collection
   * @return CFType|array CFType or list of CFTypes known to the PropertyList
   * @uses $value for retrieving CFTypes
   */
    public function getValue($cftype = false)
    {
        if (count($this->value) === 1) {
            $t = array_values($this->value);
            return $t[0];
        }
        if ($cftype) {
            $t = new CFArray();
            foreach ($this->value as $value) {
                if ($value instanceof CFType) {
                    $t->add($value);
                }
            }
            return $t;
        }
        return $this->value;
    }

  /**
   * Create CFType-structure from guessing the data-types.
   * The functionality has been moved to the more flexible {@link CFTypeDetector} facility.
   * @param mixed $value Value to convert to CFType
   * @param array $options Configuration for casting values [autoDictionary, suppressExceptions, objectToArrayMethod, castNumericStrings]
   * @return CFType CFType based on guessed type
   * @uses CFTypeDetector for actual type detection
   * @deprecated
   */
    public static function guess($value, $options = array())
    {
        static $t = null;
        if ($t === null) {
            $t = new CFTypeDetector($options);
        }

        return $t->toCFType($value);
    }


  /************************************************************************************************
   *    S E R I A L I Z I N G
   ************************************************************************************************/

  /**
   * Get PropertyList as array.
   * @return mixed primitive value of first (and only) CFType, or array of primitive values of collection
   * @uses $value for retrieving CFTypes
   */
    public function toArray()
    {
        $a = array();
        foreach ($this->value as $value) {
            $a[] = $value->toArray();
        }
        if (count($a) === 1) {
            return $a[0];
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
   * @uses $iteratorKeys store keys of {@link $value}
   */
    public function rewind(): void
    {
        $this->iteratorPosition = 0;
        $this->iteratorKeys = array_keys($this->value);
    }

  /**
   * Get Iterator's current {@link CFType} identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed current Item
   * @uses $iteratorPosition identify current key
   * @uses $iteratorKeys identify current value
   */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->value[$this->iteratorKeys[$this->iteratorPosition]];
    }

  /**
   * Get Iterator's current key identified by {@link $iteratorPosition}
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed key of the current Item
   * @uses $iteratorPosition identify current key
   * @uses $iteratorKeys identify current value
   */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->iteratorKeys[$this->iteratorPosition];
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
   * @return boolean true if current position is valid, false else
   * @uses $iteratorPosition test if within {@link $iteratorKeys}
   * @uses $iteratorPosition test if within {@link $value}
   */
    public function valid(): bool
    {
        return isset($this->iteratorKeys[$this->iteratorPosition]) && isset($this->value[$this->iteratorKeys[$this->iteratorPosition]]);
    }
}

# eof
