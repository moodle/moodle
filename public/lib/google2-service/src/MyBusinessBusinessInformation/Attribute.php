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

namespace Google\Service\MyBusinessBusinessInformation;

class Attribute extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const VALUE_TYPE_ATTRIBUTE_VALUE_TYPE_UNSPECIFIED = 'ATTRIBUTE_VALUE_TYPE_UNSPECIFIED';
  /**
   * The values for this attribute are boolean values.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The attribute has a predetermined list of available values that can be
   * used. Metadata for this attribute will list these values.
   */
  public const VALUE_TYPE_ENUM = 'ENUM';
  /**
   * The values for this attribute are URLs.
   */
  public const VALUE_TYPE_URL = 'URL';
  /**
   * The attribute value is an enum with multiple possible values that can be
   * explicitly set or unset.
   */
  public const VALUE_TYPE_REPEATED_ENUM = 'REPEATED_ENUM';
  protected $collection_key = 'values';
  /**
   * Required. The resource name for this attribute.
   *
   * @var string
   */
  public $name;
  protected $repeatedEnumValueType = RepeatedEnumAttributeValue::class;
  protected $repeatedEnumValueDataType = '';
  protected $uriValuesType = UriAttributeValue::class;
  protected $uriValuesDataType = 'array';
  /**
   * Output only. The type of value that this attribute contains. This should be
   * used to determine how to interpret the value.
   *
   * @var string
   */
  public $valueType;
  /**
   * The values for this attribute. The type of the values supplied must match
   * that expected for that attribute. This is a repeated field where multiple
   * attribute values may be provided. Attribute types only support one value.
   *
   * @var array[]
   */
  public $values;

  /**
   * Required. The resource name for this attribute.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * When the attribute value type is REPEATED_ENUM, this contains the attribute
   * value, and the other values fields must be empty.
   *
   * @param RepeatedEnumAttributeValue $repeatedEnumValue
   */
  public function setRepeatedEnumValue(RepeatedEnumAttributeValue $repeatedEnumValue)
  {
    $this->repeatedEnumValue = $repeatedEnumValue;
  }
  /**
   * @return RepeatedEnumAttributeValue
   */
  public function getRepeatedEnumValue()
  {
    return $this->repeatedEnumValue;
  }
  /**
   * When the attribute value type is URL, this field contains the value(s) for
   * this attribute, and the other values fields must be empty.
   *
   * @param UriAttributeValue[] $uriValues
   */
  public function setUriValues($uriValues)
  {
    $this->uriValues = $uriValues;
  }
  /**
   * @return UriAttributeValue[]
   */
  public function getUriValues()
  {
    return $this->uriValues;
  }
  /**
   * Output only. The type of value that this attribute contains. This should be
   * used to determine how to interpret the value.
   *
   * Accepted values: ATTRIBUTE_VALUE_TYPE_UNSPECIFIED, BOOL, ENUM, URL,
   * REPEATED_ENUM
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
  /**
   * The values for this attribute. The type of the values supplied must match
   * that expected for that attribute. This is a repeated field where multiple
   * attribute values may be provided. Attribute types only support one value.
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attribute::class, 'Google_Service_MyBusinessBusinessInformation_Attribute');
