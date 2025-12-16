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

class GoogleCloudAiplatformV1ResourceRuntimeSpec extends \Google\Model
{
  protected $raySpecType = GoogleCloudAiplatformV1RaySpec::class;
  protected $raySpecDataType = '';
  protected $serviceAccountSpecType = GoogleCloudAiplatformV1ServiceAccountSpec::class;
  protected $serviceAccountSpecDataType = '';

  /**
   * Optional. Ray cluster configuration. Required when creating a dedicated
   * RayCluster on the PersistentResource.
   *
   * @param GoogleCloudAiplatformV1RaySpec $raySpec
   */
  public function setRaySpec(GoogleCloudAiplatformV1RaySpec $raySpec)
  {
    $this->raySpec = $raySpec;
  }
  /**
   * @return GoogleCloudAiplatformV1RaySpec
   */
  public function getRaySpec()
  {
    return $this->raySpec;
  }
  /**
   * Optional. Configure the use of workload identity on the PersistentResource
   *
   * @param GoogleCloudAiplatformV1ServiceAccountSpec $serviceAccountSpec
   */
  public function setServiceAccountSpec(GoogleCloudAiplatformV1ServiceAccountSpec $serviceAccountSpec)
  {
    $this->serviceAccountSpec = $serviceAccountSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ServiceAccountSpec
   */
  public function getServiceAccountSpec()
  {
    return $this->serviceAccountSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ResourceRuntimeSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ResourceRuntimeSpec');
