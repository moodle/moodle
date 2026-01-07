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

class YoutubeAssetAssociation extends \Google\Model
{
  /**
   * YouTube asset type is not specified or is unknown in this version.
   */
  public const YOUTUBE_ASSET_TYPE_YOUTUBE_ASSET_TYPE_UNSPECIFIED = 'YOUTUBE_ASSET_TYPE_UNSPECIFIED';
  /**
   * Location asset.
   */
  public const YOUTUBE_ASSET_TYPE_YOUTUBE_ASSET_TYPE_LOCATION = 'YOUTUBE_ASSET_TYPE_LOCATION';
  /**
   * Affiliate location asset.
   */
  public const YOUTUBE_ASSET_TYPE_YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION = 'YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION';
  /**
   * Sitelink asset.
   */
  public const YOUTUBE_ASSET_TYPE_YOUTUBE_ASSET_TYPE_SITELINK = 'YOUTUBE_ASSET_TYPE_SITELINK';
  protected $linkedYoutubeAssetType = YoutubeAssetAssociationLinkedYouTubeAsset::class;
  protected $linkedYoutubeAssetDataType = '';
  /**
   * Identifier. The resource name of the association. For line item-level
   * associations: The name pattern is `advertisers/{advertiser_id}/lineItems/{l
   * ine_item_id}/youtubeAssetTypes/{youtube_asset_type}/youtubeAssetAssociation
   * s/{youtube_asset_association_id}`. For ad group-level associations: The
   * name pattern is `advertisers/{advertiser_id}/adGroups/{ad_group_id}/youtube
   * AssetTypes/{youtube_asset_type}/youtubeAssetAssociations/{youtube_asset_ass
   * ociation_id}`. For `YOUTUBE_ASSET_TYPE_LOCATION` and
   * `YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION` associations:
   * `youtube_asset_association_id` is the ID of the asset set linked, or 0 if
   * the location_matching_type or affiliate_location_matching_type is
   * `DISABLED`. For `YOUTUBE_ASSET_TYPE_SITELINK` associations:
   * `youtube_asset_association_id` is be the ID of the sitelink asset linked.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The type of YouTube asset associated with the resource.
   *
   * @var string
   */
  public $youtubeAssetType;

  /**
   * Required. The YouTube asset associated with the resource.
   *
   * @param YoutubeAssetAssociationLinkedYouTubeAsset $linkedYoutubeAsset
   */
  public function setLinkedYoutubeAsset(YoutubeAssetAssociationLinkedYouTubeAsset $linkedYoutubeAsset)
  {
    $this->linkedYoutubeAsset = $linkedYoutubeAsset;
  }
  /**
   * @return YoutubeAssetAssociationLinkedYouTubeAsset
   */
  public function getLinkedYoutubeAsset()
  {
    return $this->linkedYoutubeAsset;
  }
  /**
   * Identifier. The resource name of the association. For line item-level
   * associations: The name pattern is `advertisers/{advertiser_id}/lineItems/{l
   * ine_item_id}/youtubeAssetTypes/{youtube_asset_type}/youtubeAssetAssociation
   * s/{youtube_asset_association_id}`. For ad group-level associations: The
   * name pattern is `advertisers/{advertiser_id}/adGroups/{ad_group_id}/youtube
   * AssetTypes/{youtube_asset_type}/youtubeAssetAssociations/{youtube_asset_ass
   * ociation_id}`. For `YOUTUBE_ASSET_TYPE_LOCATION` and
   * `YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION` associations:
   * `youtube_asset_association_id` is the ID of the asset set linked, or 0 if
   * the location_matching_type or affiliate_location_matching_type is
   * `DISABLED`. For `YOUTUBE_ASSET_TYPE_SITELINK` associations:
   * `youtube_asset_association_id` is be the ID of the sitelink asset linked.
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
   * Required. The type of YouTube asset associated with the resource.
   *
   * Accepted values: YOUTUBE_ASSET_TYPE_UNSPECIFIED,
   * YOUTUBE_ASSET_TYPE_LOCATION, YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION,
   * YOUTUBE_ASSET_TYPE_SITELINK
   *
   * @param self::YOUTUBE_ASSET_TYPE_* $youtubeAssetType
   */
  public function setYoutubeAssetType($youtubeAssetType)
  {
    $this->youtubeAssetType = $youtubeAssetType;
  }
  /**
   * @return self::YOUTUBE_ASSET_TYPE_*
   */
  public function getYoutubeAssetType()
  {
    return $this->youtubeAssetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAssetAssociation::class, 'Google_Service_DisplayVideo_YoutubeAssetAssociation');
