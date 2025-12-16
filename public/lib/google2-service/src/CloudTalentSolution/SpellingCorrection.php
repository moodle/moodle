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

namespace Google\Service\CloudTalentSolution;

class SpellingCorrection extends \Google\Model
{
  /**
   * Indicates if the query was corrected by the spell checker.
   *
   * @var bool
   */
  public $corrected;
  /**
   * Corrected output with html tags to highlight the corrected words. Corrected
   * words are called out with the "*...*" html tags. For example, the user
   * input query is "software enginear", where the second word, "enginear," is
   * incorrect. It should be "engineer". When spelling correction is enabled,
   * this value is "software *engineer*".
   *
   * @var string
   */
  public $correctedHtml;
  /**
   * Correction output consisting of the corrected keyword string.
   *
   * @var string
   */
  public $correctedText;

  /**
   * Indicates if the query was corrected by the spell checker.
   *
   * @param bool $corrected
   */
  public function setCorrected($corrected)
  {
    $this->corrected = $corrected;
  }
  /**
   * @return bool
   */
  public function getCorrected()
  {
    return $this->corrected;
  }
  /**
   * Corrected output with html tags to highlight the corrected words. Corrected
   * words are called out with the "*...*" html tags. For example, the user
   * input query is "software enginear", where the second word, "enginear," is
   * incorrect. It should be "engineer". When spelling correction is enabled,
   * this value is "software *engineer*".
   *
   * @param string $correctedHtml
   */
  public function setCorrectedHtml($correctedHtml)
  {
    $this->correctedHtml = $correctedHtml;
  }
  /**
   * @return string
   */
  public function getCorrectedHtml()
  {
    return $this->correctedHtml;
  }
  /**
   * Correction output consisting of the corrected keyword string.
   *
   * @param string $correctedText
   */
  public function setCorrectedText($correctedText)
  {
    $this->correctedText = $correctedText;
  }
  /**
   * @return string
   */
  public function getCorrectedText()
  {
    return $this->correctedText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpellingCorrection::class, 'Google_Service_CloudTalentSolution_SpellingCorrection');
