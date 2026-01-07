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

namespace Google\Service\DeveloperConnect;

class ArtifactConfig extends \Google\Model
{
  protected $googleArtifactAnalysisType = GoogleArtifactAnalysis::class;
  protected $googleArtifactAnalysisDataType = '';
  protected $googleArtifactRegistryType = GoogleArtifactRegistry::class;
  protected $googleArtifactRegistryDataType = '';
  /**
   * Required. Immutable. The URI of the artifact that is deployed. e.g. `us-
   * docker.pkg.dev/my-project/my-repo/image`. The URI does not include the tag
   * / digest because it captures a lineage of artifacts.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. Set if the artifact metadata is stored in Artifact analysis.
   *
   * @param GoogleArtifactAnalysis $googleArtifactAnalysis
   */
  public function setGoogleArtifactAnalysis(GoogleArtifactAnalysis $googleArtifactAnalysis)
  {
    $this->googleArtifactAnalysis = $googleArtifactAnalysis;
  }
  /**
   * @return GoogleArtifactAnalysis
   */
  public function getGoogleArtifactAnalysis()
  {
    return $this->googleArtifactAnalysis;
  }
  /**
   * Optional. Set if the artifact is stored in Artifact registry.
   *
   * @param GoogleArtifactRegistry $googleArtifactRegistry
   */
  public function setGoogleArtifactRegistry(GoogleArtifactRegistry $googleArtifactRegistry)
  {
    $this->googleArtifactRegistry = $googleArtifactRegistry;
  }
  /**
   * @return GoogleArtifactRegistry
   */
  public function getGoogleArtifactRegistry()
  {
    return $this->googleArtifactRegistry;
  }
  /**
   * Required. Immutable. The URI of the artifact that is deployed. e.g. `us-
   * docker.pkg.dev/my-project/my-repo/image`. The URI does not include the tag
   * / digest because it captures a lineage of artifacts.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArtifactConfig::class, 'Google_Service_DeveloperConnect_ArtifactConfig');
