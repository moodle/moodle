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

class GoogleCloudAiplatformV1IndexDatapointNumericRestriction extends \Google\Model
{
  /**
   * Default value of the enum.
   */
  public const OP_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * Datapoints are eligible iff their value is < the query's.
   */
  public const OP_LESS = 'LESS';
  /**
   * Datapoints are eligible iff their value is <= the query's.
   */
  public const OP_LESS_EQUAL = 'LESS_EQUAL';
  /**
   * Datapoints are eligible iff their value is == the query's.
   */
  public const OP_EQUAL = 'EQUAL';
  /**
   * Datapoints are eligible iff their value is >= the query's.
   */
  public const OP_GREATER_EQUAL = 'GREATER_EQUAL';
  /**
   * Datapoints are eligible iff their value is > the query's.
   */
  public const OP_GREATER = 'GREATER';
  /**
   * Datapoints are eligible iff their value is != the query's.
   */
  public const OP_NOT_EQUAL = 'NOT_EQUAL';
  /**
   * The namespace of this restriction. e.g.: cost.
   *
   * @var string
   */
  public $namespace;
  /**
   * This MUST be specified for queries and must NOT be specified for
   * datapoints.
   *
   * @var string
   */
  public $op;
  /**
   * Represents 64 bit float.
   *
   * @var 
   */
  public $valueDouble;
  /**
   * Represents 32 bit float.
   *
   * @var float
   */
  public $valueFloat;
  /**
   * Represents 64 bit integer.
   *
   * @var string
   */
  public $valueInt;

  /**
   * The namespace of this restriction. e.g.: cost.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
  /**
   * This MUST be specified for queries and must NOT be specified for
   * datapoints.
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
   * Represents 32 bit float.
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
   * Represents 64 bit integer.
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
class_alias(GoogleCloudAiplatformV1IndexDatapointNumericRestriction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IndexDatapointNumericRestriction');
