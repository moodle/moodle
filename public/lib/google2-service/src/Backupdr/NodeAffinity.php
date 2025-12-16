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

namespace Google\Service\Backupdr;

class NodeAffinity extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const OPERATOR_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * Requires Compute Engine to seek for matched nodes.
   */
  public const OPERATOR_IN = 'IN';
  /**
   * Requires Compute Engine to avoid certain nodes.
   */
  public const OPERATOR_NOT_IN = 'NOT_IN';
  protected $collection_key = 'values';
  /**
   * Optional. Corresponds to the label key of Node resource.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Defines the operation of node selection.
   *
   * @var string
   */
  public $operator;
  /**
   * Optional. Corresponds to the label values of Node resource.
   *
   * @var string[]
   */
  public $values;

  /**
   * Optional. Corresponds to the label key of Node resource.
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
   * Optional. Defines the operation of node selection.
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
   * Optional. Corresponds to the label values of Node resource.
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
class_alias(NodeAffinity::class, 'Google_Service_Backupdr_NodeAffinity');
