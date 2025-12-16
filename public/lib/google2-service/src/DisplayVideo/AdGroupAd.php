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

class AdGroupAd extends \Google\Collection
{
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
  protected $collection_key = 'adUrls';
  /**
   * The unique ID of the ad. Assigned by the system.
   *
   * @var string
   */
  public $adGroupAdId;
  /**
   * The unique ID of the ad group that the ad belongs to. *Caution*: Parent ad
   * groups for Demand Gen ads are not currently retrieveable using
   * `advertisers.adGroups.list` or `advertisers.adGroups.get`. Demand Gen ads
   * can be identified by the absence of the `ad_details` union field.
   *
   * @var string
   */
  public $adGroupId;
  protected $adPolicyType = AdPolicy::class;
  protected $adPolicyDataType = '';
  protected $adUrlsType = AdUrl::class;
  protected $adUrlsDataType = 'array';
  /**
   * The unique ID of the advertiser the ad belongs to.
   *
   * @var string
   */
  public $advertiserId;
  protected $audioAdType = AudioAd::class;
  protected $audioAdDataType = '';
  protected $bumperAdType = BumperAd::class;
  protected $bumperAdDataType = '';
  /**
   * The display name of the ad. Must be UTF-8 encoded with a maximum size of
   * 255 bytes.
   *
   * @var string
   */
  public $displayName;
  protected $displayVideoSourceAdType = DisplayVideoSourceAd::class;
  protected $displayVideoSourceAdDataType = '';
  /**
   * The entity status of the ad.
   *
   * @var string
   */
  public $entityStatus;
  protected $inStreamAdType = InStreamAd::class;
  protected $inStreamAdDataType = '';
  protected $mastheadAdType = MastheadAd::class;
  protected $mastheadAdDataType = '';
  /**
   * The resource name of the ad.
   *
   * @var string
   */
  public $name;
  protected $nonSkippableAdType = NonSkippableAd::class;
  protected $nonSkippableAdDataType = '';
  protected $videoDiscoverAdType = VideoDiscoveryAd::class;
  protected $videoDiscoverAdDataType = '';
  protected $videoPerformanceAdType = VideoPerformanceAd::class;
  protected $videoPerformanceAdDataType = '';

