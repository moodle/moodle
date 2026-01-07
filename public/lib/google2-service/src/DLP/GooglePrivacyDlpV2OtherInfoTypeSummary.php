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

class GooglePrivacyDlpV2OtherInfoTypeSummary extends \Google\Model
{
  /**
   * Approximate percentage of non-null rows that contained data detected by
   * this infotype.
   *
   * @var int
   */
  public $estimatedPrevalence;
  /**
   * Whether this infoType was excluded from sensitivity and risk analysis due
   * to factors such as low prevalence (subject to change).
   *
   * @var bool
   */
  public $excludedFromAnalysis;
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';

  /**
   * Approximate percentage of non-null rows that contained data detected by
   * this infotype.
   *
   * @param int $estimatedPrevalence
   */
  public function setEstimatedPrevalence($estimatedPrevalence)
  {
    $this->estimatedPrevalence = $estimatedPrevalence;
  }
  /**
   * @return int
   */
  public function getEstimatedPrevalence()
  {
    return $this->estimatedPrevalence;
  }
  /**
   * Whether this infoType was excluded from sensitivity and risk analysis due
   * to factors such as low prevalence (subject to change).
   *
   * @param bool $excludedFromAnalysis
   */
  public function setExcludedFromAnalysis($excludedFromAnalysis)
  {
    $this->excludedFromAnalysis = $excludedFromAnalysis;
  }
  /**
   * @return bool
   */
  public function getExcludedFromAnalysis()
  {
    return $this->excludedFromAnalysis;
  }
  /**
   * The other infoType.
   *
   * @param GooglePrivacyDlpV2InfoType $infoType
   */
  public function setInfoType(GooglePrivacyDlpV2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2OtherInfoTypeSummary::class, 'Google_Service_DLP_GooglePrivacyDlpV2OtherInfoTypeSummary');
