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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1BasicLevel extends \Google\Collection
{
  /**
   * All `Conditions` must be true for the `BasicLevel` to be true.
   */
  public const COMBINING_FUNCTION_AND = 'AND';
  /**
   * If at least one `Condition` is true, then the `BasicLevel` is true.
   */
  public const COMBINING_FUNCTION_OR = 'OR';
  protected $collection_key = 'conditions';
  /**
   * How the `conditions` list should be combined to determine if a request is
   * granted this `AccessLevel`. If AND is used, each `Condition` in
   * `conditions` must be satisfied for the `AccessLevel` to be applied. If OR
   * is used, at least one `Condition` in `conditions` must be satisfied for the
   * `AccessLevel` to be applied. Default behavior is AND.
   *
   * @var string
   */
  public $combiningFunction;
  protected $conditionsType = GoogleIdentityAccesscontextmanagerV1Condition::class;
  protected $conditionsDataType = 'array';

  /**
   * How the `conditions` list should be combined to determine if a request is
   * granted this `AccessLevel`. If AND is used, each `Condition` in
   * `conditions` must be satisfied for the `AccessLevel` to be applied. If OR
   * is used, at least one `Condition` in `conditions` must be satisfied for the
   * `AccessLevel` to be applied. Default behavior is AND.
   *
   * Accepted values: AND, OR
   *
   * @param self::COMBINING_FUNCTION_* $combiningFunction
   */
  public function setCombiningFunction($combiningFunction)
  {
    $this->combiningFunction = $combiningFunction;
  }
  /**
   * @return self::COMBINING_FUNCTION_*
   */
  public function getCombiningFunction()
  {
    return $this->combiningFunction;
  }
  /**
   * Required. A list of requirements for the `AccessLevel` to be granted.
   *
   * @param GoogleIdentityAccesscontextmanagerV1Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1BasicLevel::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1BasicLevel');
