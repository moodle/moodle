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

class GoogleCloudContactcenterinsightsV1QaAnswerAnswerValue extends \Google\Model
{
  /**
   * Boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * A short string used as an identifier. Matches the value used in
   * QaQuestion.AnswerChoice.key.
   *
   * @var string
   */
  public $key;
  /**
   * A value of "Not Applicable (N/A)". Should only ever be `true`.
   *
   * @var bool
   */
  public $naValue;
  /**
   * Output only. Normalized score of the questions. Calculated as score /
   * potential_score.
   *
   * @var 
   */
  public $normalizedScore;
  /**
   * Numerical value.
   *
   * @var 
   */
  public $numValue;
  /**
   * Output only. The maximum potential score of the question.
   *
   * @var 
   */
  public $potentialScore;
  /**
   * Output only. Numerical score of the answer.
   *
   * @var 
   */
  public $score;
  /**
   * Output only. A value of "Skip". If provided, this field may only be set to
   * `true`. If a question receives this answer, it will be excluded from any
   * score calculations. This would mean that the question was not evaluated.
   *
   * @var bool
   */
  public $skipValue;
  /**
   * String value.
   *
   * @var string
   */
  public $strValue;

  /**
   * Boolean value.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * A short string used as an identifier. Matches the value used in
   * QaQuestion.AnswerChoice.key.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * A value of "Not Applicable (N/A)". Should only ever be `true`.
   *
   * @param bool $naValue
   */
  public function setNaValue($naValue)
  {
    $this->naValue = $naValue;
  }
  /**
   * @return bool
   */
  public function getNaValue()
  {
    return $this->naValue;
  }
  public function setNormalizedScore($normalizedScore)
  {
    $this->normalizedScore = $normalizedScore;
  }
  public function getNormalizedScore()
  {
    return $this->normalizedScore;
  }
  public function setNumValue($numValue)
  {
    $this->numValue = $numValue;
  }
  public function getNumValue()
  {
    return $this->numValue;
  }
  public function setPotentialScore($potentialScore)
  {
    $this->potentialScore = $potentialScore;
  }
  public function getPotentialScore()
  {
    return $this->potentialScore;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * Output only. A value of "Skip". If provided, this field may only be set to
   * `true`. If a question receives this answer, it will be excluded from any
   * score calculations. This would mean that the question was not evaluated.
   *
   * @param bool $skipValue
   */
  public function setSkipValue($skipValue)
  {
    $this->skipValue = $skipValue;
  }
  /**
   * @return bool
   */
  public function getSkipValue()
  {
    return $this->skipValue;
  }
  /**
   * String value.
   *
   * @param string $strValue
   */
  public function setStrValue($strValue)
  {
    $this->strValue = $strValue;
  }
  /**
   * @return string
   */
  public function getStrValue()
  {
    return $this->strValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QaAnswerAnswerValue::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaAnswerAnswerValue');
