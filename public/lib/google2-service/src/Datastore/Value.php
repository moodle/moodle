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

namespace Google\Service\Datastore;

class Value extends \Google\Model
{
  /**
   * Null value.
   */
  public const NULL_VALUE_NULL_VALUE = 'NULL_VALUE';
  protected $arrayValueType = ArrayValue::class;
  protected $arrayValueDataType = '';
  /**
   * A blob value. May have at most 1,000,000 bytes. When `exclude_from_indexes`
   * is false, may have at most 1500 bytes. In JSON requests, must be
   * base64-encoded.
   *
   * @var string
   */
  public $blobValue;
  /**
   * A boolean value.
   *
   * @var bool
   */
  public $booleanValue;
  /**
   * A double value.
   *
   * @var 
   */
  public $doubleValue;
  protected $entityValueType = Entity::class;
  protected $entityValueDataType = '';
  /**
   * If the value should be excluded from all indexes including those defined
   * explicitly.
   *
   * @var bool
   */
  public $excludeFromIndexes;
  protected $geoPointValueType = LatLng::class;
  protected $geoPointValueDataType = '';
  /**
   * An integer value.
   *
   * @var string
   */
  public $integerValue;
  protected $keyValueType = Key::class;
  protected $keyValueDataType = '';
  /**
   * The `meaning` field should only be populated for backwards compatibility.
   *
   * @var int
   */
  public $meaning;
  /**
   * A null value.
   *
   * @var string
   */
  public $nullValue;
  /**
   * A UTF-8 encoded string value. When `exclude_from_indexes` is false (it is
   * indexed) , may have at most 1500 bytes. Otherwise, may be set to at most
   * 1,000,000 bytes.
   *
   * @var string
   */
  public $stringValue;
  /**
   * A timestamp value. When stored in the Datastore, precise only to
   * microseconds; any additional precision is rounded down.
   *
   * @var string
   */
  public $timestampValue;

  /**
   * An array value. Cannot contain another array value. A `Value` instance that
   * sets field `array_value` must not set fields `meaning` or
   * `exclude_from_indexes`.
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
   * A blob value. May have at most 1,000,000 bytes. When `exclude_from_indexes`
   * is false, may have at most 1500 bytes. In JSON requests, must be
   * base64-encoded.
   *
   * @param string $blobValue
   */
  public function setBlobValue($blobValue)
  {
    $this->blobValue = $blobValue;
  }
  /**
   * @return string
   */
  public function getBlobValue()
  {
    return $this->blobValue;
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
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * An entity value. - May have no key. - May have a key with an incomplete key
   * path. - May have a reserved/read-only key.
   *
   * @param Entity $entityValue
   */
  public function setEntityValue(Entity $entityValue)
  {
    $this->entityValue = $entityValue;
  }
  /**
   * @return Entity
   */
  public function getEntityValue()
  {
    return $this->entityValue;
  }
  /**
   * If the value should be excluded from all indexes including those defined
   * explicitly.
   *
   * @param bool $excludeFromIndexes
   */
  public function setExcludeFromIndexes($excludeFromIndexes)
  {
    $this->excludeFromIndexes = $excludeFromIndexes;
  }
  /**
   * @return bool
   */
  public function getExcludeFromIndexes()
  {
    return $this->excludeFromIndexes;
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
   * A key value.
   *
   * @param Key $keyValue
   */
  public function setKeyValue(Key $keyValue)
  {
    $this->keyValue = $keyValue;
  }
  /**
   * @return Key
   */
  public function getKeyValue()
  {
    return $this->keyValue;
  }
  /**
   * The `meaning` field should only be populated for backwards compatibility.
   *
   * @param int $meaning
   */
  public function setMeaning($meaning)
  {
    $this->meaning = $meaning;
  }
  /**
   * @return int
   */
  public function getMeaning()
  {
    return $this->meaning;
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
   * A UTF-8 encoded string value. When `exclude_from_indexes` is false (it is
   * indexed) , may have at most 1500 bytes. Otherwise, may be set to at most
   * 1,000,000 bytes.
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
   * A timestamp value. When stored in the Datastore, precise only to
   * microseconds; any additional precision is rounded down.
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
class_alias(Value::class, 'Google_Service_Datastore_Value');
