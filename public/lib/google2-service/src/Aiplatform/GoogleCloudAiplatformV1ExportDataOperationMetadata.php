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

class GoogleCloudAiplatformV1ExportDataOperationMetadata extends \Google\Model
{
  /**
   * A Google Cloud Storage directory which path ends with '/'. The exported
   * data is stored in the directory.
   *
   * @var string
   */
  public $gcsOutputDirectory;
  protected $genericMetadataType = GoogleCloudAiplatformV1GenericOperationMetadata::class;
  protected $genericMetadataDataType = '';

  /**
   * A Google Cloud Storage directory which path ends with '/'. The exported
   * data is stored in the directory.
   *
   * @param string $gcsOutputDirectory
   */
  public function setGcsOutputDirectory($gcsOutputDirectory)
  {
    $this->gcsOutputDirectory = $gcsOutputDirectory;
  }
  /**
   * @return string
   */
  public function getGcsOutputDirectory()
  {
    return $this->gcsOutputDirectory;
  }
  /**
   * The common part of the operation metadata.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportDataOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportDataOperationMetadata');
