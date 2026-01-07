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

namespace Google\Service\AnalyticsHub;

class GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo extends \Google\Model
{
  protected $cloudMarketplaceType = GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo::class;
  protected $cloudMarketplaceDataType = '';

  /**
   * Output only. Details of the Marketplace Data Product associated with the
   * Listing.
   *
   * @param GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo $cloudMarketplace
   */
  public function setCloudMarketplace(GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo $cloudMarketplace)
  {
    $this->cloudMarketplace = $cloudMarketplace;
  }
  /**
   * @return GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo
   */
  public function getCloudMarketplace()
  {
    return $this->cloudMarketplace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo::class, 'Google_Service_AnalyticsHub_GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo');
