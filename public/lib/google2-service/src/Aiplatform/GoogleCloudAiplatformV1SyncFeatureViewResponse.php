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

class GoogleCloudAiplatformV1SyncFeatureViewResponse extends \Google\Model
{
  /**
   * Format: `projects/{project}/locations/{location}/featureOnlineStores/{featu
   * re_online_store}/featureViews/{feature_view}/featureViewSyncs/{feature_view
   * _sync}`
   *
   * @var string
   */
  public $featureViewSync;

  /**
   * Format: `projects/{project}/locations/{location}/featureOnlineStores/{featu
   * re_online_store}/featureViews/{feature_view}/featureViewSyncs/{feature_view
   * _sync}`
   *
   * @param string $featureViewSync
   */
  public function setFeatureViewSync($featureViewSync)
  {
    $this->featureViewSync = $featureViewSync;
  }
  /**
   * @return string
   */
  public function getFeatureViewSync()
  {
    return $this->featureViewSync;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SyncFeatureViewResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SyncFeatureViewResponse');
