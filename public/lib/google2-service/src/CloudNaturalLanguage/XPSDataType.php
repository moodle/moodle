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

namespace Google\Service\CloudNaturalLanguage;

class XPSDataType extends \Google\Collection
{
  /**
   * Not specified. Should not be used.
   */
  public const TYPE_CODE_TYPE_CODE_UNSPECIFIED = 'TYPE_CODE_UNSPECIFIED';
  /**
   * Encoded as `number`, or the strings `"NaN"`, `"Infinity"`, or
   * `"-Infinity"`.
   */
  public const TYPE_CODE_FLOAT64 = 'FLOAT64';
  /**
   * Must be between 0AD and 9999AD. Encoded as `string` according to
   * time_format, or, if that format is not set, then in RFC 3339 `date-time`
   * format, where `time-offset` = `"Z"` (e.g. 1985-04-12T23:20:50.52Z).
   */
  public const TYPE_CODE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Encoded as `string`.
   */
  public const TYPE_CODE_STRING = 'STRING';
  /**
   * Encoded as `list`, where the list elements are represented according to
   * list_element_type.
   */
  public const TYPE_CODE_ARRAY = 'ARRAY';
  /**
   * Encoded as `struct`, where field values are represented according to
   * struct_type.
   */
  public const TYPE_CODE_STRUCT = 'STRUCT';
  /**
   * Values of this type are not further understood by AutoML, e.g. AutoML is
   * unable to tell the order of values (as it could with FLOAT64), or is unable
   * to say if one value contains another (as it could with STRING). Encoded as
   * `string` (bytes should be base64-encoded, as described in RFC 4648, section
   * 4).
   */
  public const TYPE_CODE_CATEGORY = 'CATEGORY';
  protected $collection_key = 'compatibleDataTypes';
  protected $compatibleDataTypesType = XPSDataType::class;
  protected $compatibleDataTypesDataType = 'array';
  protected $listElementTypeType = XPSDataType::class;
  protected $listElementTypeDataType = '';
  /**
   * If true, this DataType can also be `null`.
   *
   * @var bool
   */
  public $nullable;
  protected $structTypeType = XPSStructType::class;
  protected $structTypeDataType = '';
  /**
   * If type_code == TIMESTAMP then `time_format` provides the format in which
   * that time field is expressed. The time_format must be written in `strftime`
   * syntax. If time_format is not set, then the default format as described on
   * the field is used.
   *
   * @var string
   */
  public $timeFormat;
  /**
   * Required. The TypeCode for this type.
   *
   * @var string
   */
  public $typeCode;

  /**
   * The highly compatible data types to this data type.
   *
   * @param XPSDataType[] $compatibleDataTypes
   */
  public function setCompatibleDataTypes($compatibleDataTypes)
  {
    $this->compatibleDataTypes = $compatibleDataTypes;
  }
  /**
   * @return XPSDataType[]
   */
  public function getCompatibleDataTypes()
  {
    return $this->compatibleDataTypes;
  }
  /**
   * If type_code == ARRAY, then `list_element_type` is the type of the
   * elements.
   *
   * @param XPSDataType $listElementType
   */
  public function setListElementType(XPSDataType $listElementType)
  {
    $this->listElementType = $listElementType;
  }
  /**
   * @return XPSDataType
   */
  public function getListElementType()
  {
    return $this->listElementType;
  }
  /**
   * If true, this DataType can also be `null`.
   *
   * @param bool $nullable
   */
  public function setNullable($nullable)
  {
    $this->nullable = $nullable;
  }
  /**
   * @return bool
   */
  public function getNullable()
  {
    return $this->nullable;
  }
  /**
   * If type_code == STRUCT, then `struct_type` provides type information for
   * the struct's fields.
   *
   * @param XPSStructType $structType
   */
  public function setStructType(XPSStructType $structType)
  {
    $this->structType = $structType;
  }
  /**
   * @return XPSStructType
   */
  public function getStructType()
  {
    return $this->structType;
  }
  /**
   * If type_code == TIMESTAMP then `time_format` provides the format in which
   * that time field is expressed. The time_format must be written in `strftime`
   * syntax. If time_format is not set, then the default format as described on
   * the field is used.
   *
   * @param string $timeFormat
   */
  public function setTimeFormat($timeFormat)
  {
    $this->timeFormat = $timeFormat;
  }
  /**
   * @return string
   */
  public function getTimeFormat()
  {
    return $this->timeFormat;
  }
  /**
   * Required. The TypeCode for this type.
   *
   * Accepted values: TYPE_CODE_UNSPECIFIED, FLOAT64, TIMESTAMP, STRING, ARRAY,
   * STRUCT, CATEGORY
   *
   * @param self::TYPE_CODE_* $typeCode
   */
  public function setTypeCode($typeCode)
  {
    $this->typeCode = $typeCode;
  }
  /**
   * @return self::TYPE_CODE_*
   */
  public function getTypeCode()
  {
    return $this->typeCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSDataType::class, 'Google_Service_CloudNaturalLanguage_XPSDataType');
