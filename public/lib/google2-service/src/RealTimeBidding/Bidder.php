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

class Bidder extends \Google\Model
{
  /**
   * Output only. An option to bypass pretargeting for private auctions and
   * preferred deals. When true, bid requests from these nonguaranteed deals
   * will always be sent. When false, bid requests will be subject to regular
   * pretargeting configurations. Programmatic Guaranteed deals will always be
   * sent to the bidder, regardless of the value for this option. Auction
   * packages are not impacted by this value and are subject to the regular
   * pretargeting configurations.
   *
   * @var bool
   */
  public $bypassNonguaranteedDealsPretargeting;
  /**
   * Output only. The buyer's network ID used for cookie matching. This ID
   * corresponds to the `google_nid` parameter in the URL used in cookie match
   * requests. Refer to https://developers.google.com/authorized-
   * buyers/rtb/cookie-guide for further information.
   *
   * @var string
   */
  public $cookieMatchingNetworkId;
  /**
   * Output only. The base URL used in cookie match requests. Refer to
   * https://developers.google.com/authorized-buyers/rtb/cookie-guide for
   * further information.
   *
   * @var string
   */
  public $cookieMatchingUrl;
  /**
   * Output only. The billing ID for the deals pretargeting config. This billing
   * ID is sent on the bid request for guaranteed and nonguaranteed deals
   * matched in pretargeting.
   *
   * @var string
   */
  public $dealsBillingId;
  /**
   * Output only. Name of the bidder resource that must follow the pattern
   * `bidders/{bidderAccountId}`, where `{bidderAccountId}` is the account ID of
   * the bidder whose information is to be received. One can get their account
   * ID on the Authorized Buyers or Open Bidding UI, or by contacting their
   * Google account manager.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. An option to bypass pretargeting for private auctions and
   * preferred deals. When true, bid requests from these nonguaranteed deals
   * will always be sent. When false, bid requests will be subject to regular
   * pretargeting configurations. Programmatic Guaranteed deals will always be
   * sent to the bidder, regardless of the value for this option. Auction
   * packages are not impacted by this value and are subject to the regular
   * pretargeting configurations.
   *
   * @param bool $bypassNonguaranteedDealsPretargeting
   */
  public function setBypassNonguaranteedDealsPretargeting($bypassNonguaranteedDealsPretargeting)
  {
    $this->bypassNonguaranteedDealsPretargeting = $bypassNonguaranteedDealsPretargeting;
  }
  /**
   * @return bool
   */
  public function getBypassNonguaranteedDealsPretargeting()
  {
    return $this->bypassNonguaranteedDealsPretargeting;
  }
  /**
   * Output only. The buyer's network ID used for cookie matching. This ID
   * corresponds to the `google_nid` parameter in the URL used in cookie match
   * requests. Refer to https://developers.google.com/authorized-
   * buyers/rtb/cookie-guide for further information.
   *
   * @param string $cookieMatchingNetworkId
   */
  public function setCookieMatchingNetworkId($cookieMatchingNetworkId)
  {
    $this->cookieMatchingNetworkId = $cookieMatchingNetworkId;
  }
  /**
   * @return string
   */
  public function getCookieMatchingNetworkId()
  {
    return $this->cookieMatchingNetworkId;
  }
  /**
   * Output only. The base URL used in cookie match requests. Refer to
   * https://developers.google.com/authorized-buyers/rtb/cookie-guide for
   * further information.
   *
   * @param string $cookieMatchingUrl
   */
  public function setCookieMatchingUrl($cookieMatchingUrl)
  {
    $this->cookieMatchingUrl = $cookieMatchingUrl;
  }
  /**
   * @return string
   */
  public function getCookieMatchingUrl()
  {
    return $this->cookieMatchingUrl;
  }
  /**
   * Output only. The billing ID for the deals pretargeting config. This billing
   * ID is sent on the bid request for guaranteed and nonguaranteed deals
   * matched in pretargeting.
   *
   * @param string $dealsBillingId
   */
  public function setDealsBillingId($dealsBillingId)
  {
    $this->dealsBillingId = $dealsBillingId;
  }
  /**
   * @return string
   */
  public function getDealsBillingId()
  {
    return $this->dealsBillingId;
  }
  /**
   * Output only. Name of the bidder resource that must follow the pattern
   * `bidders/{bidderAccountId}`, where `{bidderAccountId}` is the account ID of
   * the bidder whose information is to be received. One can get their account
   * ID on the Authorized Buyers or Open Bidding UI, or by contacting their
   * Google account manager.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bidder::class, 'Google_Service_RealTimeBidding_Bidder');
