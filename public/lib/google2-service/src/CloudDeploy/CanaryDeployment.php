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

class CanaryDeployment extends \Google\Collection
{
  protected $collection_key = 'percentages';
  /**
   * Required. The percentage based deployments that will occur as a part of a
   * `Rollout`. List is expected in ascending order and each integer n is 0 <= n
   * < 100. If the GatewayServiceMesh is configured for Kubernetes, then the
   * range for n is 0 <= n <= 100.
   *
   * @var int[]
   */
  public $percentages;
  protected $postdeployType = Postdeploy::class;
  protected $postdeployDataType = '';
  protected $predeployType = Predeploy::class;
  protected $predeployDataType = '';
  /**
   * Optional. Whether to run verify tests after each percentage deployment via
   * `skaffold verify`.
   *
   * @var bool
   */
  public $verify;

  /**
   * Required. The percentage based deployments that will occur as a part of a
   * `Rollout`. List is expected in ascending order and each integer n is 0 <= n
   * < 100. If the GatewayServiceMesh is configured for Kubernetes, then the
   * range for n is 0 <= n <= 100.
   *
   * @param int[] $percentages
   */
  public function setPercentages($percentages)
  {
    $this->percentages = $percentages;
  }
  /**
   * @return int[]
   */
  public function getPercentages()
  {
    return $this->percentages;
  }
  /**
   * Optional. Configuration for the postdeploy job of the last phase. If this
   * is not configured, there will be no postdeploy job for this phase.
   *
   * @param Postdeploy $postdeploy
   */
  public function setPostdeploy(Postdeploy $postdeploy)
  {
    $this->postdeploy = $postdeploy;
  }
  /**
   * @return Postdeploy
   */
  public function getPostdeploy()
  {
    return $this->postdeploy;
  }
  /**
   * Optional. Configuration for the predeploy job of the first phase. If this
   * is not configured, there will be no predeploy job for this phase.
   *
   * @param Predeploy $predeploy
   */
  public function setPredeploy(Predeploy $predeploy)
  {
    $this->predeploy = $predeploy;
  }
  /**
   * @return Predeploy
   */
  public function getPredeploy()
  {
    return $this->predeploy;
  }
  /**
   * Optional. Whether to run verify tests after each percentage deployment via
   * `skaffold verify`.
   *
   * @param bool $verify
   */
  public function setVerify($verify)
  {
    $this->verify = $verify;
  }
  /**
   * @return bool
   */
  public function getVerify()
  {
    return $this->verify;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CanaryDeployment::class, 'Google_Service_CloudDeploy_CanaryDeployment');
