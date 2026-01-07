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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter extends \Google\Model
{
  /**
   * AFM data source ID.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Merchant Center primary feed ID. Deprecated: use data_source_id instead.
   *
   * @deprecated
   * @var string
   */
  public $primaryFeedId;
  /**
   * Merchant Center primary feed name. The name is used for the display
   * purposes only.
   *
   * @var string
   */
  public $primaryFeedName;

  /**
   * AFM data source ID.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * Merchant Center primary feed ID. Deprecated: use data_source_id instead.
   *
   * @deprecated
   * @param string $primaryFeedId
   */
  public function setPrimaryFeedId($primaryFeedId)
  {
    $this->primaryFeedId = $primaryFeedId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPrimaryFeedId()
  {
    return $this->primaryFeedId;
  }
  /**
   * Merchant Center primary feed name. The name is used for the display
   * purposes only.
   *
   * @param string $primaryFeedName
   */
  public function setPrimaryFeedName($primaryFeedName)
  {
    $this->primaryFeedName = $primaryFeedName;
  }
  /**
   * @return string
   */
  public function getPrimaryFeedName()
  {
    return $this->primaryFeedName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2alphaMerchantCenterAccountLinkMerchantCenterFeedFilter');
