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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaDataStoreBillingEstimation extends \Google\Model
{
  /**
   * Data size for structured data in terms of bytes.
   *
   * @var string
   */
  public $structuredDataSize;
  /**
   * Last updated timestamp for structured data.
   *
   * @var string
   */
  public $structuredDataUpdateTime;
  /**
   * Data size for unstructured data in terms of bytes.
   *
   * @var string
   */
  public $unstructuredDataSize;
  /**
   * Last updated timestamp for unstructured data.
   *
   * @var string
   */
  public $unstructuredDataUpdateTime;
  /**
   * Data size for websites in terms of bytes.
   *
   * @var string
   */
  public $websiteDataSize;
  /**
   * Last updated timestamp for websites.
   *
   * @var string
   */
  public $websiteDataUpdateTime;

  /**
   * Data size for structured data in terms of bytes.
   *
   * @param string $structuredDataSize
   */
  public function setStructuredDataSize($structuredDataSize)
  {
    $this->structuredDataSize = $structuredDataSize;
  }
  /**
   * @return string
   */
  public function getStructuredDataSize()
  {
    return $this->structuredDataSize;
  }
  /**
   * Last updated timestamp for structured data.
   *
   * @param string $structuredDataUpdateTime
   */
  public function setStructuredDataUpdateTime($structuredDataUpdateTime)
  {
    $this->structuredDataUpdateTime = $structuredDataUpdateTime;
  }
  /**
   * @return string
   */
  public function getStructuredDataUpdateTime()
  {
    return $this->structuredDataUpdateTime;
  }
  /**
   * Data size for unstructured data in terms of bytes.
   *
   * @param string $unstructuredDataSize
   */
  public function setUnstructuredDataSize($unstructuredDataSize)
  {
    $this->unstructuredDataSize = $unstructuredDataSize;
  }
  /**
   * @return string
   */
  public function getUnstructuredDataSize()
  {
    return $this->unstructuredDataSize;
  }
  /**
   * Last updated timestamp for unstructured data.
   *
   * @param string $unstructuredDataUpdateTime
   */
  public function setUnstructuredDataUpdateTime($unstructuredDataUpdateTime)
  {
    $this->unstructuredDataUpdateTime = $unstructuredDataUpdateTime;
  }
  /**
   * @return string
   */
  public function getUnstructuredDataUpdateTime()
  {
    return $this->unstructuredDataUpdateTime;
  }
  /**
   * Data size for websites in terms of bytes.
   *
   * @param string $websiteDataSize
   */
  public function setWebsiteDataSize($websiteDataSize)
  {
    $this->websiteDataSize = $websiteDataSize;
  }
  /**
   * @return string
   */
  public function getWebsiteDataSize()
  {
    return $this->websiteDataSize;
  }
  /**
   * Last updated timestamp for websites.
   *
   * @param string $websiteDataUpdateTime
   */
  public function setWebsiteDataUpdateTime($websiteDataUpdateTime)
  {
    $this->websiteDataUpdateTime = $websiteDataUpdateTime;
  }
  /**
   * @return string
   */
  public function getWebsiteDataUpdateTime()
  {
    return $this->websiteDataUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaDataStoreBillingEstimation::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaDataStoreBillingEstimation');
