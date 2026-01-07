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

namespace Google\Service\Analytics;

class GoalEventDetailsEventConditions extends \Google\Model
{
  /**
   * Type of comparison. Possible values are LESS_THAN, GREATER_THAN or EQUAL.
   *
   * @var string
   */
  public $comparisonType;
  /**
   * Value used for this comparison.
   *
   * @var string
   */
  public $comparisonValue;
  /**
   * Expression used for this match.
   *
   * @var string
   */
  public $expression;
  /**
   * Type of the match to be performed. Possible values are REGEXP, BEGINS_WITH,
   * or EXACT.
   *
   * @var string
   */
  public $matchType;
  /**
   * Type of this event condition. Possible values are CATEGORY, ACTION, LABEL,
   * or VALUE.
   *
   * @var string
   */
  public $type;

  /**
   * Type of comparison. Possible values are LESS_THAN, GREATER_THAN or EQUAL.
   *
   * @param string $comparisonType
   */
  public function setComparisonType($comparisonType)
  {
    $this->comparisonType = $comparisonType;
  }
  /**
   * @return string
   */
  public function getComparisonType()
  {
    return $this->comparisonType;
  }
  /**
   * Value used for this comparison.
   *
   * @param string $comparisonValue
   */
  public function setComparisonValue($comparisonValue)
  {
    $this->comparisonValue = $comparisonValue;
  }
  /**
   * @return string
   */
  public function getComparisonValue()
  {
    return $this->comparisonValue;
  }
  /**
   * Expression used for this match.
   *
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
  /**
   * Type of the match to be performed. Possible values are REGEXP, BEGINS_WITH,
   * or EXACT.
   *
   * @param string $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return string
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * Type of this event condition. Possible values are CATEGORY, ACTION, LABEL,
   * or VALUE.
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
class_alias(GoalEventDetailsEventConditions::class, 'Google_Service_Analytics_GoalEventDetailsEventConditions');
