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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse extends \Google\Collection
{
  protected $collection_key = 'ipOverrides';
  protected $ipOverridesType = GoogleCloudRecaptchaenterpriseV1IpOverrideData::class;
  protected $ipOverridesDataType = 'array';
  /**
   * Token to retrieve the next page of results. If this field is empty, no keys
   * remain in the results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * IP Overrides details.
   *
   * @param GoogleCloudRecaptchaenterpriseV1IpOverrideData[] $ipOverrides
   */
  public function setIpOverrides($ipOverrides)
  {
    $this->ipOverrides = $ipOverrides;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1IpOverrideData[]
   */
  public function getIpOverrides()
  {
    return $this->ipOverrides;
  }
  /**
   * Token to retrieve the next page of results. If this field is empty, no keys
   * remain in the results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1ListIpOverridesResponse');
