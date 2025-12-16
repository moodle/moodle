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

namespace Google\Service\CloudTrace;

class Attributes extends \Google\Model
{
  protected $attributeMapType = AttributeValue::class;
  protected $attributeMapDataType = 'map';
  /**
   * The number of attributes that were discarded. Attributes can be discarded
   * because their keys are too long or because there are too many attributes.
   * If this value is 0 then all attributes are valid.
   *
   * @var int
   */
  public $droppedAttributesCount;

  /**
   * A set of attributes. Each attribute's key can be up to 128 bytes long. The
   * value can be a string up to 256 bytes, a signed 64-bit integer, or the
   * boolean values `true` or `false`. For example: "/instance_id": {
   * "string_value": { "value": "my-instance" } } "/http/request_bytes": {
   * "int_value": 300 } "example.com/myattribute": { "bool_value": false }
   *
   * @param AttributeValue[] $attributeMap
   */
  public function setAttributeMap($attributeMap)
  {
    $this->attributeMap = $attributeMap;
  }
  /**
   * @return AttributeValue[]
   */
  public function getAttributeMap()
  {
    return $this->attributeMap;
  }
  /**
   * The number of attributes that were discarded. Attributes can be discarded
   * because their keys are too long or because there are too many attributes.
   * If this value is 0 then all attributes are valid.
   *
   * @param int $droppedAttributesCount
   */
  public function setDroppedAttributesCount($droppedAttributesCount)
  {
    $this->droppedAttributesCount = $droppedAttributesCount;
  }
  /**
   * @return int
   */
  public function getDroppedAttributesCount()
  {
    return $this->droppedAttributesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attributes::class, 'Google_Service_CloudTrace_Attributes');
