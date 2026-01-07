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

class GoogleCloudAiplatformV1UpdateSpecialistPoolOperationMetadata extends \Google\Model
{
  protected $genericMetadataType = GoogleCloudAiplatformV1GenericOperationMetadata::class;
  protected $genericMetadataDataType = '';
  /**
   * Output only. The name of the SpecialistPool to which the specialists are
   * being added. Format: `projects/{project_id}/locations/{location_id}/special
   * istPools/{specialist_pool}`
   *
   * @var string
   */
  public $specialistPool;

  /**
   * The operation generic information.
   *
   * @param GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata
   */
  public function setGenericMetadata(GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata)
  {
    $this->genericMetadata = $genericMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GenericOperationMetadata
   */
  public function getGenericMetadata()
  {
    return $this->genericMetadata;
  }
  /**
   * Output only. The name of the SpecialistPool to which the specialists are
   * being added. Format: `projects/{project_id}/locations/{location_id}/special
   * istPools/{specialist_pool}`
   *
   * @param string $specialistPool
   */
  public function setSpecialistPool($specialistPool)
  {
    $this->specialistPool = $specialistPool;
  }
  /**
   * @return string
   */
  public function getSpecialistPool()
  {
    return $this->specialistPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UpdateSpecialistPoolOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UpdateSpecialistPoolOperationMetadata');
