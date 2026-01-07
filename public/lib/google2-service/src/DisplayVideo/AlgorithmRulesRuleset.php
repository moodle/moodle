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

namespace Google\Service\DisplayVideo;

class AlgorithmRulesRuleset extends \Google\Collection
{
  /**
   * Unknown aggregation type.
   */
  public const AGGREGATION_TYPE_RULE_AGGREGATION_TYPE_UNSPECIFIED = 'RULE_AGGREGATION_TYPE_UNSPECIFIED';
  /**
   * The sum of rule values.
   */
  public const AGGREGATION_TYPE_SUM_OF_VALUES = 'SUM_OF_VALUES';
  /**
   * The product of rule values.
   */
  public const AGGREGATION_TYPE_PRODUCT_OF_VALUES = 'PRODUCT_OF_VALUES';
  /**
   * The maximum rule value.
   */
  public const AGGREGATION_TYPE_MAXIMUM_VALUE = 'MAXIMUM_VALUE';
  protected $collection_key = 'rules';
  /**
   * How to aggregate values of evaluated rules.
   *
   * @var string
   */
  public $aggregationType;
  /**
   * Maximum value the ruleset can evaluate to.
   *
   * @var 
   */
  public $maxValue;
  protected $rulesType = AlgorithmRulesRule::class;
  protected $rulesDataType = 'array';

  /**
   * How to aggregate values of evaluated rules.
   *
   * Accepted values: RULE_AGGREGATION_TYPE_UNSPECIFIED, SUM_OF_VALUES,
   * PRODUCT_OF_VALUES, MAXIMUM_VALUE
   *
   * @param self::AGGREGATION_TYPE_* $aggregationType
   */
  public function setAggregationType($aggregationType)
  {
    $this->aggregationType = $aggregationType;
  }
  /**
   * @return self::AGGREGATION_TYPE_*
   */
  public function getAggregationType()
  {
    return $this->aggregationType;
  }
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * List of rules to generate the impression value.
   *
   * @param AlgorithmRulesRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return AlgorithmRulesRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesRuleset::class, 'Google_Service_DisplayVideo_AlgorithmRulesRuleset');
