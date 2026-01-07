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

class GoogleCloudAiplatformV1ContainerRegistryDestination extends \Google\Model
{
  /**
   * Required. Container Registry URI of a container image. Only Google
   * Container Registry and Artifact Registry are supported now. Accepted forms:
   * * Google Container Registry path. For example:
   * `gcr.io/projectId/imageName:tag`. * Artifact Registry path. For example:
   * `us-central1-docker.pkg.dev/projectId/repoName/imageName:tag`. If a tag is
   * not specified, "latest" will be used as the default tag.
   *
   * @var string
   */
  public $outputUri;

  /**
   * Required. Container Registry URI of a container image. Only Google
   * Container Registry and Artifact Registry are supported now. Accepted forms:
   * * Google Container Registry path. For example:
   * `gcr.io/projectId/imageName:tag`. * Artifact Registry path. For example:
   * `us-central1-docker.pkg.dev/projectId/repoName/imageName:tag`. If a tag is
   * not specified, "latest" will be used as the default tag.
   *
   * @param string $outputUri
   */
  public function setOutputUri($outputUri)
  {
    $this->outputUri = $outputUri;
  }
  /**
   * @return string
   */
  public function getOutputUri()
  {
    return $this->outputUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ContainerRegistryDestination::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ContainerRegistryDestination');
