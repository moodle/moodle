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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Proximity extends \Google\Model
{
  /**
   * Number of characters after the finding to consider.
   *
   * @var int
   */
  public $windowAfter;
  /**
   * Number of characters before the finding to consider. For tabular data, if
   * you want to modify the likelihood of an entire column of findngs, set this
   * to 1. For more information, see [Hotword example: Set the match likelihood
   * of a table column] (https://cloud.google.com/sensitive-data-
   * protection/docs/creating-custom-infotypes-likelihood#match-column-values).
   *
   * @var int
   */
  public $windowBefore;

  /**
   * Number of characters after the finding to consider.
   *
   * @param int $windowAfter
   */
  public function setWindowAfter($windowAfter)
  {
    $this->windowAfter = $windowAfter;
  }
  /**
   * @return int
   */
  public function getWindowAfter()
  {
    return $this->windowAfter;
  }
  /**
   * Number of characters before the finding to consider. For tabular data, if
   * you want to modify the likelihood of an entire column of findngs, set this
   * to 1. For more information, see [Hotword example: Set the match likelihood
   * of a table column] (https://cloud.google.com/sensitive-data-
   * protection/docs/creating-custom-infotypes-likelihood#match-column-values).
   *
   * @param int $windowBefore
   */
  public function setWindowBefore($windowBefore)
  {
    $this->windowBefore = $windowBefore;
  }
  /**
   * @return int
   */
  public function getWindowBefore()
  {
    return $this->windowBefore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Proximity::class, 'Google_Service_DLP_GooglePrivacyDlpV2Proximity');
