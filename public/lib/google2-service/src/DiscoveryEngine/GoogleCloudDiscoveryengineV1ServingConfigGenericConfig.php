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

class GoogleCloudDiscoveryengineV1ServingConfigGenericConfig extends \Google\Model
{
  protected $contentSearchSpecType = GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec::class;
  protected $contentSearchSpecDataType = '';

  /**
   * Specifies the expected behavior of content search. Only valid for content-
   * search enabled data store.
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec $contentSearchSpec
   */
  public function setContentSearchSpec(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec $contentSearchSpec)
  {
    $this->contentSearchSpec = $contentSearchSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec
   */
  public function getContentSearchSpec()
  {
    return $this->contentSearchSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ServingConfigGenericConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ServingConfigGenericConfig');
