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

namespace Google\Service\Container;

class NodeAffinity extends \Google\Collection
{
  /**
   * Invalid or unspecified affinity operator.
   */
  public const OPERATOR_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * Affinity operator.
   */
  public const OPERATOR_IN = 'IN';
  /**
   * Anti-affinity operator.
   */
  public const OPERATOR_NOT_IN = 'NOT_IN';
  protected $collection_key = 'values';
  /**
   * Key for NodeAffinity.
   *
   * @var string
   */
  public $key;
  /**
   * Operator for NodeAffinity.
   *
   * @var string
   */
  public $operator;
  /**
   * Values for NodeAffinity.
   *
   * @var string[]
   */
  public $values;

  /**
   * Key for NodeAffinity.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Operator for NodeAffinity.
   *
   * Accepted values: OPERATOR_UNSPECIFIED, IN, NOT_IN
   *
   * @param self::OPERATOR_* $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return self::OPERATOR_*
   */
  public function getOperator()
  {
    return $this->operator;
  }
  /**
   * Values for NodeAffinity.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeAffinity::class, 'Google_Service_Container_NodeAffinity');
