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

namespace Google\Service\Dfareporting;

class StudioCreative extends \Google\Collection
{
  /**
   * The format of the studio creative is unknown. This value is unused.
   */
  public const FORMAT_UNKNOWN = 'UNKNOWN';
  /**
   * Banner format.
   */
  public const FORMAT_BANNER = 'BANNER';
  /**
   * Expanding format.
   */
  public const FORMAT_EXPANDING = 'EXPANDING';
  /**
   * Intertitial format.
   */
  public const FORMAT_INTERSTITIAL = 'INTERSTITIAL';
  /**
   * VPAID linear video creative format.
   */
  public const FORMAT_VPAID_LINEAR_VIDEO = 'VPAID_LINEAR_VIDEO';
  /**
   * The status of the studio creative is unknown. This value is unused.
   */
  public const STATUS_UNKNOWN_STATUS = 'UNKNOWN_STATUS';
  /**
   * The creative is still being developed.
   */
  public const STATUS_IN_DEVELOPMENT = 'IN_DEVELOPMENT';
  /**
   * The creative has been published and is ready for QA.
   */
  public const STATUS_PUBLISHED = 'PUBLISHED';
  /**
   * The creative has failed QA and must be corrected.
   */
  public const STATUS_QA_REJECTED = 'QA_REJECTED';
  /**
   * The creative has passed QA and is ready to be trafficked.
   */
  public const STATUS_QA_APPROVED = 'QA_APPROVED';
  /**
   * The creative has been trafficked to an adserver.
   */
  public const STATUS_TRAFFICKED = 'TRAFFICKED';
  protected $collection_key = 'assetIds';
  /**
   * List of assets associated with this studio creative. It is a required field
   * on insertion.
   *
   * @var string[]
   */
  public $assetIds;
  /**
   * Backup image asset ID of this studio creative.
   *
   * @var string
   */
  public $backupImageAssetId;
  protected $createdInfoType = LastModifiedInfo::class;
  protected $createdInfoDataType = '';
  protected $dimensionType = StudioCreativeDimension::class;
  protected $dimensionDataType = '';
  /**
   * Dynamic profile ID of this studio creative.
   *
   * @var string
   */
  public $dynamicProfileId;
  /**
   * Format of this studio creative. This is a required field on insertion.
   *
   * @var string
   */
  public $format;
  /**
   * Output only. Unique ID of this studio creative. This is a read-only, auto-
   * generated field.
   *
   * @var string
   */
  public $id;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Identifier. Name of this studio creative. This is a required field on
   * insertion.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Status of this studio creative. It is a read-only field.
   *
   * @var string
   */
  public $status;
  /**
   * Studio account ID of this creative. This field, if left unset, will be
   * auto-populated.
   *
   * @var string
   */
  public $studioAccountId;
  /**
   * Studio advertiser ID of this studio creative. This is a required field on
   * insertion.
   *
   * @var string
   */
  public $studioAdvertiserId;
  /**
   * Studio campaign ID of this studio creative. This is a required field on
   * insertion.
   *
   * @var string
   */
  public $studioCampaignId;

  /**
   * List of assets associated with this studio creative. It is a required field
   * on insertion.
   *
   * @param string[] $assetIds
   */
  public function setAssetIds($assetIds)
  {
    $this->assetIds = $assetIds;
  }
  /**
   * @return string[]
   */
  public function getAssetIds()
  {
    return $this->assetIds;
  }
  /**
   * Backup image asset ID of this studio creative.
   *
   * @param string $backupImageAssetId
   */
  public function setBackupImageAssetId($backupImageAssetId)
  {
    $this->backupImageAssetId = $backupImageAssetId;
  }
  /**
   * @return string
   */
  public function getBackupImageAssetId()
  {
    return $this->backupImageAssetId;
  }
  /**
   * The timestamp when the studio creative was created. This is a read-only,
   * auto-generated field.
   *
   * @param LastModifiedInfo $createdInfo
   */
  public function setCreatedInfo(LastModifiedInfo $createdInfo)
  {
    $this->createdInfo = $createdInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreatedInfo()
  {
    return $this->createdInfo;
  }
  /**
   * Dimension of this studio creative. This is a required field on insertion if
   * format is BANNER or EXPANDING.
   *
   * @param StudioCreativeDimension $dimension
   */
  public function setDimension(StudioCreativeDimension $dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return StudioCreativeDimension
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Dynamic profile ID of this studio creative.
   *
   * @param string $dynamicProfileId
   */
  public function setDynamicProfileId($dynamicProfileId)
  {
    $this->dynamicProfileId = $dynamicProfileId;
  }
  /**
   * @return string
   */
  public function getDynamicProfileId()
  {
    return $this->dynamicProfileId;
  }
  /**
   * Format of this studio creative. This is a required field on insertion.
   *
   * Accepted values: UNKNOWN, BANNER, EXPANDING, INTERSTITIAL,
   * VPAID_LINEAR_VIDEO
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Output only. Unique ID of this studio creative. This is a read-only, auto-
   * generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The timestamp when the studio creative was last modified. This is a read-
   * only, auto-generated field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Identifier. Name of this studio creative. This is a required field on
   * insertion.
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
   * Output only. Status of this studio creative. It is a read-only field.
   *
   * Accepted values: UNKNOWN_STATUS, IN_DEVELOPMENT, PUBLISHED, QA_REJECTED,
   * QA_APPROVED, TRAFFICKED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Studio account ID of this creative. This field, if left unset, will be
   * auto-populated.
   *
   * @param string $studioAccountId
   */
  public function setStudioAccountId($studioAccountId)
  {
    $this->studioAccountId = $studioAccountId;
  }
  /**
   * @return string
   */
  public function getStudioAccountId()
  {
    return $this->studioAccountId;
  }
  /**
   * Studio advertiser ID of this studio creative. This is a required field on
   * insertion.
   *
   * @param string $studioAdvertiserId
   */
  public function setStudioAdvertiserId($studioAdvertiserId)
  {
    $this->studioAdvertiserId = $studioAdvertiserId;
  }
  /**
   * @return string
   */
  public function getStudioAdvertiserId()
  {
    return $this->studioAdvertiserId;
  }
  /**
   * Studio campaign ID of this studio creative. This is a required field on
   * insertion.
   *
   * @param string $studioCampaignId
   */
  public function setStudioCampaignId($studioCampaignId)
  {
    $this->studioCampaignId = $studioCampaignId;
  }
  /**
   * @return string
   */
  public function getStudioCampaignId()
  {
    return $this->studioCampaignId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudioCreative::class, 'Google_Service_Dfareporting_StudioCreative');
