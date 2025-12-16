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

class GoogleCloudAiplatformV1ResourceRuntime extends \Google\Model
{
  /**
   * Output only. URIs for user to connect to the Cluster. Example: {
   * "RAY_HEAD_NODE_INTERNAL_IP": "head-node-IP:10001" "RAY_DASHBOARD_URI":
   * "ray-dashboard-address:8888" }
   *
   * @var string[]
   */
  public $accessUris;

  /**
   * Output only. URIs for user to connect to the Cluster. Example: {
   * "RAY_HEAD_NODE_INTERNAL_IP": "head-node-IP:10001" "RAY_DASHBOARD_URI":
   * "ray-dashboard-address:8888" }
   *
   * @param string[] $accessUris
   */
  public function setAccessUris($accessUris)
  {
    $this->accessUris = $accessUris;
  }
  /**
   * @return string[]
   */
  public function getAccessUris()
  {
    return $this->accessUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ResourceRuntime::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ResourceRuntime');
