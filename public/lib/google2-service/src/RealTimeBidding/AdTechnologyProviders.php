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

namespace Google\Service\RealTimeBidding;

class AdTechnologyProviders extends \Google\Collection
{
  protected $collection_key = 'unidentifiedProviderDomains';
  /**
   * The detected IAB Global Vendor List (GVL) IDs for this creative. See the
   * IAB Global Vendor List at https://vendor-list.consensu.org/v2/vendor-
   * list.json for details about the vendors.
   *
   * @var string[]
   */
  public $detectedGvlIds;
  /**
   * The detected [Google Ad Tech Providers
   * (ATP)](https://support.google.com/admanager/answer/9012903) for this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/providers.csv for mapping of provider ID to provided name, a
   * privacy policy URL, and a list of domains which can be attributed to the
   * provider.
   *
   * @var string[]
   */
  public $detectedProviderIds;
  /**
   * Domains of detected unidentified ad technology providers (if any). You must
   * ensure that the creatives used in bids placed for inventory that will serve
   * to EEA or UK users does not contain unidentified ad technology providers.
   * Google reserves the right to filter non-compliant bids.
   *
   * @var string[]
   */
  public $unidentifiedProviderDomains;

  /**
   * The detected IAB Global Vendor List (GVL) IDs for this creative. See the
   * IAB Global Vendor List at https://vendor-list.consensu.org/v2/vendor-
   * list.json for details about the vendors.
   *
   * @param string[] $detectedGvlIds
   */
  public function setDetectedGvlIds($detectedGvlIds)
  {
    $this->detectedGvlIds = $detectedGvlIds;
  }
  /**
   * @return string[]
   */
  public function getDetectedGvlIds()
  {
    return $this->detectedGvlIds;
  }
  /**
   * The detected [Google Ad Tech Providers
   * (ATP)](https://support.google.com/admanager/answer/9012903) for this
   * creative. See https://storage.googleapis.com/adx-rtb-
   * dictionaries/providers.csv for mapping of provider ID to provided name, a
   * privacy policy URL, and a list of domains which can be attributed to the
   * provider.
   *
   * @param string[] $detectedProviderIds
   */
  public function setDetectedProviderIds($detectedProviderIds)
  {
    $this->detectedProviderIds = $detectedProviderIds;
  }
  /**
   * @return string[]
   */
  public function getDetectedProviderIds()
  {
    return $this->detectedProviderIds;
  }
  /**
   * Domains of detected unidentified ad technology providers (if any). You must
   * ensure that the creatives used in bids placed for inventory that will serve
   * to EEA or UK users does not contain unidentified ad technology providers.
   * Google reserves the right to filter non-compliant bids.
   *
   * @param string[] $unidentifiedProviderDomains
   */
  public function setUnidentifiedProviderDomains($unidentifiedProviderDomains)
  {
    $this->unidentifiedProviderDomains = $unidentifiedProviderDomains;
  }
  /**
   * @return string[]
   */
  public function getUnidentifiedProviderDomains()
  {
    return $this->unidentifiedProviderDomains;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdTechnologyProviders::class, 'Google_Service_RealTimeBidding_AdTechnologyProviders');
