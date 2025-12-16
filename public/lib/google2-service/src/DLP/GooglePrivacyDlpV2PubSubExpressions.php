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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2PubSubExpressions extends \Google\Collection
{
  /**
   * Unused.
   */
  public const LOGICAL_OPERATOR_LOGICAL_OPERATOR_UNSPECIFIED = 'LOGICAL_OPERATOR_UNSPECIFIED';
  /**
   * Conditional OR.
   */
  public const LOGICAL_OPERATOR_OR = 'OR';
  /**
   * Conditional AND.
   */
  public const LOGICAL_OPERATOR_AND = 'AND';
  protected $collection_key = 'conditions';
  protected $conditionsType = GooglePrivacyDlpV2PubSubCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * The operator to apply to the collection of conditions.
   *
   * @var string
   */
  public $logicalOperator;

  /**
   * Conditions to apply to the expression.
   *
   * @param GooglePrivacyDlpV2PubSubCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GooglePrivacyDlpV2PubSubCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * The operator to apply to the collection of conditions.
   *
   * Accepted values: LOGICAL_OPERATOR_UNSPECIFIED, OR, AND
   *
   * @param self::LOGICAL_OPERATOR_* $logicalOperator
   */
  public function setLogicalOperator($logicalOperator)
  {
    $this->logicalOperator = $logicalOperator;
  }
  /**
   * @return self::LOGICAL_OPERATOR_*
   */
  public function getLogicalOperator()
  {
    return $this->logicalOperator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2PubSubExpressions::class, 'Google_Service_DLP_GooglePrivacyDlpV2PubSubExpressions');
