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

class Stage extends \Google\Collection
{
  protected $collection_key = 'profiles';
  protected $deployParametersType = DeployParameters::class;
  protected $deployParametersDataType = 'array';
  /**
   * Optional. Skaffold profiles to use when rendering the manifest for this
   * stage's `Target`.
   *
   * @var string[]
   */
  public $profiles;
  protected $strategyType = Strategy::class;
  protected $strategyDataType = '';
  /**
   * Optional. The target_id to which this stage points. This field refers
   * exclusively to the last segment of a target name. For example, this field
   * would just be `my-target` (rather than
   * `projects/project/locations/location/targets/my-target`). The location of
   * the `Target` is inferred to be the same as the location of the
   * `DeliveryPipeline` that contains this `Stage`.
   *
   * @var string
   */
  public $targetId;

  /**
   * Optional. The deploy parameters to use for the target in this stage.
   *
   * @param DeployParameters[] $deployParameters
   */
  public function setDeployParameters($deployParameters)
  {
    $this->deployParameters = $deployParameters;
  }
  /**
   * @return DeployParameters[]
   */
  public function getDeployParameters()
  {
    return $this->deployParameters;
  }
  /**
   * Optional. Skaffold profiles to use when rendering the manifest for this
   * stage's `Target`.
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
   * Optional. The strategy to use for a `Rollout` to this stage.
   *
   * @param Strategy $strategy
   */
  public function setStrategy(Strategy $strategy)
  {
    $this->strategy = $strategy;
  }
  /**
   * @return Strategy
   */
  public function getStrategy()
  {
    return $this->strategy;
  }
  /**
   * Optional. The target_id to which this stage points. This field refers
   * exclusively to the last segment of a target name. For example, this field
   * would just be `my-target` (rather than
   * `projects/project/locations/location/targets/my-target`). The location of
   * the `Target` is inferred to be the same as the location of the
   * `DeliveryPipeline` that contains this `Stage`.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Stage::class, 'Google_Service_CloudDeploy_Stage');
