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

class ArtifactDeployment extends \Google\Collection
{
  protected $collection_key = 'sourceCommitUris';
  /**
   * Output only. The artifact alias in the deployment spec, with Tag/SHA. e.g.
   * us-docker.pkg.dev/my-project/my-repo/image:1.0.0
   *
   * @var string
   */
  public $artifactAlias;
  /**
   * Output only. The artifact that is deployed.
   *
   * @var string
   */
  public $artifactReference;
  /**
   * Output only. The summary of container status of the artifact deployment.
   * Format as `ContainerStatusState-Reason : restartCount` e.g. "Waiting-
   * ImagePullBackOff : 3"
   *
   * @var string
   */
  public $containerStatusSummary;
  /**
   * Output only. The time at which the deployment was deployed.
   *
   * @var string
   */
  public $deployTime;
  /**
   * Output only. Unique identifier of `ArtifactDeployment`.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The source commits at which this artifact was built. Extracted
   * from provenance.
   *
   * @var string[]
   */
  public $sourceCommitUris;
  /**
   * Output only. The time at which the deployment was undeployed, all artifacts
   * are considered undeployed once this time is set.
   *
   * @var string
   */
  public $undeployTime;

  /**
   * Output only. The artifact alias in the deployment spec, with Tag/SHA. e.g.
   * us-docker.pkg.dev/my-project/my-repo/image:1.0.0
   *
   * @param string $artifactAlias
   */
  public function setArtifactAlias($artifactAlias)
  {
    $this->artifactAlias = $artifactAlias;
  }
  /**
   * @return string
   */
  public function getArtifactAlias()
  {
    return $this->artifactAlias;
  }
  /**
   * Output only. The artifact that is deployed.
   *
   * @param string $artifactReference
   */
  public function setArtifactReference($artifactReference)
  {
    $this->artifactReference = $artifactReference;
  }
  /**
   * @return string
   */
  public function getArtifactReference()
  {
    return $this->artifactReference;
  }
  /**
   * Output only. The summary of container status of the artifact deployment.
   * Format as `ContainerStatusState-Reason : restartCount` e.g. "Waiting-
   * ImagePullBackOff : 3"
   *
   * @param string $containerStatusSummary
   */
  public function setContainerStatusSummary($containerStatusSummary)
  {
    $this->containerStatusSummary = $containerStatusSummary;
  }
  /**
   * @return string
   */
  public function getContainerStatusSummary()
  {
    return $this->containerStatusSummary;
  }
  /**
   * Output only. The time at which the deployment was deployed.
   *
   * @param string $deployTime
   */
  public function setDeployTime($deployTime)
  {
    $this->deployTime = $deployTime;
  }
  /**
   * @return string
   */
  public function getDeployTime()
  {
    return $this->deployTime;
  }
  /**
   * Output only. Unique identifier of `ArtifactDeployment`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The source commits at which this artifact was built. Extracted
   * from provenance.
   *
   * @param string[] $sourceCommitUris
   */
  public function setSourceCommitUris($sourceCommitUris)
  {
    $this->sourceCommitUris = $sourceCommitUris;
  }
  /**
   * @return string[]
   */
  public function getSourceCommitUris()
  {
    return $this->sourceCommitUris;
  }
  /**
   * Output only. The time at which the deployment was undeployed, all artifacts
   * are considered undeployed once this time is set.
   *
   * @param string $undeployTime
   */
  public function setUndeployTime($undeployTime)
  {
    $this->undeployTime = $undeployTime;
  }
  /**
   * @return string
   */
  public function getUndeployTime()
  {
    return $this->undeployTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArtifactDeployment::class, 'Google_Service_DeveloperConnect_ArtifactDeployment');
