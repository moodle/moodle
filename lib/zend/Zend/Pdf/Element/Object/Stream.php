<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Stream */
require_once 'Zend/Pdf/Element/Stream.php';

/** Zend_Pdf_Filter_Ascii85 */
require_once 'Zend/Pdf/Filter/Ascii85.php';

/** Zend_Pdf_Filter_AsciiHex */
require_once 'Zend/Pdf/Filter/AsciiHex.php';

/** Zend_Pdf_Filter_Compression_Flate */
require_once 'Zend/Pdf/Filter/Compression/Flate.php';

/** Zend_Pdf_Filter_Compression_Lzw */
require_once 'Zend/Pdf/Filter/Compression/Lzw.php';

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';


/**
 * PDF file 'stream object' element implementation
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Element_Object_Stream extends Zend_Pdf_Element_Object
{
    /**
     * StreamObject dictionary
     * Required enries:
     * Length
     *
     * @var Zend_Pdf_Element_Dictionary
     */
    private $_dictionary;

    /**
     * Flag which signals, that stream is decoded
     *
     * @var boolean
     */
    private $_streamDecoded;

    /**
     * Stored original stream object dictionary.
     * Used to decode stream during an access time.
     *
     * The only properties, which affect decoding, are sored here.
     *
     * @var array|null
     */
    private $_originalDictionary = null;

    /**
     * Object constructor
     *
     * @param mixed $val
     * @param integer $objNum
     * @param integer $genNum
     * @param Zend_Pdf_ElementFactory $factory
     * @param Zend_Pdf_Element_Dictionary|null $dictionary
     * @throws Zend_Pdf_Exception
     */
    public function __construct($val, $objNum, $genNum, Zend_Pdf_ElementFactory $factory, $dictionary = null)
    {
        parent::__construct(new Zend_Pdf_Element_Stream($val), $objNum, $genNum, $factory);

        if ($dictionary === null) {
            $this->_dictionary    = new Zend_Pdf_Element_Dictionary();
            $this->_dictionary->Length = new Zend_Pdf_Element_Numeric(strlen( $val ));
            $this->_streamDecoded = true;
        } else {
            $this->_dictionary    = $dictionary;
            $this->_streamDecoded = false;
        }
    }


    /**
     * Store original dictionary information in $_originalDictionary class member.
     * Used to store information and to normalize filters information before defiltering.
     *
     */
    private function _storeOriginalDictionary()
    {
        $this->_originalDictionary = array();

        $this->_originalDictionary['Filter']      = array();
        $this->_originalDictionary['DecodeParms'] = array();
        if ($this->_dictionary->Filter === null) {
            // Do nothing.
        } else if ($this->_dictionary->Filter->getType() == Zend_Pdf_Element::TYPE_ARRAY) {
            foreach ($this->_dictionary->Filter->items as $id => $filter) {
                $this->_originalDictionary['Filter'][$id]      = $filter->value;
                $this->_originalDictionary['DecodeParms'][$id] = array();

                if ($this->_dictionary->DecodeParms !== null ) {
                    if ($this->_dictionary->DecodeParms->items[$id] !== null &&
                        $this->_dictionary->DecodeParms->items[$id]->value !== null ) {
                        foreach ($this->_dictionary->DecodeParms->items[$id]->getKeys() as $paramKey) {
                            $this->_originalDictionary['DecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->DecodeParms->items[$id]->$paramKey->value;
                        }
                    }
                }
            }
        } else if ($this->_dictionary->Filter->getType() != Zend_Pdf_Element::TYPE_NULL) {
            $this->_originalDictionary['Filter'][0]      = $this->_dictionary->Filter->value;
            $this->_originalDictionary['DecodeParms'][0] = array();
            if ($this->_dictionary->DecodeParms !== null ) {
                foreach ($this->_dictionary->DecodeParms->getKeys() as $paramKey) {
                    $this->_originalDictionary['DecodeParms'][0][$paramKey] =
                          $this->_dictionary->DecodeParms->$paramKey->value;
                }
            }
        }

        if ($this->_dictionary->F !== null) {
            $this->_originalDictionary['F'] = $this->_dictionary->F->value;
        }

        $this->_originalDictionary['FFilter']      = array();
        $this->_originalDictionary['FDecodeParms'] = array();
        if ($this->_dictionary->FFilter === null) {
            // Do nothing.
        } else if ($this->_dictionary->FFilter->getType() == Zend_Pdf_Element::TYPE_ARRAY) {
            foreach ($this->_dictionary->FFilter->items as $id => $filter) {
                $this->_originalDictionary['FFilter'][$id]      = $filter->value;
                $this->_originalDictionary['FDecodeParms'][$id] = array();

                if ($this->_dictionary->FDecodeParms !== null ) {
                    if ($this->_dictionary->FDecodeParms->items[$id] !== null &&
                        $this->_dictionary->FDecodeParms->items[$id]->value !== null) {
                        foreach ($this->_dictionary->FDecodeParms->items[$id]->getKeys() as $paramKey) {
                            $this->_originalDictionary['FDecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->FDecodeParms->items[$id]->items[$paramKey]->value;
                        }
                    }
                }
            }
        } else {
            $this->_originalDictionary['FFilter'][0]      = $this->_dictionary->FFilter->value;
            $this->_originalDictionary['FDecodeParms'][0] = array();
            if ($this->_dictionary->FDecodeParms !== null ) {
                foreach ($this->_dictionary->FDecodeParms->getKeys() as $paramKey) {
                    $this->_originalDictionary['FDecodeParms'][0][$paramKey] =
                          $this->_dictionary->FDecodeParms->items[$paramKey]->value;
                }
            }
        }
    }

    /**
     * Decode stream
     *
     * @throws Zend_Pdf_Exception
     */
    private function _decodeStream()
    {
        if ($this->_originalDictionary === null) {
            $this->_storeOriginalDictionary();
        }

        /**
         * All applied stream filters must be processed to decode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_originalDictionary['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Zend_Pdf_Exception('External filters are not supported now.');
        }

        foreach ($this->_originalDictionary['Filter'] as $id => $filterName ) {
            $valueRef = &$this->_value->value->getRef();
            $this->_value->value->touch();
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $valueRef = Zend_Pdf_Filter_AsciiHex::decode($valueRef);
                    break;

                case 'ASCII85Decode':
                    $valueRef = Zend_Pdf_Filter_Ascii85::decode($valueRef);
                    break;

                case 'FlateDecode':
                    $valueRef = Zend_Pdf_Filter_Compression_Flate::decode($valueRef,
                                                                          $this->_originalDictionary['DecodeParms'][$id]);
                    break;

                case 'LZWDecode':
                    $valueRef = Zend_Pdf_Filter_Compression_Lzw::decode($valueRef,
                                                                        $this->_originalDictionary['DecodeParms'][$id]);
                    break;

                default:
                    throw new Zend_Pdf_Exception('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = true;
    }

    /**
     * Encode stream
     *
     * @throws Zend_Pdf_Exception
     */
    private function _encodeStream()
    {
        /**
         * All applied stream filters must be processed to encode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_originalDictionary['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Zend_Pdf_Exception('External filters are not supported now.');
        }

        $filters = array_reverse($this->_originalDictionary['Filter'], true);

        foreach ($filters as $id => $filterName ) {
            $valueRef = &$this->_value->value->getRef();
            $this->_value->value->touch();
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $valueRef = Zend_Pdf_Filter_AsciiHex::encode($valueRef);
                    break;

                case 'ASCII85Decode':
                    $valueRef = Zend_Pdf_Filter_Ascii85::encode($valueRef);
                    break;

                case 'FlateDecode':
                    $valueRef = Zend_Pdf_Filter_Compression_Flate::encode($valueRef,
                                                                          $this->_originalDictionary['DecodeParms'][$id]);
                    break;

                case 'LZWDecode':
                    $valueRef = Zend_Pdf_Filter_Compression_Lzw::encode($valueRef,
                                                                        $this->_originalDictionary['DecodeParms'][$id]);
                    break;

                default:
                    throw new Zend_Pdf_Exception('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = false;
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     * @throws Zend_Pdf_Exception
     */
    public function __get($property)
    {
        if ($property == 'dictionary') {
            /**
             * If stream is note decoded yet, then store original decoding options (do it only once).
             */
            if (( !$this->_streamDecoded ) && ($this->_originalDictionary === null)) {
                $this->_storeOriginalDictionary();
            }

            return $this->_dictionary;
        }

        if ($property == 'value') {
            if (!$this->_streamDecoded) {
                $this->_decodeStream();
            }

            return $this->_value->value->getRef();
        }

        throw new Zend_Pdf_Exception('Unknown stream object property requested.');
    }


    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        if ($property == 'value') {
            $valueRef = &$this->_value->value->getRef();
            $valueRef = $value;
            $this->_value->value->touch();

            $this->_streamDecoded = true;

            return;
        }

        throw new Zend_Pdf_Exception('Unknown stream object property: \'' . $property . '\'.');
    }


    /**
     * Treat stream data as already encoded
     */
    public function skipFilters()
    {
        $this->_streamDecoded = false;
    }


    /**
     * Call handler
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!$this->_streamDecoded) {
            $this->_decodeStream();
        }

        switch (count($args)) {
            case 0:
                return $this->_value->$method();
            case 1:
                return $this->_value->$method($args[0]);
            default:
                throw new Zend_Pdf_Exception('Unsupported number of arguments');
        }
    }

    /**
     * Dump object to a string to save within PDF file
     *
     * $factory parameter defines operation context.
     *
     * @param Zend_Pdf_ElementFactory $factory
     * @return string
     */
    public function dump(Zend_Pdf_ElementFactory $factory)
    {
        $shift = $factory->getEnumerationShift($this->_factory);

        if ($this->_streamDecoded) {
            $this->_storeOriginalDictionary();
            $this->_encodeStream();
        } else if ($this->_originalDictionary != null) {
            $startDictionary = $this->_originalDictionary;
            $this->_storeOriginalDictionary();
            $newDictionary = $this->_originalDictionary;

            if ($startDictionary !== $newDictionary) {
                $this->_originalDictionary = $startDictionary;
                $this->_decodeStream();

                $this->_originalDictionary = $newDictionary;
                $this->_encodeStream();
            }
        }

        // Update stream length
        $this->dictionary->Length->value = $this->_value->length();

        return  $this->_objNum + $shift . " " . $this->_genNum . " obj \n"
             .  $this->dictionary->toString($factory) . "\n"
             .  $this->_value->toString($factory) . "\n"
             . "endobj\n";
    }

    /**
     * Clean up resources, used by object
     */
    public function cleanUp()
    {
        $this->_dictionary = null;
        $this->_value      = null;
    }
}
