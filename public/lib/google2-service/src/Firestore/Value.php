<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Firestore;

class Value extends \Google\Model
{
  /**
   * Null value.
   */
  public const NULL_VALUE_NULL_VALUE = 'NULL_VALUE';
  protected $arrayValueType = ArrayValue::class;
  protected $arrayValueDataType = '';
  /**
   * A boolean value.
   *
   * @var bool
   */
  public $booleanValue;
  /**
   * A bytes value. Must not exceed 1 MiB - 89 bytes. Only the first 1,500 bytes
   * are considered by queries.
   *
   * @var string
   */
  public $bytesValue;
  /**
   * A double value.
   *
   * @var 
   */
  public $doubleValue;
  protected $geoPointValueType = LatLng::class;
  protected $geoPointValueDataType = '';
  /**
   * An integer value.
   *
   * @var string
   */
  public $integerValue;
  protected $mapValueType = MapValue::class;
  protected $mapValueDataType = '';
  /**
   * A null value.
   *
   * @var string
   */
  public $nullValue;
  /**
   * A reference to a document. For example:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @var string
   */
  public $referenceValue;
  /**
   * A string value. The string, represented as UTF-8, must not exceed 1 MiB -
   * 89 bytes. Only the first 1,500 bytes of the UTF-8 representation are
   * considered by queries.
   *
   * @var string
   */
  public $stringValue;
  /**
   * A timestamp value. Precise only to microseconds. When stored, any
   * additional precision is rounded down.
   *
   * @var string
   */
  public $timestampValue;

  /**
   * An array value. Cannot directly contain another array value, though can
   * contain a map which contains another array.
   *
   * @param ArrayValue $arrayValue
   */
  public function setArrayValue(ArrayValue $arrayValue)
  {
    $this->arrayValue = $arrayValue;
  }
  /**
   * @return ArrayValue
   */
  public function getArrayValue()
  {
    return $this->arrayValue;
  }
  /**
   * A boolean value.
   *
   * @param bool $booleanValue
   */
  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }
  /**
   * @return bool
   */
  public function getBooleanValue()
  {
    return $this->booleanValue;
  }
  /**
   * A bytes value. Must not exceed 1 MiB - 89 bytes. Only the first 1,500 bytes
   * are considered by queries.
   *
   * @param string $bytesValue
   */
  public function setBytesValue($bytesValue)
  {
    $this->bytesValue = $bytesValue;
  }
  /**
   * @return string
   */
  public function getBytesValue()
  {
    return $this->bytesValue;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * A geo point value representing a point on the surface of Earth.
   *
   * @param LatLng $geoPointValue
   */
  public function setGeoPointValue(LatLng $geoPointValue)
  {
    $this->geoPointValue = $geoPointValue;
  }
  /**
   * @return LatLng
   */
  public function getGeoPointValue()
  {
    return $this->geoPointValue;
  }
  /**
   * An integer value.
   *
   * @param string $integerValue
   */
  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }
  /**
   * @return string
   */
  public function getIntegerValue()
  {
    return $this->integerValue;
  }
  /**
   * A map value.
   *
   * @param MapValue $mapValue
   */
  public function setMapValue(MapValue $mapValue)
  {
    $this->mapValue = $mapValue;
  }
  /**
   * @return MapValue
   */
  public function getMapValue()
  {
    return $this->mapValue;
  }
  /**
   * A null value.
   *
   * Accepted values: NULL_VALUE
   *
   * @param self::NULL_VALUE_* $nullValue
   */
  public function setNullValue($nullValue)
  {
    $this->nullValue = $nullValue;
  }
  /**
   * @return self::NULL_VALUE_*
   */
  public function getNullValue()
  {
    return $this->nullValue;
  }
  /**
   * A reference to a document. For example:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @param string $referenceValue
   */
  public function setReferenceValue($referenceValue)
  {
    $this->referenceValue = $referenceValue;
  }
  /**
   * @return string
   */
  public function getReferenceValue()
  {
    return $this->referenceValue;
  }
  /**
   * A string value. The string, represented as UTF-8, must not exceed 1 MiB -
   * 89 bytes. Only the first 1,500 bytes of the UTF-8 representation are
   * considered by queries.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * A timestamp value. Precise only to microseconds. When stored, any
   * additional precision is rounded down.
   *
   * @param string $timestampValue
   */
  public function setTimestampValue($timestampValue)
  {
    $this->timestampValue = $timestampValue;
  }
  /**
   * @return string
   */
  public function getTimestampValue()
  {
    return $this->timestampValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Value::class, 'Google_Service_Firestore_Value');
