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

class GooglePrivacyDlpV2HotwordRule extends \Google\Model
{
  protected $hotwordRegexType = GooglePrivacyDlpV2Regex::class;
  protected $hotwordRegexDataType = '';
  protected $likelihoodAdjustmentType = GooglePrivacyDlpV2LikelihoodAdjustment::class;
  protected $likelihoodAdjustmentDataType = '';
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
   * Likelihood adjustment to apply to all matching findings.
   *
   * @param GooglePrivacyDlpV2LikelihoodAdjustment $likelihoodAdjustment
   */
  public function setLikelihoodAdjustment(GooglePrivacyDlpV2LikelihoodAdjustment $likelihoodAdjustment)
  {
    $this->likelihoodAdjustment = $likelihoodAdjustment;
  }
  /**
   * @return GooglePrivacyDlpV2LikelihoodAdjustment
   */
  public function getLikelihoodAdjustment()
  {
    return $this->likelihoodAdjustment;
  }
  /**
   * Range of characters within which the entire hotword must reside. The total
   * length of the window cannot exceed 1000 characters. The finding itself will
   * be included in the window, so that hotwords can be used to match substrings
   * of the finding itself. Suppose you want Cloud DLP to promote the likelihood
   * of the phone number regex "\(\d{3}\) \d{3}-\d{4}" if the area code is known
   * to be the area code of a company's office. In this case, use the hotword
   * regex "\(xxx\)", where "xxx" is the area code in question. For tabular
   * data, if you want to modify the likelihood of an entire column of findngs,
   * see [Hotword example: Set the match likelihood of a table column]
   * (https://cloud.google.com/sensitive-data-protection/docs/creating-custom-
   * infotypes-likelihood#match-column-values).
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
class_alias(GooglePrivacyDlpV2HotwordRule::class, 'Google_Service_DLP_GooglePrivacyDlpV2HotwordRule');
