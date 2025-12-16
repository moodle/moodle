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

class GoogleCloudAiplatformV1AddContextArtifactsAndExecutionsRequest extends \Google\Collection
{
  protected $collection_key = 'executions';
  /**
   * The resource names of the Artifacts to attribute to the Context. Format: `p
   * rojects/{project}/locations/{location}/metadataStores/{metadatastore}/artif
   * acts/{artifact}`
   *
   * @var string[]
   */
  public $artifacts;
  /**
   * The resource names of the Executions to associate with the Context. Format:
   * `projects/{project}/locations/{location}/metadataStores/{metadatastore}/exe
   * cutions/{execution}`
   *
   * @var string[]
   */
  public $executions;

  /**
   * The resource names of the Artifacts to attribute to the Context. Format: `p
   * rojects/{project}/locations/{location}/metadataStores/{metadatastore}/artif
   * acts/{artifact}`
   *
   * @param string[] $artifacts
   */
  public function setArtifacts($artifacts)
  {
    $this->artifacts = $artifacts;
  }
  /**
   * @return string[]
   */
  public function getArtifacts()
  {
    return $this->artifacts;
  }
  /**
   * The resource names of the Executions to associate with the Context. Format:
   * `projects/{project}/locations/{location}/metadataStores/{metadatastore}/exe
   * cutions/{execution}`
   *
   * @param string[] $executions
   */
  public function setExecutions($executions)
  {
    $this->executions = $executions;
  }
  /**
   * @return string[]
   */
  public function getExecutions()
  {
    return $this->executions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AddContextArtifactsAndExecutionsRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AddContextArtifactsAndExecutionsRequest');
