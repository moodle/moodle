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
 * @package    Zend_Amf
 * @subpackage Parse_Amf3
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Amf_Parse_Serializer */
require_once 'Zend/Amf/Parse/Serializer.php';

/** Zend_Amf_Parse_TypeLoader */
require_once 'Zend/Amf/Parse/TypeLoader.php';

/**
 * Detect PHP object type and convert it to a corresponding AMF3 object type
 *
 * @package    Zend_Amf
 * @subpackage Parse_Amf3
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Amf_Parse_Amf3_Serializer extends Zend_Amf_Parse_Serializer
{
    /**
     * Serialize PHP types to AMF3 and write to stream
     *
     * Checks to see if the type was declared and then either
     * auto negotiates the type or use the user defined markerType to
     * serialize the data from php back to AMF3
     *
     * @param  mixed $content
     * @param  int $markerType
     * @return void
     */
    public function writeTypeMarker($data, $markerType=null)
    {
        if (null !== $markerType) {
            // Write the Type Marker to denote the following action script data type
            $this->_stream->writeByte($markerType);

            switch ($markerType) {
                case Zend_Amf_Constants::AMF3_NULL:
                    break;
                case Zend_Amf_Constants::AMF3_BOOLEAN_FALSE:
                    break;
                case Zend_Amf_Constants::AMF3_BOOLEAN_TRUE:
                    break;
                case Zend_Amf_Constants::AMF3_INTEGER:
                    $this->writeInteger($data);
                    break;
                case Zend_Amf_Constants::AMF3_NUMBER:
                    $this->_stream->writeDouble($data);
                    break;
                case Zend_Amf_Constants::AMF3_STRING:
                    $this->writeString($data);
                    break;
                case Zend_Amf_Constants::AMF3_DATE:
                    $this->writeDate($data);
                    break;
                case Zend_Amf_Constants::AMF3_ARRAY:
                    $this->writeArray($data);
                    break;
                case Zend_Amf_Constants::AMF3_OBJECT:
                    $this->writeObject($data);
                    break;
                case Zend_Amf_Constants::AMF3_BYTEARRAY:
                    $this->writeString($data);
                    break;
                default:
                    require_once 'Zend/Amf/Exception.php';
                    throw new Zend_Amf_Exception('Unknown Type Marker: ' . $markerType);
            }
        } else {
            // Detect Type Marker
             switch (true) {
                case (null === $data):
                    $markerType = Zend_Amf_Constants::AMF3_NULL;
                    break;
                case (is_bool($data)):
                    if ($data){
                        $markerType = Zend_Amf_Constants::AMF3_BOOLEAN_TRUE;
                    } else {
                        $markerType = Zend_Amf_Constants::AMF3_BOOLEAN_FALSE;
                    }
                    break;
                case (is_int($data)):
                    if (($data > 0xFFFFFFF) || ($data < -268435456)) {
                        $markerType = Zend_Amf_Constants::AMF3_NUMBER;
                    } else {
                        $markerType = Zend_Amf_Constants::AMF3_INTEGER;
                    }
                    break;
                case (is_float($data)):
                    $markerType = Zend_Amf_Constants::AMF3_NUMBER;
                    break;
                case (is_string($data)):
                    $markerType = Zend_Amf_Constants::AMF3_STRING;
                    break;
                case (is_array($data)):
                    $markerType = Zend_Amf_Constants::AMF3_ARRAY;
                    break;
                case (is_object($data)):
                    // Handle object types.
                    if (($data instanceof DateTime) || ($data instanceof Zend_Date)) {
                        $markerType = Zend_Amf_Constants::AMF3_DATE;
                    } else if ($data instanceof Zend_Amf_Value_ByteArray) {
                        $markerType = Zend_Amf_Constants::AMF3_BYTEARRAY;
                    } else {
                        $markerType = Zend_Amf_Constants::AMF3_OBJECT;
                    }
                    break;
                default: 
                    require_once 'Zend/Amf/Exception.php';
                    throw new Zend_Amf_Exception('Unsupported data type: ' . gettype($data));
             }
            $this->writeTypeMarker($data, $markerType);
        }
    }

    /**
     * Write an AMF3 integer
     *
     * @param int|float $data
     * @return Zend_Amf_Parse_Amf3_Serializer
     */
    public function writeInteger($int)
    {
        if (($int & 0xffffff80) == 0) {
            $this->_stream->writeByte($int & 0x7f);
            return $this;
        }

        if (($int & 0xffffc000) == 0 ) {
            $this->_stream->writeByte(($int >> 7 ) | 0x80);
            $this->_stream->writeByte($int & 0x7f);
            return $this;
        }

        if (($int & 0xffe00000) == 0) {
            $this->_stream->writeByte(($int >> 14 ) | 0x80);
            $this->_stream->writeByte(($int >> 7 ) | 0x80);
            $this->_stream->writeByte($int & 0x7f);
            return $this;
        }

        $this->_stream->writeByte(($int >> 22 ) | 0x80);
        $this->_stream->writeByte(($int >> 15 ) | 0x80);
        $this->_stream->writeByte(($int >> 8 ) | 0x80);
        $this->_stream->writeByte($int & 0xff);
        return $this;
    }

    /**
     * Send string to output stream
     *
     * @param  string $string
     * @return Zend_Amf_Parse_Amf3_Serializer
     */
    public function writeString($string)
    {
        $ref = strlen($string) << 1 | 0x01;
        $this->writeInteger($ref);
        $this->_stream->writeBytes($string);
        return $this;
    }

    /**
     * Convert DateTime/Zend_Date to AMF date
     *
     * @param  DateTime|Zend_Date $date
     * @return Zend_Amf_Parse_Amf3_Serializer
     */
    public function writeDate($date)
    {
        if ($date instanceof DateTime) {
            $dateString = $date->format('U') * 1000;
        } elseif ($date instanceof Zend_Date) {
            $dateString = $date->toString('U') * 1000;
        } else {
            require_once 'Zend/Amf/Exception.php';
            throw new Zend_Amf_Exception('Invalid date specified; must be a string DateTime or Zend_Date object');
        }

        $this->writeInteger(0x01);
        // write time to stream minus milliseconds
        $this->_stream->writeDouble($dateString);
        return $this;
    }

    /**
     * Write a PHP array back to the amf output stream
     *
     * @param array $array
     * @return Zend_Amf_Parse_Amf3_Serializer
     */
    public function writeArray(array $array)
    {
        // have to seperate mixed from numberic keys.
        $numeric = array();
        $string  = array();
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $numeric[] = $value;
            } else {
                $string[$key] = $value;
            }
        }

        // write the preamble id of the array
        $length = count($numeric);
        $id     = ($length << 1) | 0x01;
        $this->writeInteger($id);

        //Write the mixed type array to the output stream
        foreach($string as $key => $value) {
            $this->writeString($key)
                 ->writeTypeMarker($value);
        }
        $this->writeString('');

        // Write the numeric array to ouput stream
        foreach($numeric as $value) {
            $this->writeTypeMarker($value);
        }
        return $this;
    }

    /**
     * Write object to ouput stream
     *
     * @param  mixed $data
     * @return Zend_Amf_Parse_Amf3_Serializer
     */
    public function writeObject($object)
    {
        $encoding  = Zend_Amf_Constants::ET_PROPLIST;
        $className = '';

        //Check to see if the object is a typed object and we need to change
        switch (true) {
             // the return class mapped name back to actionscript class name.
            case ($className = Zend_Amf_Parse_TypeLoader::getMappedClassName(get_class($object))):
                break;

            // Check to see if the user has defined an explicit Action Script type.
            case isset($object->_explicitType):
                $className = $object->_explicitType;
                unset($object->_explicitType);
                break;

            // Check if user has defined a method for accessing the Action Script type
            case method_exists($object, 'getASClassName'):
                $className = $object->getASClassName();
                break;

            // No return class name is set make it a generic object
            default:
                break;
        }

        $traitsInfo  = Zend_Amf_Constants::AMF3_OBJECT_ENCODING;
        $traitsInfo |= $encoding << 2;
        try {
            switch($encoding) {
                case Zend_Amf_Constants::ET_PROPLIST:
                    $count = 0;
                    foreach($object as $key => $value) {
                        $count++;
                    }
                    $traitsInfo |= ($count << 4);

                    // Write the object ID
                    $this->writeInteger($traitsInfo);

                    // Write the classname
                    $this->writeString($className);

                    // Write the object Key's to the output stream
                    foreach ($object as $key => $value) {
                        $this->writeString($key);
                    }

                    //Write the object values to the output stream.
                    foreach ($object as $key => $value) {
                        $this->writeTypeMarker($value);
                    }
                    break;
                case Zend_Amf_Constants::ET_EXTERNAL:
                    require_once 'Zend/Amf/Exception.php';
                    throw new Zend_Amf_Exception('External Object Encoding not implemented');
                    break;
                default: 
                    require_once 'Zend/Amf/Exception.php';
                    throw new Zend_Amf_Exception('Unknown Object Encoding type: ' . $encoding);
            }
        } catch (Exception $e) {
            require_once 'Zend/Amf/Exception.php';
            throw new Zend_Amf_Exception('Unable to writeObject output: ' . $e->getMessage());
        }

        return $this;
    }
}
