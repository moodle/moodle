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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1FlowHookConfig extends \Google\Model
{
  /**
   * Flag that specifies whether the flow should abort after an error in the
   * flow hook. Defaults to `true` (continue on error).
   *
   * @var bool
   */
  public $continueOnError;
  /**
   * Name of the flow hook in the following format:
   * `organizations/{org}/environments/{env}/flowhooks/{point}`. Valid `point`
   * values include: `PreProxyFlowHook`, `PostProxyFlowHook`,
   * `PreTargetFlowHook`, and `PostTargetFlowHook`
   *
   * @var string
   */
  public $name;
  /**
   * Name of the shared flow to invoke in the following format:
   * `organizations/{org}/sharedflows/{sharedflow}`
   *
   * @var string
   */
  public $sharedFlowName;

  /**
   * Flag that specifies whether the flow should abort after an error in the
   * flow hook. Defaults to `true` (continue on error).
   *
   * @param bool $continueOnError
   */
  public function setContinueOnError($continueOnError)
  {
    $this->continueOnError = $continueOnError;
  }
  /**
   * @return bool
   */
  public function getContinueOnError()
  {
    return $this->continueOnError;
  }
  /**
   * Name of the flow hook in the following format:
   * `organizations/{org}/environments/{env}/flowhooks/{point}`. Valid `point`
   * values include: `PreProxyFlowHook`, `PostProxyFlowHook`,
   * `PreTargetFlowHook`, and `PostTargetFlowHook`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Name of the shared flow to invoke in the following format:
   * `organizations/{org}/sharedflows/{sharedflow}`
   *
   * @param string $sharedFlowName
   */
  public function setSharedFlowName($sharedFlowName)
  {
    $this->sharedFlowName = $sharedFlowName;
  }
  /**
   * @return string
   */
  public function getSharedFlowName()
  {
    return $this->sharedFlowName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1FlowHookConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1FlowHookConfig');
