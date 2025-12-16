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

class GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice extends \Google\Model
{
  /**
   * Boolean value.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * A short string used as an identifier.
   *
   * @var string
   */
  public $key;
  /**
   * A value of "Not Applicable (N/A)". If provided, this field may only be set
   * to `true`. If a question receives this answer, it will be excluded from any
   * score calculations.
   *
   * @var bool
   */
  public $naValue;
  /**
   * Numerical value.
   *
   * @var 
   */
  public $numValue;
  /**
   * Numerical score of the answer, used for generating the overall score of a
   * QaScorecardResult. If the answer uses na_value, this field is unused.
   *
   * @var 
   */
  public $score;
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
   * A short string used as an identifier.
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
   * A value of "Not Applicable (N/A)". If provided, this field may only be set
   * to `true`. If a question receives this answer, it will be excluded from any
   * score calculations.
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
  public function setNumValue($numValue)
  {
    $this->numValue = $numValue;
  }
  public function getNumValue()
  {
    return $this->numValue;
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
class_alias(GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice');
