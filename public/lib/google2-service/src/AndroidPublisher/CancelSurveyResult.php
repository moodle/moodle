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

namespace Google\Service\AndroidPublisher;

class CancelSurveyResult extends \Google\Model
{
  /**
   * Unspecified cancel survey reason.
   */
  public const REASON_CANCEL_SURVEY_REASON_UNSPECIFIED = 'CANCEL_SURVEY_REASON_UNSPECIFIED';
  /**
   * Not enough usage of the subscription.
   */
  public const REASON_CANCEL_SURVEY_REASON_NOT_ENOUGH_USAGE = 'CANCEL_SURVEY_REASON_NOT_ENOUGH_USAGE';
  /**
   * Technical issues while using the app.
   */
  public const REASON_CANCEL_SURVEY_REASON_TECHNICAL_ISSUES = 'CANCEL_SURVEY_REASON_TECHNICAL_ISSUES';
  /**
   * Cost related issues.
   */
  public const REASON_CANCEL_SURVEY_REASON_COST_RELATED = 'CANCEL_SURVEY_REASON_COST_RELATED';
  /**
   * The user found a better app.
   */
  public const REASON_CANCEL_SURVEY_REASON_FOUND_BETTER_APP = 'CANCEL_SURVEY_REASON_FOUND_BETTER_APP';
  /**
   * Other reasons.
   */
  public const REASON_CANCEL_SURVEY_REASON_OTHERS = 'CANCEL_SURVEY_REASON_OTHERS';
  /**
   * The reason the user selected in the cancel survey.
   *
   * @var string
   */
  public $reason;
  /**
   * Only set for CANCEL_SURVEY_REASON_OTHERS. This is the user's freeform
   * response to the survey.
   *
   * @var string
   */
  public $reasonUserInput;

  /**
   * The reason the user selected in the cancel survey.
   *
   * Accepted values: CANCEL_SURVEY_REASON_UNSPECIFIED,
   * CANCEL_SURVEY_REASON_NOT_ENOUGH_USAGE,
   * CANCEL_SURVEY_REASON_TECHNICAL_ISSUES, CANCEL_SURVEY_REASON_COST_RELATED,
   * CANCEL_SURVEY_REASON_FOUND_BETTER_APP, CANCEL_SURVEY_REASON_OTHERS
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Only set for CANCEL_SURVEY_REASON_OTHERS. This is the user's freeform
   * response to the survey.
   *
   * @param string $reasonUserInput
   */
  public function setReasonUserInput($reasonUserInput)
  {
    $this->reasonUserInput = $reasonUserInput;
  }
  /**
   * @return string
   */
  public function getReasonUserInput()
  {
    return $this->reasonUserInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CancelSurveyResult::class, 'Google_Service_AndroidPublisher_CancelSurveyResult');
