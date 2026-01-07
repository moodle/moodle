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

namespace Google\Service\DisplayVideo;

class AuditAdvertiserResponse extends \Google\Model
{
  /**
   * The number of individual targeting options from the following targeting
   * types that are assigned to a line item under this advertiser. These
   * individual targeting options count towards the limit of 4500000 ad group
   * targeting options per advertiser. Qualifying Targeting types: * Channels,
   * URLs, apps, and collections * Demographic * Google Audiences, including
   * Affinity, Custom Affinity, and In-market audiences * Inventory source *
   * Keyword * Mobile app category * User lists * Video targeting * Viewability
   *
   * @var string
   */
  public $adGroupCriteriaCount;
  /**
   * The number of individual targeting options from the following targeting
   * types that are assigned to a line item under this advertiser. These
   * individual targeting options count towards the limit of 900000 campaign
   * targeting options per advertiser. Qualifying Targeting types: * Position *
   * Browser * Connection speed * Day and time * Device and operating system *
   * Digital content label * Sensitive categories * Environment * Geography,
   * including business chains and proximity * ISP * Language * Third-party
   * verification
   *
   * @var string
   */
  public $campaignCriteriaCount;
  /**
   * The number of channels created under this advertiser. These channels count
   * towards the limit of 1000 channels per advertiser.
   *
   * @var string
   */
  public $channelsCount;
  /**
   * The number of negative keyword lists created under this advertiser. These
   * negative keyword lists count towards the limit of 20 negative keyword lists
   * per advertiser.
   *
   * @var string
   */
  public $negativeKeywordListsCount;
  /**
   * The number of negatively targeted channels created under this advertiser.
   * These negatively targeted channels count towards the limit of 5 negatively
   * targeted channels per advertiser.
   *
   * @var string
   */
  public $negativelyTargetedChannelsCount;
  /**
   * The number of ACTIVE and PAUSED campaigns under this advertiser. These
   * campaigns count towards the limit of 9999 campaigns per advertiser.
   *
   * @var string
   */
  public $usedCampaignsCount;
  /**
   * The number of ACTIVE, PAUSED and DRAFT insertion orders under this
   * advertiser. These insertion orders count towards the limit of 9999
   * insertion orders per advertiser.
   *
   * @var string
   */
  public $usedInsertionOrdersCount;
  /**
   * The number of ACTIVE, PAUSED, and DRAFT line items under this advertiser.
   * These line items count towards the limit of 9999 line items per advertiser.
   *
   * @var string
   */
  public $usedLineItemsCount;

  /**
   * The number of individual targeting options from the following targeting
   * types that are assigned to a line item under this advertiser. These
   * individual targeting options count towards the limit of 4500000 ad group
   * targeting options per advertiser. Qualifying Targeting types: * Channels,
   * URLs, apps, and collections * Demographic * Google Audiences, including
   * Affinity, Custom Affinity, and In-market audiences * Inventory source *
   * Keyword * Mobile app category * User lists * Video targeting * Viewability
   *
   * @param string $adGroupCriteriaCount
   */
  public function setAdGroupCriteriaCount($adGroupCriteriaCount)
  {
    $this->adGroupCriteriaCount = $adGroupCriteriaCount;
  }
  /**
   * @return string
   */
  public function getAdGroupCriteriaCount()
  {
    return $this->adGroupCriteriaCount;
  }
  /**
   * The number of individual targeting options from the following targeting
   * types that are assigned to a line item under this advertiser. These
   * individual targeting options count towards the limit of 900000 campaign
   * targeting options per advertiser. Qualifying Targeting types: * Position *
   * Browser * Connection speed * Day and time * Device and operating system *
   * Digital content label * Sensitive categories * Environment * Geography,
   * including business chains and proximity * ISP * Language * Third-party
   * verification
   *
   * @param string $campaignCriteriaCount
   */
  public function setCampaignCriteriaCount($campaignCriteriaCount)
  {
    $this->campaignCriteriaCount = $campaignCriteriaCount;
  }
  /**
   * @return string
   */
  public function getCampaignCriteriaCount()
  {
    return $this->campaignCriteriaCount;
  }
  /**
   * The number of channels created under this advertiser. These channels count
   * towards the limit of 1000 channels per advertiser.
   *
   * @param string $channelsCount
   */
  public function setChannelsCount($channelsCount)
  {
    $this->channelsCount = $channelsCount;
  }
  /**
   * @return string
   */
  public function getChannelsCount()
  {
    return $this->channelsCount;
  }
  /**
   * The number of negative keyword lists created under this advertiser. These
   * negative keyword lists count towards the limit of 20 negative keyword lists
   * per advertiser.
   *
   * @param string $negativeKeywordListsCount
   */
  public function setNegativeKeywordListsCount($negativeKeywordListsCount)
  {
    $this->negativeKeywordListsCount = $negativeKeywordListsCount;
  }
  /**
   * @return string
   */
  public function getNegativeKeywordListsCount()
  {
    return $this->negativeKeywordListsCount;
  }
  /**
   * The number of negatively targeted channels created under this advertiser.
   * These negatively targeted channels count towards the limit of 5 negatively
   * targeted channels per advertiser.
   *
   * @param string $negativelyTargetedChannelsCount
   */
  public function setNegativelyTargetedChannelsCount($negativelyTargetedChannelsCount)
  {
    $this->negativelyTargetedChannelsCount = $negativelyTargetedChannelsCount;
  }
  /**
   * @return string
   */
  public function getNegativelyTargetedChannelsCount()
  {
    return $this->negativelyTargetedChannelsCount;
  }
  /**
   * The number of ACTIVE and PAUSED campaigns under this advertiser. These
   * campaigns count towards the limit of 9999 campaigns per advertiser.
   *
   * @param string $usedCampaignsCount
   */
  public function setUsedCampaignsCount($usedCampaignsCount)
  {
    $this->usedCampaignsCount = $usedCampaignsCount;
  }
  /**
   * @return string
   */
  public function getUsedCampaignsCount()
  {
    return $this->usedCampaignsCount;
  }
  /**
   * The number of ACTIVE, PAUSED and DRAFT insertion orders under this
   * advertiser. These insertion orders count towards the limit of 9999
   * insertion orders per advertiser.
   *
   * @param string $usedInsertionOrdersCount
   */
  public function setUsedInsertionOrdersCount($usedInsertionOrdersCount)
  {
    $this->usedInsertionOrdersCount = $usedInsertionOrdersCount;
  }
  /**
   * @return string
   */
  public function getUsedInsertionOrdersCount()
  {
    return $this->usedInsertionOrdersCount;
  }
  /**
   * The number of ACTIVE, PAUSED, and DRAFT line items under this advertiser.
   * These line items count towards the limit of 9999 line items per advertiser.
   *
   * @param string $usedLineItemsCount
   */
  public function setUsedLineItemsCount($usedLineItemsCount)
  {
    $this->usedLineItemsCount = $usedLineItemsCount;
  }
  /**
   * @return string
   */
  public function getUsedLineItemsCount()
  {
    return $this->usedLineItemsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuditAdvertiserResponse::class, 'Google_Service_DisplayVideo_AuditAdvertiserResponse');
