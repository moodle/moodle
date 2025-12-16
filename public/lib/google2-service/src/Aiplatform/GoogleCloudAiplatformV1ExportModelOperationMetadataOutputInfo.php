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

class GoogleCloudAiplatformV1ExportModelOperationMetadataOutputInfo extends \Google\Model
{
  /**
   * Output only. If the Model artifact is being exported to Google Cloud
   * Storage this is the full path of the directory created, into which the
   * Model files are being written to.
   *
   * @var string
   */
  public $artifactOutputUri;
  /**
   * Output only. If the Model image is being exported to Google Container
   * Registry or Artifact Registry this is the full path of the image created.
   *
   * @var string
   */
  public $imageOutputUri;

  /**
   * Output only. If the Model artifact is being exported to Google Cloud
   * Storage this is the full path of the directory created, into which the
   * Model files are being written to.
   *
   * @param string $artifactOutputUri
   */
  public function setArtifactOutputUri($artifactOutputUri)
  {
    $this->artifactOutputUri = $artifactOutputUri;
  }
  /**
   * @return string
   */
  public function getArtifactOutputUri()
  {
    return $this->artifactOutputUri;
  }
  /**
   * Output only. If the Model image is being exported to Google Container
   * Registry or Artifact Registry this is the full path of the image created.
   *
   * @param string $imageOutputUri
   */
  public function setImageOutputUri($imageOutputUri)
  {
    $this->imageOutputUri = $imageOutputUri;
  }
  /**
   * @return string
   */
  public function getImageOutputUri()
  {
    return $this->imageOutputUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportModelOperationMetadataOutputInfo::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportModelOperationMetadataOutputInfo');
