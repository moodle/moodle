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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings extends \Google\Model
{
  /**
   * Whether ads will be served on specified placements in the Google Display
   * Network. Placements are specified using the Placement criterion.
   *
   * @var bool
   */
  public $targetContentNetwork;
  /**
   * Whether ads will be served with google.com search results.
   *
   * @var bool
   */
  public $targetGoogleSearch;
  /**
   * Whether ads will be served on the Google Partner Network. This is available
   * only to some select Google partner accounts.
   *
   * @var bool
   */
  public $targetPartnerSearchNetwork;
  /**
   * Whether ads will be served on partner sites in the Google Search Network
   * (requires `target_google_search` to also be `true`).
   *
   * @var bool
   */
  public $targetSearchNetwork;

  /**
   * Whether ads will be served on specified placements in the Google Display
   * Network. Placements are specified using the Placement criterion.
   *
   * @param bool $targetContentNetwork
   */
  public function setTargetContentNetwork($targetContentNetwork)
  {
    $this->targetContentNetwork = $targetContentNetwork;
  }
  /**
   * @return bool
   */
  public function getTargetContentNetwork()
  {
    return $this->targetContentNetwork;
  }
  /**
   * Whether ads will be served with google.com search results.
   *
   * @param bool $targetGoogleSearch
   */
  public function setTargetGoogleSearch($targetGoogleSearch)
  {
    $this->targetGoogleSearch = $targetGoogleSearch;
  }
  /**
   * @return bool
   */
  public function getTargetGoogleSearch()
  {
    return $this->targetGoogleSearch;
  }
  /**
   * Whether ads will be served on the Google Partner Network. This is available
   * only to some select Google partner accounts.
   *
   * @param bool $targetPartnerSearchNetwork
   */
  public function setTargetPartnerSearchNetwork($targetPartnerSearchNetwork)
  {
    $this->targetPartnerSearchNetwork = $targetPartnerSearchNetwork;
  }
  /**
   * @return bool
   */
  public function getTargetPartnerSearchNetwork()
  {
    return $this->targetPartnerSearchNetwork;
  }
  /**
   * Whether ads will be served on partner sites in the Google Search Network
   * (requires `target_google_search` to also be `true`).
   *
   * @param bool $targetSearchNetwork
   */
  public function setTargetSearchNetwork($targetSearchNetwork)
  {
    $this->targetSearchNetwork = $targetSearchNetwork;
  }
  /**
   * @return bool
   */
  public function getTargetSearchNetwork()
  {
    return $this->targetSearchNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings');
