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

namespace Google\Service\Forms;

class Grading extends \Google\Model
{
  protected $correctAnswersType = CorrectAnswers::class;
  protected $correctAnswersDataType = '';
  protected $generalFeedbackType = Feedback::class;
  protected $generalFeedbackDataType = '';
  /**
   * Required. The maximum number of points a respondent can automatically get
   * for a correct answer. This must not be negative.
   *
   * @var int
   */
  public $pointValue;
  protected $whenRightType = Feedback::class;
  protected $whenRightDataType = '';
  protected $whenWrongType = Feedback::class;
  protected $whenWrongDataType = '';

  /**
   * Required. The answer key for the question. Responses are automatically
   * graded based on this field.
   *
   * @param CorrectAnswers $correctAnswers
   */
  public function setCorrectAnswers(CorrectAnswers $correctAnswers)
  {
    $this->correctAnswers = $correctAnswers;
  }
  /**
   * @return CorrectAnswers
   */
  public function getCorrectAnswers()
  {
    return $this->correctAnswers;
  }
  /**
   * The feedback displayed for all answers. This is commonly used for short
   * answer questions when a quiz owner wants to quickly give respondents some
   * sense of whether they answered the question correctly before they've had a
   * chance to officially grade the response. General feedback cannot be set for
   * automatically graded multiple choice questions.
   *
   * @param Feedback $generalFeedback
   */
  public function setGeneralFeedback(Feedback $generalFeedback)
  {
    $this->generalFeedback = $generalFeedback;
  }
  /**
   * @return Feedback
   */
  public function getGeneralFeedback()
  {
    return $this->generalFeedback;
  }
  /**
   * Required. The maximum number of points a respondent can automatically get
   * for a correct answer. This must not be negative.
   *
   * @param int $pointValue
   */
  public function setPointValue($pointValue)
  {
    $this->pointValue = $pointValue;
  }
  /**
   * @return int
   */
  public function getPointValue()
  {
    return $this->pointValue;
  }
  /**
   * The feedback displayed for correct responses. This feedback can only be set
   * for multiple choice questions that have correct answers provided.
   *
   * @param Feedback $whenRight
   */
  public function setWhenRight(Feedback $whenRight)
  {
    $this->whenRight = $whenRight;
  }
  /**
   * @return Feedback
   */
  public function getWhenRight()
  {
    return $this->whenRight;
  }
  /**
   * The feedback displayed for incorrect responses. This feedback can only be
   * set for multiple choice questions that have correct answers provided.
   *
   * @param Feedback $whenWrong
   */
  public function setWhenWrong(Feedback $whenWrong)
  {
    $this->whenWrong = $whenWrong;
  }
  /**
   * @return Feedback
   */
  public function getWhenWrong()
  {
    return $this->whenWrong;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Grading::class, 'Google_Service_Forms_Grading');
