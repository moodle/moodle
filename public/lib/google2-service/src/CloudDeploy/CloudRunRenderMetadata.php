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

namespace Google\Service\CloudDeploy;

class CloudRunRenderMetadata extends \Google\Model
{
  /**
   * Output only. The name of the Cloud Run Service in the rendered manifest.
   * Format is `projects/{project}/locations/{location}/services/{service}`.
   *
   * @var string
   */
  public $service;

  /**
   * Output only. The name of the Cloud Run Service in the rendered manifest.
   * Format is `projects/{project}/locations/{location}/services/{service}`.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRunRenderMetadata::class, 'Google_Service_CloudDeploy_CloudRunRenderMetadata');
