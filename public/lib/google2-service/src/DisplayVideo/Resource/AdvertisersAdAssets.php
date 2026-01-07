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

namespace Google\Service\DisplayVideo\Resource;

use Google\Service\DisplayVideo\AdAsset;
use Google\Service\DisplayVideo\BulkCreateAdAssetsRequest;
use Google\Service\DisplayVideo\BulkCreateAdAssetsResponse;
use Google\Service\DisplayVideo\CreateAdAssetRequest;
use Google\Service\DisplayVideo\ListAdAssetsResponse;
use Google\Service\DisplayVideo\UploadAdAssetRequest;
use Google\Service\DisplayVideo\UploadAdAssetResponse;

/**
 * The "adAssets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $adAssets = $displayvideoService->advertisers_adAssets;
 *  </code>
 */
class AdvertisersAdAssets extends \Google\Service\Resource
{
  /**
   * Creates multiple ad assets in a single request. Returns the newly-created ad
   * assets if successful. Only supports the creation of assets of AdAssetType
   * `AD_ASSET_TYPE_YOUTUBE_VIDEO`. (adAssets.bulkCreate)
   *
   * @param string $advertiserId Required. The ID of the advertiser these ad
   * assets belong to.
   * @param BulkCreateAdAssetsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BulkCreateAdAssetsResponse
   * @throws \Google\Service\Exception
   */
  public function bulkCreate($advertiserId, BulkCreateAdAssetsRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkCreate', [$params], BulkCreateAdAssetsResponse::class);
  }
  /**
   * Creates an ad asset. Returns the newly-created ad asset if successful. Only
   * supports the creation of assets of AdAssetType `AD_ASSET_TYPE_YOUTUBE_VIDEO`.
   * (adAssets.create)
   *
   * @param string $advertiserId Required. The ID of the advertiser this ad asset
   * belongs to.
   * @param CreateAdAssetRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AdAsset
   * @throws \Google\Service\Exception
   */
  public function create($advertiserId, CreateAdAssetRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AdAsset::class);
  }
  /**
   * Gets an ad asset. Only supports the retrieval of assets of AdAssetType
   * `AD_ASSET_TYPE_YOUTUBE_VIDEO`. (adAssets.get)
   *
   * @param string $advertiserId Required. The ID of the advertiser this ad asset
   * belongs to.
   * @param string $adAssetId Required. The ID of the ad asset to fetch. Only
   * supports assets of AdAssetType `AD_ASSET_TYPE_YOUTUBE_VIDEO`
   * @param array $optParams Optional parameters.
   * @return AdAsset
   * @throws \Google\Service\Exception
   */
  public function get($advertiserId, $adAssetId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'adAssetId' => $adAssetId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AdAsset::class);
  }
  /**
   * Lists ad assets under an advertiser ID. Only supports the retrieval of assets
   * of AdAssetType `AD_ASSET_TYPE_YOUTUBE_VIDEO`.
   * (adAssets.listAdvertisersAdAssets)
   *
   * @param string $advertiserId Required. The ID of the advertiser the ad assets
   * belong to.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Allows filtering of the results by ad
   * asset fields. Supported syntax: * A restriction has the form of `{field}
   * {operator} {value}`. * All fields must use the `EQUALS (=)` operator.
   * Supported fields: * `youtubeVideoAsset.youtubeVideoId` * `entityStatus`
   * Examples: * All active YouTube video ad assets under an advertiser:
   * `entityStatus=ENTITY_STATUS_ACTIVE`
   * @opt_param string orderBy Optional. Field by which to sort the list.
   * Acceptable values are: * `entityStatus` * `youtubeVideoAsset.youtubeVideoId`
   * * `adAssetId` (default) The default sorting order is ascending. To specify
   * descending order for a field, a suffix "desc" should be added to the field
   * name. Example: `adAssetId desc`.
   * @opt_param int pageSize Optional. Requested page size. Must be between `1`
   * and `5000`. If unspecified will default to `5000`. Returns error code
   * `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of next_page_token
   * returned from the previous call to `ListAdAssets` method. If not specified,
   * the first page of results will be returned.
   * @return ListAdAssetsResponse
   * @throws \Google\Service\Exception
   */
  public function listAdvertisersAdAssets($advertiserId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAdAssetsResponse::class);
  }
  /**
   * Uploads and creates an ad asset. Returns the ID of the newly-created ad asset
   * if successful. Only supports the uploading of assets with the AdAssetType
   * `AD_ASSET_TYPE_IMAGE`. (adAssets.upload)
   *
   * @param string $advertiserId Required. The ID of the advertiser this ad asset
   * belongs to.
   * @param UploadAdAssetRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UploadAdAssetResponse
   * @throws \Google\Service\Exception
   */
  public function upload($advertiserId, UploadAdAssetRequest $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], UploadAdAssetResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersAdAssets::class, 'Google_Service_DisplayVideo_Resource_AdvertisersAdAssets');
