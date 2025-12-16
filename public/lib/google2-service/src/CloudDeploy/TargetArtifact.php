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

namespace Google\Service\CloudDeploy;

class TargetArtifact extends \Google\Model
{
  /**
   * Output only. URI of a directory containing the artifacts. This contains
   * deployment configuration used by Skaffold during a rollout, and all paths
   * are relative to this location.
   *
   * @var string
   */
  public $artifactUri;
  /**
   * Output only. File path of the rendered manifest relative to the URI for the
   * stable phase.
   *
   * @var string
   */
  public $manifestPath;
  protected $phaseArtifactsType = PhaseArtifact::class;
  protected $phaseArtifactsDataType = 'map';
  /**
   * Output only. File path of the resolved Skaffold configuration for the
   * stable phase, relative to the URI.
   *
   * @var string
   */
  public $skaffoldConfigPath;

  /**
   * Output only. URI of a directory containing the artifacts. This contains
   * deployment configuration used by Skaffold during a rollout, and all paths
   * are relative to this location.
   *
   * @param string $artifactUri
   */
  public function setArtifactUri($artifactUri)
  {
    $this->artifactUri = $artifactUri;
  }
  /**
   * @return string
   */
  public function getArtifactUri()
  {
    return $this->artifactUri;
  }
  /**
   * Output only. File path of the rendered manifest relative to the URI for the
   * stable phase.
   *
   * @param string $manifestPath
   */
  public function setManifestPath($manifestPath)
  {
    $this->manifestPath = $manifestPath;
  }
  /**
   * @return string
   */
  public function getManifestPath()
  {
    return $this->manifestPath;
  }
  /**
   * Output only. Map from the phase ID to the phase artifacts for the `Target`.
   *
   * @param PhaseArtifact[] $phaseArtifacts
   */
  public function setPhaseArtifacts($phaseArtifacts)
  {
    $this->phaseArtifacts = $phaseArtifacts;
  }
  /**
   * @return PhaseArtifact[]
   */
  public function getPhaseArtifacts()
  {
    return $this->phaseArtifacts;
  }
  /**
   * Output only. File path of the resolved Skaffold configuration for the
   * stable phase, relative to the URI.
   *
   * @param string $skaffoldConfigPath
   */
  public function setSkaffoldConfigPath($skaffoldConfigPath)
  {
    $this->skaffoldConfigPath = $skaffoldConfigPath;
  }
  /**
   * @return string
   */
  public function getSkaffoldConfigPath()
  {
    return $this->skaffoldConfigPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetArtifact::class, 'Google_Service_CloudDeploy_TargetArtifact');
