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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1ExpressionDataCondition extends \Google\Model
{
  /**
   * Unspecified condition type.
   */
  public const CONDITION_TYPE_CONDITION_TYPE_UNSPECIFIED = 'CONDITION_TYPE_UNSPECIFIED';
  /**
   * The expression evaluation was successful.
   */
  public const CONDITION_TYPE_EXPRESSION_EVALUATION_SUCCESS = 'EXPRESSION_EVALUATION_SUCCESS';
  /**
   * The expression evaluation was unsuccessful.
   */
  public const CONDITION_TYPE_EXPRESSION_EVALUATION_FAILURE = 'EXPRESSION_EVALUATION_FAILURE';
  /**
   * The type of the condition.
   *
   * @var string
   */
  public $conditionType;

  /**
   * The type of the condition.
   *
   * Accepted values: CONDITION_TYPE_UNSPECIFIED, EXPRESSION_EVALUATION_SUCCESS,
   * EXPRESSION_EVALUATION_FAILURE
   *
   * @param self::CONDITION_TYPE_* $conditionType
   */
  public function setConditionType($conditionType)
  {
    $this->conditionType = $conditionType;
  }
  /**
   * @return self::CONDITION_TYPE_*
   */
  public function getConditionType()
  {
    return $this->conditionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1ExpressionDataCondition::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1ExpressionDataCondition');
