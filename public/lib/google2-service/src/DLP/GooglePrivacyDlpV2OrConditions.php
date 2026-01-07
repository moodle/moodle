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

class GooglePrivacyDlpV2OrConditions extends \Google\Model
{
  /**
   * Minimum age a table must have before Cloud DLP can profile it. Value must
   * be 1 hour or greater.
   *
   * @var string
   */
  public $minAge;
  /**
   * Minimum number of rows that should be present before Cloud DLP profiles a
   * table
   *
   * @var int
   */
  public $minRowCount;

  /**
   * Minimum age a table must have before Cloud DLP can profile it. Value must
   * be 1 hour or greater.
   *
   * @param string $minAge
   */
  public function setMinAge($minAge)
  {
    $this->minAge = $minAge;
  }
  /**
   * @return string
   */
  public function getMinAge()
  {
    return $this->minAge;
  }
  /**
   * Minimum number of rows that should be present before Cloud DLP profiles a
   * table
   *
   * @param int $minRowCount
   */
  public function setMinRowCount($minRowCount)
  {
    $this->minRowCount = $minRowCount;
  }
  /**
   * @return int
   */
  public function getMinRowCount()
  {
    return $this->minRowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2OrConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2OrConditions');
