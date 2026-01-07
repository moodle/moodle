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

namespace Google\Service\AuthorizedBuyersMarketplace;

class AuctionPackage extends \Google\Collection
{
  protected $collection_key = 'subscribedMediaPlanners';
  /**
   * Output only. Time the auction package was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The buyer that created this auction package. Format:
   * `buyers/{buyerAccountId}`
   *
   * @var string
   */
  public $creator;
  /**
   * Output only. If set, this field contains the DSP specific seat id set by
   * the media planner account that is considered the owner of this deal. The
   * seat ID is in the calling DSP's namespace.
   *
   * @var string
   */
  public $dealOwnerSeatId;
  /**
   * Output only. A description of the auction package.
   *
   * @var string
   */
  public $description;
  /**
   * The display_name assigned to the auction package.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. If set, this field identifies a seat that the media planner
   * selected as the owner of this auction package. This is a seat ID in the
   * DSP's namespace that was provided to the media planner.
   *
   * @var string[]
   */
  public $eligibleSeatIds;
  protected $floorPriceCpmType = Money::class;
  protected $floorPriceCpmDataType = '';
  /**
   * Immutable. The unique identifier for the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}` The
   * auction_package_id part of name is sent in the BidRequest to all RTB
   * bidders and is returned as deal_id by the bidder in the BidResponse.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The list of buyers that are subscribed to the AuctionPackage.
   * This field is only populated when calling as a bidder. Format:
   * `buyers/{buyerAccountId}`
   *
   * @var string[]
   */
  public $subscribedBuyers;
  /**
   * Output only. When calling as a buyer, the list of clients of the current
   * buyer that are subscribed to the AuctionPackage. When calling as a bidder,
   * the list of clients that are subscribed to the AuctionPackage owned by the
   * bidder or its buyers. Format:
   * `buyers/{buyerAccountId}/clients/{clientAccountId}`
   *
   * @var string[]
   */
  public $subscribedClients;
  protected $subscribedMediaPlannersType = MediaPlanner::class;
  protected $subscribedMediaPlannersDataType = 'array';
  /**
   * Output only. Time the auction package was last updated. This value is only
   * increased when this auction package is updated but never when a buyer
   * subscribed.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time the auction package was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The buyer that created this auction package. Format:
   * `buyers/{buyerAccountId}`
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Output only. If set, this field contains the DSP specific seat id set by
   * the media planner account that is considered the owner of this deal. The
   * seat ID is in the calling DSP's namespace.
   *
   * @param string $dealOwnerSeatId
   */
  public function setDealOwnerSeatId($dealOwnerSeatId)
  {
    $this->dealOwnerSeatId = $dealOwnerSeatId;
  }
  /**
   * @return string
   */
  public function getDealOwnerSeatId()
  {
    return $this->dealOwnerSeatId;
  }
  /**
   * Output only. A description of the auction package.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The display_name assigned to the auction package.
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
   * Output only. If set, this field identifies a seat that the media planner
   * selected as the owner of this auction package. This is a seat ID in the
   * DSP's namespace that was provided to the media planner.
   *
   * @param string[] $eligibleSeatIds
   */
  public function setEligibleSeatIds($eligibleSeatIds)
  {
    $this->eligibleSeatIds = $eligibleSeatIds;
  }
  /**
   * @return string[]
   */
  public function getEligibleSeatIds()
  {
    return $this->eligibleSeatIds;
  }
  /**
   * Output only. The minimum price a buyer has to bid to compete in this
   * auction package. If this is field is not populated, there is no floor
   * price.
   *
   * @param Money $floorPriceCpm
   */
  public function setFloorPriceCpm(Money $floorPriceCpm)
  {
    $this->floorPriceCpm = $floorPriceCpm;
  }
  /**
   * @return Money
   */
  public function getFloorPriceCpm()
  {
    return $this->floorPriceCpm;
  }
  /**
   * Immutable. The unique identifier for the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}` The
   * auction_package_id part of name is sent in the BidRequest to all RTB
   * bidders and is returned as deal_id by the bidder in the BidResponse.
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
  /**
   * Output only. The list of buyers that are subscribed to the AuctionPackage.
   * This field is only populated when calling as a bidder. Format:
   * `buyers/{buyerAccountId}`
   *
   * @param string[] $subscribedBuyers
   */
  public function setSubscribedBuyers($subscribedBuyers)
  {
    $this->subscribedBuyers = $subscribedBuyers;
  }
  /**
   * @return string[]
   */
  public function getSubscribedBuyers()
  {
    return $this->subscribedBuyers;
  }
  /**
   * Output only. When calling as a buyer, the list of clients of the current
   * buyer that are subscribed to the AuctionPackage. When calling as a bidder,
   * the list of clients that are subscribed to the AuctionPackage owned by the
   * bidder or its buyers. Format:
   * `buyers/{buyerAccountId}/clients/{clientAccountId}`
   *
   * @param string[] $subscribedClients
   */
  public function setSubscribedClients($subscribedClients)
  {
    $this->subscribedClients = $subscribedClients;
  }
  /**
   * @return string[]
   */
  public function getSubscribedClients()
  {
    return $this->subscribedClients;
  }
  /**
   * Output only. The list of media planners that are subscribed to the
   * AuctionPackage. This field is only populated when calling as a bidder.
   *
   * @param MediaPlanner[] $subscribedMediaPlanners
   */
  public function setSubscribedMediaPlanners($subscribedMediaPlanners)
  {
    $this->subscribedMediaPlanners = $subscribedMediaPlanners;
  }
  /**
   * @return MediaPlanner[]
   */
  public function getSubscribedMediaPlanners()
  {
    return $this->subscribedMediaPlanners;
  }
  /**
   * Output only. Time the auction package was last updated. This value is only
   * increased when this auction package is updated but never when a buyer
   * subscribed.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuctionPackage::class, 'Google_Service_AuthorizedBuyersMarketplace_AuctionPackage');
