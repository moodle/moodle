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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDiscoverySpec extends \Google\Model
{
  protected $bigqueryPublishingConfigType = GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig::class;
  protected $bigqueryPublishingConfigDataType = '';
  protected $storageConfigType = GoogleCloudDataplexV1DataDiscoverySpecStorageConfig::class;
  protected $storageConfigDataType = '';

  /**
   * Optional. Configuration for metadata publishing.
   *
   * @param GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig $bigqueryPublishingConfig
   */
  public function setBigqueryPublishingConfig(GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig $bigqueryPublishingConfig)
  {
    $this->bigqueryPublishingConfig = $bigqueryPublishingConfig;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoverySpecBigQueryPublishingConfig
   */
  public function getBigqueryPublishingConfig()
  {
    return $this->bigqueryPublishingConfig;
  }
  /**
   * Cloud Storage related configurations.
   *
   * @param GoogleCloudDataplexV1DataDiscoverySpecStorageConfig $storageConfig
   */
  public function setStorageConfig(GoogleCloudDataplexV1DataDiscoverySpecStorageConfig $storageConfig)
  {
    $this->storageConfig = $storageConfig;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoverySpecStorageConfig
   */
  public function getStorageConfig()
  {
    return $this->storageConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoverySpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoverySpec');
