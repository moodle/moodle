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

class GooglePrivacyDlpV2ExcludeByHotword extends \Google\Model
{
  protected $hotwordRegexType = GooglePrivacyDlpV2Regex::class;
  protected $hotwordRegexDataType = '';
  protected $proximityType = GooglePrivacyDlpV2Proximity::class;
  protected $proximityDataType = '';

  /**
   * Regular expression pattern defining what qualifies as a hotword.
   *
   * @param GooglePrivacyDlpV2Regex $hotwordRegex
   */
  public function setHotwordRegex(GooglePrivacyDlpV2Regex $hotwordRegex)
  {
    $this->hotwordRegex = $hotwordRegex;
  }
  /**
   * @return GooglePrivacyDlpV2Regex
   */
  public function getHotwordRegex()
  {
    return $this->hotwordRegex;
  }
  /**
   * Range of characters within which the entire hotword must reside. The total
   * length of the window cannot exceed 1000 characters. The windowBefore
   * property in proximity should be set to 1 if the hotword needs to be
   * included in a column header.
   *
   * @param GooglePrivacyDlpV2Proximity $proximity
   */
  public function setProximity(GooglePrivacyDlpV2Proximity $proximity)
  {
    $this->proximity = $proximity;
  }
  /**
   * @return GooglePrivacyDlpV2Proximity
   */
  public function getProximity()
  {
    return $this->proximity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ExcludeByHotword::class, 'Google_Service_DLP_GooglePrivacyDlpV2ExcludeByHotword');
