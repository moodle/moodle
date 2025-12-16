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

class PhaseConfig extends \Google\Collection
{
  protected $collection_key = 'profiles';
  /**
   * Required. Percentage deployment for the phase.
   *
   * @var int
   */
  public $percentage;
  /**
   * Required. The ID to assign to the `Rollout` phase. This value must consist
   * of lower-case letters, numbers, and hyphens, start with a letter and end
   * with a letter or a number, and have a max length of 63 characters. In other
   * words, it must match the following regex:
   * `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   *
   * @var string
   */
  public $phaseId;
  protected $postdeployType = Postdeploy::class;
  protected $postdeployDataType = '';
  protected $predeployType = Predeploy::class;
  protected $predeployDataType = '';
  /**
   * Optional. Skaffold profiles to use when rendering the manifest for this
   * phase. These are in addition to the profiles list specified in the
   * `DeliveryPipeline` stage.
   *
   * @var string[]
   */
  public $profiles;
  /**
   * Optional. Whether to run verify tests after the deployment via `skaffold
   * verify`.
   *
   * @var bool
   */
  public $verify;

  /**
   * Required. Percentage deployment for the phase.
   *
   * @param int $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return int
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
  /**
   * Required. The ID to assign to the `Rollout` phase. This value must consist
   * of lower-case letters, numbers, and hyphens, start with a letter and end
   * with a letter or a number, and have a max length of 63 characters. In other
   * words, it must match the following regex:
   * `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   *
   * @param string $phaseId
   */
  public function setPhaseId($phaseId)
  {
    $this->phaseId = $phaseId;
  }
  /**
   * @return string
   */
  public function getPhaseId()
  {
    return $this->phaseId;
  }
  /**
   * Optional. Configuration for the postdeploy job of this phase. If this is
   * not configured, there will be no postdeploy job for this phase.
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
   * Optional. Configuration for the predeploy job of this phase. If this is not
   * configured, there will be no predeploy job for this phase.
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
   * Optional. Skaffold profiles to use when rendering the manifest for this
   * phase. These are in addition to the profiles list specified in the
   * `DeliveryPipeline` stage.
   *
   * @param string[] $profiles
   */
  public function setProfiles($profiles)
  {
    $this->profiles = $profiles;
  }
  /**
   * @return string[]
   */
  public function getProfiles()
  {
    return $this->profiles;
  }
  /**
   * Optional. Whether to run verify tests after the deployment via `skaffold
   * verify`.
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
class_alias(PhaseConfig::class, 'Google_Service_CloudDeploy_PhaseConfig');
