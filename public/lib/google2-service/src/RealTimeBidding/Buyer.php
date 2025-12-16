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

class Buyer extends \Google\Collection
{
  protected $collection_key = 'billingIds';
  /**
   * Output only. The number of creatives that this buyer submitted through the
   * API or bid with in the last 30 days. This is counted against the maximum
   * number of active creatives.
   *
   * @var string
   */
  public $activeCreativeCount;
  /**
   * Output only. The name of the bidder resource that is responsible for
   * receiving bidding traffic for this account. The bidder name must follow the
   * pattern `bidders/{bidderAccountId}`, where `{bidderAccountId}` is the
   * account ID of the bidder receiving traffic for this buyer.
   *
   * @var string
   */
  public $bidder;
  /**
   * Output only. A list of billing IDs associated with this account. These IDs
   * appear on: 1. A bid request, to signal which buyers are eligible to bid on
   * a given opportunity, and which pretargeting configurations were matched for
   * each eligible buyer. 2. The bid response, to attribute a winning impression
   * to a specific account for billing, reporting, policy and publisher block
   * enforcement.
   *
   * @var string[]
   */
  public $billingIds;
  /**
   * Output only. The diplay name associated with this buyer account, as visible
   * to sellers.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The maximum number of active creatives that this buyer can
   * have.
   *
   * @var string
   */
  public $maximumActiveCreativeCount;
  /**
   * Output only. Name of the buyer resource that must follow the pattern
   * `buyers/{buyerAccountId}`, where `{buyerAccountId}` is the account ID of
   * the buyer account whose information is to be received. One can get their
   * account ID on the Authorized Buyers or Open Bidding UI, or by contacting
   * their Google account manager.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The number of creatives that this buyer submitted through the
   * API or bid with in the last 30 days. This is counted against the maximum
   * number of active creatives.
   *
   * @param string $activeCreativeCount
   */
  public function setActiveCreativeCount($activeCreativeCount)
  {
    $this->activeCreativeCount = $activeCreativeCount;
  }
  /**
   * @return string
   */
  public function getActiveCreativeCount()
  {
    return $this->activeCreativeCount;
  }
  /**
   * Output only. The name of the bidder resource that is responsible for
   * receiving bidding traffic for this account. The bidder name must follow the
   * pattern `bidders/{bidderAccountId}`, where `{bidderAccountId}` is the
   * account ID of the bidder receiving traffic for this buyer.
   *
   * @param string $bidder
   */
  public function setBidder($bidder)
  {
    $this->bidder = $bidder;
  }
  /**
   * @return string
   */
  public function getBidder()
  {
    return $this->bidder;
  }
  /**
   * Output only. A list of billing IDs associated with this account. These IDs
   * appear on: 1. A bid request, to signal which buyers are eligible to bid on
   * a given opportunity, and which pretargeting configurations were matched for
   * each eligible buyer. 2. The bid response, to attribute a winning impression
   * to a specific account for billing, reporting, policy and publisher block
   * enforcement.
   *
   * @param string[] $billingIds
   */
  public function setBillingIds($billingIds)
  {
    $this->billingIds = $billingIds;
  }
  /**
   * @return string[]
   */
  public function getBillingIds()
  {
    return $this->billingIds;
  }
  /**
   * Output only. The diplay name associated with this buyer account, as visible
   * to sellers.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The maximum number of active creatives that this buyer can
   * have.
   *
   * @param string $maximumActiveCreativeCount
   */
  public function setMaximumActiveCreativeCount($maximumActiveCreativeCount)
  {
    $this->maximumActiveCreativeCount = $maximumActiveCreativeCount;
  }
  /**
   * @return string
   */
  public function getMaximumActiveCreativeCount()
  {
    return $this->maximumActiveCreativeCount;
  }
  /**
   * Output only. Name of the buyer resource that must follow the pattern
   * `buyers/{buyerAccountId}`, where `{buyerAccountId}` is the account ID of
   * the buyer account whose information is to be received. One can get their
   * account ID on the Authorized Buyers or Open Bidding UI, or by contacting
   * their Google account manager.
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
class_alias(Buyer::class, 'Google_Service_RealTimeBidding_Buyer');
