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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason extends \Google\Collection
{
  protected $collection_key = 'reasonLabels';
  /**
   * Optional. Additional feedback about the rating. This field can be populated
   * without choosing a predefined `reason`.
   *
   * @var string
   */
  public $feedback;
  /**
   * Optional. Custom reason labels for thumbs down rating provided by the user.
   * The maximum number of labels allowed is 10 and the maximum length of a
   * single label is 128 characters.
   *
   * @var string[]
   */
  public $reasonLabels;

  /**
   * Optional. Additional feedback about the rating. This field can be populated
   * without choosing a predefined `reason`.
   *
   * @param string $feedback
   */
  public function setFeedback($feedback)
  {
    $this->feedback = $feedback;
  }
  /**
   * @return string
   */
  public function getFeedback()
  {
    return $this->feedback;
  }
  /**
   * Optional. Custom reason labels for thumbs down rating provided by the user.
   * The maximum number of labels allowed is 10 and the maximum length of a
   * single label is 128 characters.
   *
   * @param string[] $reasonLabels
   */
  public function setReasonLabels($reasonLabels)
  {
    $this->reasonLabels = $reasonLabels;
  }
  /**
   * @return string[]
   */
  public function getReasonLabels()
  {
    return $this->reasonLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason');
