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

class QueryParameterType extends \Google\Collection
{
  protected $collection_key = 'structTypes';
  protected $arrayTypeType = QueryParameterType::class;
  protected $arrayTypeDataType = '';
  protected $rangeElementTypeType = QueryParameterType::class;
  protected $rangeElementTypeDataType = '';
  protected $structTypesType = QueryParameterTypeStructTypes::class;
  protected $structTypesDataType = 'array';
  /**
   * Optional. Precision (maximum number of total digits in base 10) for seconds
   * of TIMESTAMP type. Possible values include: * 6 (Default, for TIMESTAMP
   * type with microsecond precision) * 12 (For TIMESTAMP type with picosecond
   * precision)
   *
   * @var string
   */
  public $timestampPrecision;
  /**
   * Required. The top level type of this field.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The type of the array's elements, if this is an array.
   *
   * @param QueryParameterType $arrayType
   */
  public function setArrayType(QueryParameterType $arrayType)
  {
    $this->arrayType = $arrayType;
  }
  /**
   * @return QueryParameterType
   */
  public function getArrayType()
  {
    return $this->arrayType;
  }
  /**
   * Optional. The element type of the range, if this is a range.
   *
   * @param QueryParameterType $rangeElementType
   */
  public function setRangeElementType(QueryParameterType $rangeElementType)
  {
    $this->rangeElementType = $rangeElementType;
  }
  /**
   * @return QueryParameterType
   */
  public function getRangeElementType()
  {
    return $this->rangeElementType;
  }
  /**
   * Optional. The types of the fields of this struct, in order, if this is a
   * struct.
   *
   * @param QueryParameterTypeStructTypes[] $structTypes
   */
  public function setStructTypes($structTypes)
  {
    $this->structTypes = $structTypes;
  }
  /**
   * @return QueryParameterTypeStructTypes[]
   */
  public function getStructTypes()
  {
    return $this->structTypes;
  }
  /**
   * Optional. Precision (maximum number of total digits in base 10) for seconds
   * of TIMESTAMP type. Possible values include: * 6 (Default, for TIMESTAMP
   * type with microsecond precision) * 12 (For TIMESTAMP type with picosecond
   * precision)
   *
   * @param string $timestampPrecision
   */
  public function setTimestampPrecision($timestampPrecision)
  {
    $this->timestampPrecision = $timestampPrecision;
  }
  /**
   * @return string
   */
  public function getTimestampPrecision()
  {
    return $this->timestampPrecision;
  }
  /**
   * Required. The top level type of this field.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryParameterType::class, 'Google_Service_Bigquery_QueryParameterType');
