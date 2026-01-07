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

class GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact extends \Google\Model
{
  /**
   * Artifact resource id from MLMD. Which is the last portion of an artifact
   * resource name: `projects/{project}/locations/{location}/metadataStores/defa
   * ult/artifacts/{artifact_id}`. The artifact must stay within the same
   * project, location and default metadatastore as the pipeline.
   *
   * @var string
   */
  public $artifactId;

  /**
   * Artifact resource id from MLMD. Which is the last portion of an artifact
   * resource name: `projects/{project}/locations/{location}/metadataStores/defa
   * ult/artifacts/{artifact_id}`. The artifact must stay within the same
   * project, location and default metadatastore as the pipeline.
   *
   * @param string $artifactId
   */
  public function setArtifactId($artifactId)
  {
    $this->artifactId = $artifactId;
  }
  /**
   * @return string
   */
  public function getArtifactId()
  {
    return $this->artifactId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact');
