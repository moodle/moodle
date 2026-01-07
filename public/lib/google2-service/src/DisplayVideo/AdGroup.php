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

class AdGroup extends \Google\Model
{
  /**
   * Format value is not specified or is unknown in this version.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_UNSPECIFIED = 'AD_GROUP_FORMAT_UNSPECIFIED';
  /**
   * In-stream ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_IN_STREAM = 'AD_GROUP_FORMAT_IN_STREAM';
  /**
   * In-feed ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_VIDEO_DISCOVERY = 'AD_GROUP_FORMAT_VIDEO_DISCOVERY';
  /**
   * Bumper ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_BUMPER = 'AD_GROUP_FORMAT_BUMPER';
  /**
   * Non-skippable in-stream ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_NON_SKIPPABLE_IN_STREAM = 'AD_GROUP_FORMAT_NON_SKIPPABLE_IN_STREAM';
  /**
   * Non-skippable in-stream audio ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_AUDIO = 'AD_GROUP_FORMAT_AUDIO';
  /**
   * Responsive ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_RESPONSIVE = 'AD_GROUP_FORMAT_RESPONSIVE';
  /**
   * [Effective reach ad groups]
   * (https://support.google.com/displayvideo/answer/9173684), including in-
   * stream and bumper ads.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_REACH = 'AD_GROUP_FORMAT_REACH';
  /**
   * Masthead Ad that is surfaced on the top slot on the YouTube homepage.
   */
  public const AD_GROUP_FORMAT_AD_GROUP_FORMAT_MASTHEAD = 'AD_GROUP_FORMAT_MASTHEAD';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * The format of the ads in the ad group.
   *
   * @var string
   */
  public $adGroupFormat;
  /**
   * The unique ID of the ad group. Assigned by the system.
   *
   * @var string
   */
  public $adGroupId;
  /**
   * The unique ID of the advertiser the ad group belongs to.
   *
   * @var string
   */
  public $advertiserId;
  protected $bidStrategyType = BiddingStrategy::class;
  protected $bidStrategyDataType = '';
  /**
   * The display name of the ad group. Must be UTF-8 encoded with a maximum size
   * of 255 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Controls whether or not the ad group can spend its budget and bid on
   * inventory. If the ad group's parent line item is not active, the ad group
   * can't spend its budget even if its own status is `ENTITY_STATUS_ACTIVE`.
   *
   * @var string
   */
  public $entityStatus;
  /**
   * The unique ID of the line item that the ad group belongs to.
   *
   * @var string
   */
  public $lineItemId;
  /**
   * The resource name of the ad group.
   *
   * @var string
   */
  public $name;
  protected $productFeedDataType = ProductFeedData::class;
  protected $productFeedDataDataType = '';
  protected $targetingExpansionType = TargetingExpansionConfig::class;
  protected $targetingExpansionDataType = '';

  /**
   * The format of the ads in the ad group.
   *
   * Accepted values: AD_GROUP_FORMAT_UNSPECIFIED, AD_GROUP_FORMAT_IN_STREAM,
   * AD_GROUP_FORMAT_VIDEO_DISCOVERY, AD_GROUP_FORMAT_BUMPER,
   * AD_GROUP_FORMAT_NON_SKIPPABLE_IN_STREAM, AD_GROUP_FORMAT_AUDIO,
   * AD_GROUP_FORMAT_RESPONSIVE, AD_GROUP_FORMAT_REACH, AD_GROUP_FORMAT_MASTHEAD
   *
   * @param self::AD_GROUP_FORMAT_* $adGroupFormat
   */
  public function setAdGroupFormat($adGroupFormat)
  {
    $this->adGroupFormat = $adGroupFormat;
  }
  /**
   * @return self::AD_GROUP_FORMAT_*
   */
  public function getAdGroupFormat()
  {
    return $this->adGroupFormat;
  }
  /**
   * The unique ID of the ad group. Assigned by the system.
   *
   * @param string $adGroupId
   */
  public function setAdGroupId($adGroupId)
  {
    $this->adGroupId = $adGroupId;
  }
  /**
   * @return string
   */
  public function getAdGroupId()
  {
    return $this->adGroupId;
  }
  /**
   * The unique ID of the advertiser the ad group belongs to.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * The bidding strategy used by the ad group. Only the youtubeAndPartnersBid
   * field can be used in the bidding strategy.
   *
   * @param BiddingStrategy $bidStrategy
   */
  public function setBidStrategy(BiddingStrategy $bidStrategy)
  {
    $this->bidStrategy = $bidStrategy;
  }
  /**
   * @return BiddingStrategy
   */
  public function getBidStrategy()
  {
    return $this->bidStrategy;
  }
  /**
   * The display name of the ad group. Must be UTF-8 encoded with a maximum size
   * of 255 bytes.
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
   * Controls whether or not the ad group can spend its budget and bid on
   * inventory. If the ad group's parent line item is not active, the ad group
   * can't spend its budget even if its own status is `ENTITY_STATUS_ACTIVE`.
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * The unique ID of the line item that the ad group belongs to.
   *
   * @param string $lineItemId
   */
  public function setLineItemId($lineItemId)
  {
    $this->lineItemId = $lineItemId;
  }
  /**
   * @return string
   */
  public function getLineItemId()
  {
    return $this->lineItemId;
  }
  /**
   * The resource name of the ad group.
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
   * The settings of the product feed in this ad group.
   *
   * @param ProductFeedData $productFeedData
   */
  public function setProductFeedData(ProductFeedData $productFeedData)
  {
    $this->productFeedData = $productFeedData;
  }
  /**
   * @return ProductFeedData
   */
  public function getProductFeedData()
  {
    return $this->productFeedData;
  }
  /**
   * The [optimized
   * targeting](//support.google.com/displayvideo/answer/12060859) settings of
   * the ad group.
   *
   * @param TargetingExpansionConfig $targetingExpansion
   */
  public function setTargetingExpansion(TargetingExpansionConfig $targetingExpansion)
  {
    $this->targetingExpansion = $targetingExpansion;
  }
  /**
   * @return TargetingExpansionConfig
   */
  public function getTargetingExpansion()
  {
    return $this->targetingExpansion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdGroup::class, 'Google_Service_DisplayVideo_AdGroup');
