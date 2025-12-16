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

class GoogleCloudDialogflowCxV3AnswerFeedback extends \Google\Model
{
  /**
   * Rating not specified.
   */
  public const RATING_RATING_UNSPECIFIED = 'RATING_UNSPECIFIED';
  /**
   * Thumbs up feedback from user.
   */
  public const RATING_THUMBS_UP = 'THUMBS_UP';
  /**
   * Thumbs down feedback from user.
   */
  public const RATING_THUMBS_DOWN = 'THUMBS_DOWN';
  /**
   * Optional. Custom rating from the user about the provided answer, with
   * maximum length of 1024 characters. For example, client could use a
   * customized JSON object to indicate the rating.
   *
   * @var string
   */
  public $customRating;
  /**
   * Optional. Rating from user for the specific Dialogflow response.
   *
   * @var string
   */
  public $rating;
  protected $ratingReasonType = GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason::class;
  protected $ratingReasonDataType = '';

  /**
   * Optional. Custom rating from the user about the provided answer, with
   * maximum length of 1024 characters. For example, client could use a
   * customized JSON object to indicate the rating.
   *
   * @param string $customRating
   */
  public function setCustomRating($customRating)
  {
    $this->customRating = $customRating;
  }
  /**
   * @return string
   */
  public function getCustomRating()
  {
    return $this->customRating;
  }
  /**
   * Optional. Rating from user for the specific Dialogflow response.
   *
   * Accepted values: RATING_UNSPECIFIED, THUMBS_UP, THUMBS_DOWN
   *
   * @param self::RATING_* $rating
   */
  public function setRating($rating)
  {
    $this->rating = $rating;
  }
  /**
   * @return self::RATING_*
   */
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * Optional. In case of thumbs down rating provided, users can optionally
   * provide context about the rating.
   *
   * @param GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason $ratingReason
   */
  public function setRatingReason(GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason $ratingReason)
  {
    $this->ratingReason = $ratingReason;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AnswerFeedbackRatingReason
   */
  public function getRatingReason()
  {
    return $this->ratingReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AnswerFeedback::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AnswerFeedback');
