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

namespace Google\Service\AdExchangeBuyerII;

class AdTechnologyProviders extends \Google\Collection
{
  protected $collection_key = 'detectedProviderIds';
  /**
   * The detected ad technology provider IDs for this creative. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/providers.csv for
   * mapping of provider ID to provided name, a privacy policy URL, and a list
   * of domains which can be attributed to the provider. If the creative
   * contains provider IDs that are outside of those listed in the
   * `BidRequest.adslot.consented_providers_settings.consented_providers` field
   * on the (Google bid protocol)[https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto] and the
   * `BidRequest.user.ext.consented_providers_settings.consented_providers`
   * field on the (OpenRTB protocol)[https://developers.google.com/authorized-
   * buyers/rtb/downloads/openrtb-adx-proto], and a bid is submitted with that
   * creative for an impression that will serve to an EEA user, the bid will be
   * filtered before the auction.
   *
   * @var string[]
   */
  public $detectedProviderIds;
  /**
   * Whether the creative contains an unidentified ad technology provider. If
   * true for a given creative, any bid submitted with that creative for an
   * impression that will serve to an EEA user will be filtered before the
   * auction.
   *
   * @var bool
   */
  public $hasUnidentifiedProvider;

  /**
   * The detected ad technology provider IDs for this creative. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/providers.csv for
   * mapping of provider ID to provided name, a privacy policy URL, and a list
   * of domains which can be attributed to the provider. If the creative
   * contains provider IDs that are outside of those listed in the
   * `BidRequest.adslot.consented_providers_settings.consented_providers` field
   * on the (Google bid protocol)[https://developers.google.com/authorized-
   * buyers/rtb/downloads/realtime-bidding-proto] and the
   * `BidRequest.user.ext.consented_providers_settings.consented_providers`
   * field on the (OpenRTB protocol)[https://developers.google.com/authorized-
   * buyers/rtb/downloads/openrtb-adx-proto], and a bid is submitted with that
   * creative for an impression that will serve to an EEA user, the bid will be
   * filtered before the auction.
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
   * Whether the creative contains an unidentified ad technology provider. If
   * true for a given creative, any bid submitted with that creative for an
   * impression that will serve to an EEA user will be filtered before the
   * auction.
   *
   * @param bool $hasUnidentifiedProvider
   */
  public function setHasUnidentifiedProvider($hasUnidentifiedProvider)
  {
    $this->hasUnidentifiedProvider = $hasUnidentifiedProvider;
  }
  /**
   * @return bool
   */
  public function getHasUnidentifiedProvider()
  {
    return $this->hasUnidentifiedProvider;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdTechnologyProviders::class, 'Google_Service_AdExchangeBuyerII_AdTechnologyProviders');
