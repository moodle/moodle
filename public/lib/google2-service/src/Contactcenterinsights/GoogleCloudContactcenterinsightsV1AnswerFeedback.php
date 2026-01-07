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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1AnswerFeedback extends \Google\Model
{
  /**
   * Correctness level unspecified.
   */
  public const CORRECTNESS_LEVEL_CORRECTNESS_LEVEL_UNSPECIFIED = 'CORRECTNESS_LEVEL_UNSPECIFIED';
  /**
   * Answer is totally wrong.
   */
  public const CORRECTNESS_LEVEL_NOT_CORRECT = 'NOT_CORRECT';
  /**
   * Answer is partially correct.
   */
  public const CORRECTNESS_LEVEL_PARTIALLY_CORRECT = 'PARTIALLY_CORRECT';
  /**
   * Answer is fully correct.
   */
  public const CORRECTNESS_LEVEL_FULLY_CORRECT = 'FULLY_CORRECT';
  /**
   * Indicates whether an answer or item was clicked by the human agent.
   *
   * @var bool
   */
  public $clicked;
  /**
   * The correctness level of an answer.
   *
   * @var string
   */
  public $correctnessLevel;
  /**
   * Indicates whether an answer or item was displayed to the human agent in the
   * agent desktop UI.
   *
   * @var bool
   */
  public $displayed;

  /**
   * Indicates whether an answer or item was clicked by the human agent.
   *
   * @param bool $clicked
   */
  public function setClicked($clicked)
  {
    $this->clicked = $clicked;
  }
  /**
   * @return bool
   */
  public function getClicked()
  {
    return $this->clicked;
  }
  /**
   * The correctness level of an answer.
   *
   * Accepted values: CORRECTNESS_LEVEL_UNSPECIFIED, NOT_CORRECT,
   * PARTIALLY_CORRECT, FULLY_CORRECT
   *
   * @param self::CORRECTNESS_LEVEL_* $correctnessLevel
   */
  public function setCorrectnessLevel($correctnessLevel)
  {
    $this->correctnessLevel = $correctnessLevel;
  }
  /**
   * @return self::CORRECTNESS_LEVEL_*
   */
  public function getCorrectnessLevel()
  {
    return $this->correctnessLevel;
  }
  /**
   * Indicates whether an answer or item was displayed to the human agent in the
   * agent desktop UI.
   *
   * @param bool $displayed
   */
  public function setDisplayed($displayed)
  {
    $this->displayed = $displayed;
  }
  /**
   * @return bool
   */
  public function getDisplayed()
  {
    return $this->displayed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1AnswerFeedback::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AnswerFeedback');