  /**
   * The unique ID of the ad. Assigned by the system.
   *
   * @param string $adGroupAdId
   */
  public function setAdGroupAdId($adGroupAdId)
  {
    $this->adGroupAdId = $adGroupAdId;
  }
  /**
   * @return string
   */
  public function getAdGroupAdId()
  {
    return $this->adGroupAdId;
  }
  /**
   * The unique ID of the ad group that the ad belongs to. *Caution*: Parent ad
   * groups for Demand Gen ads are not currently retrieveable using
   * `advertisers.adGroups.list` or `advertisers.adGroups.get`. Demand Gen ads
   * can be identified by the absence of the `ad_details` union field.
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
   * The policy approval status of the ad.
   *
   * @param AdPolicy $adPolicy
   */
  public function setAdPolicy(AdPolicy $adPolicy)
  {
    $this->adPolicy = $adPolicy;
  }
  /**
   * @return AdPolicy
   */
  public function getAdPolicy()
  {
    return $this->adPolicy;
  }
  /**
   * List of URLs used by the ad.
   *
   * @param AdUrl[] $adUrls
   */
  public function setAdUrls($adUrls)
  {
    $this->adUrls = $adUrls;
  }
  /**
   * @return AdUrl[]
   */
  public function getAdUrls()
  {
    return $this->adUrls;
  }
  /**
   * The unique ID of the advertiser the ad belongs to.
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
   * Details of an [audio ad](//support.google.com/displayvideo/answer/6274216)
   * used for reach marketing objectives.
   *
   * @param AudioAd $audioAd
   */
  public function setAudioAd(AudioAd $audioAd)
  {
    $this->audioAd = $audioAd;
  }
  /**
   * @return AudioAd
   */
  public function getAudioAd()
  {
    return $this->audioAd;
  }
  /**
   * Details of a [non-skippable short video
   * ad](//support.google.com/displayvideo/answer/6274216), equal to or less
   * than 6 seconds, used for reach.
   *
   * @param BumperAd $bumperAd
   */
  public function setBumperAd(BumperAd $bumperAd)
  {
    $this->bumperAd = $bumperAd;
  }
  /**
   * @return BumperAd
   */
  public function getBumperAd()
  {
    return $this->bumperAd;
  }
  /**
   * The display name of the ad. Must be UTF-8 encoded with a maximum size of
   * 255 bytes.
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
   * Details of an ad sourced from a Display & Video 360 creative.
   *
   * @param DisplayVideoSourceAd $displayVideoSourceAd
   */
  public function setDisplayVideoSourceAd(DisplayVideoSourceAd $displayVideoSourceAd)
  {
    $this->displayVideoSourceAd = $displayVideoSourceAd;
  }
  /**
   * @return DisplayVideoSourceAd
   */
  public function getDisplayVideoSourceAd()
  {
    return $this->displayVideoSourceAd;
  }
  /**
   * The entity status of the ad.
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
   * Details of an [in-stream ad skippable after 5
   * seconds](//support.google.com/displayvideo/answer/6274216), used for brand
   * awareness or reach marketing objectives.
   *
   * @param InStreamAd $inStreamAd
   */
  public function setInStreamAd(InStreamAd $inStreamAd)
  {
    $this->inStreamAd = $inStreamAd;
  }
  /**
   * @return InStreamAd
   */
  public function getInStreamAd()
  {
    return $this->inStreamAd;
  }
  /**
   * Details of an [ad served on the YouTube Home
   * feed](//support.google.com/google-ads/answer/9709826).
   *
   * @param MastheadAd $mastheadAd
   */
  public function setMastheadAd(MastheadAd $mastheadAd)
  {
    $this->mastheadAd = $mastheadAd;
  }
  /**
   * @return MastheadAd
   */
  public function getMastheadAd()
  {
    return $this->mastheadAd;
  }
  /**
   * The resource name of the ad.
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
   * Details of a [non-skippable short in-stream video
   * ad](//support.google.com/displayvideo/answer/6274216), between 6 and 15
   * seconds, used for reach marketing objectives.
   *
   * @param NonSkippableAd $nonSkippableAd
   */
  public function setNonSkippableAd(NonSkippableAd $nonSkippableAd)
  {
    $this->nonSkippableAd = $nonSkippableAd;
  }
  /**
   * @return NonSkippableAd
   */
  public function getNonSkippableAd()
  {
    return $this->nonSkippableAd;
  }
  /**
   * Details of an [ad promoting a
   * video](//support.google.com/displayvideo/answer/6274216) that shows in
   * places of discovery.
   *
   * @param VideoDiscoveryAd $videoDiscoverAd
   */
  public function setVideoDiscoverAd(VideoDiscoveryAd $videoDiscoverAd)
  {
    $this->videoDiscoverAd = $videoDiscoverAd;
  }
  /**
   * @return VideoDiscoveryAd
   */
  public function getVideoDiscoverAd()
  {
    return $this->videoDiscoverAd;
  }
  /**
   * Details of an [ad used in a video action
   * campaign](//support.google.com/google-ads/answer/10147229) to drive actions
   * to the business, service or product.
   *
   * @param VideoPerformanceAd $videoPerformanceAd
   */
  public function setVideoPerformanceAd(VideoPerformanceAd $videoPerformanceAd)
  {
    $this->videoPerformanceAd = $videoPerformanceAd;
  }
  /**
   * @return VideoPerformanceAd
   */
  public function getVideoPerformanceAd()
  {
    return $this->videoPerformanceAd;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdGroupAd::class, 'Google_Service_DisplayVideo_AdGroupAd');
