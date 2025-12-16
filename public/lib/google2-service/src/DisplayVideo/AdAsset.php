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

class AdAsset extends \Google\Model
{
  /**
   * The ad asset type is unspecified.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_UNSPECIFIED = 'AD_ASSET_TYPE_UNSPECIFIED';
  /**
   * The ad asset is a YouTube/DemandGen image.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_IMAGE = 'AD_ASSET_TYPE_IMAGE';
  /**
   * The ad asset is a YouTube video.
   */
  public const AD_ASSET_TYPE_AD_ASSET_TYPE_YOUTUBE_VIDEO = 'AD_ASSET_TYPE_YOUTUBE_VIDEO';
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
   * Output only. The ID of the ad asset. Referred to as the asset ID when
   * assigned to an ad.
   *
   * @var string
   */
  public $adAssetId;
  /**
   * Required. The type of the ad asset.
   *
   * @var string
   */
  public $adAssetType;
  /**
   * Output only. The entity status of the ad asset.
   *
   * @var string
   */
  public $entityStatus;
  /**
   * Identifier. The resource name of the ad asset.
   *
   * @var string
   */
  public $name;
  protected $youtubeVideoAssetType = YoutubeVideoAsset::class;
  protected $youtubeVideoAssetDataType = '';

  /**
   * Output only. The ID of the ad asset. Referred to as the asset ID when
   * assigned to an ad.
   *
   * @param string $adAssetId
   */
  public function setAdAssetId($adAssetId)
  {
    $this->adAssetId = $adAssetId;
  }
  /**
   * @return string
   */
  public function getAdAssetId()
  {
    return $this->adAssetId;
  }
  /**
   * Required. The type of the ad asset.
   *
   * Accepted values: AD_ASSET_TYPE_UNSPECIFIED, AD_ASSET_TYPE_IMAGE,
   * AD_ASSET_TYPE_YOUTUBE_VIDEO
   *
   * @param self::AD_ASSET_TYPE_* $adAssetType
   */
  public function setAdAssetType($adAssetType)
  {
    $this->adAssetType = $adAssetType;
  }
  /**
   * @return self::AD_ASSET_TYPE_*
   */
  public function getAdAssetType()
  {
    return $this->adAssetType;
  }
  /**
   * Output only. The entity status of the ad asset.
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
   * Identifier. The resource name of the ad asset.
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
   * Youtube video asset data.
   *
   * @param YoutubeVideoAsset $youtubeVideoAsset
   */
  public function setYoutubeVideoAsset(YoutubeVideoAsset $youtubeVideoAsset)
  {
    $this->youtubeVideoAsset = $youtubeVideoAsset;
  }
  /**
   * @return YoutubeVideoAsset
   */
  public function getYoutubeVideoAsset()
  {
    return $this->youtubeVideoAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdAsset::class, 'Google_Service_DisplayVideo_AdAsset');
