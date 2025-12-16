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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureViewOptimizedConfig extends \Google\Model
{
  protected $automaticResourcesType = GoogleCloudAiplatformV1AutomaticResources::class;
  protected $automaticResourcesDataType = '';

  /**
   * Optional. A description of resources that the FeatureView uses, which to
   * large degree are decided by Vertex AI, and optionally allows only a modest
   * additional configuration. If min_replica_count is not set, the default
   * value is 2. If max_replica_count is not set, the default value is 6. The
   * max allowed replica count is 1000.
   *
   * @param GoogleCloudAiplatformV1AutomaticResources $automaticResources
   */
  public function setAutomaticResources(GoogleCloudAiplatformV1AutomaticResources $automaticResources)
  {
    $this->automaticResources = $automaticResources;
  }
  /**
   * @return GoogleCloudAiplatformV1AutomaticResources
   */
  public function getAutomaticResources()
  {
    return $this->automaticResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewOptimizedConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewOptimizedConfig');
