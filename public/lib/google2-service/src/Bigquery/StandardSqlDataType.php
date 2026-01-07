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

namespace Google\Service\Bigquery;

class StandardSqlDataType extends \Google\Model
{
  /**
   * Invalid type.
   */
  public const TYPE_KIND_TYPE_KIND_UNSPECIFIED = 'TYPE_KIND_UNSPECIFIED';
  /**
   * Encoded as a string in decimal format.
   */
  public const TYPE_KIND_INT64 = 'INT64';
  /**
   * Encoded as a boolean "false" or "true".
   */
  public const TYPE_KIND_BOOL = 'BOOL';
  /**
   * Encoded as a number, or string "NaN", "Infinity" or "-Infinity".
   */
  public const TYPE_KIND_FLOAT64 = 'FLOAT64';
  /**
   * Encoded as a string value.
   */
  public const TYPE_KIND_STRING = 'STRING';
  /**
   * Encoded as a base64 string per RFC 4648, section 4.
   */
  public const TYPE_KIND_BYTES = 'BYTES';
  /**
   * Encoded as an RFC 3339 timestamp with mandatory "Z" time zone string:
   * 1985-04-12T23:20:50.52Z
   */
  public const TYPE_KIND_TIMESTAMP = 'TIMESTAMP';
  /**
   * Encoded as RFC 3339 full-date format string: 1985-04-12
   */
  public const TYPE_KIND_DATE = 'DATE';
  /**
   * Encoded as RFC 3339 partial-time format string: 23:20:50.52
   */
  public const TYPE_KIND_TIME = 'TIME';
  /**
   * Encoded as RFC 3339 full-date "T" partial-time: 1985-04-12T23:20:50.52
   */
  public const TYPE_KIND_DATETIME = 'DATETIME';
  /**
   * Encoded as fully qualified 3 part: 0-5 15 2:30:45.6
   */
  public const TYPE_KIND_INTERVAL = 'INTERVAL';
  /**
   * Encoded as WKT
   */
  public const TYPE_KIND_GEOGRAPHY = 'GEOGRAPHY';
  /**
   * Encoded as a decimal string.
   */
  public const TYPE_KIND_NUMERIC = 'NUMERIC';
  /**
   * Encoded as a decimal string.
   */
  public const TYPE_KIND_BIGNUMERIC = 'BIGNUMERIC';
  /**
   * Encoded as a string.
   */
  public const TYPE_KIND_JSON = 'JSON';
  /**
   * Encoded as a list with types matching Type.array_type.
   */
  public const TYPE_KIND_ARRAY = 'ARRAY';
  /**
   * Encoded as a list with fields of type Type.struct_type[i]. List is used
   * because a JSON object cannot have duplicate field names.
   */
  public const TYPE_KIND_STRUCT = 'STRUCT';
  /**
   * Encoded as a pair with types matching range_element_type. Pairs must begin
   * with "[", end with ")", and be separated by ", ".
   */
  public const TYPE_KIND_RANGE = 'RANGE';
  protected $arrayElementTypeType = StandardSqlDataType::class;
  protected $arrayElementTypeDataType = '';
  protected $rangeElementTypeType = StandardSqlDataType::class;
  protected $rangeElementTypeDataType = '';
  protected $structTypeType = StandardSqlStructType::class;
  protected $structTypeDataType = '';
  /**
   * Required. The top level type of this field. Can be any GoogleSQL data type
   * (e.g., "INT64", "DATE", "ARRAY").
   *
   * @var string
   */
  public $typeKind;

  /**
   * The type of the array's elements, if type_kind = "ARRAY".
   *
   * @param StandardSqlDataType $arrayElementType
   */
  public function setArrayElementType(StandardSqlDataType $arrayElementType)
  {
    $this->arrayElementType = $arrayElementType;
  }
  /**
   * @return StandardSqlDataType
   */
  public function getArrayElementType()
  {
    return $this->arrayElementType;
  }
  /**
   * The type of the range's elements, if type_kind = "RANGE".
   *
   * @param StandardSqlDataType $rangeElementType
   */
  public function setRangeElementType(StandardSqlDataType $rangeElementType)
  {
    $this->rangeElementType = $rangeElementType;
  }
  /**
   * @return StandardSqlDataType
   */
  public function getRangeElementType()
  {
    return $this->rangeElementType;
  }
  /**
   * The fields of this struct, in order, if type_kind = "STRUCT".
   *
   * @param StandardSqlStructType $structType
   */
  public function setStructType(StandardSqlStructType $structType)
  {
    $this->structType = $structType;
  }
  /**
   * @return StandardSqlStructType
   */
  public function getStructType()
  {
    return $this->structType;
  }
  /**
   * Required. The top level type of this field. Can be any GoogleSQL data type
   * (e.g., "INT64", "DATE", "ARRAY").
   *
   * Accepted values: TYPE_KIND_UNSPECIFIED, INT64, BOOL, FLOAT64, STRING,
   * BYTES, TIMESTAMP, DATE, TIME, DATETIME, INTERVAL, GEOGRAPHY, NUMERIC,
   * BIGNUMERIC, JSON, ARRAY, STRUCT, RANGE
   *
   * @param self::TYPE_KIND_* $typeKind
   */
  public function setTypeKind($typeKind)
  {
    $this->typeKind = $typeKind;
  }
  /**
   * @return self::TYPE_KIND_*
   */
  public function getTypeKind()
  {
    return $this->typeKind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StandardSqlDataType::class, 'Google_Service_Bigquery_StandardSqlDataType');
