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

class GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource extends \Google\Model
{
  /**
   * The commit SHA to read repository with. If unset, the file will be read at
   * HEAD.
   *
   * @var string
   */
  public $commitSha;
  /**
   * The resource name of the Dataform Repository. Format:
   * `projects/{project_id}/locations/{location}/repositories/{repository_id}`
   *
   * @var string
   */
  public $dataformRepositoryResourceName;

  /**
   * The commit SHA to read repository with. If unset, the file will be read at
   * HEAD.
   *
   * @param string $commitSha
   */
  public function setCommitSha($commitSha)
  {
    $this->commitSha = $commitSha;
  }
  /**
   * @return string
   */
  public function getCommitSha()
  {
    return $this->commitSha;
  }
  /**
   * The resource name of the Dataform Repository. Format:
   * `projects/{project_id}/locations/{location}/repositories/{repository_id}`
   *
   * @param string $dataformRepositoryResourceName
   */
  public function setDataformRepositoryResourceName($dataformRepositoryResourceName)
  {
    $this->dataformRepositoryResourceName = $dataformRepositoryResourceName;
  }
  /**
   * @return string
   */
  public function getDataformRepositoryResourceName()
  {
    return $this->dataformRepositoryResourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource');
