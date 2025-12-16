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

class GoogleCloudAiplatformV1PipelineTaskDetailArtifactList extends \Google\Collection
{
  protected $collection_key = 'artifacts';
  protected $artifactsType = GoogleCloudAiplatformV1Artifact::class;
  protected $artifactsDataType = 'array';

  /**
   * Output only. A list of artifact metadata.
   *
   * @param GoogleCloudAiplatformV1Artifact[] $artifacts
   */
  public function setArtifacts($artifacts)
  {
    $this->artifacts = $artifacts;
  }
  /**
   * @return GoogleCloudAiplatformV1Artifact[]
   */
  public function getArtifacts()
  {
    return $this->artifacts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineTaskDetailArtifactList::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineTaskDetailArtifactList');
