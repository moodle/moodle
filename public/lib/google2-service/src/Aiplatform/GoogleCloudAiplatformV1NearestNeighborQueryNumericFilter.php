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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter extends \Google\Model
{
  /**
   * Unspecified operator.
   */
  public const OP_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * Entities are eligible if their value is < the query's.
   */
  public const OP_LESS = 'LESS';
  /**
   * Entities are eligible if their value is <= the query's.
   */
  public const OP_LESS_EQUAL = 'LESS_EQUAL';
  /**
   * Entities are eligible if their value is == the query's.
   */
  public const OP_EQUAL = 'EQUAL';
  /**
   * Entities are eligible if their value is >= the query's.
   */
  public const OP_GREATER_EQUAL = 'GREATER_EQUAL';
  /**
   * Entities are eligible if their value is > the query's.
   */
  public const OP_GREATER = 'GREATER';
  /**
   * Entities are eligible if their value is != the query's.
   */
  public const OP_NOT_EQUAL = 'NOT_EQUAL';
  /**
   * Required. Column name in BigQuery that used as filters.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. This MUST be specified for queries and must NOT be specified for
   * database points.
   *
   * @var string
   */
  public $op;
  /**
   * double value type.
   *
   * @var 
   */
  public $valueDouble;
  /**
   * float value type.
   *
   * @var float
   */
  public $valueFloat;
  /**
   * int value type.
   *
   * @var string
   */
  public $valueInt;

  /**
   * Required. Column name in BigQuery that used as filters.
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
   * Optional. This MUST be specified for queries and must NOT be specified for
   * database points.
   *
   * Accepted values: OPERATOR_UNSPECIFIED, LESS, LESS_EQUAL, EQUAL,
   * GREATER_EQUAL, GREATER, NOT_EQUAL
   *
   * @param self::OP_* $op
   */
  public function setOp($op)
  {
    $this->op = $op;
  }
  /**
   * @return self::OP_*
   */
  public function getOp()
  {
    return $this->op;
  }
  public function setValueDouble($valueDouble)
  {
    $this->valueDouble = $valueDouble;
  }
  public function getValueDouble()
  {
    return $this->valueDouble;
  }
  /**
   * float value type.
   *
   * @param float $valueFloat
   */
  public function setValueFloat($valueFloat)
  {
    $this->valueFloat = $valueFloat;
  }
  /**
   * @return float
   */
  public function getValueFloat()
  {
    return $this->valueFloat;
  }
  /**
   * int value type.
   *
   * @param string $valueInt
   */
  public function setValueInt($valueInt)
  {
    $this->valueInt = $valueInt;
  }
  /**
   * @return string
   */
  public function getValueInt()
  {
    return $this->valueInt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborQueryNumericFilter');
