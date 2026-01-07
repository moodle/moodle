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

use Google\Service\DisplayVideo\DisplayvideoEmpty;
use Google\Service\DisplayVideo\ListYoutubeAssetAssociationsResponse;
use Google\Service\DisplayVideo\YoutubeAssetAssociation;

/**
 * The "youtubeAssetAssociations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $youtubeAssetAssociations = $displayvideoService->advertisers_lineItems_youtubeAssetTypes_youtubeAssetAssociations;
 *  </code>
 */
class AdvertisersLineItemsYoutubeAssetTypesYoutubeAssetAssociations extends \Google\Service\Resource
{
  /**
   * Creates a new association between the identified resource and a YouTube
   * asset. Returns the newly-created association. *Warning:* This method is only
   * available to an informed subset of users. (youtubeAssetAssociations.create)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * linked entity belongs to.
   * @param string $lineItemId The ID of a line item.
   * @param string $youtubeAssetType Required. The type of YouTube asset
   * associated with the resource.
   * @param YoutubeAssetAssociation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string linkedEntity.adGroupId The ID of an ad group.
   * @return YoutubeAssetAssociation
   * @throws \Google\Service\Exception
   */
  public function create($advertiserId, $lineItemId, $youtubeAssetType, YoutubeAssetAssociation $postBody, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId, 'youtubeAssetType' => $youtubeAssetType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], YoutubeAssetAssociation::class);
  }
  /**
   * Deletes an existing association between the identified resource and a YouTube
   * asset. *Warning:* This method is only available to an informed subset of
   * users. (youtubeAssetAssociations.delete)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * linked entity belongs to.
   * @param string $lineItemId The ID of a line item.
   * @param string $youtubeAssetType Required. The type of YouTube asset
   * associated with the resource.
   * @param string $youtubeAssetAssociationId Required. The ID of the YouTube
   * asset in the association. For `YOUTUBE_ASSET_TYPE_LOCATION` and
   * `YOUTUBE_ASSET_TYPE_AFFILIATE_LOCATION` associations: This should be the ID
   * of the asset set linked, or 0 if the location_asset_filter or
   * affiliate_location_asset_filter is `DISABLED`. For
   * `YOUTUBE_ASSET_TYPE_SITELINK` associations: This should be the ID of the
   * sitelink asset linked.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string linkedEntity.adGroupId The ID of an ad group.
   * @return DisplayvideoEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($advertiserId, $lineItemId, $youtubeAssetType, $youtubeAssetAssociationId, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId, 'youtubeAssetType' => $youtubeAssetType, 'youtubeAssetAssociationId' => $youtubeAssetAssociationId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DisplayvideoEmpty::class);
  }
  /**
   * Lists the YouTube asset associations linked to the given resource. (youtubeAs
   * setAssociations.listAdvertisersLineItemsYoutubeAssetTypesYoutubeAssetAssociat
   * ions)
   *
   * @param string $advertiserId Required. The ID of the advertiser that the
   * linked entity belongs to.
   * @param string $lineItemId The ID of a line item.
   * @param string $youtubeAssetType Required. The type of YouTube asset being
   * associated with the resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string linkedEntity.adGroupId The ID of an ad group.
   * @opt_param string orderBy Optional. Field by which to sort the list. The only
   * acceptable values are: * `linkedYoutubeAsset.locationAssetFilter.assetSetId`,
   * * `linkedYoutubeAsset.affiliateLocationAssetFilter.assetSetId`, *
   * `linkedYoutubeAsset.sitelinkAsset.assetId` The default sorting order is
   * ascending. To specify descending order for a field, a suffix " desc" should
   * be added to the field name. Example:
   * `linkedYoutubeAsset.sitelinkAsset.assetId desc`.
   * @opt_param int pageSize Optional. Requested page size. Must be between `1`
   * and `10000`. If unspecified will default to `100`. Returns error code
   * `INVALID_ARGUMENT` if an invalid value is specified.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of next_page_token
   * returned from the previous call to `ListYoutubeAssetAssociations` method. If
   * not specified, the first page of results will be returned.
   * @return ListYoutubeAssetAssociationsResponse
   * @throws \Google\Service\Exception
   */
  public function listAdvertisersLineItemsYoutubeAssetTypesYoutubeAssetAssociations($advertiserId, $lineItemId, $youtubeAssetType, $optParams = [])
  {
    $params = ['advertiserId' => $advertiserId, 'lineItemId' => $lineItemId, 'youtubeAssetType' => $youtubeAssetType];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListYoutubeAssetAssociationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertisersLineItemsYoutubeAssetTypesYoutubeAssetAssociations::class, 'Google_Service_DisplayVideo_Resource_AdvertisersLineItemsYoutubeAssetTypesYoutubeAssetAssociations');
