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

class ReleaseCondition extends \Google\Model
{
  protected $dockerVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $dockerVersionSupportedConditionDataType = '';
  protected $helmVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $helmVersionSupportedConditionDataType = '';
  protected $kptVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $kptVersionSupportedConditionDataType = '';
  protected $kubectlVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $kubectlVersionSupportedConditionDataType = '';
  protected $kustomizeVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $kustomizeVersionSupportedConditionDataType = '';
  protected $releaseReadyConditionType = ReleaseReadyCondition::class;
  protected $releaseReadyConditionDataType = '';
  protected $skaffoldSupportedConditionType = SkaffoldSupportedCondition::class;
  protected $skaffoldSupportedConditionDataType = '';
  protected $skaffoldVersionSupportedConditionType = ToolVersionSupportedCondition::class;
  protected $skaffoldVersionSupportedConditionDataType = '';

  /**
   * Output only. Details around the support state of the release's Docker
   * version.
   *
   * @param ToolVersionSupportedCondition $dockerVersionSupportedCondition
   */
  public function setDockerVersionSupportedCondition(ToolVersionSupportedCondition $dockerVersionSupportedCondition)
  {
    $this->dockerVersionSupportedCondition = $dockerVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getDockerVersionSupportedCondition()
  {
    return $this->dockerVersionSupportedCondition;
  }
  /**
   * Output only. Details around the support state of the release's Helm
   * version.
   *
   * @param ToolVersionSupportedCondition $helmVersionSupportedCondition
   */
  public function setHelmVersionSupportedCondition(ToolVersionSupportedCondition $helmVersionSupportedCondition)
  {
    $this->helmVersionSupportedCondition = $helmVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getHelmVersionSupportedCondition()
  {
    return $this->helmVersionSupportedCondition;
  }
  /**
   * Output only. Details around the support state of the release's Kpt version.
   *
   * @param ToolVersionSupportedCondition $kptVersionSupportedCondition
   */
  public function setKptVersionSupportedCondition(ToolVersionSupportedCondition $kptVersionSupportedCondition)
  {
    $this->kptVersionSupportedCondition = $kptVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getKptVersionSupportedCondition()
  {
    return $this->kptVersionSupportedCondition;
  }
  /**
   * Output only. Details around the support state of the release's Kubectl
   * version.
   *
   * @param ToolVersionSupportedCondition $kubectlVersionSupportedCondition
   */
  public function setKubectlVersionSupportedCondition(ToolVersionSupportedCondition $kubectlVersionSupportedCondition)
  {
    $this->kubectlVersionSupportedCondition = $kubectlVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getKubectlVersionSupportedCondition()
  {
    return $this->kubectlVersionSupportedCondition;
  }
  /**
   * Output only. Details around the support state of the release's Kustomize
   * version.
   *
   * @param ToolVersionSupportedCondition $kustomizeVersionSupportedCondition
   */
  public function setKustomizeVersionSupportedCondition(ToolVersionSupportedCondition $kustomizeVersionSupportedCondition)
  {
    $this->kustomizeVersionSupportedCondition = $kustomizeVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getKustomizeVersionSupportedCondition()
  {
    return $this->kustomizeVersionSupportedCondition;
  }
  /**
   * Details around the Releases's overall status.
   *
   * @param ReleaseReadyCondition $releaseReadyCondition
   */
  public function setReleaseReadyCondition(ReleaseReadyCondition $releaseReadyCondition)
  {
    $this->releaseReadyCondition = $releaseReadyCondition;
  }
  /**
   * @return ReleaseReadyCondition
   */
  public function getReleaseReadyCondition()
  {
    return $this->releaseReadyCondition;
  }
  /**
   * Details around the support state of the release's Skaffold version.
   *
   * @param SkaffoldSupportedCondition $skaffoldSupportedCondition
   */
  public function setSkaffoldSupportedCondition(SkaffoldSupportedCondition $skaffoldSupportedCondition)
  {
    $this->skaffoldSupportedCondition = $skaffoldSupportedCondition;
  }
  /**
   * @return SkaffoldSupportedCondition
   */
  public function getSkaffoldSupportedCondition()
  {
    return $this->skaffoldSupportedCondition;
  }
  /**
   * Output only. Details around the support state of the release's Skaffold
   * version.
   *
   * @param ToolVersionSupportedCondition $skaffoldVersionSupportedCondition
   */
  public function setSkaffoldVersionSupportedCondition(ToolVersionSupportedCondition $skaffoldVersionSupportedCondition)
  {
    $this->skaffoldVersionSupportedCondition = $skaffoldVersionSupportedCondition;
  }
  /**
   * @return ToolVersionSupportedCondition
   */
  public function getSkaffoldVersionSupportedCondition()
  {
    return $this->skaffoldVersionSupportedCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReleaseCondition::class, 'Google_Service_CloudDeploy_ReleaseCondition');
