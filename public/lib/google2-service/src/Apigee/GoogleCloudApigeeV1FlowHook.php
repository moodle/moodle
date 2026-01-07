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

class GoogleCloudApigeeV1FlowHook extends \Google\Model
{
  /**
   * Optional. Flag that specifies whether execution should continue if the flow
   * hook throws an exception. Set to `true` to continue execution. Set to
   * `false` to stop execution if the flow hook throws an exception. Defaults to
   * `true`.
   *
   * @var bool
   */
  public $continueOnError;
  /**
   * Description of the flow hook.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Where in the API call flow the flow hook is invoked. Must be
   * one of `PreProxyFlowHook`, `PostProxyFlowHook`, `PreTargetFlowHook`, or
   * `PostTargetFlowHook`.
   *
   * @var string
   */
  public $flowHookPoint;
  /**
   * Shared flow attached to this flow hook, or empty if there is none attached.
   *
   * @var string
   */
  public $sharedFlow;

  /**
   * Optional. Flag that specifies whether execution should continue if the flow
   * hook throws an exception. Set to `true` to continue execution. Set to
   * `false` to stop execution if the flow hook throws an exception. Defaults to
   * `true`.
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
   * Description of the flow hook.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Where in the API call flow the flow hook is invoked. Must be
   * one of `PreProxyFlowHook`, `PostProxyFlowHook`, `PreTargetFlowHook`, or
   * `PostTargetFlowHook`.
   *
   * @param string $flowHookPoint
   */
  public function setFlowHookPoint($flowHookPoint)
  {
    $this->flowHookPoint = $flowHookPoint;
  }
  /**
   * @return string
   */
  public function getFlowHookPoint()
  {
    return $this->flowHookPoint;
  }
  /**
   * Shared flow attached to this flow hook, or empty if there is none attached.
   *
   * @param string $sharedFlow
   */
  public function setSharedFlow($sharedFlow)
  {
    $this->sharedFlow = $sharedFlow;
  }
  /**
   * @return string
   */
  public function getSharedFlow()
  {
    return $this->sharedFlow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1FlowHook::class, 'Google_Service_Apigee_GoogleCloudApigeeV1FlowHook');
