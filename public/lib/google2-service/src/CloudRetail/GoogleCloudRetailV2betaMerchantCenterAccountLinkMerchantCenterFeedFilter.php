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

class GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter extends \Google\Model
{
  /**
   * @var string
   */
  public $primaryFeedId;
  /**
   * @var string
   */
  public $primaryFeedName;

  /**
   * @param string
   */
  public function setPrimaryFeedId($primaryFeedId)
  {
    $this->primaryFeedId = $primaryFeedId;
  }
  /**
   * @return string
   */
  public function getPrimaryFeedId()
  {
    return $this->primaryFeedId;
  }
  /**
   * @param string
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
class_alias(GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2betaMerchantCenterAccountLinkMerchantCenterFeedFilter');
