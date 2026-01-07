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

class ToolVersions extends \Google\Model
{
  /**
   * Optional. The docker version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $docker;
  /**
   * Optional. The helm version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $helm;
  /**
   * Optional. The kpt version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $kpt;
  /**
   * Optional. The kubectl version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $kubectl;
  /**
   * Optional. The kustomize version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $kustomize;
  /**
   * Optional. The skaffold version to use for Cloud Deploy operations.
   *
   * @var string
   */
  public $skaffold;

  /**
   * Optional. The docker version to use for Cloud Deploy operations.
   *
   * @param string $docker
   */
  public function setDocker($docker)
  {
    $this->docker = $docker;
  }
  /**
   * @return string
   */
  public function getDocker()
  {
    return $this->docker;
  }
  /**
   * Optional. The helm version to use for Cloud Deploy operations.
   *
   * @param string $helm
   */
  public function setHelm($helm)
  {
    $this->helm = $helm;
  }
  /**
   * @return string
   */
  public function getHelm()
  {
    return $this->helm;
  }
  /**
   * Optional. The kpt version to use for Cloud Deploy operations.
   *
   * @param string $kpt
   */
  public function setKpt($kpt)
  {
    $this->kpt = $kpt;
  }
  /**
   * @return string
   */
  public function getKpt()
  {
    return $this->kpt;
  }
  /**
   * Optional. The kubectl version to use for Cloud Deploy operations.
   *
   * @param string $kubectl
   */
  public function setKubectl($kubectl)
  {
    $this->kubectl = $kubectl;
  }
  /**
   * @return string
   */
  public function getKubectl()
  {
    return $this->kubectl;
  }
  /**
   * Optional. The kustomize version to use for Cloud Deploy operations.
   *
   * @param string $kustomize
   */
  public function setKustomize($kustomize)
  {
    $this->kustomize = $kustomize;
  }
  /**
   * @return string
   */
  public function getKustomize()
  {
    return $this->kustomize;
  }
  /**
   * Optional. The skaffold version to use for Cloud Deploy operations.
   *
   * @param string $skaffold
   */
  public function setSkaffold($skaffold)
  {
    $this->skaffold = $skaffold;
  }
  /**
   * @return string
   */
  public function getSkaffold()
  {
    return $this->skaffold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ToolVersions::class, 'Google_Service_CloudDeploy_ToolVersions');
